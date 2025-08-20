<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ga4Setting extends Model
{
    use HasFactory;

    protected $table = 'ga4_setting';

    protected $fillable = [
        'id',
        'session_medium_filter',
        'service_account_json',
        'property_id',
    ];

    public $incrementing = false; // ← オートインクリメントしない設定
    protected $keyType = 'int'; // 主キーは整数型
}
