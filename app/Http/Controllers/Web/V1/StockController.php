<?php

namespace App\Http\Controllers\Web\V1;

use App\Exports\StockSampleExport;
use App\Http\Controllers\Controller;
use App\Imports\StockImport;
use App\Models\Stock;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class StockController extends Controller
{
    public function sample(Request $request): BinaryFileResponse
    {
        return Excel::download(new StockSampleExport, 'stocks_sample.xlsx');
    }

    public function upload(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {

            Excel::import(new StockImport, $request->file('file'));

            DB::commit();
            return response()->json([
                'result' => true,
                'data' => null,
                'message'=> null,
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'result' => false,
                'message' => $th->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $data = Stock::with(['product.retailer', 'batch'])
            ->when(!is_null($request->search_filter['search']), fn($q) =>

                $q->whereHas('product', fn($p) =>
                    $p->where('code' , 'like', "%{$request->search_filter['search']}%")
                )
                ->orWhereHas('batch', fn($p) =>
                    $p->where('batch_code' , 'like', "%{$request->search_filter['search']}%")
                )
            )
            ->when(!is_null($request->search_filter['product']), fn($q) =>
                $q->whereHas('product', fn($p) =>
                    $p->where('id', $request->search_filter['product'])
                )
            )
            ->when(!is_null($request->search_filter['retailer']), fn($q) =>
                $q->whereHas('product.retailer', fn($r) =>
                    $r->where('id', $request->search_filter['retailer'])
                )
            )
            ->when(!is_null($request->search_filter['batch']), fn($q) =>
                $q->whereHas('batch', fn($r) =>
                    $r->where('id', $request->search_filter['batch'])
                )            )
            ->paginate(request('entries', 10));

            return response()->json([
                'result' => true,
                'data' => $data,
                'message'=> null,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'result' => false,
                'message' => $th->getMessage(),
                'data' => null
            ], 500);
        }
    }
}
