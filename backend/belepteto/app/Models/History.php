<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    /**
     * A modellhez társított tábla neve..
     *
     * @var string
     */
    protected $table = 'history';
    /**
     * A táblához társított elsődleges kulcs
     *
     * @var string
     */
    protected $primaryKey = 'history_id';
    //A modell további mezői
    protected $fillable = [
        'arriveTime',
        'successful',
        'leaveTime',
        'workTime',
        'direction',
        'user_id'
    ];
}
