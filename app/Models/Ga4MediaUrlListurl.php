<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ga4MediaUrlListurl extends Model
{
    protected $table = 'ga4_media_url_listurls';

    protected $fillable = [
        'url',
        'is_active',
    ];
}
