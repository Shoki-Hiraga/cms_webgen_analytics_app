<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GscDirectoryListurl extends Model
{
    protected $table = 'gsc_directory_listurls';

    protected $fillable = [
        'url',
        'is_active',
    ];
}
