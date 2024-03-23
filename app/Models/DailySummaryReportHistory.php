<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailySummaryReportHistory extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'date',
        'branch_wise',
        'all_branch',
        'operation_status',
        'operation_message',
        'created_at',
        'updated_at'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'branch_wise' => 'json',
        'all_branch' => 'json',
    ];
}
