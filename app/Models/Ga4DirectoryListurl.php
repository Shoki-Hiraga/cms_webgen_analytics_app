<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ga4DirectoryListurl extends Model
{
    protected $table = 'ga4_directory_listurls';

    protected $fillable = [
        'url',
        'is_active'
    ];

    // timestamps を使ってるので特別な設定は不要
}
