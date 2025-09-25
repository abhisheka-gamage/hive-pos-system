<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class Stock extends Model
{
    protected $fillable = [
        'product_id',
        'batch_id',
        'amount'
    ];

    public function product(): Relation
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

    public function batch(): Relation
    {
        return $this->hasOne(Batch::class, 'id', 'batch_id');
    }
}
