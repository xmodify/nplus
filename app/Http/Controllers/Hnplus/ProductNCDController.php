<?php

namespace App\Http\Controllers\Hnplus;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;  
use App\Models\Nurse_productivity_ncd;
use Illuminate\Routing\Middleware\Middleware;

#[Middleware('auth', only: ['ncd_report','ncd_product_delete'])]

class ProductNCDController extends Controller
{
//ncd_report--------------------------------------------------------------------------------------------------------------------------
    public function ncd_report(Request $request)
    {
        $start_date = $request->start_date ?: date('Y-m-d', strtotime("first day of this month"));
        $end_date = $request->end_date ?: date('Y-m-d');  
        
        $product=Nurse_productivity_ncd::whereBetween('report_date',[$start_date, $end_date])
            ->orderBy('report_date', 'desc')->get(); 
        $product_summary=DB::select('
            SELECT shift_time,COUNT(shift_time) AS shift_time_sum,SUM(patient_all) AS patient_all,
            SUM(patient_hr) AS patient_hr,SUM(nurse_oncall) AS nurse_oncall,
            SUM(nurse_partime) AS nurse_partime,SUM(nurse_fulltime) AS nurse_fulltime, SUM(nurse_hr) AS nurse_hr,
            ((SUM(patient_hr)*100)/SUM(nurse_hr)) AS productivity,(SUM(patient_hr)/SUM(patient_all)) AS hhpuos,
            (SUM(patient_all)*(SUM(patient_hr)/SUM(patient_all))*(1.4/9))/COUNT(shift_time) AS nurse_shift_time
            FROM nurse_productivity_ncds
            WHERE report_date BETWEEN ? AND ?
            GROUP BY shift_time ORDER BY shift_time DESC',[$start_date,$end_date]); 

        // เตรียมข้อมูลสำหรับกราฟ
        $product_asc=Nurse_productivity_ncd::whereBetween('report_date',[$start_date, $end_date])
            ->orderBy('report_date', 'asc')->get(); 
        $grouped = $product_asc->groupBy('report_date');
        $report_date = [];
        $morning = [];
        foreach ($grouped as $date => $rows) {
            $report_date[] = DateThai($date);
            // ค้นหาค่า productivity ของแต่ละเวร
            $morning[]   = optional($rows->firstWhere('shift_time', 'เวรเช้า'))->productivity ?? 0;           
        }

        // ลบ Product ------------------
        $del_product = Auth::check() && Auth::user()->del_product === 'Y';
        
        return view('hnplus.product.ncd_report',compact('product_summary','product','start_date',
                'end_date','del_product','report_date','morning'));        
    }

//product_delete----------------------------------------------------------------------------------------------------------------
    public function ncd_product_delete($id)
    {
        $product=Nurse_productivity_ncd::find($id)->delete();
        return redirect()->route('hnplus.product.ncd_report')->with('danger', 'ลบข้อมูลเรียบร้อยแล้ว');
    }

//แจ้งเตือนสถานะการณ์สรุปเวรเช้า รัน 16.00 น.---------------------------------------------------------------------------------------------
    public function ncd_morning_notify()
    {
        $notify = DB::connection('hosxp')->select("
            SELECT IFNULL(COUNT(DISTINCT o1.vn),0) AS patient_all
            FROM opd_dep_queue o1, ovst o2 WHERE o1.depcode IN ('025')
            AND o1.vn = o2.vn AND o2.vstdate = DATE(NOW()) 
            AND o2.vsttime BETWEEN '00:00:00' AND '15:59:59' ");         

        foreach ($notify as $row) {
            $patient_all = $row->patient_all;      
            $url = url('hnplus/product/ncd_morning');
        }
                
    //แจ้งเตือน Telegram

        $message = "🧑‍⚕️งานผู้ป่วย NCD" ."\n"
        ."วันที่ " . DateThai(date('Y-m-d')) ."\n"
        ."เวลา 08.00-16.00 น. 🌅เวรเช้า" ."\n"
        ."ผู้ป่วยในเวร " .$patient_all ." ราย" ."\n"     
        ."บันทึก Productivity " ."\n"
        . $url. "\n";

        // ✅ ส่งข้อความ Telegram
        $token = DB::table('nurse_setting')->where('name', 'telegram_token')->value('value');
        $chat_ids = explode(',', DB::table('nurse_setting')->where('name', 'telegram_chat_id_product_ncd')->value('value'));

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

//ncd_morning-------------------------------------------------------------------------------------------------------------
    public function ncd_morning()
    {
        $shift = DB::connection('hosxp')->select("
            SELECT IFNULL(COUNT(DISTINCT o1.vn),0) AS patient_all
            FROM opd_dep_queue o1, ovst o2 WHERE o1.depcode IN ('025')
            AND o1.vn = o2.vn AND o2.vstdate = DATE(NOW()) 
            AND o2.vsttime BETWEEN '00:00:00' AND '15:59:59' "); 

        return view('hnplus.product.ncd_morning',compact('shift'));            
    }

//ncd_morning_save------------------------------------------------------------------------------------------------------------------
    public function ncd_morning_save(Request $request)
    {
        // ตรวจสอบข้อมูลที่จำเป็น
        $request->validate([
            'nurse_oncall'   => 'required|numeric',
            'nurse_partime'  => 'required|numeric',
            'nurse_fulltime' => 'required|numeric',
            'recorder'       => 'required|string',
        ]);

        // ==============================
        //   กำหนดค่า default = 0
        // ==============================
        $patient_all = $request->patient_all ?? 0;

        $nurse_oncall   = $request->nurse_oncall ?? 0;
        $nurse_partime  = $request->nurse_partime ?? 0;
        $nurse_fulltime = $request->nurse_fulltime ?? 0;

        // ==============================
        //   คำนวณสูตร Productivity NCD
        // ==============================
        $patient_hr = $patient_all * 0.5;
        $nurse_total = $nurse_oncall + $nurse_partime + $nurse_fulltime;
        $nurse_hr    = $nurse_total * 9;  // NCD ใช้ 9 ชั่วโมง
        $productivity = ($patient_hr * 100) / max(1, $nurse_hr);
        $hhpuos = $patient_hr / max(1, $patient_all);
        $nurse_shift_time = $patient_all * $hhpuos * (1.4 / 9);

        // ==============================
        //   บันทึกข้อมูลลงฐานข้อมูล
        // ==============================
        Nurse_productivity_ncd::updateOrCreate(
            // 🔎 เงื่อนไขเช็คซ้ำ (วันที่ + เวร)
            [
                'report_date' => $request->report_date,
                'shift_time'  => $request->shift_time,
            ],
            // 📝 ข้อมูล insert / update (คอลัมน์เดิมทั้งหมด)
            [
                'nurse_fulltime'    => $nurse_fulltime,
                'nurse_partime'     => $nurse_partime,
                'nurse_oncall'      => $nurse_oncall,
                'recorder'          => $request->recorder,
                'note'              => $request->note,

                'patient_all'       => $patient_all,

                'patient_hr'        => $patient_hr,
                'nurse_hr'          => $nurse_hr,
                'nurse_shift_time'  => $nurse_shift_time,
                'hhpuos'            => $hhpuos,
                'productivity'      => $productivity,
            ]
        );

        // ==============================
        //   ข้อความแจ้งเตือน Telegram (รูปแบบใหม่)
        // ==============================
        $message =
            "🏥 งานผู้ป่วย NCD\n" .
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
            "🧮 HHPUOS: " . number_format($hhpuos, 2) . "\n" .
            "ผู้บันทึก: {$request->recorder}";

        // ==============================
        //   ส่งข้อความ Telegram
        // ==============================
        $token = DB::table('nurse_setting')->where('name', 'telegram_token')->value('value');
        $chat_ids = explode(',', DB::table('nurse_setting')->where('name', 'telegram_chat_id_product_ncd')->value('value'));

        foreach ($chat_ids as $chat_id) {
            Http::asForm()->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => trim($chat_id),
                'text'    => $message,
            ]);
            usleep(500000);
        }

        return redirect()->back()->with('success', '✅ ส่งข้อมูลเวรเช้าเรียบร้อยแล้ว');
    }


}
