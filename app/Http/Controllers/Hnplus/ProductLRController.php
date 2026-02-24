<?php

namespace App\Http\Controllers\Hnplus;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Productivity_lr;
use App\Models\MainSetting;
use Illuminate\Routing\Middleware\Middleware;

#[Middleware('auth', only: ['lr_report', 'lr_product_delete'])]

class ProductLRController extends Controller
{
    //lr_report-------------------------------
    public function lr_report(Request $request)
    {
        $start_date = $request->start_date ? DateThaiToEn($request->start_date) : date('Y-m-d', strtotime("first day of this month"));
        $end_date = $request->end_date ? DateThaiToEn($request->end_date) : date('Y-m-d');

        $product = Productivity_lr::whereBetween('report_date', [$start_date, $end_date])
            ->orderBy('report_date', 'desc')->get();

        $lr_working_hours = MainSetting::where('name', 'lr_working_hours')->value('value') ?? 7;

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
            FROM productivity_lr
            WHERE report_date BETWEEN ? AND ?
            GROUP BY shift_time ORDER BY id', [$lr_working_hours, $start_date, $end_date]);

        $product_asc = Productivity_lr::whereBetween('report_date', [$start_date, $end_date])
            ->orderBy('report_date', 'asc')->get();
        $grouped = $product_asc->groupBy('report_date');
        $report_date = [];
        $night = [];
        $morning = [];
        $afternoon = [];
        foreach ($grouped as $date => $rows) {
            $report_date[] = DateThai($date);
            $night[] = optional($rows->firstWhere('shift_time', 'เวรดึก'))->productivity ?? 0;
            $morning[] = optional($rows->firstWhere('shift_time', 'เวรเช้า'))->productivity ?? 0;
            $afternoon[] = optional($rows->firstWhere('shift_time', 'เวรบ่าย'))->productivity ?? 0;
        }

        $del_product = Auth::check() && Auth::user()->del_product === 'Y';

        return view('hnplus.product.lr_report', compact(
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

    public function lr_product_delete($id)
    {
        Productivity_lr::find($id)->delete();
        return redirect()->route('hnplus.product.lr_report')->with('danger', 'ลบข้อมูลเรียบร้อยแล้ว');
    }

    // Notify Functions
    public function lr_night_notify()
    {
        $lr_ward = MainSetting::where('name', 'lr_ward')->value('value') ?? '08';
        return $this->notify('??เวรดึก', '00:00:01', '07:59:59', $lr_ward, 'lr_night', '?? งานห้องคลอด LR', 'lr_notifytelegram');
    }

    public function lr_morning_notify()
    {
        $lr_ward = MainSetting::where('name', 'lr_ward')->value('value') ?? '08';
        return $this->notify('??เวรเช้า', '08:00:00', '15:59:59', $lr_ward, 'lr_morning', '?? งานห้องคลอด LR', 'lr_notifytelegram');
    }

    public function lr_afternoon_notify()
    {
        $lr_ward = MainSetting::where('name', 'lr_ward')->value('value') ?? '08';
        $target_date = date('Y-m-d');
        return $this->notify('??เวรบ่าย', '16:00:00', '23:59:59', $lr_ward, 'lr_afternoon', '?? งานห้องคลอด LR', 'lr_notifytelegram', $target_date);
    }

    private function notify($shift_name, $start_time, $end_time, $wards, $route, $dep_name, $telegram_key, $date = null)
    {
        $date = $date ?: date('Y-m-d');
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
                    WHERE note_date = ? AND note_time BETWEEN ? AND ?
                    GROUP BY an, note_date
                ) x ON x.an = n.an AND x.note_date = n.note_date AND x.last_time = n.note_time
                WHERE i.ward IN ($wards) AND i.confirm_discharge = 'N'
            ) t
        ", [$date, $start_time, $end_time]);

        $row = $notify[0];
        $url = url("hnplus/product/$route");
        $message = "$dep_name\nวันที่ " . DateThai($date) . "\nเวลา $start_time-$end_time $shift_name\nผู้ป่วยในเวร {$row->patient_all} ราย\n -Convalescent {$row->convalescent}\n -Moderate {$row->Moderate}\n -Semi critical {$row->Semi_critical}\n -Critical {$row->Critical}\n -ไม่ระบุความรุนแรง {$row->severe_type_null}\n\nบันทึก Productivity\n$url\n";

        $token = MainSetting::where('name', 'telegram_token')->value('value');
        $chat_ids = explode(',', MainSetting::where('name', $telegram_key)->value('value'));

        foreach ($chat_ids as $chat_id) {
            Http::asForm()->post("https://api.telegram.org/bot$token/sendMessage", ['chat_id' => trim($chat_id), 'text' => $message]);
        }
        return response()->json(['success' => 'success'], 200);
    }

    // Views
    public function lr_night() { return $this->view_shift('lr_night', 'lr_ward', '00:00:01', '07:59:59'); }
    public function lr_morning() { return $this->view_shift('lr_morning', 'lr_ward', '08:00:00', '15:59:59'); }
    public function lr_afternoon() { return $this->view_shift('lr_afternoon', 'lr_ward', '16:00:00', '23:59:59'); }

    private function view_shift($view, $ward_key, $start_time, $end_time)
    {
        $wards = MainSetting::where('name', $ward_key)->value('value') ?? '08';
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
                    WHERE note_date = CURDATE() AND note_time BETWEEN ? AND ?
                    GROUP BY an, note_date
                ) x ON x.an = n.an AND x.note_date = n.note_date AND x.last_time = n.note_time
                WHERE i.ward IN ($wards) AND i.confirm_discharge = 'N'
            ) t
        ", [$start_time, $end_time]);
        return view("hnplus.product.$view", compact('shift'));
    }

    // Save Functions
    public function lr_night_save(Request $request) { return $this->save_shift($request, '??เวรดึก', '00.00–08.00 น.'); }
    public function lr_morning_save(Request $request) { return $this->save_shift($request, '??เวรเช้า', '08.00–16.00 น.'); }
    public function lr_afternoon_save(Request $request) { return $this->save_shift($request, '??เวรบ่าย', '16.00–24.00 น.'); }

    private function save_shift(Request $request, $shift_name, $time_range)
    {
        $request->validate(['nurse_oncall' => 'required|numeric', 'nurse_partime' => 'required|numeric', 'nurse_fulltime' => 'required|numeric', 'recorder' => 'required|string']);

        $hours = MainSetting::where('name', 'lr_working_hours')->value('value') ?? 7;
        $t1 = MainSetting::where('name', 'lr_patient_type1')->value('value') ?? 1.5;
        $t2 = MainSetting::where('name', 'lr_patient_type2')->value('value') ?? 3.5;
        $t3 = MainSetting::where('name', 'lr_patient_type3')->value('value') ?? 5.5;
        $t4 = MainSetting::where('name', 'lr_patient_type4')->value('value') ?? 7.5;

        $p_all = max(1, $request->patient_all);
        $n_total = $request->nurse_oncall + $request->nurse_partime + $request->nurse_fulltime;
        $n_hr = max(1, $n_total * $hours);

        $p_hr = ($request->convalescent * $t1) + ($request->Moderate * $t2) + ($request->Semi_critical * $t3) + ($request->Critical * $t4);
        $prod = ($p_hr * 100) / $n_hr;
        $nhppd = $p_hr / $p_all;
        $n_shift = $p_all * $nhppd * (1.4 / $hours);

        Productivity_lr::updateOrCreate(
            ['report_date' => $request->report_date, 'shift_time' => $request->shift_time],
            [
                'nurse_fulltime' => $request->nurse_fulltime, 'nurse_partime' => $request->nurse_partime, 'nurse_oncall' => $request->nurse_oncall,
                'recorder' => $request->recorder, 'note' => $request->note, 'patient_all' => $p_all,
                'patient_convalescent' => $request->convalescent, 'patient_moderate' => $request->Moderate,
                'patient_semi_critical' => $request->Semi_critical, 'patient_critical' => $request->Critical,
                'nursing_hours' => $p_hr, 'working_hours' => $n_total * $hours, 'nurse_shift_time' => $n_shift, 'nhppd' => $nhppd, 'productivity' => $prod,
            ]
        );

        $msg = "?? งานห้องคลอด LR\nวันที่ " . DateThai(date('Y-m-d')) . "\nเวลา $time_range $shift_name\nผู้ป่วยในเวร: $p_all ราย\n - Convalescent: {$request->convalescent}\n - Moderate: {$request->Moderate}\n - Semi critical: {$request->Semi_critical}\n - Critical: {$request->Critical}\n????? Oncall: {$request->nurse_oncall}\n????? เสริม: {$request->nurse_partime}\n????? ปกติ: {$request->nurse_fulltime}\n?? Productivity: " . number_format($prod, 2) . "\nผู้บันทึก: {$request->recorder}";

        $token = MainSetting::where('name', 'telegram_token')->value('value');
        $chat_ids = explode(',', MainSetting::where('name', 'lr_notifytelegram_save')->value('value'));
        foreach ($chat_ids as $chat_id) {
            Http::asForm()->post("https://api.telegram.org/bot$token/sendMessage", ['chat_id' => trim($chat_id), 'text' => $msg]);
        }
        return redirect()->back()->with('success', "? ส่งข้อมูล$shift_nameเรียบร้อยแล้ว");
    }
}
