<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceProducts extends Model
{
    protected $fillable = [
        'invoice_id',
        'product_id',
        'batch_id',
        'pro_quantity',
        'pro_price',
        'pro_discount',
        'pro_net_amount',
    ];
}
