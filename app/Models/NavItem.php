<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class NavItem extends Model
{
    protected $fillable = [
        'nav_header_id',
        'code',
        'display_name'
    ];

    public function parent(): Relation
    {
        return $this->hasOne(NavHeader::class, 'id', 'nav_header_id');
    }

    public function children(): Relation
    {
        return $this->hasMany(NavSubItem::class, 'nav_item_id', 'id');
    }
}
