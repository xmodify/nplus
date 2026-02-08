<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Productivity_er extends Model
{
    use HasFactory;

    protected $table = 'productivity_er';

    protected $fillable = [
        'report_date',
        'shift_time',
        'nurse_fulltime',
        'nurse_partime',
        'nurse_oncall',
        'recorder',
        'note',
        'patient_all',
        'patient_resuscitation',
        'patient_emergent',
        'patient_urgent',
        'patient_semi_urgent',
        'patient_non_urgent',
        'nursing_hours',
        'working_hours',
        'nhppd',
        'nurse_shift_time',
        'productivity',
    ];
}
