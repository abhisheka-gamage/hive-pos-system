<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_no',
        'inv_amount',
        'inv_discount',
        'inv_net_amount',
        'invoice_timestamp',
        'delivered',
        'status'
    ];
}
