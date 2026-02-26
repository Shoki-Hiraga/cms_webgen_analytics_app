<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AreaAdsKeyword extends Model
{
    protected $fillable = [
        'keyword',
        'product',
        'priority',
    ];

    protected $table = 'area_ads_keywords';
}
