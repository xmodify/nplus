<?php

namespace App\Http\Controllers\Hnplus;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Productivity_er; // Changed Model
use App\Models\MainSetting;     // Added Settings
use Illuminate\Routing\Middleware\Middleware;

#[Middleware('auth', only: ['er_report', 'er_product_delete'])]

class ProductERController extends Controller
{

    //er_report--------------------------------------------------------------------------------------------------------------------------
    public function er_report(Request $request)
    {
        $start_date = $request->start_date ? DateThaiToEn($request->start_date) : date('Y-m-d', strtotime("first day of this month"));
        $end_date = $request->end_date ? DateThaiToEn($request->end_date) : date('Y-m-d');

        $er_product = Productivity_er::whereBetween('report_date', [$start_date, $end_date])
            ->orderBy('report_date', 'desc')->get();

        $er_working_hours = MainSetting::where('name', 'er_working_hours')->value('value') ?? 7;

        $er_product_summary = DB::select("
            SELECT CASE WHEN shift_time = 'เวรเช้า' THEN '1' WHEN shift_time = 'เวรบ่าย' THEN '2'
            WHEN shift_time = 'เวรดึก' THEN '3' END AS id,
            shift_time,
            COUNT(shift_time) AS shift_time_sum,
            SUM(patient_all) AS patient_all, 
            SUM(patient_resuscitation) AS patient_resuscitation,
            SUM(patient_emergent) AS patient_emergent,
            SUM(patient_urgent) AS patient_urgent,
            SUM(patient_semi_urgent) AS patient_semi_urgent,
            SUM(patient_non_urgent) AS patient_non_urgent,
            SUM(nursing_hours) AS patient_hr,
            SUM(nurse_oncall) AS nurse_oncall,
            SUM(nurse_partime) AS nurse_partime,
            SUM(nurse_fulltime) AS nurse_fulltime, 
            SUM(working_hours) AS nurse_hr,
            ((SUM(nursing_hours)*100)/SUM(working_hours)) AS productivity,
            (SUM(nursing_hours)/SUM(patient_all)) AS nhppd,
            (SUM(patient_all)*(SUM(nursing_hours)/SUM(patient_all))*(1.4/{$er_working_hours}))/COUNT(shift_time) AS nurse_shift_time
            FROM productivity_er
            WHERE report_date BETWEEN ? AND ?
            GROUP BY shift_time ORDER BY id", [$start_date, $end_date]);

        // เตรียมข้อมูลสำหรับกราฟ
        $er_product_asc = Productivity_er::whereBetween('report_date', [$start_date, $end_date])
            ->orderBy('report_date', 'asc')->get();
        $grouped = $er_product_asc->groupBy('report_date');
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

        return view('hnplus.product.er_report', compact(
            'er_product_summary',
            'er_product',
            'start_date',
            'end_date',
            'del_product',
            'report_date',
            'night',
            'morning',
            'afternoon'
        ));
    }

    //er_product_delete----------------------------------------------------------------------------------------------------------------
    public function er_product_delete($id)
    {
        $er_product = Productivity_er::find($id)->delete();
        return redirect()->route('hnplus.product.er_report')->with('danger', 'ลบข้อมูลเรียบร้อยแล้ว');
    }

    //แจ้งเตือนสถานะการณ์สรุปเวรดึก รัน 08.00 น.---------------------------------------------------------------------------------------------
    public function er_night_notify()
    {
        $service = DB::connection('hosxp')->select("
            SELECT 
                DATE(NOW()) AS vstdate,
                COALESCE(COUNT(DISTINCT e.vn), 0) AS visit,
                COALESCE(SUM(CASE WHEN et.export_code IN ('1') THEN 1 ELSE 0 END), 0) AS resuscitation,
                COALESCE(SUM(CASE WHEN et.export_code IN ('2') THEN 1 ELSE 0 END), 0) AS emergent,
                COALESCE(SUM(CASE WHEN et.export_code IN ('3') THEN 1 ELSE 0 END), 0) AS urgent,
                COALESCE(SUM(CASE WHEN et.export_code IN ('4') THEN 1 ELSE 0 END), 0) AS semi_urgent,
                COALESCE(SUM(CASE WHEN et.export_code IN ('5') THEN 1 ELSE 0 END), 0) AS non_urgent
            FROM er_regist e
            LEFT JOIN er_emergency_type et 
                ON et.er_emergency_type = e.er_emergency_type
            WHERE DATE(e.enter_er_time) = CURDATE()
            AND TIME(e.enter_er_time) BETWEEN '00:00:00' AND '07:59:59'");

        foreach ($service as $row) {
            $vstdate = $row->vstdate;
            $visit = $row->visit;
            $resuscitation = $row->resuscitation;
            $emergent = $row->emergent;
            $urgent = $row->urgent;
            $semi_urgent = $row->semi_urgent;
            $non_urgent = $row->non_urgent;
            $url = url('hnplus/product/er_night');
        }

        //แจ้งเตือน Telegram

        $message = "🚨งานอุบัติเหตุ-ฉุกเฉิน" . "\n"
            . "วันที่ " . DateThai($vstdate) . "\n"
            . "เวลา 00.00-08.00 น. 🌙เวรดึก" . "\n"
            . "ผู้ป่วยในเวร " . $visit . " ราย" . "\n"
            . " -Resuscitation " . $resuscitation . " ราย" . "\n"
            . " -Emergent " . $emergent . " ราย" . "\n"
            . " -Urgent " . $urgent . " ราย" . "\n"
            . " -Semi Urgent " . $semi_urgent . " ราย" . "\n"
            . " -Non Urgent " . $non_urgent . " ราย" . "\n" . "\n"
            . "บันทึก Productivity " . "\n"
            . $url . "\n";

        $token = MainSetting::where('name', 'telegram_token')->value('value');
        $telegram_chat_id = MainSetting::where('name', 'er_notifytelegram')->value('value');
        $chat_ids = explode(',', $telegram_chat_id);

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

    //er_night------------------------------------------------------------------------------------------------------------------------
    public function er_night()
    {
        $shift = DB::connection('hosxp')->select("
            SELECT 
                DATE(NOW()) AS vstdate,
                COALESCE(COUNT(DISTINCT e.vn), 0) AS visit,
                COALESCE(SUM(CASE WHEN et.export_code IN ('1') THEN 1 ELSE 0 END), 0) AS resuscitation,
                COALESCE(SUM(CASE WHEN et.export_code IN ('2') THEN 1 ELSE 0 END), 0) AS emergent,
                COALESCE(SUM(CASE WHEN et.export_code IN ('3') THEN 1 ELSE 0 END), 0) AS urgent,
                COALESCE(SUM(CASE WHEN et.export_code IN ('4') THEN 1 ELSE 0 END), 0) AS semi_urgent,
                COALESCE(SUM(CASE WHEN et.export_code IN ('5') THEN 1 ELSE 0 END), 0) AS non_urgent
            FROM er_regist e
            LEFT JOIN er_emergency_type et 
                ON et.er_emergency_type = e.er_emergency_type
            WHERE DATE(e.enter_er_time) = CURDATE()
            AND TIME(e.enter_er_time) BETWEEN '00:00:00' AND '07:59:59'");

        return view('hnplus.product.er_night', compact('shift'));
    }

    //er_night_save--------------------------------------------------------------------------------------------------------------------
    public function er_night_save(Request $request)
    {
        // ✅ ตรวจสอบข้อมูลที่จำเป็น
        $request->validate([
            'nurse_oncall' => 'required|numeric',
            'nurse_partime' => 'required|numeric',
            'nurse_fulltime' => 'required|numeric',
            'recorder' => 'required|string',
        ]);

        // ✅ Get Constants from MainSetting
        $er_working_hours = MainSetting::where('name', 'er_working_hours')->value('value') ?? 7;
        $type1_c = MainSetting::where('name', 'er_patient_type1')->value('value') ?? 3.2;
        $type2_c = MainSetting::where('name', 'er_patient_type2')->value('value') ?? 2.5;
        $type3_c = MainSetting::where('name', 'er_patient_type3')->value('value') ?? 1;
        $type4_c = MainSetting::where('name', 'er_patient_type4')->value('value') ?? 0.5;
        $type5_c = MainSetting::where('name', 'er_patient_type5')->value('value') ?? 0.24;

        // ✅ เตรียมค่าที่ใช้ซ้ำ
        $resuscitation = $request->resuscitation;
        $emergent = $request->emergent;
        $urgent = $request->urgent;
        $semi_urgent = $request->semi_urgent;
        $non_urgent = $request->non_urgent;

        $patient_all = max(1, $request->patient_all);
        $nurse_total = $request->nurse_oncall + $request->nurse_partime + $request->nurse_fulltime;
        $nurse_total_hr = max(1, $nurse_total * $er_working_hours);

        // ✅ คำนวณค่าทางสถิติ
        $patient_hr = ($resuscitation * $type1_c) + ($emergent * $type2_c) +
            ($urgent * $type3_c) + ($semi_urgent * $type4_c) +
            ($non_urgent * $type5_c);

        $nurse_hr = $nurse_total * $er_working_hours;
        $productivity = ($patient_hr * 100) / $nurse_total_hr;
        $nhppd = $patient_hr / $patient_all;
        $nurse_shift_time = $patient_all * $nhppd * (1.4 / $er_working_hours);

        // ✅ บันทึกข้อมูลลงฐานข้อมูล
        Productivity_er::updateOrCreate(
            [
                'report_date' => $request->report_date,
                'shift_time' => $request->shift_time,
            ],
            [
                'nurse_fulltime' => $request->nurse_fulltime,
                'nurse_partime' => $request->nurse_partime,
                'nurse_oncall' => $request->nurse_oncall,
                'recorder' => $request->recorder,
                'note' => $request->note,

                'patient_all' => $patient_all,
                'patient_resuscitation' => $resuscitation,
                'patient_emergent' => $emergent,
                'patient_urgent' => $urgent,
                'patient_semi_urgent' => $semi_urgent,
                'patient_non_urgent' => $non_urgent,

                'nursing_hours' => $patient_hr,
                'working_hours' => $nurse_hr,
                'nurse_shift_time' => $nurse_shift_time,
                'nhppd' => $nhppd,
                'productivity' => $productivity,
            ]
        );

        // ✅ เตรียมข้อความแจ้ง Telegram
        $message = "🚨 งานอุบัติเหตุ-ฉุกเฉิน \n"
            . "วันที่ " . DateThai($request->report_date) . "\n"
            . "เวลา 00.00–08.00 น. 🌙เวรดึก\n"
            . "ผู้ป่วยในเวร: {$patient_all} ราย\n"
            . " - Resuscitation: {$resuscitation} ราย\n"
            . " - Emergent: {$emergent} ราย\n"
            . " - Urgent: {$urgent} ราย\n"
            . " - Semi Urgent: {$semi_urgent} ราย\n"
            . " - Non Urgent: {$non_urgent} ราย\n"
            . "👩‍⚕️ Oncall: {$request->nurse_oncall}\n"
            . "👩‍⚕️ เสริม: {$request->nurse_partime}\n"
            . "👩‍⚕️ ปกติ: {$request->nurse_fulltime}\n"
            . "🕒 ชม.การพยาบาล: " . number_format($patient_hr, 2) . "\n"
            . "🕒 ชม.การทำงาน: " . number_format($nurse_hr, 2) . "\n"
            . "📈 Productivity: " . number_format($productivity, 2) . "%\n"
            . "ผู้บันทึก: {$request->recorder}";

        // ✅ ส่งข้อความ Telegram
        $token = MainSetting::where('name', 'telegram_token')->value('value');
        $chat_ids = explode(',', MainSetting::where('name', 'er_notifytelegram_save')->value('value'));

        foreach ($chat_ids as $chat_id) {
            Http::asForm()->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => trim($chat_id),
                'text' => $message,
            ]);
            usleep(500000);
        }

        return redirect()->back()->with('success', '✅ ส่งข้อมูลเวรดึกเรียบร้อยแล้ว');
    }

    //แจ้งเตือนสถานะการณ์สรุปเวรเช้า รัน 16.00 น.---------------------------------------------------------------------------------------------
    public function er_morning_notify()
    {
        $service = DB::connection('hosxp')->select("
            SELECT 
                DATE(NOW()) AS vstdate,
                COALESCE(COUNT(DISTINCT e.vn), 0) AS visit,
                COALESCE(SUM(CASE WHEN et.export_code IN ('1') THEN 1 ELSE 0 END), 0) AS resuscitation,
                COALESCE(SUM(CASE WHEN et.export_code IN ('2') THEN 1 ELSE 0 END), 0) AS emergent,
                COALESCE(SUM(CASE WHEN et.export_code IN ('3') THEN 1 ELSE 0 END), 0) AS urgent,
                COALESCE(SUM(CASE WHEN et.export_code IN ('4') THEN 1 ELSE 0 END), 0) AS semi_urgent,
                COALESCE(SUM(CASE WHEN et.export_code IN ('5') THEN 1 ELSE 0 END), 0) AS non_urgent
            FROM er_regist e
            LEFT JOIN er_emergency_type et 
                ON et.er_emergency_type = e.er_emergency_type
            WHERE DATE(e.enter_er_time) = CURDATE()
            AND TIME(e.enter_er_time) BETWEEN '08:00:00' AND '15:59:59'");

        foreach ($service as $row) {
            $vstdate = $row->vstdate;
            $visit = $row->visit;
            $resuscitation = $row->resuscitation;
            $emergent = $row->emergent;
            $urgent = $row->urgent;
            $semi_urgent = $row->semi_urgent;
            $non_urgent = $row->non_urgent;
            $url = url('hnplus/product/er_morning');
        }

        //แจ้งเตือน Telegram

        $message = "🚨งานอุบัติเหตุ-ฉุกเฉิน" . "\n"
            . "วันที่ " . DateThai($vstdate) . "\n"
            . "เวลา 08.00-16.00 น. 🌅เวรเช้า" . "\n"
            . "ผู้ป่วยในเวร " . $visit . " ราย" . "\n"
            . " -Resuscitation " . $resuscitation . " ราย" . "\n"
            . " -Emergent " . $emergent . " ราย" . "\n"
            . " -Urgent " . $urgent . " ราย" . "\n"
            . " -Semi Urgent " . $semi_urgent . " ราย" . "\n"
            . " -Non Urgent " . $non_urgent . " ราย" . "\n" . "\n"
            . "บันทึก Productivity " . "\n"
            . $url . "\n";

        $token = MainSetting::where('name', 'telegram_token')->value('value');
        $telegram_chat_id = MainSetting::where('name', 'er_notifytelegram')->value('value');
        $chat_ids = explode(',', $telegram_chat_id);

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

    //er_morning-------------------------------------------------------------------------------------------------------------
    public function er_morning()
    {
        $shift = DB::connection('hosxp')->select("
            SELECT 
                DATE(NOW()) AS vstdate,
                COALESCE(COUNT(DISTINCT e.vn), 0) AS visit,
                COALESCE(SUM(CASE WHEN et.export_code IN ('1') THEN 1 ELSE 0 END), 0) AS resuscitation,
                COALESCE(SUM(CASE WHEN et.export_code IN ('2') THEN 1 ELSE 0 END), 0) AS emergent,
                COALESCE(SUM(CASE WHEN et.export_code IN ('3') THEN 1 ELSE 0 END), 0) AS urgent,
                COALESCE(SUM(CASE WHEN et.export_code IN ('4') THEN 1 ELSE 0 END), 0) AS semi_urgent,
                COALESCE(SUM(CASE WHEN et.export_code IN ('5') THEN 1 ELSE 0 END), 0) AS non_urgent
            FROM er_regist e
            LEFT JOIN er_emergency_type et 
                ON et.er_emergency_type = e.er_emergency_type
            WHERE DATE(e.enter_er_time) = CURDATE()
            AND TIME(e.enter_er_time) BETWEEN '08:00:00' AND '15:59:59'");

        return view('hnplus.product.er_morning', compact('shift'));
    }

    //er_morning_save------------------------------------------------------------------------------------------------------------------
    public function er_morning_save(Request $request)
    {
        // ✅ ตรวจสอบข้อมูลที่จำเป็น
        $request->validate([
            'nurse_oncall' => 'required|numeric',
            'nurse_partime' => 'required|numeric',
            'nurse_fulltime' => 'required|numeric',
            'recorder' => 'required|string',
        ]);

        // ✅ Get Constants from MainSetting
        $er_working_hours = MainSetting::where('name', 'er_working_hours')->value('value') ?? 7;
        $type1_c = MainSetting::where('name', 'er_patient_type1')->value('value') ?? 3.2;
        $type2_c = MainSetting::where('name', 'er_patient_type2')->value('value') ?? 2.5;
        $type3_c = MainSetting::where('name', 'er_patient_type3')->value('value') ?? 1;
        $type4_c = MainSetting::where('name', 'er_patient_type4')->value('value') ?? 0.5;
        $type5_c = MainSetting::where('name', 'er_patient_type5')->value('value') ?? 0.24;

        // ✅ เตรียมค่าที่ใช้ซ้ำ
        $resuscitation = $request->resuscitation;
        $emergent = $request->emergent;
        $urgent = $request->urgent;
        $semi_urgent = $request->semi_urgent;
        $non_urgent = $request->non_urgent;

        $patient_all = max(1, $request->patient_all);
        $nurse_total = $request->nurse_oncall + $request->nurse_partime + $request->nurse_fulltime;
        $nurse_total_hr = max(1, $nurse_total * $er_working_hours);

        // ✅ คำนวณค่าทางสถิติ
        $patient_hr = ($resuscitation * $type1_c) + ($emergent * $type2_c) +
            ($urgent * $type3_c) + ($semi_urgent * $type4_c) +
            ($non_urgent * $type5_c);

        $nurse_hr = $nurse_total * $er_working_hours;
        $productivity = ($patient_hr * 100) / $nurse_total_hr;
        $nhppd = $patient_hr / $patient_all;
        $nurse_shift_time = $patient_all * $nhppd * (1.4 / $er_working_hours);

        // ✅ บันทึกข้อมูลลงฐานข้อมูล
        Productivity_er::updateOrCreate(
            [
                'report_date' => $request->report_date,
                'shift_time' => $request->shift_time,
            ],
            [
                'nurse_fulltime' => $request->nurse_fulltime,
                'nurse_partime' => $request->nurse_partime,
                'nurse_oncall' => $request->nurse_oncall,
                'recorder' => $request->recorder,
                'note' => $request->note,

                'patient_all' => $patient_all,
                'patient_resuscitation' => $resuscitation,
                'patient_emergent' => $emergent,
                'patient_urgent' => $urgent,
                'patient_semi_urgent' => $semi_urgent,
                'patient_non_urgent' => $non_urgent,

                'nursing_hours' => $patient_hr,
                'working_hours' => $nurse_hr,
                'nurse_shift_time' => $nurse_shift_time,
                'nhppd' => $nhppd,
                'productivity' => $productivity,
            ]
        );

        // ✅ เตรียมข้อความแจ้ง Telegram
        $message = "🚨 งานอุบัติเหตุ-ฉุกเฉิน \n"
            . "วันที่ " . DateThai($request->report_date) . "\n"
            . "เวลา 08.00–16.00 น. 🌅เวรเช้า\n"
            . "ผู้ป่วยในเวร: {$patient_all} ราย\n"
            . " - Resuscitation: {$resuscitation} ราย\n"
            . " - Emergent: {$emergent} ราย\n"
            . " - Urgent: {$urgent} ราย\n"
            . " - Semi Urgent: {$semi_urgent} ราย\n"
            . " - Non Urgent: {$non_urgent} ราย\n"
            . "👩‍⚕️ Oncall: {$request->nurse_oncall}\n"
            . "👩‍⚕️ เสริม: {$request->nurse_partime}\n"
            . "👩‍⚕️ ปกติ: {$request->nurse_fulltime}\n"
            . "🕒 ชม.การพยาบาล: " . number_format($patient_hr, 2) . "\n"
            . "🕒 ชม.การทำงาน: " . number_format($nurse_hr, 2) . "\n"
            . "📈 Productivity: " . number_format($productivity, 2) . "%\n"
            . "ผู้บันทึก: {$request->recorder}";

        // ✅ ส่งข้อความ Telegram
        $token = MainSetting::where('name', 'telegram_token')->value('value');
        $chat_ids = explode(',', MainSetting::where('name', 'er_notifytelegram_save')->value('value'));

        foreach ($chat_ids as $chat_id) {
            Http::asForm()->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => trim($chat_id),
                'text' => $message,
            ]);
            usleep(500000);
        }

        return redirect()->back()->with('success', '✅ ส่งข้อมูลเวรเช้าเรียบร้อยแล้ว');
    }

    //แจ้งเตือนสถานะการณ์สรุปเวรบ่าย รัน 00.01 น.---------------------------------------------------------------------------------------------
    public function er_afternoon_notify()
    {
        $service = DB::connection('hosxp')->select("
            SELECT 
                DATE(NOW()) AS vstdate,
                COALESCE(COUNT(DISTINCT e.vn), 0) AS visit,
                COALESCE(SUM(CASE WHEN et.export_code IN ('1') THEN 1 ELSE 0 END), 0) AS resuscitation,
                COALESCE(SUM(CASE WHEN et.export_code IN ('2') THEN 1 ELSE 0 END), 0) AS emergent,
                COALESCE(SUM(CASE WHEN et.export_code IN ('3') THEN 1 ELSE 0 END), 0) AS urgent,
                COALESCE(SUM(CASE WHEN et.export_code IN ('4') THEN 1 ELSE 0 END), 0) AS semi_urgent,
                COALESCE(SUM(CASE WHEN et.export_code IN ('5') THEN 1 ELSE 0 END), 0) AS non_urgent
            FROM er_regist e
            LEFT JOIN er_emergency_type et 
                ON et.er_emergency_type = e.er_emergency_type
            WHERE DATE(e.enter_er_time) = date(DATE_ADD(now(), INTERVAL -1 DAY ))
            AND TIME(enter_er_time) BETWEEN '16:00:00' AND '23:59:59'");

        foreach ($service as $row) {
            $vstdate = $row->vstdate;
            $visit = $row->visit;
            $resuscitation = $row->resuscitation;
            $emergent = $row->emergent;
            $urgent = $row->urgent;
            $semi_urgent = $row->semi_urgent;
            $non_urgent = $row->non_urgent;
            $url = url('hnplus/product/er_afternoon');
        }

        //แจ้งเตือน Telegram 

        $message = "🚨งานอุบัติเหตุ-ฉุกเฉิน" . "\n"
            . "วันที่ " . DateThai(date("Y-m-d", strtotime("-1 day"))) . "\n"
            . "เวลา 16.00-24.00 น. 🌇เวรบ่าย" . "\n"
            . "ผู้ป่วยในเวร " . $visit . " ราย" . "\n"
            . " -Resuscitation " . $resuscitation . " ราย" . "\n"
            . " -Emergent " . $emergent . " ราย" . "\n"
            . " -Urgent " . $urgent . " ราย" . "\n"
            . " -Semi Urgent " . $semi_urgent . " ราย" . "\n"
            . " -Non Urgent " . $non_urgent . " ราย" . "\n" . "\n"
            . "บันทึก Productivity " . "\n"
            . $url . "\n";

        $token = MainSetting::where('name', 'telegram_token')->value('value');
        $telegram_chat_id = MainSetting::where('name', 'er_notifytelegram')->value('value');
        $chat_ids = explode(',', $telegram_chat_id);

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

    //er_afternoon------------------------------------------------------------------------------------------------------------
    public function er_afternoon()
    {
        $shift = DB::connection('hosxp')->select("
            SELECT 
                DATE(NOW()) AS vstdate,
                COALESCE(COUNT(DISTINCT e.vn), 0) AS visit,
                COALESCE(SUM(CASE WHEN et.export_code IN ('1') THEN 1 ELSE 0 END), 0) AS resuscitation,
                COALESCE(SUM(CASE WHEN et.export_code IN ('2') THEN 1 ELSE 0 END), 0) AS emergent,
                COALESCE(SUM(CASE WHEN et.export_code IN ('3') THEN 1 ELSE 0 END), 0) AS urgent,
                COALESCE(SUM(CASE WHEN et.export_code IN ('4') THEN 1 ELSE 0 END), 0) AS semi_urgent,
                COALESCE(SUM(CASE WHEN et.export_code IN ('5') THEN 1 ELSE 0 END), 0) AS non_urgent
            FROM er_regist e
            LEFT JOIN er_emergency_type et 
                ON et.er_emergency_type = e.er_emergency_type
            WHERE DATE(e.enter_er_time) = date(DATE_ADD(now(), INTERVAL -1 DAY ))
            AND TIME(enter_er_time) BETWEEN '16:00:00' AND '23:59:59'");

        return view('hnplus.product.er_afternoon', compact('shift'));
    }

    //er_afternoon_save---------------------------------------------------------------------------------------------------------------
    public function er_afternoon_save(Request $request)
    {
        // ✅ ตรวจสอบข้อมูลที่จำเป็น
        $request->validate([
            'nurse_oncall' => 'required|numeric',
            'nurse_partime' => 'required|numeric',
            'nurse_fulltime' => 'required|numeric',
            'recorder' => 'required|string',
        ]);

        // ✅ Get Constants from MainSetting
        $er_working_hours = MainSetting::where('name', 'er_working_hours')->value('value') ?? 7;
        $type1_c = MainSetting::where('name', 'er_patient_type1')->value('value') ?? 3.2;
        $type2_c = MainSetting::where('name', 'er_patient_type2')->value('value') ?? 2.5;
        $type3_c = MainSetting::where('name', 'er_patient_type3')->value('value') ?? 1;
        $type4_c = MainSetting::where('name', 'er_patient_type4')->value('value') ?? 0.5;
        $type5_c = MainSetting::where('name', 'er_patient_type5')->value('value') ?? 0.24;

        // ✅ เตรียมค่าที่ใช้ซ้ำ
        $resuscitation = $request->resuscitation;
        $emergent = $request->emergent;
        $urgent = $request->urgent;
        $semi_urgent = $request->semi_urgent;
        $non_urgent = $request->non_urgent;

        $patient_all = max(1, $request->patient_all);
        $nurse_total = $request->nurse_oncall + $request->nurse_partime + $request->nurse_fulltime;
        $nurse_total_hr = max(1, $nurse_total * $er_working_hours);

        // ✅ คำนวณค่าทางสถิติ
        $patient_hr = ($resuscitation * $type1_c) + ($emergent * $type2_c) +
            ($urgent * $type3_c) + ($semi_urgent * $type4_c) +
            ($non_urgent * $type5_c);

        $nurse_hr = $nurse_total * $er_working_hours;
        $productivity = ($patient_hr * 100) / $nurse_total_hr;
        $nhppd = $patient_hr / $patient_all;
        $nurse_shift_time = $patient_all * $nhppd * (1.4 / $er_working_hours);

        // ✅ บันทึกข้อมูลลงฐานข้อมูล
        Productivity_er::updateOrCreate(
            [
                'report_date' => $request->report_date,
                'shift_time' => $request->shift_time,
            ],
            [
                'nurse_fulltime' => $request->nurse_fulltime,
                'nurse_partime' => $request->nurse_partime,
                'nurse_oncall' => $request->nurse_oncall,
                'recorder' => $request->recorder,
                'note' => $request->note,

                'patient_all' => $patient_all,
                'patient_resuscitation' => $resuscitation,
                'patient_emergent' => $emergent,
                'patient_urgent' => $urgent,
                'patient_semi_urgent' => $semi_urgent,
                'patient_non_urgent' => $non_urgent,

                'nursing_hours' => $patient_hr,
                'working_hours' => $nurse_hr,
                'nurse_shift_time' => $nurse_shift_time,
                'nhppd' => $nhppd,
                'productivity' => $productivity,
            ]
        );

        // ✅ เตรียมข้อความแจ้ง Telegram
        $message = "🚨 งานอุบัติเหตุ-ฉุกเฉิน \n"
            . "วันที่ " . DateThai($request->report_date) . "\n"
            . "เวลา 16.00–24.00 น. 🌇เวรบ่าย\n"
            . "ผู้ป่วยในเวร: {$patient_all} ราย\n"
            . " - Resuscitation: {$resuscitation} ราย\n"
            . " - Emergent: {$emergent} ราย\n"
            . " - Urgent: {$urgent} ราย\n"
            . " - Semi Urgent: {$semi_urgent} ราย\n"
            . " - Non Urgent: {$non_urgent} ราย\n"
            . "👩‍⚕️ Oncall: {$request->nurse_oncall}\n"
            . "👩‍⚕️ เสริม: {$request->nurse_partime}\n"
            . "👩‍⚕️ ปกติ: {$request->nurse_fulltime}\n"
            . "🕒 ชม.การพยาบาล: " . number_format($patient_hr, 2) . "\n"
            . "🕒 ชม.การทำงาน: " . number_format($nurse_hr, 2) . "\n"
            . "📈 Productivity: " . number_format($productivity, 2) . "%\n"
            . "ผู้บันทึก: {$request->recorder}";

        // ✅ ส่งข้อความ Telegram
        $token = MainSetting::where('name', 'telegram_token')->value('value');
        $chat_ids = explode(',', MainSetting::where('name', 'er_notifytelegram_save')->value('value'));

        foreach ($chat_ids as $chat_id) {
            Http::asForm()->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => trim($chat_id),
                'text' => $message,
            ]);
            usleep(500000);
        }

        return redirect()->back()->with('success', '✅ ส่งข้อมูลเวรบ่ายเรียบร้อยแล้ว');
    }



}
