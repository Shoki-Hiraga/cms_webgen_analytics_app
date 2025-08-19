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
        'service_account_json',
        'property_id',
    ];

    public $incrementing = false; // オートインクリメントしない
    protected $keyType = 'int';   // 主キーは整数型

    // Ga4Filter リレーション
    public function filter()
    {
        return $this->hasOne(Ga4Filter::class, 'ga4_setting_id');
    }
}
