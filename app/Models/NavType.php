<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class NavType extends Model
{
    protected $fillable = [
        'name'
    ];

    public function menus(): Relation
    {
        return $this->hasMany(NavSubItem::class, 'nav_type_id', 'id');
    }
}
