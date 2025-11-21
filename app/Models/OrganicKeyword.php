<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganicKeyword extends Model
{
    protected $fillable = [
        'keyword',
        'product',
        'priority',
    ];

    protected $table = 'organic_keywords';
}
