<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ga4FullurlListurl extends Model
{
    protected $table = 'ga4_fullurl_listurls';

    protected $fillable = [
        'url',
        'is_active',
    ];
}
