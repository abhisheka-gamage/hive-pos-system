<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockHistory extends Model
{
    protected $fillable = [
        'product_id',
        'batch_id',
        'balance_after',
        'stock_movement_type_id',
        'stock_movement_referance_id'
    ];
}
