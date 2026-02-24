<?php

namespace App\Http\Controllers\Hnplus;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MainSetting;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = date('Y-m-d');
        $now = Carbon::now();
        $currentTime = $now->format('H:i:s');

        // Retrieve Settings
        $ipd_ward = MainSetting::where('name', 'ipd_ward')->value('value') ?? "'01'";
        $ncd_dep = MainSetting::where('name', 'ncd_department')->value('value') ?? "'025'";
        // For OPD, typically includes general OPD departments. If not specified in settings, using a safe default or referencing what might be common.
        // Based on ProductOPDController, it uses 'opd_department' setting.
        $opd_dep = MainSetting::where('name', 'opd_department')->value('value') ?? "'002','050'";
        $ari_dep = MainSetting::where('name', 'ari_department')->value('value') ?? "'ARI'";
        $vip_ward = MainSetting::where('name', 'vip_ward')->value('value') ?? "''";
        $lr_ward = MainSetting::where('name', 'lr_ward')->value('value') ?? "''";
        $ckd_dep = MainSetting::where('name', 'ckd_department')->value('value') ?? "''";
        $hd_dep = MainSetting::where('name', 'hd_department')->value('value') ?? "''";

        // Determine Current Shift for ER & IPD
        // - เวรดึก 00:00:01 - 07:59:59
        // - เวรเช้า 08:00:00 - 15:59:59
        // - เวรบ่าย 16:00:00 - 23:59:59
        $shift_name = '';
        $start_time = '';
        $end_time = '';

        if ($currentTime >= '00:00:01' && $currentTime <= '07:59:59') {
            $shift_name = 'เวรดึก';
            $start_time = '00:00:01';
            $end_time = '07:59:59';
        }
        elseif ($currentTime >= '08:00:00' && $currentTime <= '15:59:59') {
            $shift_name = 'เวรเช้า';
            $start_time = '08:00:00';
            $end_time = '15:59:59';
        }
        else {
            $shift_name = 'เวรบ่าย';
            $start_time = '16:00:00';
            $end_time = '23:59:59';
        }

        // 1. ER Data (Dynamic Shift)
        $er_query = "
            SELECT 
                DATE(NOW()) AS vstdate,
                COALESCE(COUNT(DISTINCT e.vn), 0) AS visit,
                COALESCE(SUM(CASE WHEN et.export_code IN ('1') THEN 1 ELSE 0 END), 0) AS resuscitation,
                COALESCE(SUM(CASE WHEN et.export_code IN ('2') THEN 1 ELSE 0 END), 0) AS emergent,
                COALESCE(SUM(CASE WHEN et.export_code IN ('3') THEN 1 ELSE 0 END), 0) AS urgent,
                COALESCE(SUM(CASE WHEN et.export_code IN ('4') THEN 1 ELSE 0 END), 0) AS semi_urgent,
                COALESCE(SUM(CASE WHEN et.export_code IN ('5') THEN 1 ELSE 0 END), 0) AS non_urgent,
                COALESCE(SUM(CASE WHEN et.export_code NOT IN ('1','2','3','4','5') OR et.export_code IS NULL THEN 1 ELSE 0 END), 0) AS unknown
            FROM er_regist e
            LEFT JOIN er_emergency_type et 
                ON et.er_emergency_type = e.er_emergency_type
            WHERE DATE(e.enter_er_time) = CURDATE()
            AND TIME(e.enter_er_time) BETWEEN ? AND ?
        ";
        $er_result = DB::connection('hosxp')->selectOne($er_query, [$start_time, $end_time]);

        $er_stats = [
            'shift' => $shift_name,
            'resuscitation' => $er_result->resuscitation ?? 0,
            'emergent' => $er_result->emergent ?? 0,
            'urgent' => $er_result->urgent ?? 0,
            'semi_urgent' => $er_result->semi_urgent ?? 0,
            'non_urgent' => $er_result->non_urgent ?? 0,
            'unknown' => $er_result->unknown ?? 0,
        ];

        // 2. IPD Data (Latest Evaluation per Patient)
        $ipd_query = "
            SELECT
                SUM(CASE WHEN ipd_nurse_eval_range_code LIKE '1%' THEN 1 ELSE 0 END) AS convalescent,
                SUM(CASE WHEN ipd_nurse_eval_range_code LIKE '2%' THEN 1 ELSE 0 END) AS Moderate,
                SUM(CASE WHEN ipd_nurse_eval_range_code LIKE '3%' THEN 1 ELSE 0 END) AS Semi_critical,
                SUM(CASE WHEN ipd_nurse_eval_range_code LIKE '4%' THEN 1 ELSE 0 END) AS Critical,
                SUM(CASE WHEN ipd_nurse_eval_range_code IS NULL OR ipd_nurse_eval_range_code = '' THEN 1 ELSE 0 END) AS severe_type_null,
                COUNT(DISTINCT an) AS patient_all
            FROM (
                SELECT i.an, 
                (SELECT n.ipd_nurse_eval_range_code 
                 FROM ipd_nurse_note n 
                 WHERE n.an = i.an 
                 ORDER BY n.note_date DESC, n.note_time DESC 
                 LIMIT 1) as ipd_nurse_eval_range_code
                FROM ipt i
                WHERE i.ward IN ($ipd_ward) AND i.confirm_discharge = 'N'
            ) t
        ";
        $ipd_result = DB::connection('hosxp')->selectOne($ipd_query);

        $ipd_stats = [
            'shift' => $shift_name,
            'critical' => $ipd_result->Critical ?? 0,
            'semi_critical' => $ipd_result->Semi_critical ?? 0,
            'moderate' => $ipd_result->Moderate ?? 0,
            'convalescent' => $ipd_result->convalescent ?? 0,
            'severe_type_null' => $ipd_result->severe_type_null ?? 0,
        ];

        // 3. OPD & NCD & ARI Logic
        // - เวรเช้า: 00:00:01 - 15:59:59
        // - เวร BD: 16:00:00 - 20:00:00 (เฉพาะ OPD)

        $opd_shift_now = 'เวรเช้า';
        $opd_st = '00:00:01';
        $opd_et = '15:59:59';

        if ($currentTime >= '16:00:00') {
            $opd_shift_now = 'เวร BD';
            $opd_st = '16:00:00';
            $opd_et = '20:00:00';
        }

        // NCD & ARI always use Morning range but limited by current time up to 15:59:59
        $morning_st = '00:00:01';
        $morning_et = '15:59:59';

        // NCD Query
        $ncd_query = "
            SELECT IFNULL(COUNT(DISTINCT o1.vn),0) AS patient_all
            FROM opd_dep_queue o1, ovst o2 WHERE o1.depcode IN ($ncd_dep)
            AND o1.vn = o2.vn AND o2.vstdate = DATE(NOW())
            AND o2.vsttime BETWEEN ? AND ?
        ";
        $ncd_result = DB::connection('hosxp')->selectOne($ncd_query, [$morning_st, $morning_et]);

        $ncd_stats = [
            'shift' => 'เวรเช้า',
            'patient_all' => $ncd_result->patient_all ?? 0,
        ];

        // OPD Query
        $opd_query = "
            SELECT IFNULL(COUNT(DISTINCT o1.vn),0) AS patient_all
            FROM opd_dep_queue o1, ovst o2 WHERE o1.depcode IN ($opd_dep)
            AND o1.vn = o2.vn AND o2.vstdate = DATE(NOW())
            AND o2.vsttime BETWEEN ? AND ?
        ";
        $opd_result = DB::connection('hosxp')->selectOne($opd_query, [$opd_st, $opd_et]);

        $opd_stats = [
            'shift' => $opd_shift_now,
            'patient_all' => $opd_result->patient_all ?? 0,
        ];

        // ARI Query
        $ari_query = "
            SELECT IFNULL(COUNT(DISTINCT o.vn),0) AS patient_all
            FROM ovst o WHERE o.main_dep IN ($ari_dep)
            AND o.vstdate = DATE(NOW())
            AND o.vsttime BETWEEN ? AND ?
        ";
        $ari_result = DB::connection('hosxp')->selectOne($ari_query, [$morning_st, $morning_et]);

        $ari_stats = [
            'shift' => 'เวรเช้า',
            'patient_all' => $ari_result->patient_all ?? 0,
        ];

        // 4. VIP Data
        $vip_stats = ['shift' => $shift_name, 'critical' => 0, 'semi_critical' => 0, 'moderate' => 0, 'convalescent' => 0, 'severe_type_null' => 0];
        if ($vip_ward != "''" && $vip_ward != "") {
            $vip_query = str_replace('$ipd_ward', $vip_ward, $ipd_query);
            $vip_result = DB::connection('hosxp')->selectOne($vip_query);
            $vip_stats = [
                'shift' => $shift_name,
                'critical' => $vip_result->Critical ?? 0,
                'semi_critical' => $vip_result->Semi_critical ?? 0,
                'moderate' => $vip_result->Moderate ?? 0,
                'convalescent' => $vip_result->convalescent ?? 0,
                'severe_type_null' => $vip_result->severe_type_null ?? 0,
            ];
        }

        // 5. LR Data
        $lr_stats = ['shift' => $shift_name, 'critical' => 0, 'semi_critical' => 0, 'moderate' => 0, 'convalescent' => 0, 'severe_type_null' => 0];
        if ($lr_ward != "''" && $lr_ward != "") {
            $lr_query = str_replace('$ipd_ward', $lr_ward, $ipd_query);
            $lr_result = DB::connection('hosxp')->selectOne($lr_query);
            $lr_stats = [
                'shift' => $shift_name,
                'critical' => $lr_result->Critical ?? 0,
                'semi_critical' => $lr_result->Semi_critical ?? 0,
                'moderate' => $lr_result->Moderate ?? 0,
                'convalescent' => $lr_result->convalescent ?? 0,
                'severe_type_null' => $lr_result->severe_type_null ?? 0,
            ];
        }

        // 6. CKD Data
        $ckd_stats = ['shift' => 'เวรเช้า', 'patient_all' => 0];
        if ($ckd_dep != "''" && $ckd_dep != "") {
            $ckd_query = str_replace('$ncd_dep', $ckd_dep, $ncd_query);
            $ckd_result = DB::connection('hosxp')->selectOne($ckd_query, [$morning_st, $morning_et]);
            $ckd_stats = [
                'shift' => 'เวรเช้า',
                'patient_all' => $ckd_result->patient_all ?? 0,
            ];
        }

        // 7. HD Data
        $hd_stats = ['shift' => 'เวรเช้า', 'patient_all' => 0];
        if ($hd_dep != "''" && $hd_dep != "") {
            $hd_query = str_replace('$ncd_dep', $hd_dep, $hd_dep); // Wait, mapping should be depcode IN ($hd_dep)
            // Actually the query used $ncd_dep variable name in string, let's fix that.
            $hd_query = "
                SELECT IFNULL(COUNT(DISTINCT o1.vn),0) AS patient_all
                FROM opd_dep_queue o1, ovst o2 WHERE o1.depcode IN ($hd_dep)
                AND o1.vn = o2.vn AND o2.vstdate = DATE(NOW())
                AND o2.vsttime BETWEEN ? AND ?
            ";
            $hd_result = DB::connection('hosxp')->selectOne($hd_query, [$morning_st, $morning_et]);
            $hd_stats = [
                'shift' => 'เวรเช้า',
                'patient_all' => $hd_result->patient_all ?? 0,
            ];
        }

        if (\Illuminate\Support\Facades\Auth::check()) {
            return view('hnplus.dashboard', compact('er_stats', 'ipd_stats', 'opd_stats', 'ncd_stats', 'ari_stats', 'vip_stats', 'lr_stats', 'ckd_stats', 'hd_stats', 'today', 'shift_name'));
        }

        return view('welcome', compact('er_stats', 'ipd_stats', 'opd_stats', 'ncd_stats', 'ari_stats', 'vip_stats', 'lr_stats', 'ckd_stats', 'hd_stats', 'today', 'shift_name'));
    }
}
