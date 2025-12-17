<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SetSlug extends Model
{
    protected $table = 'set_slug';
    protected $fillable = ['slug', 'label', 'type', 'handler', 'parent_id', 'active', 'sort_order'];

    public function parent()
    {
        return $this->belongsTo(SetSlug::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(SetSlug::class, 'parent_id');
    }
}
