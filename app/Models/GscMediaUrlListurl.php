<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GscMediaUrlListurl extends Model
{
    protected $table = 'gsc_media_url_listurls';

    protected $fillable = [
        'url',
        'is_active',
    ];
}
