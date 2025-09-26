<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class Batch extends Model
{
    protected $fillable = [
        'product_id',
        'batch_code',
        'retail_price',
        'selling_price',
        'effective_from',
        'effective_to',
        'status',
    ];

    public function product(): Relation
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

    public function stock(): Relation
    {
        return $this->hasOne(Stock::class, 'batch_id', 'id');
    }
}
