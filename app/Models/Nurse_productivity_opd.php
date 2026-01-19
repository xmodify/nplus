<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nurse_productivity_opd extends Model
{
    use HasFactory;

    protected $table = 'nurse_productivity_opds';

    protected $fillable = [
        'report_date',
        'shift_time',

        // ข้อมูลผู้ป่วย
        'patient_all',
        'opd',
        'ari',
        'patient_hr',

        // อัตรากำลัง
        'nurse_oncall',
        'nurse_partime',
        'nurse_fulltime',
        'nurse_hr',

        // ค่าคำนวณ
        'productivity',
        'hhpuos',
        'nurse_shift_time',

        // บันทึก
        'recorder',
        'note',
    ];
}
