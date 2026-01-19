<?php

namespace App\Http\Controllers\Hnplus;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;  
use App\Models\Nurse_productivity_ipd;
use Illuminate\Routing\Middleware\Middleware;

#[Middleware('auth', only: ['ipd_report','ipd_product_delete'])]

class ProductIPDController extends Controller
{
//ipd_report--------------------------------------------------------------------------------------------------------------------------
    public function ipd_report(Request $request)
    {
        $start_date = $request->start_date ?: date('Y-m-d', strtotime("first day of this month"));
        $end_date = $request->end_date ?: date('Y-m-d');  
        
        $product=Nurse_productivity_ipd::whereBetween('report_date',[$start_date, $end_date])
            ->orderBy('report_date', 'desc')->get(); 
        $product_summary=DB::select('
            SELECT CASE WHEN shift_time = "เวรเช้า" THEN "1" WHEN shift_time = "เวรบ่าย" THEN "2"
            WHEN shift_time = "เวรดึก" THEN "3" END AS "id",shift_time,COUNT(shift_time) AS shift_time_sum,
            SUM(patient_all) AS patient_all,SUM(convalescent) AS convalescent,SUM(moderate_ill) AS moderate_ill,
            SUM(semi_critical_ill) AS semi_critical_ill,SUM(critical_ill) AS critical_ill,SUM(patient_hr) AS patient_hr,
            SUM(nurse_oncall) AS nurse_oncall,SUM(nurse_partime) AS nurse_partime,SUM(nurse_fulltime) AS nurse_fulltime,
            SUM(nurse_hr) AS nurse_hr,((SUM(patient_hr)*100)/SUM(nurse_hr)) AS productivity,(SUM(patient_hr)/SUM(patient_all)) AS hhpuos,
            (SUM(patient_all)*(SUM(patient_hr)/SUM(patient_all))*(1.4/7))/COUNT(shift_time) AS nurse_shift_time
            FROM nurse_productivity_ipds
            WHERE report_date BETWEEN ? AND ?
            GROUP BY shift_time ORDER BY id',[$start_date,$end_date]);

        // เตรียมข้อมูลสำหรับกราฟ
        $product_asc=Nurse_productivity_ipd::whereBetween('report_date',[$start_date, $end_date])
            ->orderBy('report_date', 'asc')->get(); 
        $grouped = $product_asc->groupBy('report_date');
        $report_date = [];
        $night = [];
        $morning = [];
        $afternoon = [];
        foreach ($grouped as $date => $rows) {
            $report_date[] = DateThai($date);
            // ค้นหาค่า productivity ของแต่ละเวร
            $night[]     = optional($rows->firstWhere('shift_time', 'เวรดึก'))->productivity ?? 0;
            $morning[]   = optional($rows->firstWhere('shift_time', 'เวรเช้า'))->productivity ?? 0;
            $afternoon[] = optional($rows->firstWhere('shift_time', 'เวรบ่าย'))->productivity ?? 0;
        }

        // ลบ Product ------------------
        $del_product = Auth::check() && Auth::user()->del_product === 'Y';
        
        return view('hnplus.product.ipd_report',compact('product_summary','product','start_date',
                'end_date','del_product','report_date','night','morning','afternoon'));        
    }

//product_delete----------------------------------------------------------------------------------------------------------------
    public function ipd_product_delete($id)
    {
        $product=Nurse_productivity_ipd::find($id)->delete();
        return redirect()->route('hnplus.product.ipd_report')->with('danger', 'ลบข้อมูลเรียบร้อยแล้ว');
    }

//แจ้งเตือนสถานะการณ์สรุปเวรดึก รัน 08.00 น.---------------------------------------------------------------------------------------------
    public function ipd_night_notify()
    {
        $notify = DB::connection('hosxp')->select("
            SELECT COUNT(DISTINCT i.an) AS patient_all,
                COALESCE(SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE '1%' THEN 1 ELSE 0 END), 0) AS convalescent,
                COALESCE(SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE '2%' THEN 1 ELSE 0 END), 0) AS moderate_ill,
                COALESCE(SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE '3%' THEN 1 ELSE 0 END), 0) AS semi_critical_ill,
                COALESCE(SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE '4%' THEN 1 ELSE 0 END), 0) AS critical_ill,
                COALESCE(SUM(CASE WHEN (i.ipd_nurse_eval_range_code IS NULL OR i.ipd_nurse_eval_range_code = '') THEN 1 ELSE 0 END), 0) AS severe_type_null
            FROM ipt i
            LEFT JOIN an_stat a ON a.an = i.an
            LEFT JOIN patient p ON p.hn = i.hn
            WHERE i.ward IN ('01')
            AND i.confirm_discharge = 'N'");         

        foreach ($notify as $row) {
            $patient_all = $row->patient_all;
            $convalescent = $row->convalescent;
            $moderate_ill = $row->moderate_ill;
            $semi_critical_ill = $row->semi_critical_ill;
            $critical_ill = $row->critical_ill;
            $severe_type_null = $row->severe_type_null;
            $url = url('hnplus/product/ipd_night');
        }
                
    //แจ้งเตือน Telegram

        $message = "🛏️ งานผู้ป่วยใน สามัญ" ."\n"
        ."วันที่ " . DateThai(date('Y-m-d')) ."\n" 
        ."เวลา 00.00-08.00 น. 🌙เวรดึก" ."\n"
        ."ผู้ป่วยในเวร " .$patient_all ." ราย" ."\n"       
        ." -Convalescent " .$convalescent ." ราย" ."\n"
        ." -Moderate ill " .$moderate_ill ." ราย" ."\n"
        ." -Semi critical ill " .$semi_critical_ill ." ราย" ."\n" 
        ." -Critical ill " .$critical_ill ." ราย" ."\n"
        ." -ไม่ระบุความรุนแรง ". $severe_type_null ." ราย" ."\n" ."\n"
        ."บันทึก Productivity " ."\n"
        . $url. "\n";

        // ✅ ส่งข้อความ Telegram
        $token = DB::table('nurse_setting')->where('name', 'telegram_token')->value('value');
        $chat_ids = explode(',', DB::table('nurse_setting')->where('name', 'telegram_chat_id_product_ipd')->value('value'));

        foreach ($chat_ids as $chat_id) {
                $url = "https://api.telegram.org/bot$token/sendMessage";
                $data = [
                    'chat_id' => $chat_id,
                    'text'    => $message
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
        $shift = DB::connection('hosxp')->select("
            SELECT COUNT(DISTINCT i.an) AS patient_all,
                COALESCE(SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE '1%' THEN 1 ELSE 0 END), 0) AS convalescent,
                COALESCE(SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE '2%' THEN 1 ELSE 0 END), 0) AS moderate_ill,
                COALESCE(SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE '3%' THEN 1 ELSE 0 END), 0) AS semi_critical_ill,
                COALESCE(SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE '4%' THEN 1 ELSE 0 END), 0) AS critical_ill,
                COALESCE(SUM(CASE WHEN (i.ipd_nurse_eval_range_code IS NULL OR i.ipd_nurse_eval_range_code = '') THEN 1 ELSE 0 END), 0) AS severe_type_null
            FROM ipt i
            LEFT JOIN an_stat a ON a.an = i.an
            LEFT JOIN patient p ON p.hn = i.hn
            WHERE i.ward IN ('01')
            AND i.confirm_discharge = 'N'"); 

        return view('hnplus.product.ipd_night',compact('shift'));            
    }

//ipd_night_save--------------------------------------------------------------------------------------------------------------------
    public function ipd_night_save(Request $request)
    {
        // ✅ ตรวจสอบข้อมูลที่จำเป็น
        $request->validate([
            'nurse_oncall'   => 'required|numeric',
            'nurse_partime'  => 'required|numeric',
            'nurse_fulltime' => 'required|numeric',
            'recorder'       => 'required|string',
        ]);

        // ✅ เตรียมค่าที่ใช้ซ้ำ
        $convalescent      = $request->convalescent;
        $moderate_ill      = $request->moderate_ill;
        $semi_critical_ill = $request->semi_critical_ill;
        $critical_ill      = $request->critical_ill;
        $patient_all       = max(1, $request->patient_all); // ป้องกันหาร 0
        $nurse_total    = $request->nurse_oncall + $request->nurse_partime + $request->nurse_fulltime;
        $nurse_total_hr = max(1, $nurse_total * 7); // ป้องกันหาร 0

        // ✅ คำนวณค่าทางสถิติ
        $patient_hr = ($convalescent * 0.45)
            + ($moderate_ill * 1.17)
            + ($semi_critical_ill * 1.71)
            + ($critical_ill * 1.99);
        $nurse_hr = $nurse_total * 7;
        $productivity = ($patient_hr * 100) / $nurse_total_hr;
        $hhpuos = $patient_hr / $patient_all;
        $nurse_shift_time = $patient_all * $hhpuos * (1.4 / 7);

        // ✅ บันทึกข้อมูลลงฐานข้อมูล
        $productivity_ipd = Nurse_productivity_ipd::create([
            'report_date'      => $request->report_date,
            'shift_time'       => $request->shift_time,
            'patient_all'      => $patient_all,
            'convalescent'     => $convalescent,
            'moderate_ill'     => $moderate_ill,
            'semi_critical_ill'=> $semi_critical_ill,
            'critical_ill'     => $critical_ill,
            'patient_hr'       => $patient_hr,
            'nurse_oncall'     => $request->nurse_oncall,
            'nurse_partime'    => $request->nurse_partime,
            'nurse_fulltime'   => $request->nurse_fulltime,
            'nurse_hr'         => $nurse_hr,
            'productivity'     => $productivity,
            'hhpuos'           => $hhpuos,
            'nurse_shift_time' => $nurse_shift_time,
            'recorder'         => $request->recorder,
            'note'             => $request->note,
        ]);

        // ✅ เตรียมข้อความแจ้ง Telegram
        $message = "🛏️ งานผู้ป่วยใน สามัญ" ."\n"
            ."วันที่ " . DateThai(date('Y-m-d')) ."\n"
            ."เวลา 00.00–08.00 น. 🌙เวรดึก" ."\n"
            ."ผู้ป่วยในเวร: {$patient_all} ราย" ."\n"
            ." - Convalescent: {$convalescent} ราย" ."\n"
            ." - Moderate ill: {$moderate_ill} ราย" ."\n"
            ." - Semi critical ill: {$semi_critical_ill} ราย" ."\n"
            ." - Critical ill: {$critical_ill} ราย" ."\n"
            ."👩‍⚕️ Oncall: {$request->nurse_oncall}" ."\n"
            ."👩‍⚕️ เสริม: {$request->nurse_partime}" ."\n"
            ."👩‍⚕️ ปกติ: {$request->nurse_fulltime}" ."\n"
            ."🕒 ชม.การพยาบาล: " . number_format($patient_hr, 2) ."\n"
            ."🕒 ชม.การทำงาน: " . number_format($nurse_hr, 2) ."\n"
            ."📊 Productivity: " . number_format($productivity, 2) ."\n"
            ."🧮 HHPUOS: " . number_format($hhpuos, 2) ."\n"
            ."ผู้บันทึก: {$request->recorder}";

        // ✅ ส่งข้อความ Telegram
        $token = DB::table('nurse_setting')->where('name', 'telegram_token')->value('value');
        $chat_ids = explode(',', DB::table('nurse_setting')->where('name', 'telegram_chat_id_product_ipd_save')->value('value'));

        foreach ($chat_ids as $chat_id) {
            Http::asForm()->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => trim($chat_id),
                'text'    => $message,
            ]);
            usleep(500000); // พัก 0.5 วินาที
        }

        return redirect()->back()->with('success', '✅ ส่งข้อมูลเวรดึกเรียบร้อยแล้ว');
    }

//แจ้งเตือนสถานะการณ์สรุปเวรเช้า รัน 16.00 น.---------------------------------------------------------------------------------------------
    public function ipd_morning_notify()
    {
        $notify = DB::connection('hosxp')->select("
            SELECT COUNT(DISTINCT i.an) AS patient_all,
                COALESCE(SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE '1%' THEN 1 ELSE 0 END), 0) AS convalescent,
                COALESCE(SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE '2%' THEN 1 ELSE 0 END), 0) AS moderate_ill,
                COALESCE(SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE '3%' THEN 1 ELSE 0 END), 0) AS semi_critical_ill,
                COALESCE(SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE '4%' THEN 1 ELSE 0 END), 0) AS critical_ill,
                COALESCE(SUM(CASE WHEN (i.ipd_nurse_eval_range_code IS NULL OR i.ipd_nurse_eval_range_code = '') THEN 1 ELSE 0 END), 0) AS severe_type_null
            FROM ipt i
            LEFT JOIN an_stat a ON a.an = i.an
            LEFT JOIN patient p ON p.hn = i.hn
            WHERE i.ward IN ('01')
            AND i.confirm_discharge = 'N'");         

        foreach ($notify as $row) {
            $patient_all = $row->patient_all;
            $convalescent = $row->convalescent;
            $moderate_ill = $row->moderate_ill;
            $semi_critical_ill = $row->semi_critical_ill;
            $critical_ill = $row->critical_ill;
            $severe_type_null = $row->severe_type_null;
            $url = url('hnplus/product/ipd_morning');
        }
                
    //แจ้งเตือน Telegram

        $message = "🛏️ งานผู้ป่วยใน สามัญ" ."\n"
        ."วันที่ " . DateThai(date('Y-m-d')) ."\n"
        ."เวลา 08.00-16.00 น. 🌅เวรเช้า" ."\n"
        ."ผู้ป่วยในเวร " .$patient_all ." ราย" ."\n"       
        ." -Convalescent " .$convalescent ." ราย" ."\n"
        ." -Moderate ill " .$moderate_ill ." ราย" ."\n"
        ." -Semi critical ill " .$semi_critical_ill ." ราย" ."\n" 
        ." -Critical ill " .$critical_ill ." ราย" ."\n"
        ." -ไม่ระบุความรุนแรง ". $severe_type_null ." ราย" ."\n" ."\n"
        ."บันทึก Productivity " ."\n"
        . $url. "\n";

        // ✅ ส่งข้อความ Telegram
        $token = DB::table('nurse_setting')->where('name', 'telegram_token')->value('value');
        $chat_ids = explode(',', DB::table('nurse_setting')->where('name', 'telegram_chat_id_product_ipd')->value('value'));

        foreach ($chat_ids as $chat_id) {
                $url = "https://api.telegram.org/bot$token/sendMessage";

                $data = [
                    'chat_id' => $chat_id,
                    'text'    => $message
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
        $shift = DB::connection('hosxp')->select("
            SELECT COUNT(DISTINCT i.an) AS patient_all,
                COALESCE(SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE '1%' THEN 1 ELSE 0 END), 0) AS convalescent,
                COALESCE(SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE '2%' THEN 1 ELSE 0 END), 0) AS moderate_ill,
                COALESCE(SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE '3%' THEN 1 ELSE 0 END), 0) AS semi_critical_ill,
                COALESCE(SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE '4%' THEN 1 ELSE 0 END), 0) AS critical_ill,
                COALESCE(SUM(CASE WHEN (i.ipd_nurse_eval_range_code IS NULL OR i.ipd_nurse_eval_range_code = '') THEN 1 ELSE 0 END), 0) AS severe_type_null
            FROM ipt i
            LEFT JOIN an_stat a ON a.an = i.an
            LEFT JOIN patient p ON p.hn = i.hn
            WHERE i.ward IN ('01')
            AND i.confirm_discharge = 'N'"); 

        return view('hnplus.product.ipd_morning',compact('shift'));            
    }

//ipd_morning_save------------------------------------------------------------------------------------------------------------------
    public function ipd_morning_save(Request $request)
    {
        // ✅ ตรวจสอบข้อมูลที่จำเป็น
        $request->validate([
            'nurse_oncall'   => 'required|numeric',
            'nurse_partime'  => 'required|numeric',
            'nurse_fulltime' => 'required|numeric',
            'recorder'       => 'required|string',
        ]);

        // ✅ เตรียมค่าที่ใช้ซ้ำ
        $convalescent      = $request->convalescent;
        $moderate_ill      = $request->moderate_ill;
        $semi_critical_ill = $request->semi_critical_ill;
        $critical_ill      = $request->critical_ill;
        $patient_all       = max(1, $request->patient_all); // ป้องกันหาร 0
        $nurse_total    = $request->nurse_oncall + $request->nurse_partime + $request->nurse_fulltime;
        $nurse_total_hr = max(1, $nurse_total * 7); // ป้องกันหาร 0

        // ✅ คำนวณค่าทางสถิติ
        $patient_hr = ($convalescent * 0.45)
            + ($moderate_ill * 1.17)
            + ($semi_critical_ill * 1.71)
            + ($critical_ill * 1.99);
        $nurse_hr = $nurse_total * 7;
        $productivity = ($patient_hr * 100) / $nurse_total_hr;
        $hhpuos = $patient_hr / $patient_all;
        $nurse_shift_time = $patient_all * $hhpuos * (1.4 / 7);

        // ✅ บันทึกข้อมูลลงฐานข้อมูล
        $productivity_ipd = Nurse_productivity_ipd::create([
            'report_date'      => $request->report_date,
            'shift_time'       => $request->shift_time,
            'patient_all'      => $patient_all,
            'convalescent'     => $convalescent,
            'moderate_ill'     => $moderate_ill,
            'semi_critical_ill'=> $semi_critical_ill,
            'critical_ill'     => $critical_ill,
            'patient_hr'       => $patient_hr,
            'nurse_oncall'     => $request->nurse_oncall,
            'nurse_partime'    => $request->nurse_partime,
            'nurse_fulltime'   => $request->nurse_fulltime,
            'nurse_hr'         => $nurse_hr,
            'productivity'     => $productivity,
            'hhpuos'           => $hhpuos,
            'nurse_shift_time' => $nurse_shift_time,
            'recorder'         => $request->recorder,
            'note'             => $request->note,
        ]);

        // ✅ เตรียมข้อความแจ้ง Telegram
        $message = "🛏️ งานผู้ป่วยใน สามัญ" ."\n"
            ."วันที่ " . DateThai(date('Y-m-d')) ."\n"
            ."เวลา 08.00–16.00 น. 🌅เวรเช้า" ."\n"
            ."ผู้ป่วยในเวร: {$patient_all} ราย" ."\n"
            ." - Convalescent: {$convalescent} ราย" ."\n"
            ." - Moderate ill: {$moderate_ill} ราย" ."\n"
            ." - Semi critical ill: {$semi_critical_ill} ราย" ."\n"
            ." - Critical ill: {$critical_ill} ราย" ."\n"
            ."👩‍⚕️ Oncall: {$request->nurse_oncall}" ."\n"
            ."👩‍⚕️ เสริม: {$request->nurse_partime}" ."\n"
            ."👩‍⚕️ ปกติ: {$request->nurse_fulltime}" ."\n"
            ."🕒 ชม.การพยาบาล: " . number_format($patient_hr, 2) ."\n"
            ."🕒 ชม.การทำงาน: " . number_format($nurse_hr, 2) ."\n"
            ."📊 Productivity: " . number_format($productivity, 2) ."\n"
            ."🧮 HHPUOS: " . number_format($hhpuos, 2) ."\n"
            ."ผู้บันทึก: {$request->recorder}";

        // ✅ ส่งข้อความ Telegram
        $token = DB::table('nurse_setting')->where('name', 'telegram_token')->value('value');
        $chat_ids = explode(',', DB::table('nurse_setting')->where('name', 'telegram_chat_id_product_ipd_save')->value('value'));

        foreach ($chat_ids as $chat_id) {
            Http::asForm()->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => trim($chat_id),
                'text'    => $message,
            ]);
            usleep(500000); // พัก 0.5 วินาที
        }

        return redirect()->back()->with('success', '✅ ส่งข้อมูลเวรเช้าเรียบร้อยแล้ว');
    }

//แจ้งเตือนสถานะการณ์สรุปเวรบ่าย รัน 00.01 น.---------------------------------------------------------------------------------------------
    public function ipd_afternoon_notify()
    {
        $notify = DB::connection('hosxp')->select("
            SELECT COUNT(DISTINCT i.an) AS patient_all,
                COALESCE(SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE '1%' THEN 1 ELSE 0 END), 0) AS convalescent,
                COALESCE(SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE '2%' THEN 1 ELSE 0 END), 0) AS moderate_ill,
                COALESCE(SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE '3%' THEN 1 ELSE 0 END), 0) AS semi_critical_ill,
                COALESCE(SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE '4%' THEN 1 ELSE 0 END), 0) AS critical_ill,
                COALESCE(SUM(CASE WHEN (i.ipd_nurse_eval_range_code IS NULL OR i.ipd_nurse_eval_range_code = '') THEN 1 ELSE 0 END), 0) AS severe_type_null
            FROM ipt i
            LEFT JOIN an_stat a ON a.an = i.an
            LEFT JOIN patient p ON p.hn = i.hn
            WHERE i.ward IN ('01')
            AND i.confirm_discharge = 'N'");         

        foreach ($notify as $row) {
            $patient_all = $row->patient_all;
            $convalescent = $row->convalescent;
            $moderate_ill = $row->moderate_ill;
            $semi_critical_ill = $row->semi_critical_ill;
            $critical_ill = $row->critical_ill;
            $severe_type_null = $row->severe_type_null;
            $url=url('hnplus/product/ipd_afternoon'); 
        }  
                
        //แจ้งเตือน Telegram

        $message = "🛏️ งานผู้ป่วยใน สามัญ" ."\n"
        ."วันที่ " .DateThai(date("Y-m-d", strtotime("-1 day"))) ."\n"  
        ."เวลา 16.00-24.00 น. 🌇เวรบ่าย" ."\n"
        ."ผู้ป่วยในเวร " .$patient_all ." ราย" ."\n"       
        ." -Convalescent " .$convalescent ." ราย" ."\n"
        ." -Moderate ill " .$moderate_ill ." ราย" ."\n"
        ." -Semi critical ill " .$semi_critical_ill ." ราย" ."\n" 
        ." -Critical ill " .$critical_ill ." ราย" ."\n"
        ." -ไม่ระบุความรุนแรง ". $severe_type_null ." ราย" ."\n" ."\n"
        ."บันทึก Productivity " ."\n"
        . $url. "\n";

        // ✅ ส่งข้อความ Telegram
        $token = DB::table('nurse_setting')->where('name', 'telegram_token')->value('value');
        $chat_ids = explode(',', DB::table('nurse_setting')->where('name', 'telegram_chat_id_product_ipd')->value('value'));

        foreach ($chat_ids as $chat_id) {
                $url = "https://api.telegram.org/bot$token/sendMessage";

                $data = [
                    'chat_id' => $chat_id,
                    'text'    => $message
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
        $shift = DB::connection('hosxp')->select("
            SELECT COUNT(DISTINCT i.an) AS patient_all,
                COALESCE(SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE '1%' THEN 1 ELSE 0 END), 0) AS convalescent,
                COALESCE(SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE '2%' THEN 1 ELSE 0 END), 0) AS moderate_ill,
                COALESCE(SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE '3%' THEN 1 ELSE 0 END), 0) AS semi_critical_ill,
                COALESCE(SUM(CASE WHEN i.ipd_nurse_eval_range_code LIKE '4%' THEN 1 ELSE 0 END), 0) AS critical_ill,
                COALESCE(SUM(CASE WHEN (i.ipd_nurse_eval_range_code IS NULL OR i.ipd_nurse_eval_range_code = '') THEN 1 ELSE 0 END), 0) AS severe_type_null
            FROM ipt i
            LEFT JOIN an_stat a ON a.an = i.an
            LEFT JOIN patient p ON p.hn = i.hn
            WHERE i.ward IN ('01')
            AND i.confirm_discharge = 'N'");

        return view('hnplus.product.ipd_afternoon',compact('shift'));            
    }

//ipd_afternoon_save---------------------------------------------------------------------------------------------------------------
    public function ipd_afternoon_save(Request $request)
    {
        // ✅ ตรวจสอบข้อมูลที่จำเป็น
        $request->validate([
            'nurse_oncall'   => 'required|numeric',
            'nurse_partime'  => 'required|numeric',
            'nurse_fulltime' => 'required|numeric',
            'recorder'       => 'required|string',
        ]);

        // ✅ เตรียมค่าที่ใช้ซ้ำ
        $convalescent      = $request->convalescent;
        $moderate_ill      = $request->moderate_ill;
        $semi_critical_ill = $request->semi_critical_ill;
        $critical_ill      = $request->critical_ill;
        $patient_all       = max(1, $request->patient_all); // ป้องกันหาร 0
        $nurse_total    = $request->nurse_oncall + $request->nurse_partime + $request->nurse_fulltime;
        $nurse_total_hr = max(1, $nurse_total * 7); // ป้องกันหาร 0

        // ✅ คำนวณค่าทางสถิติ
        $patient_hr = ($convalescent * 0.45)
            + ($moderate_ill * 1.17)
            + ($semi_critical_ill * 1.71)
            + ($critical_ill * 1.99);
        $nurse_hr = $nurse_total * 7;
        $productivity = ($patient_hr * 100) / $nurse_total_hr;
        $hhpuos = $patient_hr / $patient_all;
        $nurse_shift_time = $patient_all * $hhpuos * (1.4 / 7);

        // ✅ บันทึกข้อมูลลงฐานข้อมูล
        $productivity_ipd = Nurse_productivity_ipd::create([
            'report_date'      => $request->report_date,
            'shift_time'       => $request->shift_time,
            'patient_all'      => $patient_all,
            'convalescent'     => $convalescent,
            'moderate_ill'     => $moderate_ill,
            'semi_critical_ill'=> $semi_critical_ill,
            'critical_ill'     => $critical_ill,
            'patient_hr'       => $patient_hr,
            'nurse_oncall'     => $request->nurse_oncall,
            'nurse_partime'    => $request->nurse_partime,
            'nurse_fulltime'   => $request->nurse_fulltime,
            'nurse_hr'         => $nurse_hr,
            'productivity'     => $productivity,
            'hhpuos'           => $hhpuos,
            'nurse_shift_time' => $nurse_shift_time,
            'recorder'         => $request->recorder,
            'note'             => $request->note,
        ]);

        // ✅ เตรียมข้อความแจ้ง Telegram
        $message = "🛏️ งานผู้ป่วยใน สามัญ" ."\n"
            ."วันที่ " .DateThai(date("Y-m-d", strtotime("-1 day"))) ."\n"  
            ."เวลา 16.00–24.00 น. 🌇เวรบ่าย" ."\n"
            ."ผู้ป่วยในเวร: {$patient_all} ราย" ."\n"
            ." - Convalescent: {$convalescent} ราย" ."\n"
            ." - Moderate ill: {$moderate_ill} ราย" ."\n"
            ." - Semi critical ill: {$semi_critical_ill} ราย" ."\n"
            ." - Critical ill: {$critical_ill} ราย" ."\n"
            ."👩‍⚕️ Oncall: {$request->nurse_oncall}" ."\n"
            ."👩‍⚕️ เสริม: {$request->nurse_partime}" ."\n"
            ."👩‍⚕️ ปกติ: {$request->nurse_fulltime}" ."\n"
            ."🕒 ชม.การพยาบาล: " . number_format($patient_hr, 2) ."\n"
            ."🕒 ชม.การทำงาน: " . number_format($nurse_hr, 2) ."\n"
            ."📊 Productivity: " . number_format($productivity, 2) ."\n"
            ."🧮 HHPUOS: " . number_format($hhpuos, 2) ."\n"
            ."ผู้บันทึก: {$request->recorder}";

        // ✅ ส่งข้อความ Telegram
        $token = DB::table('nurse_setting')->where('name', 'telegram_token')->value('value');
        $chat_ids = explode(',', DB::table('nurse_setting')->where('name', 'telegram_chat_id_product_ipd_save')->value('value'));

        foreach ($chat_ids as $chat_id) {
            Http::asForm()->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => trim($chat_id),
                'text'    => $message,
            ]);
            usleep(500000); // พัก 0.5 วินาที
        }
         
        return redirect()->back()->with('success', '✅ ส่งข้อมูลเวรบ่ายเรียบร้อยแล้ว');
    }

}
