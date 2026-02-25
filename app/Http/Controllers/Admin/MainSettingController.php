<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MainSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;

class MainSettingController extends Controller
{
    public function index()
    {
        $settings = MainSetting::all()->sortBy(function ($setting) {
            $name = $setting->name;
            $weight = 50;

            // Custom logical ordering for department settings
            if (str_ends_with($name, '_active'))
                $weight = 10;
            elseif (str_ends_with($name, '_department') || str_ends_with($name, '_ward'))
                $weight = 20;
            elseif (str_ends_with($name, '_working_hours'))
                $weight = 30;
            elseif (str_contains($name, '_patient_type'))
                $weight = 40;
            // Push Telegram down to the bottom
            elseif (str_ends_with($name, '_notifytelegram'))
                $weight = 90;
            elseif (str_ends_with($name, '_notifytelegram_save'))
                $weight = 91;

            return sprintf('%02d_%s', $weight, $name);
        });

        $general_settings = $settings->filter(function ($item) {
            return !str_starts_with($item->name, 'er_') &&
            !str_starts_with($item->name, 'ipd_') &&
            !str_starts_with($item->name, 'opd_') &&
            !str_starts_with($item->name, 'ncd_') &&
            !str_starts_with($item->name, 'ari_') &&
            !str_starts_with($item->name, 'ckd_') &&
            !str_starts_with($item->name, 'hd_') &&
            !str_starts_with($item->name, 'vip_') &&
            !str_starts_with($item->name, 'lr_');
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

        $ckd_settings = $settings->filter(function ($item) {
            return str_starts_with($item->name, 'ckd_');
        });

        $hd_settings = $settings->filter(function ($item) {
            return str_starts_with($item->name, 'hd_');
        });

        $vip_settings = $settings->filter(function ($item) {
            return str_starts_with($item->name, 'vip_');
        });

        $lr_settings = $settings->filter(function ($item) {
            return str_starts_with($item->name, 'lr_');
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
            'hosxp_wards',
            'ckd_settings',
            'hd_settings',
            'vip_settings',
            'lr_settings'
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
        // Run Migrations
        Artisan::call('migrate');

        //Update Table main_setting-----------------------------------------------------------------------------------------------------------
        if (Schema::hasColumn('main_setting', 'id')) {
            DB::statement('ALTER TABLE main_setting MODIFY id INT NOT NULL;');
            DB::statement('ALTER TABLE main_setting DROP PRIMARY KEY;');
            DB::statement('ALTER TABLE main_setting DROP COLUMN id;');
            DB::statement('ALTER TABLE main_setting MODIFY name VARCHAR(100) NOT NULL;');
            DB::statement('ALTER TABLE main_setting ADD PRIMARY KEY (name);');
        }
        $main_setting = [
            ['name_th' => 'Telegram Bot Token', 'name' => 'telegram_token', 'value' => ''],
            ['name_th' => 'ER ชม.การทำงานพยาบาล', 'name' => 'er_working_hours', 'value' => '7'],
            ['name_th' => 'ER ชม.ผู้ป่วยวิกฤต(Resuscitation)', 'name' => 'er_patient_type1', 'value' => '3.2'],
            ['name_th' => 'ER ชม.ผู้ป่วยฉุกเฉินสูง(Emergent)', 'name' => 'er_patient_type2', 'value' => '2.5'],
            ['name_th' => 'ER ชม.ผู้ป่วยฉุกเฉิน(Urgent) ', 'name' => 'er_patient_type3', 'value' => '1'],
            ['name_th' => 'ER ชม.ผู้ป่วยฉุกเฉินน้อย(Semi Urgent)', 'name' => 'er_patient_type4', 'value' => '0.5'],
            ['name_th' => 'ER ชม.ผู้ป่วยไม่ฉุกเฉิน(Non Urgent)', 'name' => 'er_patient_type5', 'value' => '0.24'],
            ['name_th' => 'ER NotifyTelegram แจ้งเตือน', 'name' => 'er_notifytelegram', 'value' => ''],
            ['name_th' => 'ER NotifyTelegram บันทึก', 'name' => 'er_notifytelegram_save', 'value' => ''],
            ['name_th' => 'ER สถานะ', 'name' => 'er_active', 'value' => 'Y'],
            ['name_th' => 'IPD รหัส Ward (HOSxP)', 'name' => 'ipd_ward', 'value' => '01'],
            ['name_th' => 'IPD ชม.การทำงานพยาบาล', 'name' => 'ipd_working_hours', 'value' => '7'],
            ['name_th' => 'IPD ชม.ผู้ป่วยพักฟื้น(Convalescent)', 'name' => 'ipd_patient_type1', 'value' => '0.5'],
            ['name_th' => 'IPD ชม.ผู้ป่วยปานกลาง(Moderate)', 'name' => 'ipd_patient_type2', 'value' => '1.16'],
            ['name_th' => 'IPD ชม.ผู้ป่วยกึ่งหนัก(Semi-critical)', 'name' => 'ipd_patient_type3', 'value' => '1.83'],
            ['name_th' => 'IPD ชม.ผู้ป่วยหนัก(Critical)', 'name' => 'ipd_patient_type4', 'value' => '2.5'],
            ['name_th' => 'IPD NotifyTelegram แจ้งเตือน', 'name' => 'ipd_notifytelegram', 'value' => ''],
            ['name_th' => 'IPD NotifyTelegram บันทึก', 'name' => 'ipd_notifytelegram_save', 'value' => ''],
            ['name_th' => 'IPD สถานะ', 'name' => 'ipd_active', 'value' => 'Y'],
            ['name_th' => 'OPD รหัสห้องตรวจ (HOSxP)', 'name' => 'opd_department', 'value' => ''],
            ['name_th' => 'OPD ชม.การทำงานพยาบาล', 'name' => 'opd_working_hours', 'value' => '7'],
            ['name_th' => 'OPD ชม.ผู้ป่วยทั่วไป', 'name' => 'opd_patient_type', 'value' => '0.24'],
            ['name_th' => 'OPD NotifyTelegram แจ้งเตือน', 'name' => 'opd_notifytelegram', 'value' => ''],
            ['name_th' => 'OPD NotifyTelegram บันทึก', 'name' => 'opd_notifytelegram_save', 'value' => ''],
            ['name_th' => 'OPD สถานะ', 'name' => 'opd_active', 'value' => 'Y'],
            ['name_th' => 'NCD รหัสห้องตรวจ (HOSxP)', 'name' => 'ncd_department', 'value' => ''],
            ['name_th' => 'NCD ชม.การทำงานพยาบาล', 'name' => 'ncd_working_hours', 'value' => '8'],
            ['name_th' => 'NCD ชม.ผู้ป่วยทั่วไป', 'name' => 'ncd_patient_type', 'value' => '0.24'],
            ['name_th' => 'NCD NotifyTelegram แจ้งเตือน', 'name' => 'ncd_notifytelegram', 'value' => ''],
            ['name_th' => 'NCD NotifyTelegram บันทึก', 'name' => 'ncd_notifytelegram_save', 'value' => ''],
            ['name_th' => 'NCD สถานะ', 'name' => 'ncd_active', 'value' => 'Y'],
            ['name_th' => 'ARI รหัสห้องตรวจ (HOSxP)', 'name' => 'ari_department', 'value' => ''],
            ['name_th' => 'ARI ชม.การทำงานพยาบาล', 'name' => 'ari_working_hours', 'value' => '7'],
            ['name_th' => 'ARI ชม.ผู้ป่วยทั่วไป', 'name' => 'ari_patient_type', 'value' => '0.24'],
            ['name_th' => 'ARI NotifyTelegram แจ้งเตือน', 'name' => 'ari_notifytelegram', 'value' => ''],
            ['name_th' => 'ARI NotifyTelegram บันทึก', 'name' => 'ari_notifytelegram_save', 'value' => ''],
            ['name_th' => 'ARI สถานะ', 'name' => 'ari_active', 'value' => 'Y'],
            ['name_th' => 'CKD รหัสห้องตรวจ (HOSxP)', 'name' => 'ckd_department', 'value' => ''],
            ['name_th' => 'CKD ชม.การทำงานพยาบาล', 'name' => 'ckd_working_hours', 'value' => '7'],
            ['name_th' => 'CKD ชม.ผู้ป่วยทั่วไป', 'name' => 'ckd_patient_type', 'value' => '0.24'],
            ['name_th' => 'CKD NotifyTelegram แจ้งเตือน', 'name' => 'ckd_notifytelegram', 'value' => ''],
            ['name_th' => 'CKD NotifyTelegram บันทึก', 'name' => 'ckd_notifytelegram_save', 'value' => ''],
            ['name_th' => 'CKD สถานะ', 'name' => 'ckd_active', 'value' => 'Y'],
            ['name_th' => 'HD รหัสห้องตรวจ (HOSxP)', 'name' => 'hd_department', 'value' => ''],
            ['name_th' => 'HD ชม.ทำงานพยาบาล', 'name' => 'hd_working_hours', 'value' => '7'],
            ['name_th' => 'HD ชม.ผู้ป่วยทั่วไป', 'name' => 'hd_patient_type', 'value' => '0.24'],
            ['name_th' => 'HD NotifyTelegram แจ้งเตือน', 'name' => 'hd_notifytelegram', 'value' => ''],
            ['name_th' => 'HD NotifyTelegram บันทึก', 'name' => 'hd_notifytelegram_save', 'value' => ''],
            ['name_th' => 'HD สถานะ', 'name' => 'hd_active', 'value' => 'Y'],
            ['name_th' => 'VIP รหัส Ward (HOSxP)', 'name' => 'vip_ward', 'value' => ''],
            ['name_th' => 'VIP ชม.ทำงานพยาบาล', 'name' => 'vip_working_hours', 'value' => '7'],
            ['name_th' => 'VIP ชม.ผู้ป่วย Type 1', 'name' => 'vip_patient_type1', 'value' => '0.5'],
            ['name_th' => 'VIP ชม.ผู้ป่วย Type 2', 'name' => 'vip_patient_type2', 'value' => '1.16'],
            ['name_th' => 'VIP ชม.ผู้ป่วย Type 3', 'name' => 'vip_patient_type3', 'value' => '1.83'],
            ['name_th' => 'VIP ชม.ผู้ป่วย Type 4', 'name' => 'vip_patient_type4', 'value' => '2.5'],
            ['name_th' => 'VIP NotifyTelegram แจ้งเตือน', 'name' => 'vip_notifytelegram', 'value' => ''],
            ['name_th' => 'VIP NotifyTelegram บันทึก', 'name' => 'vip_notifytelegram_save', 'value' => ''],
            ['name_th' => 'VIP สถานะ', 'name' => 'vip_active', 'value' => 'Y'],
            ['name_th' => 'LR รหัส Ward (HOSxP)', 'name' => 'lr_ward', 'value' => ''],
            ['name_th' => 'LR ชม.ทำงานพยาบาล', 'name' => 'lr_working_hours', 'value' => '7'],
            ['name_th' => 'LR ชม.ผู้ป่วย Type 1', 'name' => 'lr_patient_type1', 'value' => '0.5'],
            ['name_th' => 'LR ชม.ผู้ป่วย Type 2', 'name' => 'lr_patient_type2', 'value' => '1.16'],
            ['name_th' => 'LR ชม.ผู้ป่วย Type 3', 'name' => 'lr_patient_type3', 'value' => '1.83'],
            ['name_th' => 'LR ชม.ผู้ป่วย Type 4', 'name' => 'lr_patient_type4', 'value' => '2.5'],
            ['name_th' => 'LR NotifyTelegram แจ้งเตือน', 'name' => 'lr_notifytelegram', 'value' => ''],
            ['name_th' => 'LR NotifyTelegram บันทึก', 'name' => 'lr_notifytelegram_save', 'value' => ''],
            ['name_th' => 'LR สถานะ', 'name' => 'lr_active', 'value' => 'Y'],
        ];
        foreach ($main_setting as $row) {
            $check = MainSetting::where('name', $row['name'])->count();
            if ($check > 0) {
                DB::table('main_setting')
                    ->where('name', $row['name'])
                    ->update([
                    'name_th' => $row['name_th'],
                ]);
            }
            else {
                DB::table('main_setting')
                    ->insert([
                    'name_th' => $row['name_th'],
                    'name' => $row['name'],
                    'value' => $row['value'],
                ]);
            }
        }

        //After Table-----------------------------------------------------------------------------------------------------------
        $productivity_tables = [
            'productivity_ari', 'productivity_ckd', 'productivity_er',
            'productivity_hd', 'productivity_ipd', 'productivity_lr',
            'productivity_ncd', 'productivity_opd', 'productivity_vip'
        ];

        foreach ($productivity_tables as $ptable) {
            if (Schema::hasTable($ptable)) {
                if (!Schema::hasColumn($ptable, 'active')) {
                    Schema::table($ptable, function (Blueprint $table) {
                        $table->string('active', 1)->default('Y')->after('id');
                    });
                }
            }
        }

        try {
            // Create Productivity CKD Table if not exists
            if (!Schema::hasTable('productivity_ckd')) {
                Schema::create('productivity_ckd', function (Blueprint $table) {
                    $table->id();
                    $table->date('report_date');
                    $table->string('shift_time', 20);
                    $table->integer('nurse_fulltime')->nullable();
                    $table->integer('nurse_partime')->nullable();
                    $table->integer('nurse_oncall')->nullable();
                    $table->string('recorder')->nullable();
                    $table->string('note')->nullable();
                    $table->integer('patient_all')->nullable();
                    $table->double('nursing_hours', 5, 2)->nullable();
                    $table->double('working_hours', 5, 2)->nullable();
                    $table->double('nhppd', 5, 2)->nullable();
                    $table->double('nurse_shift_time', 5, 2)->nullable();
                    $table->double('productivity', 5, 2)->nullable();
                    $table->timestamps();
                });
            }

            // Create Productivity HD Table if not exists
            if (!Schema::hasTable('productivity_hd')) {
                Schema::create('productivity_hd', function (Blueprint $table) {
                    $table->id();
                    $table->date('report_date');
                    $table->string('shift_time', 20);
                    $table->integer('nurse_fulltime')->nullable();
                    $table->integer('nurse_partime')->nullable();
                    $table->integer('nurse_oncall')->nullable();
                    $table->string('recorder')->nullable();
                    $table->string('note')->nullable();
                    $table->integer('patient_all')->nullable();
                    $table->double('nursing_hours', 5, 2)->nullable();
                    $table->double('working_hours', 5, 2)->nullable();
                    $table->double('nhppd', 5, 2)->nullable();
                    $table->double('nurse_shift_time', 5, 2)->nullable();
                    $table->double('productivity', 5, 2)->nullable();
                    $table->timestamps();
                });
            }

            // Create Productivity VIP Table if not exists
            if (!Schema::hasTable('productivity_vip')) {
                Schema::create('productivity_vip', function (Blueprint $table) {
                    $table->id();
                    $table->date('report_date');
                    $table->string('shift_time', 20);
                    $table->integer('nurse_fulltime')->nullable();
                    $table->integer('nurse_partime')->nullable();
                    $table->integer('nurse_oncall')->nullable();
                    $table->string('recorder')->nullable();
                    $table->string('note')->nullable();
                    $table->integer('patient_all')->nullable();
                    $table->integer('patient_critical')->nullable();
                    $table->integer('patient_semi_critical')->nullable();
                    $table->integer('patient_moderate')->nullable();
                    $table->integer('patient_convalescent')->nullable();
                    $table->double('nursing_hours', 5, 2)->nullable();
                    $table->double('working_hours', 5, 2)->nullable();
                    $table->double('nhppd', 5, 2)->nullable();
                    $table->double('nurse_shift_time', 5, 2)->nullable();
                    $table->double('productivity', 5, 2)->nullable();
                    $table->timestamps();
                });
            }

            // Create Productivity LR Table if not exists
            if (!Schema::hasTable('productivity_lr')) {
                Schema::create('productivity_lr', function (Blueprint $table) {
                    $table->id();
                    $table->date('report_date');
                    $table->string('shift_time', 20);
                    $table->integer('nurse_fulltime')->nullable();
                    $table->integer('nurse_partime')->nullable();
                    $table->integer('nurse_oncall')->nullable();
                    $table->string('recorder')->nullable();
                    $table->string('note')->nullable();
                    $table->integer('patient_all')->nullable();
                    $table->integer('patient_critical')->nullable();
                    $table->integer('patient_semi_critical')->nullable();
                    $table->integer('patient_moderate')->nullable();
                    $table->integer('patient_convalescent')->nullable();
                    $table->double('nursing_hours', 5, 2)->nullable();
                    $table->double('working_hours', 5, 2)->nullable();
                    $table->double('nhppd', 5, 2)->nullable();
                    $table->double('nurse_shift_time', 5, 2)->nullable();
                    $table->double('productivity', 5, 2)->nullable();
                    $table->timestamps();
                });
            }

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
                        }
                        else {
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

        }
        catch (\Exception $e) {
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

}
