<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GscSetting extends Model
{
    use HasFactory;

    protected $table = 'gsc_setting';

    protected $fillable = [
        'id',
        'site_url',
        'service_account_json',
    ];

    public $incrementing = false;
    protected $keyType = 'int';
}
