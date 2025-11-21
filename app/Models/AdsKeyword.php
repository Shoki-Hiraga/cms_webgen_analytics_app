<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdsKeyword extends Model
{
    protected $fillable = [
        'keyword',
        'product',
        'priority',
    ];

    protected $table = 'ads_keywords';
}
