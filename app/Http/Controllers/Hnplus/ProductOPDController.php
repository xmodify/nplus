<?php

namespace App\Http\Controllers\Hnplus;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Productivity_opd;
use App\Models\MainSetting;
use Illuminate\Routing\Controllers\Middleware;

#[Middleware('auth', only: ['opd_report', 'opd_product_delete'])]

class ProductOPDController extends Controller
{
    //opd_product_delete--------------------------------------------------------------------------------------------------------------------------
    public function opd_report(Request $request)
    {
        $start_date = $request->start_date ? DateThaiToEn($request->start_date) : date('Y-m-d', strtotime("first day of this month"));
        $end_date = $request->end_date ? DateThaiToEn($request->end_date) : date('Y-m-d');

        $product = Productivity_opd::whereBetween('report_date', [$start_date, $end_date])
            ->where('is_holiday', 'N')
            ->orderBy('report_date', 'desc')->get();

        $opd_working_hours = MainSetting::where('name', 'opd_working_hours')->value('value') ?? 7;

        $product_summary = DB::select('
            SELECT shift_time,COUNT(shift_time) AS shift_time_sum,SUM(patient_all) AS patient_all,
            SUM(nursing_hours) AS patient_hr,SUM(nurse_oncall) AS nurse_oncall,
            SUM(nurse_partime) AS nurse_partime,SUM(nurse_fulltime) AS nurse_fulltime, SUM(working_hours) AS nurse_hr,
            ((SUM(nursing_hours)*100)/SUM(working_hours)) AS productivity,(SUM(nursing_hours)/SUM(patient_all)) AS nhppd,
            (SUM(patient_all)*(SUM(nursing_hours)/SUM(patient_all))*(1.4/?))/COUNT(shift_time) AS nurse_shift_time
            FROM productivity_opd
            WHERE report_date BETWEEN ? AND ? AND is_holiday = \'N\'
            GROUP BY shift_time ORDER BY shift_time DESC', [$opd_working_hours, $start_date, $end_date]);

        $product_asc = Productivity_opd::whereBetween('report_date', [$start_date, $end_date])
            ->where('is_holiday', 'N')
            ->orderBy('report_date', 'asc')->get();
        $grouped = $product_asc->groupBy('report_date');
        $report_date = [];
        $morning = [];
        $bd = [];
        foreach ($grouped as $date => $rows) {
            $report_date[] = DateThai($date);
            $morning[] = optional($rows->firstWhere('shift_time', 'เวรเช้า'))->productivity ?? 0;
            $bd[] = optional($rows->firstWhere('shift_time', 'เวร BD'))->productivity ?? 0;
        }

        $del_product = Auth::check() && Auth::user()->del_product === 'Y';

        return view('hnplus.product.opd_report', compact(
            'product_summary',
            'product',
            'start_date',
            'end_date',
            'del_product',
            'report_date',
            'morning',
            'bd'
        ));
    }

    //opd_holiday_report--------------------------------------------------------------------------------------------------------------------------
    public function opd_holiday_report(Request $request)
    {
        $start_date = $request->start_date ? DateThaiToEn($request->start_date) : date('Y-m-d', strtotime("first day of this month"));
        $end_date = $request->end_date ? DateThaiToEn($request->end_date) : date('Y-m-d');

        $product = Productivity_opd::whereBetween('report_date', [$start_date, $end_date])
            ->where('is_holiday', 'Y')
            ->orderBy('report_date', 'desc')->get();

        $opd_working_hours = MainSetting::where('name', 'opd_working_hours')->value('value') ?? 7;

        $product_summary = DB::select('
            SELECT shift_time,COUNT(shift_time) AS shift_time_sum,SUM(patient_all) AS patient_all,
            SUM(nursing_hours) AS patient_hr,SUM(nurse_oncall) AS nurse_oncall,
            SUM(nurse_partime) AS nurse_partime,SUM(nurse_fulltime) AS nurse_fulltime, SUM(working_hours) AS nurse_hr,
            ((SUM(nursing_hours)*100)/SUM(working_hours)) AS productivity,(SUM(nursing_hours)/SUM(patient_all)) AS nhppd,
            (SUM(patient_all)*(SUM(nursing_hours)/SUM(patient_all))*(1.4/?))/COUNT(shift_time) AS nurse_shift_time
            FROM productivity_opd
            WHERE report_date BETWEEN ? AND ? AND is_holiday = \'Y\'
            GROUP BY shift_time ORDER BY shift_time DESC', [$opd_working_hours, $start_date, $end_date]);

        $product_asc = Productivity_opd::whereBetween('report_date', [$start_date, $end_date])
            ->where('is_holiday', 'Y')
            ->orderBy('report_date', 'asc')->get();
        $grouped = $product_asc->groupBy('report_date');
        $report_date = [];
        $morning = [];
        $bd = [];
        foreach ($grouped as $date => $rows) {
            $report_date[] = DateThai($date);
            $morning[] = optional($rows->firstWhere('shift_time', 'เวรเช้า'))->productivity ?? 0;
            $bd[] = optional($rows->firstWhere('shift_time', 'เวร BD'))->productivity ?? 0;
        }

        $del_product = Auth::check() && Auth::user()->del_product === 'Y';

        return view('hnplus.product.opd_holiday_report', compact(
            'product_summary',
            'product',
            'start_date',
            'end_date',
            'del_product',
            'report_date',
            'morning',
            'bd'
        ));
    }

    //product_delete----------------------------------------------------------------------------------------------------------------
    public function opd_product_delete($id)
    {
        $product = Productivity_opd::find($id)->delete();
        return redirect()->route('hnplus.product.opd_report')->with('danger', 'ลบข้อมูลเรียบร้อยแล้ว');
    }

    //แจ้งเตือนสถานะการณ์สรุปเวรเช้า รัน 16.00 น.---------------------------------------------------------------------------------------------
    public function opd_morning_notify()
    {
        $opd_dep = MainSetting::where('name', 'opd_department')->value('value');
        $opd_dep = $opd_dep ?: "'002'";

        $notify = DB::connection('hosxp')->select("
            SELECT COUNT(DISTINCT vn) as patient_all,
            COALESCE(SUM(CASE WHEN main_dep IN ($opd_dep) THEN 1 ELSE 0 END), 0) AS opd
            FROM ovst WHERE vstdate = DATE(NOW()) AND (main_dep IN ($opd_dep))
            AND vsttime BETWEEN '00:00:00' AND '15:59:59' ");

        foreach ($notify as $row) {
            $patient_all = $row->patient_all;
            $url = url('product/opd_morning');
        }

        //แจ้งเตือน Telegram

        $message = "🧑‍⚕️งานผู้ป่วยนอก OPD" . "\n"
            . "วันที่ " . DateThai(date('Y-m-d')) . "\n"
            . "เวลา 08.00-16.00 น. 🌅เวรเช้า" . "\n"
            . "ผู้ป่วยในเวร " . $patient_all . " ราย" . "\n"
            . "บันทึก Productivity " . "\n"
            . $url . "\n";

        // ✅ ส่งข้อความ Telegram
        $token = MainSetting::where('name', 'telegram_token')->value('value');
        $chat_ids = explode(',', MainSetting::where('name', 'opd_notifytelegram')->value('value'));

        foreach ($chat_ids as $chat_id) {
            $url = "https://api.telegram.org/bot$token/sendMessage";

            $data = [
                'chat_id' => $chat_id,
                'text' => $message
            ];
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

    //opd_morning-------------------------------------------------------------------------------------------------------------
    public function opd_morning()
    {
        $opd_dep = MainSetting::where('name', 'opd_department')->value('value');
        $opd_dep = $opd_dep ?: "'002'";

        $shift = DB::connection('hosxp')->select("
            SELECT COUNT(DISTINCT vn) as patient_all,
            COALESCE(SUM(CASE WHEN main_dep IN ($opd_dep) THEN 1 ELSE 0 END), 0) AS opd
            FROM ovst WHERE vstdate = DATE(NOW()) AND (main_dep IN ($opd_dep))
            AND vsttime BETWEEN '00:00:00' AND '15:59:59' ");

        return view('hnplus.product.opd_morning', compact('shift'));
    }

    //opd_morning_save------------------------------------------------------------------------------------------------------------------
    public function opd_morning_save(Request $request)
    {
        // ตรวจสอบข้อมูลที่จำเป็น
        $request->validate([
            'nurse_oncall' => 'required|numeric',
            'nurse_partime' => 'required|numeric',
            'nurse_fulltime' => 'required|numeric',
            'recorder' => 'required|string',
        ]);

        // ==============================
        //   Get Constants from MainSetting
        // ==============================
        $opd_working_hours = MainSetting::where('name', 'opd_working_hours')->value('value') ?? 7;
        $opd_c = MainSetting::where('name', 'opd_patient_type_opd')->value('value') ?? 0.37;


        // ==============================
        //   กำหนดค่า default = 0
        // ==============================
        $patient_all = $request->patient_all ?? 0;


        $nurse_oncall = $request->nurse_oncall ?? 0;
        $nurse_partime = $request->nurse_partime ?? 0;
        $nurse_fulltime = $request->nurse_fulltime ?? 0;

        // ==============================
        //   คำนวณแบบเดิม (OPD Original)
        // ==============================
        $patient_hr = ($patient_all * $opd_c);
        $nurse_total = $nurse_oncall + $nurse_partime + $nurse_fulltime;
        // $nurse_hr = $nurse_total * 7;
        $nurse_hr = $nurse_total * $opd_working_hours;

        $productivity = ($patient_hr * 100) / max(1, $nurse_hr);
        $nhppd = $patient_hr / max(1, $patient_all);
        $nurse_shift_time = $patient_all * $nhppd * (1.4 / $opd_working_hours);

        // ==============================
        //   เช็ควันหยุด
        // ==============================
        $is_holiday = DB::table('holiday')
            ->where('holiday_date', $request->report_date)
            ->exists() ? 'Y' : 'N';

        // ==============================
        //   บันทึกข้อมูลลงฐานข้อมูล
        // ==============================

        Productivity_opd::updateOrCreate(
            // 🔎 เงื่อนไขเช็คซ้ำ (วันที่ + เวร)
            [
                'report_date' => $request->report_date,
                'shift_time' => $request->shift_time,
            ],
            // 📝 ข้อมูล insert / update (คอลัมน์เดิมทั้งหมด)
            [
                'nurse_fulltime' => $nurse_fulltime,
                'nurse_partime' => $nurse_partime,
                'nurse_oncall' => $nurse_oncall,
                'recorder' => $request->recorder,
                'note' => $request->note,
                'is_holiday' => $is_holiday,

                'patient_all' => $patient_all,

                'nursing_hours' => $patient_hr,
                'working_hours' => $nurse_hr,
                'nurse_shift_time' => $nurse_shift_time,
                'nhppd' => $nhppd,
                'productivity' => $productivity,
            ]
        );

        // ==============================
        //   ข้อความแจ้งเตือน Telegram (แบบ VIP)
        // ==============================
        $message =
            "🏥 งานผู้ป่วยนอก OPD\n" .
            "วันที่ " . DateThai(date('Y-m-d')) . "\n" .
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

        // ==============================
        //   ส่งข้อความ Telegram
        // ==============================
        $token = MainSetting::where('name', 'telegram_token')->value('value');
        $chat_ids = explode(',', MainSetting::where('name', 'opd_notifytelegram_save')->value('value'));

        foreach ($chat_ids as $chat_id) {
            Http::asForm()->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => trim($chat_id),
                'text' => $message,
            ]);
            usleep(500000);
        }

        return redirect()->back()->with('success', '✅ ส่งข้อมูลเวรเช้าเรียบร้อยแล้ว');
    }

    //แจ้งเตือนสถานะการณ์สรุปเวร BD รัน 20.00 น.---------------------------------------------------------------------------------------------
    public function opd_bd_notify()
    {
        $opd_dep = MainSetting::where('name', 'opd_department')->value('value');
        $opd_dep = $opd_dep ?: "'002'";

        $notify = DB::connection('hosxp')->select("
            SELECT COUNT(DISTINCT vn) as patient_all,
            COALESCE(SUM(CASE WHEN main_dep IN ($opd_dep) THEN 1 ELSE 0 END), 0) AS opd
            FROM ovst WHERE vstdate = DATE(NOW()) AND (main_dep IN ($opd_dep))
            AND vsttime BETWEEN '16:00:00' AND '20:00:00' ");

        $patient_all = 0;
        $report_url  = url('product/opd_bd');

        foreach ($notify as $row) {
            $patient_all = $row->patient_all;
        }

        //แจ้งเตือน Telegram
        $message = "🧑‍⚕️งานผู้ป่วยนอก OPD" . "\n"
            . "วันที่ " . DateThai(date('Y-m-d')) . "\n"
            . "🌇เวร BD" . "\n"
            . "ผู้ป่วยในเวร " . $patient_all . " ราย" . "\n"
            . "บันทึก Productivity " . "\n"
            . $report_url . "\n";

        // ✅ ส่งข้อความ Telegram
        $token    = MainSetting::where('name', 'telegram_token')->value('value');
        $chat_ids = explode(',', MainSetting::where('name', 'opd_notifytelegram')->value('value'));

        foreach ($chat_ids as $chat_id) {
            Http::asForm()->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => trim($chat_id),
                'text'    => $message,
            ]);
            sleep(1);
        }

        return response()->json(['success' => 'success'], 200);
    }

    //opd_bd-------------------------------------------------------------------------------------------------------------
    public function opd_bd()
    {
        $opd_dep = MainSetting::where('name', 'opd_department')->value('value');
        $opd_dep = $opd_dep ?: "'002'";

        $shift = DB::connection('hosxp')->select("
            SELECT COUNT(DISTINCT vn) as patient_all,
            COALESCE(SUM(CASE WHEN main_dep IN ($opd_dep) THEN 1 ELSE 0 END), 0) AS opd
            FROM ovst WHERE vstdate = DATE(NOW()) AND (main_dep IN ($opd_dep))
            AND vsttime BETWEEN '16:00:00' AND '20:00:00' ");

        return view('hnplus.product.opd_bd', compact('shift'));
    }

    //opd_bd_save------------------------------------------------------------------------------------------------------------------
    public function opd_bd_save(Request $request)
    {
        // ตรวจสอบข้อมูลที่จำเป็น
        $request->validate([
            'nurse_oncall' => 'required|numeric',
            'nurse_partime' => 'required|numeric',
            'nurse_fulltime' => 'required|numeric',
            'recorder' => 'required|string',
        ]);

        // ==============================
        //   Get Constants from MainSetting
        // ==============================
        $opd_working_hours = MainSetting::where('name', 'opd_working_hours')->value('value') ?? 7;
        $opd_c = MainSetting::where('name', 'opd_patient_type_opd')->value('value') ?? 0.37;


        // ==============================
        //   กำหนดค่า default = 0
        // ==============================
        $patient_all = $request->patient_all ?? 0;


        $nurse_oncall = $request->nurse_oncall ?? 0;
        $nurse_partime = $request->nurse_partime ?? 0;
        $nurse_fulltime = $request->nurse_fulltime ?? 0;

        // ==============================
        //   คำนวณแบบเดิม (OPD Original)
        // ==============================
        $patient_hr = ($patient_all * $opd_c);
        $nurse_total = $nurse_oncall + $nurse_partime + $nurse_fulltime;
        // $nurse_hr = $nurse_total * 7;
        $nurse_hr = $nurse_total * $opd_working_hours;

        $productivity = ($patient_hr * 100) / max(1, $nurse_hr);
        $nhppd = $patient_hr / max(1, $patient_all);
        $nurse_shift_time = $patient_all * $nhppd * (1.4 / $opd_working_hours);

        // ==============================
        //   เช็ควันหยุด
        // ==============================
        $is_holiday = DB::table('holiday')
            ->where('holiday_date', $request->report_date)
            ->exists() ? 'Y' : 'N';

        // ==============================
        //   บันทึกข้อมูลลงฐานข้อมูล
        // ==============================

        Productivity_opd::updateOrCreate(
            // 🔎 เงื่อนไขเช็คซ้ำ (วันที่ + เวร)
            [
                'report_date' => $request->report_date,
                'shift_time' => $request->shift_time,
            ],
            // 📝 ข้อมูล insert / update (คอลัมน์เดิมทั้งหมด)
            [
                'nurse_fulltime' => $nurse_fulltime,
                'nurse_partime' => $nurse_partime,
                'nurse_oncall' => $nurse_oncall,
                'recorder' => $request->recorder,
                'note' => $request->note,
                'is_holiday' => $is_holiday,

                'patient_all' => $patient_all,

                'nursing_hours' => $patient_hr,
                'working_hours' => $nurse_hr,
                'nurse_shift_time' => $nurse_shift_time,
                'nhppd' => $nhppd,
                'productivity' => $productivity,
            ]
        );

        // ==============================
        //   ข้อความแจ้งเตือน Telegram (แบบ VIP)
        // ==============================
        $message =
            "🏥 งานผู้ป่วยนอก OPD\n" .
            "วันที่ " . DateThai(date('Y-m-d')) . "\n" .
            "🌇เวร BD\n" .
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

        // ==============================
        //   ส่งข้อความ Telegram
        // ==============================
        $token = MainSetting::where('name', 'telegram_token')->value('value');
        $chat_ids = explode(',', MainSetting::where('name', 'opd_notifytelegram_save')->value('value'));

        foreach ($chat_ids as $chat_id) {
            Http::asForm()->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => trim($chat_id),
                'text' => $message,
            ]);
            usleep(500000);
        }

        return redirect()->back()->with('success', '✅ ส่งข้อมูลเวร BD เรียบร้อยแล้ว');
    }
}
