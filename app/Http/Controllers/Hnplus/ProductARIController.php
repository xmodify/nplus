<?php

namespace App\Http\Controllers\Hnplus;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Productivity_opd;
use App\Models\MainSetting;
use Illuminate\Routing\Middleware\Middleware;

#[Middleware('auth', only: ['ari_report', 'ari_product_delete'])]

class ProductARIController extends Controller
{
    public function ari_report(Request $request)
    {
        $start_date = $request->start_date ?: date('Y-m-d', strtotime("first day of this month"));
        $end_date = $request->end_date ?: date('Y-m-d');

        $product = Productivity_opd::whereBetween('report_date', [$start_date, $end_date])
            ->where('shift_time', 'LIKE', '%ARI%')
            ->orderBy('report_date', 'desc')->get();

        $ari_working_hours = MainSetting::where('name', 'ari_working_hours')->value('value') ?? 7;

        $product_summary = DB::select('
            SELECT shift_time, COUNT(shift_time) AS shift_time_sum, SUM(patient_all) AS patient_all,
            SUM(nursing_hours) AS patient_hr, SUM(nurse_oncall) AS nurse_oncall,
            SUM(nurse_partime) AS nurse_partime, SUM(nurse_fulltime) AS nurse_fulltime, SUM(working_hours) AS nurse_hr,
            ((SUM(nursing_hours)*100)/SUM(working_hours)) AS productivity, (SUM(nursing_hours)/SUM(patient_all)) AS nhppd,
            (SUM(patient_all)*(SUM(nursing_hours)/SUM(patient_all))*(1.4/?))/COUNT(shift_time) AS nurse_shift_time
            FROM productivity_opd
            WHERE report_date BETWEEN ? AND ? AND shift_time LIKE "%ARI%"
            GROUP BY shift_time ORDER BY shift_time DESC', [$ari_working_hours, $start_date, $end_date]);

        $product_asc = Productivity_opd::whereBetween('report_date', [$start_date, $end_date])
            ->where('shift_time', 'LIKE', '%ARI%')
            ->orderBy('report_date', 'asc')->get();

        $grouped = $product_asc->groupBy('report_date');
        $report_date = [];
        $morning = [];

        foreach ($grouped as $date => $rows) {
            $report_date[] = DateThai($date);
            $morning[] = optional($rows->firstWhere('shift_time', 'เวรเช้า ARI'))->productivity ?? 0;
        }

        $del_product = Auth::check() && Auth::user()->del_product === 'Y';

        return view('hnplus.product.ari_report', compact(
            'product_summary',
            'product',
            'start_date',
            'end_date',
            'del_product',
            'report_date',
            'morning'
        ));
    }

    public function ari_morning_notify()
    {
        $ari_dep = MainSetting::where('name', 'ari_department')->value('value');
        $ari_dep = $ari_dep ?: "'ARI'";

        $notify = DB::connection('hosxp')->select("
            SELECT COUNT(DISTINCT vn) as patient_all
            FROM ovst WHERE vstdate = DATE(NOW()) AND (main_dep IN ($ari_dep))
            AND vsttime BETWEEN '00:00:00' AND '15:59:59' ");

        foreach ($notify as $row) {
            $patient_all = $row->patient_all;
            $url = url('hnplus/product/ari_morning');
        }

        $message = "🧑‍⚕️งาน ARI" . "\n"
            . "วันที่ " . DateThai(date('Y-m-d')) . "\n"
            . "เวลา 08.00-16.00 น. 🌅เวรเช้า ARI" . "\n"
            . "ผู้ป่วยในเวร " . $patient_all . " ราย" . "\n"
            . "บันทึก Productivity " . "\n"
            . $url . "\n";

        $token = MainSetting::where('name', 'telegram_token')->value('value');
        $chat_ids = explode(',', MainSetting::where('name', 'ari_notifytelegram')->value('value'));

        foreach ($chat_ids as $chat_id) {
            if (empty(trim($chat_id)))
                continue;
            Http::asForm()->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => trim($chat_id),
                'text' => $message,
            ]);
            usleep(500000);
        }

        return response()->json(['success' => 'success'], 200);
    }

    public function ari_morning()
    {
        $ari_dep = MainSetting::where('name', 'ari_department')->value('value');
        $ari_dep = $ari_dep ?: "'ARI'";

        $shift = DB::connection('hosxp')->select("
            SELECT COUNT(DISTINCT vn) as patient_all
            FROM ovst WHERE vstdate = DATE(NOW()) AND (main_dep IN ($ari_dep))
            AND vsttime BETWEEN '00:00:00' AND '15:59:59' ");

        return view('hnplus.product.ari_morning', compact('shift'));
    }

    public function ari_morning_save(Request $request)
    {
        $ari_working_hours = MainSetting::where('name', 'ari_working_hours')->value('value') ?? 7;
        $ari_c = MainSetting::where('name', 'ari_patient_type')->value('value') ?? 0.24;

        $patient_all = $request->patient_all ?? 0;
        $nurse_oncall = $request->nurse_oncall ?? 0;
        $nurse_partime = $request->nurse_partime ?? 0;
        $nurse_fulltime = $request->nurse_fulltime ?? 0;

        $patient_hr = ($patient_all * $ari_c);
        $nurse_total = $nurse_oncall + $nurse_partime + $nurse_fulltime;
        $nurse_hr = $nurse_total * $ari_working_hours;

        if ($nurse_hr > 0) {
            $productivity = ($patient_hr * 100) / $nurse_hr;
        } else {
            $productivity = 0;
        }

        if ($patient_all > 0) {
            $nhppd = $patient_hr / $patient_all;
        } else {
            $nhppd = 0;
        }

        $nurse_shift_time = $patient_all * $nhppd * (1.4 / $ari_working_hours);

        Productivity_opd::updateOrCreate(
            [
                'report_date' => $request->report_date,
                'shift_time' => $request->shift_time,
            ],
            [
                'nurse_fulltime' => $nurse_fulltime,
                'nurse_partime' => $nurse_partime,
                'nurse_oncall' => $nurse_oncall,
                'recorder' => $request->recorder,
                'note' => $request->note,
                'patient_all' => $patient_all,
                'opd' => 0,
                'ari' => $patient_all,
                'nursing_hours' => $patient_hr,
                'working_hours' => $nurse_hr,
                'nurse_shift_time' => $nurse_shift_time,
                'nhppd' => $nhppd,
                'productivity' => $productivity,
            ]
        );

        $message = "🧑‍⚕️ บันทึกงาน ARI 🌅\n" .
            "วันที่ " . DateThai(date('Y-m-d')) . "\n" .
            "เวลา 08.00–16.00 น. 🌅 เวรเช้า ARI\n" .
            "👨‍⚕️ ผู้ป่วยในเวร: {$patient_all} ราย\n" .
            "👩‍⚕️ อัตรากำลัง\n" .
            " - Oncall: {$nurse_oncall}\n" .
            " - เสริม: {$nurse_partime}\n" .
            " - ปกติ: {$nurse_fulltime}\n" .
            "🕒 ชม.การพยาบาล: " . number_format($patient_hr, 2) . "\n" .
            "🕒 ชม.การทำงาน: " . number_format($nurse_hr, 2) . "\n" .
            "📈 Productivity: " . number_format($productivity, 2) . "%\n" .
            "ผู้บันทึก: {$request->recorder}";

        $token = MainSetting::where('name', 'telegram_token')->value('value');
        $chat_ids = explode(',', MainSetting::where('name', 'ari_notifytelegram_save')->value('value'));

        foreach ($chat_ids as $chat_id) {
            if (empty(trim($chat_id)))
                continue;
            Http::asForm()->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => trim($chat_id),
                'text' => $message,
            ]);
            usleep(500000);
        }

        return redirect()->back()->with('success', 'บันทึกข้อมูลและส่งแจ้งเตือนเรียบร้อยแล้ว');
    }

    public function ari_product_delete($id)
    {
        Productivity_opd::find($id)->delete();
        return redirect()->route('hnplus.product.ari_report')->with('danger', 'ลบข้อมูลเรียบร้อยแล้ว');
    }
}
