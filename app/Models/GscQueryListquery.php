<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GscQueryListquery extends Model
{
    protected $table = 'gsc_query_listqueries';

    protected $fillable = [
        'query',
        'is_active',
    ];
}
