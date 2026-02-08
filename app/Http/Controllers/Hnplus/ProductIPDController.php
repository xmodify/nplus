<?php

namespace App\Http\Controllers\Hnplus;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Productivity_ipd;
use App\Models\MainSetting;
use Illuminate\Routing\Middleware\Middleware;

#[Middleware('auth', only: ['ipd_report', 'ipd_product_delete'])]

class ProductIPDController extends Controller
{
    //ipd_report--------------------------------------------------------------------------------------------------------------------------
    public function ipd_report(Request $request)
    {
        $start_date = $request->start_date ?: date('Y-m-d', strtotime("first day of this month"));
        $end_date = $request->end_date ?: date('Y-m-d');

        // $product=Nurse_productivity_ipd::whereBetween('report_date',[$start_date, $end_date])
        //     ->orderBy('report_date', 'desc')->get(); 
        $product = Productivity_ipd::whereBetween('report_date', [$start_date, $end_date])
            ->orderBy('report_date', 'desc')->get();

        $ipd_working_hours = MainSetting::where('name', 'ipd_working_hours')->value('value') ?? 7;

        $product_summary = DB::select('
            SELECT CASE WHEN shift_time = "เวรเช้า" THEN "1" WHEN shift_time = "เวรบ่าย" THEN "2"
            WHEN shift_time = "เวรดึก" THEN "3" END AS "id",shift_time,COUNT(shift_time) AS shift_time_sum,
            SUM(patient_all) AS patient_all,
            SUM(patient_convalescent) AS patient_convalescent,
            SUM(patient_moderate) AS patient_moderate,
            SUM(patient_semi_critical) AS patient_semi_critical,
            SUM(patient_critical) AS patient_critical,
            SUM(nursing_hours) AS patient_hr,
            SUM(nurse_oncall) AS nurse_oncall,SUM(nurse_partime) AS nurse_partime,SUM(nurse_fulltime) AS nurse_fulltime,
            SUM(working_hours) AS nurse_hr,
            ((SUM(nursing_hours)*100)/SUM(working_hours)) AS productivity,
            (SUM(nursing_hours)/SUM(patient_all)) AS nhppd,
            (SUM(patient_all)*(SUM(nursing_hours)/SUM(patient_all))*(1.4/?))/COUNT(shift_time) AS nurse_shift_time
            FROM productivity_ipd
            WHERE report_date BETWEEN ? AND ?
            GROUP BY shift_time ORDER BY id', [$ipd_working_hours, $start_date, $end_date]);

        // เตรียมข้อมูลสำหรับกราฟ
        $product_asc = Productivity_ipd::whereBetween('report_date', [$start_date, $end_date])
            ->orderBy('report_date', 'asc')->get();
        $grouped = $product_asc->groupBy('report_date');
        $report_date = [];
        $night = [];
        $morning = [];
        $afternoon = [];
        foreach ($grouped as $date => $rows) {
            $report_date[] = DateThai($date);
            // ค้นหาค่า productivity ของแต่ละเวร
            $night[] = optional($rows->firstWhere('shift_time', 'เวรดึก'))->productivity ?? 0;
            $morning[] = optional($rows->firstWhere('shift_time', 'เวรเช้า'))->productivity ?? 0;
            $afternoon[] = optional($rows->firstWhere('shift_time', 'เวรบ่าย'))->productivity ?? 0;
        }

        // ลบ Product ------------------
        $del_product = Auth::check() && Auth::user()->del_product === 'Y';

        return view('hnplus.product.ipd_report', compact(
            'product_summary',
            'product',
            'start_date',
            'end_date',
            'del_product',
            'report_date',
            'night',
            'morning',
            'afternoon'
        ));
    }

    //product_delete----------------------------------------------------------------------------------------------------------------
    public function ipd_product_delete($id)
    {
        $product = Productivity_ipd::find($id)->delete();
        return redirect()->route('hnplus.product.ipd_report')->with('danger', 'ลบข้อมูลเรียบร้อยแล้ว');
    }

    //แจ้งเตือนสถานะการณ์สรุปเวรดึก รัน 08.00 น.---------------------------------------------------------------------------------------------
    public function ipd_night_notify()
    {
        $ipd_ward = MainSetting::where('name', 'ipd_ward')->value('value') ?? '01';

        $notify = DB::connection('hosxp')->select("
            SELECT
                SUM(CASE WHEN ipd_nurse_eval_range_code LIKE '1%' THEN 1 ELSE 0 END) AS convalescent,
                SUM(CASE WHEN ipd_nurse_eval_range_code LIKE '2%' THEN 1 ELSE 0 END) AS Moderate,
                SUM(CASE WHEN ipd_nurse_eval_range_code LIKE '3%' THEN 1 ELSE 0 END) AS Semi_critical,
                SUM(CASE WHEN ipd_nurse_eval_range_code LIKE '4%' THEN 1 ELSE 0 END) AS Critical,
                SUM(CASE WHEN ipd_nurse_eval_range_code IS NULL OR ipd_nurse_eval_range_code = '' THEN 1 ELSE 0 END) AS severe_type_null,
                COUNT(DISTINCT an) AS patient_all
            FROM (
                SELECT n.an, n.ipd_nurse_eval_range_code
                FROM ipt i
                JOIN ipd_nurse_note n ON n.an = i.an
                JOIN (
                    SELECT an, note_date, MAX(note_time) AS last_time
                    FROM ipd_nurse_note
                    WHERE note_date = CURDATE() AND note_time BETWEEN '00:00:01' AND '07:59:59'
                    GROUP BY an, note_date
                ) x ON x.an = n.an AND x.note_date = n.note_date AND x.last_time = n.note_time
                WHERE i.ward IN ('$ipd_ward') AND i.confirm_discharge = 'N'
            ) t
        ");

        foreach ($notify as $row) {
            $patient_all = $row->patient_all;
            $convalescent = $row->convalescent;
            $Moderate = $row->Moderate;
            $Semi_critical = $row->Semi_critical;
            $Critical = $row->Critical;
            $severe_type_null = $row->severe_type_null;
            $url = url('hnplus/product/ipd_night');
        }

        //แจ้งเตือน Telegram

        $message = "🛏️ งานผู้ป่วยใน สามัญ" . "\n"
            . "วันที่ " . DateThai(date('Y-m-d')) . "\n"
            . "เวลา 00.00-08.00 น. 🌙เวรดึก" . "\n"
            . "ผู้ป่วยในเวร " . $patient_all . " ราย" . "\n"
            . " -Convalescent " . $convalescent . " ราย" . "\n"
            . " -Moderate " . $Moderate . " ราย" . "\n"
            . " -Semi critical " . $Semi_critical . " ราย" . "\n"
            . " -Critical " . $Critical . " ราย" . "\n"
            . " -ไม่ระบุความรุนแรง " . $severe_type_null . " ราย" . "\n" . "\n"
            . "บันทึก Productivity " . "\n"
            . $url . "\n";

        // ✅ ส่งข้อความ Telegram
        $token = MainSetting::where('name', 'telegram_token')->value('value');
        $chat_ids = explode(',', MainSetting::where('name', 'ipd_notifytelegram')->value('value'));

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

    //ipd_night------------------------------------------------------------------------------------------------------------------------
    public function ipd_night()
    {
        $ipd_ward = MainSetting::where('name', 'ipd_ward')->value('value') ?? '01';

        $shift = DB::connection('hosxp')->select("
            SELECT
                SUM(CASE WHEN ipd_nurse_eval_range_code LIKE '1%' THEN 1 ELSE 0 END) AS convalescent,
                SUM(CASE WHEN ipd_nurse_eval_range_code LIKE '2%' THEN 1 ELSE 0 END) AS Moderate,
                SUM(CASE WHEN ipd_nurse_eval_range_code LIKE '3%' THEN 1 ELSE 0 END) AS Semi_critical,
                SUM(CASE WHEN ipd_nurse_eval_range_code LIKE '4%' THEN 1 ELSE 0 END) AS Critical,
                SUM(CASE WHEN ipd_nurse_eval_range_code IS NULL OR ipd_nurse_eval_range_code = '' THEN 1 ELSE 0 END) AS severe_type_null,
                COUNT(DISTINCT an) AS patient_all
            FROM (
                SELECT n.an, n.ipd_nurse_eval_range_code
                FROM ipt i
                JOIN ipd_nurse_note n ON n.an = i.an
                JOIN (
                    SELECT an, note_date, MAX(note_time) AS last_time
                    FROM ipd_nurse_note
                    WHERE note_date = CURDATE() AND note_time BETWEEN '00:00:01' AND '07:59:59'
                    GROUP BY an, note_date
                ) x ON x.an = n.an AND x.note_date = n.note_date AND x.last_time = n.note_time
                WHERE i.ward IN ('$ipd_ward') AND i.confirm_discharge = 'N'
            ) t
        ");

        return view('hnplus.product.ipd_night', compact('shift'));
    }

    //ipd_night_save--------------------------------------------------------------------------------------------------------------------
    //ipd_night_save--------------------------------------------------------------------------------------------------------------------
    public function ipd_night_save(Request $request)
    {
        // ✅ ตรวจสอบข้อมูลที่จำเป็น
        $request->validate([
            'nurse_oncall' => 'required|numeric',
            'nurse_partime' => 'required|numeric',
            'nurse_fulltime' => 'required|numeric',
            'recorder' => 'required|string',
        ]);

        // ✅ Get Constants from MainSetting
        $ipd_working_hours = MainSetting::where('name', 'ipd_working_hours')->value('value') ?? 7;
        $type1_c = MainSetting::where('name', 'ipd_patient_type1')->value('value') ?? 1.5;
        $type2_c = MainSetting::where('name', 'ipd_patient_type2')->value('value') ?? 3.5;
        $type3_c = MainSetting::where('name', 'ipd_patient_type3')->value('value') ?? 5.5;
        $type4_c = MainSetting::where('name', 'ipd_patient_type4')->value('value') ?? 7.5;

        // ✅ เตรียมค่าที่ใช้ซ้ำ
        $convalescent = $request->convalescent;
        $Moderate = $request->Moderate;
        $Semi_critical = $request->Semi_critical;
        $Critical = $request->Critical;
        $patient_all = max(1, $request->patient_all); // ป้องกันหาร 0
        $nurse_total = $request->nurse_oncall + $request->nurse_partime + $request->nurse_fulltime;
        // $nurse_total_hr = max(1, $nurse_total * 7); // ป้องกันหาร 0
        $nurse_total_hr = max(1, $nurse_total * $ipd_working_hours);

        // ✅ คำนวณค่าทางสถิติ
        // $patient_hr = ($convalescent * 0.45)
        //     + ($moderate_ill * 1.17)
        //     + ($semi_critical_ill * 1.71)
        //     + ($critical_ill * 1.99);
        $patient_hr = ($convalescent * $type1_c)
            + ($Moderate * $type2_c)
            + ($Semi_critical * $type3_c)
            + ($Critical * $type4_c);

        $nurse_hr = $nurse_total * $ipd_working_hours;
        $productivity = ($patient_hr * 100) / $nurse_total_hr;
        $nhppd = $patient_hr / $patient_all;
        $nurse_shift_time = $patient_all * $nhppd * (1.4 / $ipd_working_hours);

        // ✅ บันทึกข้อมูลลงฐานข้อมูล
        Productivity_ipd::updateOrCreate(
            // 🔎 เงื่อนไขตรวจสอบข้อมูลซ้ำ
            [
                'report_date' => $request->report_date,
                'shift_time' => $request->shift_time,
            ],
            // 📝 ข้อมูลที่ insert / update
            [
                'nurse_fulltime' => $request->nurse_fulltime,
                'nurse_partime' => $request->nurse_partime,
                'nurse_oncall' => $request->nurse_oncall,
                'recorder' => $request->recorder,
                'note' => $request->note,

                'patient_all' => $patient_all,
                'patient_convalescent' => $convalescent,
                'patient_moderate' => $Moderate,
                'patient_semi_critical' => $Semi_critical,
                'patient_critical' => $Critical,

                'nursing_hours' => $patient_hr,
                'working_hours' => $nurse_hr,
                'nurse_shift_time' => $nurse_shift_time,
                'nhppd' => $nhppd,
                'productivity' => $productivity,
            ]
        );

        // ✅ เตรียมข้อความแจ้ง Telegram
        $message = "🛏️ งานผู้ป่วยใน สามัญ" . "\n"
            . "วันที่ " . DateThai(date('Y-m-d')) . "\n"
            . "เวลา 00.00–08.00 น. 🌙เวรดึก" . "\n"
            . "ผู้ป่วยในเวร: {$patient_all} ราย" . "\n"
            . " - Convalescent: {$convalescent} ราย" . "\n"
            . " - Moderate: {$Moderate} ราย" . "\n"
            . " - Semi critical: {$Semi_critical} ราย" . "\n"
            . " - Critical: {$Critical} ราย" . "\n"
            . "👩‍⚕️ Oncall: {$request->nurse_oncall}" . "\n"
            . "👩‍⚕️ เสริม: {$request->nurse_partime}" . "\n"
            . "👩‍⚕️ ปกติ: {$request->nurse_fulltime}" . "\n"
            . "🕒 ชม.การพยาบาล: " . number_format($patient_hr, 2) . "\n"
            . "🕒 ชม.การทำงาน: " . number_format($nurse_hr, 2) . "\n"
            . "📊 Productivity: " . number_format($productivity, 2) . "\n"
            . "🧮 NHPPD: " . number_format($nhppd, 2) . "\n"
            . "ผู้บันทึก: {$request->recorder}";

        // ✅ ส่งข้อความ Telegram
        $token = MainSetting::where('name', 'telegram_token')->value('value');
        $chat_ids = explode(',', MainSetting::where('name', 'ipd_notifytelegram_save')->value('value'));

        foreach ($chat_ids as $chat_id) {
            Http::asForm()->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => trim($chat_id),
                'text' => $message,
            ]);
            usleep(500000); // พัก 0.5 วินาที
        }

        return redirect()->back()->with('success', '✅ ส่งข้อมูลเวรดึกเรียบร้อยแล้ว');
    }

    //แจ้งเตือนสถานะการณ์สรุปเวรเช้า รัน 16.00 น.---------------------------------------------------------------------------------------------
    //แจ้งเตือนสถานะการณ์สรุปเวรเช้า รัน 16.00 น.---------------------------------------------------------------------------------------------
    public function ipd_morning_notify()
    {
        $ipd_ward = MainSetting::where('name', 'ipd_ward')->value('value') ?? '01';

        $notify = DB::connection('hosxp')->select("
            SELECT
                SUM(CASE WHEN ipd_nurse_eval_range_code LIKE '1%' THEN 1 ELSE 0 END) AS convalescent,
                SUM(CASE WHEN ipd_nurse_eval_range_code LIKE '2%' THEN 1 ELSE 0 END) AS Moderate,
                SUM(CASE WHEN ipd_nurse_eval_range_code LIKE '3%' THEN 1 ELSE 0 END) AS Semi_critical,
                SUM(CASE WHEN ipd_nurse_eval_range_code LIKE '4%' THEN 1 ELSE 0 END) AS Critical,
                SUM(CASE WHEN ipd_nurse_eval_range_code IS NULL OR ipd_nurse_eval_range_code = '' THEN 1 ELSE 0 END) AS severe_type_null,
                COUNT(DISTINCT an) AS patient_all
            FROM (
                SELECT n.an, n.ipd_nurse_eval_range_code
                FROM ipt i
                JOIN ipd_nurse_note n ON n.an = i.an
                JOIN (
                    SELECT an, note_date, MAX(note_time) AS last_time
                    FROM ipd_nurse_note
                    WHERE note_date = CURDATE() AND note_time BETWEEN '08:00:00' AND '15:59:59'
                    GROUP BY an, note_date
                ) x ON x.an = n.an AND x.note_date = n.note_date AND x.last_time = n.note_time
                WHERE i.ward IN ('$ipd_ward') AND i.confirm_discharge = 'N'
            ) t
        ");

        foreach ($notify as $row) {
            $patient_all = $row->patient_all;
            $convalescent = $row->convalescent;
            $Moderate = $row->Moderate;
            $Semi_critical = $row->Semi_critical;
            $Critical = $row->Critical;
            $severe_type_null = $row->severe_type_null;
            $url = url('hnplus/product/ipd_morning');
        }

        //แจ้งเตือน Telegram

        $message = "🛏️ งานผู้ป่วยใน สามัญ" . "\n"
            . "วันที่ " . DateThai(date('Y-m-d')) . "\n"
            . "เวลา 08.00-16.00 น. 🌅เวรเช้า" . "\n"
            . "ผู้ป่วยในเวร " . $patient_all . " ราย" . "\n"
            . " -Convalescent " . $convalescent . " ราย" . "\n"
            . " -Moderate " . $Moderate . " ราย" . "\n"
            . " -Semi critical " . $Semi_critical . " ราย" . "\n"
            . " -Critical " . $Critical . " ราย" . "\n"
            . " -ไม่ระบุความรุนแรง " . $severe_type_null . " ราย" . "\n" . "\n"
            . "บันทึก Productivity " . "\n"
            . $url . "\n";

        // ✅ ส่งข้อความ Telegram
        $token = MainSetting::where('name', 'telegram_token')->value('value');
        $chat_ids = explode(',', MainSetting::where('name', 'ipd_notifytelegram')->value('value'));

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

    //ipd_morning-------------------------------------------------------------------------------------------------------------
    public function ipd_morning()
    {
        $ipd_ward = MainSetting::where('name', 'ipd_ward')->value('value') ?? '01';

        $shift = DB::connection('hosxp')->select("
            SELECT
                SUM(CASE WHEN ipd_nurse_eval_range_code LIKE '1%' THEN 1 ELSE 0 END) AS convalescent,
                SUM(CASE WHEN ipd_nurse_eval_range_code LIKE '2%' THEN 1 ELSE 0 END) AS Moderate,
                SUM(CASE WHEN ipd_nurse_eval_range_code LIKE '3%' THEN 1 ELSE 0 END) AS Semi_critical,
                SUM(CASE WHEN ipd_nurse_eval_range_code LIKE '4%' THEN 1 ELSE 0 END) AS Critical,
                SUM(CASE WHEN ipd_nurse_eval_range_code IS NULL OR ipd_nurse_eval_range_code = '' THEN 1 ELSE 0 END) AS severe_type_null,
                COUNT(DISTINCT an) AS patient_all
            FROM (
                SELECT n.an, n.ipd_nurse_eval_range_code
                FROM ipt i
                JOIN ipd_nurse_note n ON n.an = i.an
                JOIN (
                    SELECT an, note_date, MAX(note_time) AS last_time
                    FROM ipd_nurse_note
                    WHERE note_date = CURDATE() AND note_time BETWEEN '08:00:00' AND '15:59:59'
                    GROUP BY an, note_date
                ) x ON x.an = n.an AND x.note_date = n.note_date AND x.last_time = n.note_time
                WHERE i.ward IN ('$ipd_ward') AND i.confirm_discharge = 'N'
            ) t
        ");

        return view('hnplus.product.ipd_morning', compact('shift'));
    }

    //ipd_morning_save------------------------------------------------------------------------------------------------------------------
    //ipd_morning_save------------------------------------------------------------------------------------------------------------------
    public function ipd_morning_save(Request $request)
    {
        // ✅ ตรวจสอบข้อมูลที่จำเป็น
        $request->validate([
            'nurse_oncall' => 'required|numeric',
            'nurse_partime' => 'required|numeric',
            'nurse_fulltime' => 'required|numeric',
            'recorder' => 'required|string',
        ]);

        // ✅ Get Constants from MainSetting
        $ipd_working_hours = MainSetting::where('name', 'ipd_working_hours')->value('value') ?? 7;
        $type1_c = MainSetting::where('name', 'ipd_patient_type1')->value('value') ?? 1.5;
        $type2_c = MainSetting::where('name', 'ipd_patient_type2')->value('value') ?? 3.5;
        $type3_c = MainSetting::where('name', 'ipd_patient_type3')->value('value') ?? 5.5;
        $type4_c = MainSetting::where('name', 'ipd_patient_type4')->value('value') ?? 7.5;

        // ✅ เตรียมค่าที่ใช้ซ้ำ
        $convalescent = $request->convalescent;
        $Moderate = $request->Moderate;
        $Semi_critical = $request->Semi_critical;
        $Critical = $request->Critical;
        $patient_all = max(1, $request->patient_all); // ป้องกันหาร 0
        $nurse_total = $request->nurse_oncall + $request->nurse_partime + $request->nurse_fulltime;
        // $nurse_total_hr = max(1, $nurse_total * 7); // ป้องกันหาร 0
        $nurse_total_hr = max(1, $nurse_total * $ipd_working_hours);

        // ✅ คำนวณค่าทางสถิติ
        // $patient_hr = ($convalescent * 0.45)
        //     + ($moderate_ill * 1.17)
        //     + ($semi_critical_ill * 1.71)
        //     + ($critical_ill * 1.99);
        $patient_hr = ($convalescent * $type1_c)
            + ($Moderate * $type2_c)
            + ($Semi_critical * $type3_c)
            + ($Critical * $type4_c);

        $nurse_hr = $nurse_total * $ipd_working_hours;
        $productivity = ($patient_hr * 100) / $nurse_total_hr;
        $nhppd = $patient_hr / $patient_all;
        $nurse_shift_time = $patient_all * $nhppd * (1.4 / $ipd_working_hours);

        // ✅ บันทึกข้อมูลลงฐานข้อมูล
        Productivity_ipd::updateOrCreate(
            // 🔎 เงื่อนไขตรวจสอบข้อมูลซ้ำ
            [
                'report_date' => $request->report_date,
                'shift_time' => $request->shift_time,
            ],
            // 📝 ข้อมูลที่ insert / update
            [
                'nurse_fulltime' => $request->nurse_fulltime,
                'nurse_partime' => $request->nurse_partime,
                'nurse_oncall' => $request->nurse_oncall,
                'recorder' => $request->recorder,
                'note' => $request->note,

                'patient_all' => $patient_all,
                'patient_convalescent' => $convalescent,
                'patient_moderate' => $Moderate,
                'patient_semi_critical' => $Semi_critical,
                'patient_critical' => $Critical,

                'nursing_hours' => $patient_hr,
                'working_hours' => $nurse_hr,
                'nurse_shift_time' => $nurse_shift_time,
                'nhppd' => $nhppd,
                'productivity' => $productivity,
            ]
        );

        // ✅ เตรียมข้อความแจ้ง Telegram
        $message = "🛏️ งานผู้ป่วยใน สามัญ" . "\n"
            . "วันที่ " . DateThai(date('Y-m-d')) . "\n"
            . "เวลา 08.00–16.00 น. 🌅เวรเช้า" . "\n"
            . "ผู้ป่วยในเวร: {$patient_all} ราย" . "\n"
            . " - Convalescent: {$convalescent} ราย" . "\n"
            . " - Moderate: {$Moderate} ราย" . "\n"
            . " - Semi critical: {$Semi_critical} ราย" . "\n"
            . " - Critical: {$Critical} ราย" . "\n"
            . "👩‍⚕️ Oncall: {$request->nurse_oncall}" . "\n"
            . "👩‍⚕️ เสริม: {$request->nurse_partime}" . "\n"
            . "👩‍⚕️ ปกติ: {$request->nurse_fulltime}" . "\n"
            . "🕒 ชม.การพยาบาล: " . number_format($patient_hr, 2) . "\n"
            . "🕒 ชม.การทำงาน: " . number_format($nurse_hr, 2) . "\n"
            . "📊 Productivity: " . number_format($productivity, 2) . "\n"
            . "🧮 NHPPD: " . number_format($nhppd, 2) . "\n"
            . "ผู้บันทึก: {$request->recorder}";

        // ✅ ส่งข้อความ Telegram
        $token = MainSetting::where('name', 'telegram_token')->value('value');
        $chat_ids = explode(',', MainSetting::where('name', 'ipd_notifytelegram_save')->value('value'));

        foreach ($chat_ids as $chat_id) {
            Http::asForm()->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => trim($chat_id),
                'text' => $message,
            ]);
            usleep(500000); // พัก 0.5 วินาที
        }

        return redirect()->back()->with('success', '✅ ส่งข้อมูลเวรเช้าเรียบร้อยแล้ว');
    }

    //แจ้งเตือนสถานะการณ์สรุปเวรบ่าย รัน 00.01 น.---------------------------------------------------------------------------------------------
    //แจ้งเตือนสถานะการณ์สรุปเวรบ่าย รัน 00.01 น.---------------------------------------------------------------------------------------------
    public function ipd_afternoon_notify()
    {
        $ipd_ward = MainSetting::where('name', 'ipd_ward')->value('value') ?? '01';

        $notify = DB::connection('hosxp')->select("
            SELECT
                SUM(CASE WHEN ipd_nurse_eval_range_code LIKE '1%' THEN 1 ELSE 0 END) AS convalescent,
                SUM(CASE WHEN ipd_nurse_eval_range_code LIKE '2%' THEN 1 ELSE 0 END) AS Moderate,
                SUM(CASE WHEN ipd_nurse_eval_range_code LIKE '3%' THEN 1 ELSE 0 END) AS Semi_critical,
                SUM(CASE WHEN ipd_nurse_eval_range_code LIKE '4%' THEN 1 ELSE 0 END) AS Critical,
                SUM(CASE WHEN ipd_nurse_eval_range_code IS NULL OR ipd_nurse_eval_range_code = '' THEN 1 ELSE 0 END) AS severe_type_null,
                COUNT(DISTINCT an) AS patient_all
            FROM (
                SELECT n.an, n.ipd_nurse_eval_range_code
                FROM ipt i
                JOIN ipd_nurse_note n ON n.an = i.an
                JOIN (
                    SELECT an, note_date, MAX(note_time) AS last_time
                    FROM ipd_nurse_note
                    WHERE note_date = CURDATE() - INTERVAL 1 DAY AND note_time BETWEEN '16:00:00' AND '23:59:59'
                    GROUP BY an, note_date
                ) x ON x.an = n.an AND x.note_date = n.note_date AND x.last_time = n.note_time
                WHERE i.ward IN ('$ipd_ward') AND i.confirm_discharge = 'N'
            ) t
        ");

        foreach ($notify as $row) {
            $patient_all = $row->patient_all;
            $convalescent = $row->convalescent;
            $Moderate = $row->Moderate;
            $Semi_critical = $row->Semi_critical;
            $Critical = $row->Critical;
            $severe_type_null = $row->severe_type_null;
            $url = url('hnplus/product/ipd_afternoon');
        }

        //แจ้งเตือน Telegram

        $message = "🛏️ งานผู้ป่วยใน สามัญ" . "\n"
            . "วันที่ " . DateThai(date("Y-m-d", strtotime("-1 day"))) . "\n"
            . "เวลา 16.00-24.00 น. 🌇เวรบ่าย" . "\n"
            . "ผู้ป่วยในเวร " . $patient_all . " ราย" . "\n"
            . " -Convalescent " . $convalescent . " ราย" . "\n"
            . " -Moderate " . $Moderate . " ราย" . "\n"
            . " -Semi critical " . $Semi_critical . " ราย" . "\n"
            . " -Critical " . $Critical . " ราย" . "\n"
            . " -ไม่ระบุความรุนแรง " . $severe_type_null . " ราย" . "\n" . "\n"
            . "บันทึก Productivity " . "\n"
            . $url . "\n";

        // ✅ ส่งข้อความ Telegram
        $token = MainSetting::where('name', 'telegram_token')->value('value');
        $chat_ids = explode(',', MainSetting::where('name', 'ipd_notifytelegram')->value('value'));

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

    //ipd_afternoon------------------------------------------------------------------------------------------------------------
    public function ipd_afternoon()
    {
        $ipd_ward = MainSetting::where('name', 'ipd_ward')->value('value') ?? '01';

        $shift = DB::connection('hosxp')->select("
            SELECT
                SUM(CASE WHEN ipd_nurse_eval_range_code LIKE '1%' THEN 1 ELSE 0 END) AS convalescent,
                SUM(CASE WHEN ipd_nurse_eval_range_code LIKE '2%' THEN 1 ELSE 0 END) AS Moderate,
                SUM(CASE WHEN ipd_nurse_eval_range_code LIKE '3%' THEN 1 ELSE 0 END) AS Semi_critical,
                SUM(CASE WHEN ipd_nurse_eval_range_code LIKE '4%' THEN 1 ELSE 0 END) AS Critical,
                SUM(CASE WHEN ipd_nurse_eval_range_code IS NULL OR ipd_nurse_eval_range_code = '' THEN 1 ELSE 0 END) AS severe_type_null,
                COUNT(DISTINCT an) AS patient_all
            FROM (
                SELECT n.an, n.ipd_nurse_eval_range_code
                FROM ipt i
                JOIN ipd_nurse_note n ON n.an = i.an
                JOIN (
                    SELECT an, note_date, MAX(note_time) AS last_time
                    FROM ipd_nurse_note
                    WHERE note_date = CURDATE() AND note_time BETWEEN '16:00:00' AND '23:59:59'
                    GROUP BY an, note_date
                ) x ON x.an = n.an AND x.note_date = n.note_date AND x.last_time = n.note_time
                WHERE i.ward IN ('$ipd_ward') AND i.confirm_discharge = 'N'
            ) t
        ");

        return view('hnplus.product.ipd_afternoon', compact('shift'));
    }

    //ipd_afternoon_save---------------------------------------------------------------------------------------------------------------
    //ipd_afternoon_save---------------------------------------------------------------------------------------------------------------
    public function ipd_afternoon_save(Request $request)
    {
        // ✅ ตรวจสอบข้อมูลที่จำเป็น
        $request->validate([
            'nurse_oncall' => 'required|numeric',
            'nurse_partime' => 'required|numeric',
            'nurse_fulltime' => 'required|numeric',
            'recorder' => 'required|string',
        ]);

        // ✅ Get Constants from MainSetting
        $ipd_working_hours = MainSetting::where('name', 'ipd_working_hours')->value('value') ?? 7;
        $type1_c = MainSetting::where('name', 'ipd_patient_type1')->value('value') ?? 1.5;
        $type2_c = MainSetting::where('name', 'ipd_patient_type2')->value('value') ?? 3.5;
        $type3_c = MainSetting::where('name', 'ipd_patient_type3')->value('value') ?? 5.5;
        $type4_c = MainSetting::where('name', 'ipd_patient_type4')->value('value') ?? 7.5;

        // ✅ เตรียมค่าที่ใช้ซ้ำ
        $convalescent = $request->convalescent;
        $Moderate = $request->Moderate;
        $Semi_critical = $request->Semi_critical;
        $Critical = $request->Critical;
        $patient_all = max(1, $request->patient_all); // ป้องกันหาร 0
        $nurse_total = $request->nurse_oncall + $request->nurse_partime + $request->nurse_fulltime;
        // $nurse_total_hr = max(1, $nurse_total * 7); // ป้องกันหาร 0
        $nurse_total_hr = max(1, $nurse_total * $ipd_working_hours);

        // ✅ คำนวณค่าทางสถิติ
        // $patient_hr = ($convalescent * 0.45)
        //     + ($moderate_ill * 1.17)
        //     + ($semi_critical_ill * 1.71)
        //     + ($critical_ill * 1.99);
        $patient_hr = ($convalescent * $type1_c)
            + ($Moderate * $type2_c)
            + ($Semi_critical * $type3_c)
            + ($Critical * $type4_c);

        $nurse_hr = $nurse_total * $ipd_working_hours;
        $productivity = ($patient_hr * 100) / $nurse_total_hr;
        $nhppd = $patient_hr / $patient_all;
        $nurse_shift_time = $patient_all * $nhppd * (1.4 / $ipd_working_hours);

        // ✅ บันทึกข้อมูลลงฐานข้อมูล
        Productivity_ipd::updateOrCreate(
            // 🔎 เงื่อนไขตรวจสอบข้อมูลซ้ำ
            [
                'report_date' => $request->report_date,
                'shift_time' => $request->shift_time,
            ],
            // 📝 ข้อมูลที่ insert / update
            [
                'nurse_fulltime' => $request->nurse_fulltime,
                'nurse_partime' => $request->nurse_partime,
                'nurse_oncall' => $request->nurse_oncall,
                'recorder' => $request->recorder,
                'note' => $request->note,

                'patient_all' => $patient_all,
                'patient_convalescent' => $convalescent,
                'patient_moderate' => $Moderate,
                'patient_semi_critical' => $Semi_critical,
                'patient_critical' => $Critical,

                'nursing_hours' => $patient_hr,
                'working_hours' => $nurse_hr,
                'nurse_shift_time' => $nurse_shift_time,
                'nhppd' => $nhppd,
                'productivity' => $productivity,
            ]
        );

        // ✅ เตรียมข้อความแจ้ง Telegram
        $message = "🛏️ งานผู้ป่วยใน สามัญ" . "\n"
            . "วันที่ " . DateThai(date("Y-m-d", strtotime("-1 day"))) . "\n"
            . "เวลา 16.00–24.00 น. 🌇เวรบ่าย" . "\n"
            . "ผู้ป่วยในเวร: {$patient_all} ราย" . "\n"
            . " - Convalescent: {$convalescent} ราย" . "\n"
            . " - Moderate: {$Moderate} ราย" . "\n"
            . " - Semi critical: {$Semi_critical} ราย" . "\n"
            . " - Critical: {$Critical} ราย" . "\n"
            . "👩‍⚕️ Oncall: {$request->nurse_oncall}" . "\n"
            . "👩‍⚕️ เสริม: {$request->nurse_partime}" . "\n"
            . "👩‍⚕️ ปกติ: {$request->nurse_fulltime}" . "\n"
            . "🕒 ชม.การพยาบาล: " . number_format($patient_hr, 2) . "\n"
            . "🕒 ชม.การทำงาน: " . number_format($nurse_hr, 2) . "\n"
            . "📊 Productivity: " . number_format($productivity, 2) . "\n"
            . "🧮 NHPPD: " . number_format($nhppd, 2) . "\n"
            . "ผู้บันทึก: {$request->recorder}";

        // ✅ ส่งข้อความ Telegram
        $token = MainSetting::where('name', 'telegram_token')->value('value');
        $chat_ids = explode(',', MainSetting::where('name', 'ipd_notifytelegram_save')->value('value'));

        foreach ($chat_ids as $chat_id) {
            Http::asForm()->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => trim($chat_id),
                'text' => $message,
            ]);
            usleep(500000); // พัก 0.5 วินาที
        }

        return redirect()->back()->with('success', '✅ ส่งข้อมูลเวรบ่ายเรียบร้อยแล้ว');
    }

}
