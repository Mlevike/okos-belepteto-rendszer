<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

//A beállításokat tároló adatbázis modell

class Settings extends Model
{
    use HasFactory;

    protected $fillable = [
        'setting_name',
        'setting_value',
    ];
}
