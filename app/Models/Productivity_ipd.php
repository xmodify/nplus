<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Productivity_ipd extends Model
{
    use HasFactory;

    protected $table = 'productivity_ipd';

    protected $fillable = [
        'report_date',
        'shift_time',
        'nurse_fulltime',
        'nurse_partime',
        'nurse_oncall',
        'recorder',
        'note',
        'patient_all',
        'patient_critical',
        'patient_semi_critical',
        'patient_moderate',
        'patient_convalescent',
        'patient_severe_type_null',
        'nursing_hours',
        'working_hours',
        'nhppd',
        'nurse_shift_time',
        'productivity',
    ];
}
