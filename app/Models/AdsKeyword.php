<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdsKeyword extends Model
{
    protected $fillable = ['keyword'];
    protected $table = 'ads_keywords';
}
