<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class Product extends Model
{
    protected $fillable = [
        'name',
        'code',
        'barcode',
        'status',
        'retailer_id'
    ];

    public function retailer(): Relation
    {
        return $this->hasOne(ProductRetailer::class, 'id', 'retailer_id');
    }
}
