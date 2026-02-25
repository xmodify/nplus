<?php

namespace App\Http\Controllers\Hnplus;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Productivity_hd;
use App\Models\MainSetting;
use Illuminate\Routing\Middleware\Middleware;

#[Middleware('auth', only: ['hd_report', 'hd_product_delete'])]

class ProductHDController extends Controller
{
    //hd_report--------------------------------------------------------------------------------------------------------------------------
    public function hd_report(Request $request)
    {
        $start_date = $request->start_date ? DateThaiToEn($request->start_date) : date('Y-m-d', strtotime('first day of this month'));
        $end_date = $request->end_date ? DateThaiToEn($request->end_date) : date('Y-m-d');

        $product = Productivity_hd::whereBetween('report_date', [$start_date, $end_date])
            ->orderBy('report_date', 'desc')->get();

        $hd_working_hours = MainSetting::where('name', 'hd_working_hours')->value('value') ?? 7;

        $product_summary = DB::select('
            SELECT shift_time,COUNT(shift_time) AS shift_time_sum,SUM(patient_all) AS patient_all,
            SUM(nursing_hours) AS patient_hr,SUM(nurse_oncall) AS nurse_oncall,
            SUM(nurse_partime) AS nurse_partime,SUM(nurse_fulltime) AS nurse_fulltime, SUM(working_hours) AS nurse_hr,
            ((SUM(nursing_hours)*100)/SUM(working_hours)) AS productivity,(SUM(nursing_hours)/SUM(patient_all)) AS nhppd,
            (SUM(patient_all)*(SUM(nursing_hours)/SUM(patient_all))*(1.4/?))/COUNT(shift_time) AS nurse_shift_time
            FROM productivity_hd
            WHERE report_date BETWEEN ? AND ?
            GROUP BY shift_time ORDER BY shift_time DESC', [$hd_working_hours, $start_date, $end_date]);

        $product_asc = Productivity_hd::whereBetween('report_date', [$start_date, $end_date])
            ->orderBy('report_date', 'asc')->get();
        $grouped = $product_asc->groupBy('report_date');
        $report_date = [];
        $morning = [];
        foreach ($grouped as $date => $rows) {
            $report_date[] = DateThai($date);
            $morning[] = optional($rows->firstWhere('shift_time', 'เวรเช้า'))->productivity ?? 0;
        }

        $del_product = Auth::check() && Auth::user()->del_product === 'Y';

        return view('hnplus.product.hd_report', compact(
            'product_summary',
            'product',
            'start_date',
            'end_date',
            'del_product',
            'report_date',
            'morning'
        ));
    }

    public function hd_product_delete($id)
    {
        Productivity_hd::find($id)->delete();
        return redirect()->route('hnplus.product.hd_report')->with('danger', 'ลบข้อมูลเรียบร้อยแล้ว');
    }

    public function hd_morning_notify()
    {
        $hd_dep = MainSetting::where('name', 'hd_department')->value('value');
        $hd_dep = $hd_dep ?: "'025'";

        $notify = DB::connection('hosxp')->select("
            SELECT IFNULL(COUNT(DISTINCT o1.vn),0) AS patient_all
            FROM opd_dep_queue o1, ovst o2 WHERE o1.depcode IN ($hd_dep)
            AND o1.vn = o2.vn AND o2.vstdate = DATE(NOW())
            AND o2.vsttime BETWEEN '00:00:00' AND '15:59:59' ");

        foreach ($notify as $row) {
            $patient_all = $row->patient_all;
            $url = url('product/hd_morning');
        }

        $message = "🧑‍⚕️งานฟอกเลือดด้วยเครื่องไตเทียม HD" . "\n"
            . "วันที่ " . DateThai(date('Y-m-d')) . "\n"
            . "เวลา 08.00-16.00 น. 🌅เวรเช้า" . "\n"
            . "ผู้ป่วยในเวร " . $patient_all . " ราย" . "\n"
            . "บันทึก Productivity " . "\n"
            . $url . "\n";

        $token = MainSetting::where('name', 'telegram_token')->value('value');
        $chat_ids = explode(',', MainSetting::where('name', 'hd_notifytelegram')->value('value'));

        foreach ($chat_ids as $chat_id) {
            $url = "https://api.telegram.org/bot$token/sendMessage";
            $data = ['chat_id' => $chat_id, 'text' => $message];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);
            curl_close($ch);
            sleep(1);
        }

        return response()->json(['success' => 'success'], 200);
    }

    public function hd_morning()
    {
        $hd_dep = MainSetting::where('name', 'hd_department')->value('value');
        $hd_dep = $hd_dep ?: "'025'";

        $shift = DB::connection('hosxp')->select("
            SELECT IFNULL(COUNT(DISTINCT o1.vn),0) AS patient_all
            FROM opd_dep_queue o1, ovst o2 WHERE o1.depcode IN ($hd_dep)
            AND o1.vn = o2.vn AND o2.vstdate = DATE(NOW())
            AND o2.vsttime BETWEEN '00:00:00' AND '15:59:59' ");

        return view('hnplus.product.hd_morning', compact('shift'));
    }

    public function hd_morning_save(Request $request)
    {
        $request->validate([
            'nurse_oncall' => 'required|numeric',
            'nurse_partime' => 'required|numeric',
            'nurse_fulltime' => 'required|numeric',
            'recorder' => 'required|string',
        ]);

        $hd_working_hours = MainSetting::where('name', 'hd_working_hours')->value('value') ?? 7;
        $hd_patient_type = MainSetting::where('name', 'hd_patient_type')->value('value') ?? 0.24;

        $patient_all = $request->patient_all ?? 0;
        $nurse_oncall = $request->nurse_oncall ?? 0;
        $nurse_partime = $request->nurse_partime ?? 0;
        $nurse_fulltime = $request->nurse_fulltime ?? 0;

        $patient_hr = $patient_all * $hd_patient_type;
        $nurse_total = $nurse_oncall + $nurse_partime + $nurse_fulltime;
        $nurse_hr = $nurse_total * $hd_working_hours;

        $productivity = ($patient_hr * 100) / max(1, $nurse_hr);
        $nhppd = $patient_hr / max(1, $patient_all);
        $nurse_shift_time = $patient_all * $nhppd * (1.4 / $hd_working_hours);

        Productivity_hd::updateOrCreate(
            ['report_date' => $request->report_date, 'shift_time' => $request->shift_time],
            [
                'nurse_fulltime' => $nurse_fulltime,
                'nurse_partime' => $nurse_partime,
                'nurse_oncall' => $nurse_oncall,
                'recorder' => $request->recorder,
                'note' => $request->note,
                'patient_all' => $patient_all,
                'nursing_hours' => $patient_hr,
                'working_hours' => $nurse_hr,
                'nurse_shift_time' => $nurse_shift_time,
                'nhppd' => $nhppd,
                'productivity' => $productivity,
            ]
        );

        $message = "🏥 งานฟอกเลือดด้วยเครื่องไตเทียม HD\n" .
            "วันที่ " . DateThai(date("Y-m-d")) . "\n" .
            "เวลา 08.00–16.00 น. 🌅 เวรเช้า\n" .
            "👨‍⚕️ ผู้ป่วยในเวร: {$patient_all} ราย\n" .
            "👩‍⚕️ อัตรากำลัง\n" .
            " - Oncall: {$nurse_oncall}\n" .
            " - เสริม: {$nurse_partime}\n" .
            " - ปกติ: {$nurse_fulltime}\n" .
            "🕒 ชม.การพยาบาล: " . number_format($patient_hr, 2) . "\n" .
            "🕒 ชม.การทำงาน: " . number_format($nurse_hr, 2) . "\n" .
            "📊 Productivity: " . number_format($productivity, 2) . " %\n" .
            "🧮 NHPPD: " . number_format($nhppd, 2) . "\n" .
            "ผู้บันทึก: {$request->recorder}";

        $token = MainSetting::where('name', 'telegram_token')->value('value');
        $chat_ids = explode(',', MainSetting::where('name', 'hd_notifytelegram_save')->value('value'));

        foreach ($chat_ids as $chat_id) {
            Http::asForm()->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => trim($chat_id),
                'text' => $message,
            ]);
            usleep(500000);
        }

        return redirect()->back()->with('success', '✅ ส่งข้อมูลเวรเช้าเรียบร้อยแล้ว');
    }
}
