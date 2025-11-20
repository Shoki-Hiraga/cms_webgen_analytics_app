<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganicKeyword extends Model
{
    protected $fillable = ['keyword'];
    protected $table = 'organic_keywords';
}
