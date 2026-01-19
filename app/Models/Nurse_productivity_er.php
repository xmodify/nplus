<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nurse_productivity_er extends Model
{
    use HasFactory;

    protected $table = 'nurse_productivity_ers'; // ✅ ใส่ชื่อ table ถ้าไม่ตรงกับ model name

    // ✅ อนุญาตให้บันทึกผ่าน create() ได้
    protected $fillable = [
        'report_date',
        'shift_time',
        'patient_all',
        'emergent',
        'urgent',
        'acute_illness',
        'non_acute_illness',
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
}
