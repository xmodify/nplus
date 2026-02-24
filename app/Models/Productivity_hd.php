<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Productivity_hd extends Model
{
    use HasFactory;

    protected $table = 'productivity_hd';

    protected $fillable = [
        'report_date',
        'shift_time',
        'nurse_fulltime',
        'nurse_partime',
        'nurse_oncall',
        'recorder',
        'note',
        'patient_all',
        'nursing_hours',
        'working_hours',
        'nhppd',
        'nurse_shift_time',
        'productivity',
    ];
}
