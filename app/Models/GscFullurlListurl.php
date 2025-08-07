<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GscFullurlListurl extends Model
{
    protected $table = 'gsc_fullurl_listurls';

    protected $fillable = [
        'url',
        'is_active',
    ];
}
