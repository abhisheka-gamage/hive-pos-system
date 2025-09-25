<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StockSampleExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Product::with('batch')->get()->map(function ($product) {
            return [
                'Product' => preg_replace('/[\x00-\x1F\x7F]/u', '', (string) ($product->code ?? '')),
                'Batch'   => preg_replace('/[\x00-\x1F\x7F]/u', '', (string) ($product->batch?->batch_code ?? '')),
                'Amount'  => '', // keep blank
            ];
        });

        return collect($data->toArray());
    }

    public function headings(): array
    {
        return [
            'Product',
            'Batch',
            'Amount'
        ];
    }
}
