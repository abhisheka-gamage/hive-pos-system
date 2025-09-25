<?php

namespace App\Imports;

use App\Models\Batch;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockHistory;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StockImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $product = Product::where('code', $row['product'])->first();
        $batch = Batch::where('batch_code', $row['batch'])->first();

        if($product && $batch){
            $stock = Stock::where('product_id', $product->id)->where('batch_id', $batch->id)->first();

            if($stock){
                $stock->increment('amount', $row['amount']);
            } else {
                Stock::create([
                    'product_id' => $product->id,
                    'batch_id' => $batch->id,
                    'amount' => $row['amount']
                ]);
            }

            StockHistory::create([
                'product_id' => $product->id,
                'batch_id' => $batch->id,
                'balance_after' => $row['amount'],
                'stock_movement_type_id' => 1,
                'stock_movement_referance_id' => 2
            ]);
        }

        return $stock;
    }
}
