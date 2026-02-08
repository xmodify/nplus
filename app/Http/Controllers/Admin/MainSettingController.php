<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MainSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class MainSettingController extends Controller
{
    public function index()
    {
        $settings = MainSetting::all();

        $general_settings = $settings->filter(function ($item) {
            return !str_starts_with($item->name, 'er_') &&
                !str_starts_with($item->name, 'ipd_') &&
                !str_starts_with($item->name, 'opd_') &&
                !str_starts_with($item->name, 'ncd_') &&
                !str_starts_with($item->name, 'ari_');
        });

        $er_settings = $settings->filter(function ($item) {
            return str_starts_with($item->name, 'er_');
        });

        $ipd_settings = $settings->filter(function ($item) {
            return str_starts_with($item->name, 'ipd_');
        });

        $opd_settings = $settings->filter(function ($item) {
            return str_starts_with($item->name, 'opd_');
        });

        $ncd_settings = $settings->filter(function ($item) {
            return str_starts_with($item->name, 'ncd_');
        });

        $ari_settings = $settings->filter(function ($item) {
            return str_starts_with($item->name, 'ari_');
        });

        $hosxp_departments = DB::connection('hosxp')
            ->select("
                SELECT k.depcode, k.department, s.name AS spclty, k.depcode_active 
                FROM kskdepartment k
                LEFT JOIN spclty s ON s.spclty = k.spclty
                WHERE k.depcode_active = 'Y'
                ORDER BY k.depcode ASC
            ");

        $hosxp_wards = DB::connection('hosxp')
            ->select("
                SELECT w.ward, w.name, s.name AS spclty, w.ward_active
                FROM ward w
                LEFT JOIN spclty s ON s.spclty = w.spclty
                WHERE w.ward_active = 'Y'
                ORDER BY w.ward ASC
            ");

        return view('admin.main_setting', compact(
            'general_settings',
            'er_settings',
            'ipd_settings',
            'opd_settings',
            'ncd_settings',
            'ari_settings',
            'hosxp_departments',
            'hosxp_wards'
        ));
    }

    public function update(Request $request)
    {
        $data = $request->except(['_token']);
        foreach ($data as $key => $value) {
            MainSetting::where('name', $key)->update(['value' => $value]);
        }
        return redirect()->back()->with('success', 'บันทึกข้อมูลเรียบร้อยแล้ว');
    }

    #######################################################################################################################################    
// UP Structure -----------------------------------------------------------------------------------------------------------------------    
    public function up_structure(Request $request)
    {
        //Update Table main_setting-----------------------------------------------------------------------------------------------------------
        $main_setting = [
            ['id' => 1, 'name_th' => 'Telegram Bot Token', 'name' => 'telegram_token', 'value' => ''],
            ['id' => 2, 'name_th' => 'ER ชม.การทำงานพยาบาล', 'name' => 'er_working_hours', 'value' => '7'],
            ['id' => 3, 'name_th' => 'ER ชม.ผู้ป่วยวิกฤต(Resuscitation)', 'name' => 'er_patient_type1', 'value' => '3.2'],
            ['id' => 4, 'name_th' => 'ER ชม.ผู้ป่วยฉุกเฉินสูง(Emergent)', 'name' => 'er_patient_type2', 'value' => '2.5'],
            ['id' => 5, 'name_th' => 'ER ชม.ผู้ป่วยฉุกเฉิน(Urgent) ', 'name' => 'er_patient_type3', 'value' => '1'],
            ['id' => 6, 'name_th' => 'ER ชม.ผู้ป่วยฉุกเฉินน้อย(Semi Urgent)', 'name' => 'er_patient_type4', 'value' => '0.5'],
            ['id' => 7, 'name_th' => 'ER ชม.ผู้ป่วยไม่ฉุกเฉิน(Non Urgent)', 'name' => 'er_patient_type5', 'value' => '0.24'],
            ['id' => 8, 'name_th' => 'ER NotifyTelegram แจ้งเตือน', 'name' => 'er_notifytelegram', 'value' => ''],
            ['id' => 9, 'name_th' => 'ER NotifyTelegram บันทึก', 'name' => 'er_notifytelegram_save', 'value' => ''],
            ['id' => 10, 'name_th' => 'IPD รหัส Ward (HOSxP)', 'name' => 'ipd_ward', 'value' => '01'],
            ['id' => 11, 'name_th' => 'IPD ชม.การทำงานพยาบาล', 'name' => 'ipd_working_hours', 'value' => '7'],
            ['id' => 12, 'name_th' => 'IPD ชม.ผู้ป่วยพักฟื้น(Convalescent)', 'name' => 'ipd_patient_type1', 'value' => '1.5'],
            ['id' => 13, 'name_th' => 'IPD ชม.ผู้ป่วยปานกลาง(Moderate)', 'name' => 'ipd_patient_type2', 'value' => '3.5'],
            ['id' => 14, 'name_th' => 'IPD ชม.ผู้ป่วยกึ่งหนัก(Semi-critical)', 'name' => 'ipd_patient_type3', 'value' => '5.5'],
            ['id' => 15, 'name_th' => 'IPD ชม.ผู้ป่วยหนัก(Critical)', 'name' => 'ipd_patient_type4', 'value' => '7.5'],
            ['id' => 16, 'name_th' => 'IPD NotifyTelegram แจ้งเตือน', 'name' => 'ipd_notifytelegram', 'value' => ''],
            ['id' => 17, 'name_th' => 'IPD NotifyTelegram บันทึก', 'name' => 'ipd_notifytelegram_save', 'value' => ''],
            ['id' => 18, 'name_th' => 'OPD รหัสห้องตรวจ (HOSxP)', 'name' => 'opd_department', 'value' => ''],
            ['id' => 19, 'name_th' => 'OPD ชม.การทำงานพยาบาล', 'name' => 'opd_working_hours', 'value' => '7'],
            ['id' => 20, 'name_th' => 'OPD ชม.ผู้ป่วยทั่วไป', 'name' => 'opd_patient_type', 'value' => '0.24'],
            ['id' => 21, 'name_th' => 'OPD NotifyTelegram แจ้งเตือน', 'name' => 'opd_notifytelegram', 'value' => ''],
            ['id' => 22, 'name_th' => 'OPD NotifyTelegram บันทึก', 'name' => 'opd_notifytelegram_save', 'value' => ''],
            ['id' => 23, 'name_th' => 'NCD รหัสห้องตรวจ (HOSxP)', 'name' => 'ncd_department', 'value' => ''],
            ['id' => 24, 'name_th' => 'NCD ชม.การทำงานพยาบาล', 'name' => 'ncd_working_hours', 'value' => '8'],
            ['id' => 25, 'name_th' => 'NCD ชม.ผู้ป่วยทั่วไป', 'name' => 'ncd_patient_type', 'value' => '0.24'],
            ['id' => 26, 'name_th' => 'NCD NotifyTelegram แจ้งเตือน', 'name' => 'ncd_notifytelegram', 'value' => ''],
            ['id' => 27, 'name_th' => 'NCD NotifyTelegram บันทึก', 'name' => 'ncd_notifytelegram_save', 'value' => ''],
            ['id' => 28, 'name_th' => 'ARI รหัสห้องตรวจ (HOSxP)', 'name' => 'ari_department', 'value' => ''],
            ['id' => 29, 'name_th' => 'ARI ชม.การทำงานพยาบาล', 'name' => 'ari_working_hours', 'value' => '7'],
            ['id' => 30, 'name_th' => 'ARI ชม.ผู้ป่วยทั่วไป', 'name' => 'ari_patient_type', 'value' => '0.24'],
            ['id' => 31, 'name_th' => 'ARI NotifyTelegram แจ้งเตือน', 'name' => 'ari_notifytelegram', 'value' => ''],
            ['id' => 32, 'name_th' => 'ARI NotifyTelegram บันทึก', 'name' => 'ari_notifytelegram_save', 'value' => ''],
        ];
        foreach ($main_setting as $row) {
            $check = MainSetting::where('id', $row['id'])->count();
            if ($check > 0) {
                DB::table('main_setting')
                    ->where('id', $row['id'])
                    ->update([
                        'name_th' => $row['name_th'],
                        'name' => $row['name'],
                    ]);
            } else {
                DB::table('main_setting')
                    ->insert([
                        'id' => $row['id'],
                        'name_th' => $row['name_th'],
                        'name' => $row['name'],
                        'value' => $row['value'],
                    ]);
            }
        }

        //After Table-----------------------------------------------------------------------------------------------------------
        // $tables = [
        //     // ---------------- lookup ----------------
        //     'lookup_icode' => [
        //         ['name' => 'ems', 'type' => 'VARCHAR(1) NULL', 'after' => 'kidney'],
        //     ],
        //     'lookup_ward' => [
        //         ['name' => 'ward_normal', 'type' => 'VARCHAR(1) NULL', 'after' => 'ward_name'],
        //         ['name' => 'bed_qty', 'type' => 'INT UNSIGNED NULL', 'after' => 'ward_homeward'],
        //     ],            
        // ];

        try {
            if (isset($tables) && is_array($tables)) {
                foreach ($tables as $table => $columns) {
                    if (!Schema::hasTable($table)) {
                        continue;
                    }
                    foreach ($columns as $col) {
                        if (Schema::hasColumn($table, $col['name'])) {
                            DB::statement("
                                ALTER TABLE `$table`
                                MODIFY COLUMN `{$col['name']}` {$col['type']}
                            ");
                        } else {
                            $afterSql = '';
                            if (
                                isset($col['after']) &&
                                $col['after'] !== '' &&
                                Schema::hasColumn($table, $col['after'])
                            ) {
                                $afterSql = " AFTER `{$col['after']}`";
                            }
                            DB::statement("
                                ALTER TABLE `$table`
                                ADD COLUMN `{$col['name']}` {$col['type']}{$afterSql}
                            ");
                        }
                    }
                }
            }

            // CREATE TABLE fdh_claim_status ----------------------------------------------------------------------------------------
            // if (!Schema::hasTable('fdh_claim_status')) {
            //     DB::statement("
            //         CREATE TABLE `fdh_claim_status` (
            //             `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
            //             `hn` VARCHAR(50) NOT NULL,
            //             `seq` VARCHAR(50) DEFAULT NULL,
            //             `an` VARCHAR(50) DEFAULT NULL,
            //             `hcode` VARCHAR(10) NOT NULL,
            //             `status` VARCHAR(50) NOT NULL,
            //             `process_status` VARCHAR(10) DEFAULT NULL,
            //             `status_message_th` VARCHAR(255) DEFAULT NULL,
            //             `stm_period` VARCHAR(50) DEFAULT NULL,
            //             `created_at` TIMESTAMP NULL DEFAULT NULL,
            //             `updated_at` TIMESTAMP NULL DEFAULT NULL,
            //             PRIMARY KEY (`id`),
            //             KEY `idx_hn` (`hn`),
            //             KEY `idx_an` (`an`)
            //         ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            //     ");
            // }

            // END --------------------------------------------------------------------------------------------------------
            return redirect()->route('admin.main_setting')
                ->with('success', 'Upgrade Structure สำเร็จ');

        } catch (\Exception $e) {
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

}
