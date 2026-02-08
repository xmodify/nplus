<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetYear extends Model
{
    use HasFactory;

    protected $table = 'budget_year';
    protected $primaryKey = 'LEAVE_YEAR_ID';
    public $timestamps = true;

    protected $fillable = [
        'LEAVE_YEAR_ID',
        'LEAVE_YEAR_NAME',
        'DATE_BEGIN',
        'DATE_END',
        'ACTIVE',
        'DAY_PER_YEAR',
    ];
}
