<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemSideOperations extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'options',
        'operation_state',
        'reference_token',
        'timeout',
        'sent_time'
    ];
}
