<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dataseting extends Model
{
    use HasFactory;

    protected $table = 'dataseting';

    protected $fillable = [
        'target',       // 'GA4' または 'GSC'
        'start_year',
        'start_month',
    ];
}
