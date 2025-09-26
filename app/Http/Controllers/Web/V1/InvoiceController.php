<?php

namespace App\Http\Controllers\Web\V1;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function getProducts(Request $request): JsonResponse
    {
        try {

            $data = Batch::with(['product', 'stock'])
            ->whereHas('stock',function($q){
                return $q->where('amount', '>', 0);
            })
            ->whereHas('product')
            ->where('status', 1)
            ->get();

            return response()->json([
                'result' => true,
                'data' => $data,
                'message' => null
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'result' => false,
                'data' => null,
                'message' => $th->getMessage()
            ],500);
        }
    }
}
