<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
