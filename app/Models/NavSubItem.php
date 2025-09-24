<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Spatie\Permission\Models\Permission;

class NavSubItem extends Model
{
    protected $fillable = [
        'nav_item_id',
        'permission_id',
        'nav_type_id',
        'display_name',
        'url'
    ];


    public function permissions(): Relation
    {
        return $this->belongsTo(Permission::class, 'permission_id', 'id');
    }

    public function parent(): Relation
    {
        return $this->belongsTo(NavItem::class, 'nav_item_id', 'id');
    }

    public function types(): Relation
    {
        return $this->hasOne(NavType::class, 'id', 'nav_type_id');
    }
}
