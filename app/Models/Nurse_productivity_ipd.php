<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nurse_productivity_ipd extends Model
{
    use HasFactory;

    // ✅ กำหนดชื่อตาราง (ถ้าไม่ได้ใช้ชื่อตาม convention)
    protected $table = 'nurse_productivity_ipds';

    // ✅ อนุญาตให้กรอกค่าทีละหลายฟิลด์ได้ (mass assignment)
    protected $fillable = [
        'report_date',
        'shift_time',
        'patient_all',
        'convalescent',
        'moderate_ill',
        'semi_critical_ill',
        'critical_ill',
        'patient_hr',
        'nurse_oncall',
        'nurse_partime',
        'nurse_fulltime',
        'nurse_hr',
        'productivity',
        'hhpuos',
        'nurse_shift_time',
        'recorder',
        'note',
    ];

    // ✅ ถ้าตารางนี้ไม่มี timestamps (created_at / updated_at)
    public $timestamps = true; // ถ้ามีคอลัมน์พวกนี้ในตารางให้เปิดไว้
}
