<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class NavHeader extends Model
{
    protected $fillable = [
        'display_name',
        'code',
        'icon'
    ];

    public function children(): Relation
    {
        return $this->hasMany(NavItem::class, 'nav_header_id', 'id');
    }
}
