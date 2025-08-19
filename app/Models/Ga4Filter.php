<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ga4Filter extends Model
{
    use HasFactory;

    protected $table = 'ga4_filter';

    protected $fillable = [
        'ga4_setting_id',
        'session_medium_filter',
    ];

    public function setting()
    {
        return $this->belongsTo(Ga4Setting::class, 'ga4_setting_id');
    }
}
