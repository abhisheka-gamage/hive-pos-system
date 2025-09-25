<?php

namespace App\Http\Controllers\Web\V1;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Product;
use App\Models\ProductRetailer;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BatchController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $data = Batch::with('product.retailer')
            ->when(!is_null($request->search_filter['search']), fn($q) =>
                $q->where('batch_code' , 'like', "%{$request->search_filter['search']}%")
                ->orWhereHas('product', fn($p) =>
                    $p->where('name' , 'like', "%{$request->search_filter['search']}%")
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
            ->when(!is_null($request->search_filter['status']), fn($q) =>
                $q->where('status', $request->search_filter['status'])
            )
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

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'batch.product' => 'required|numeric|exists:products,id',
            'batch.retail' => 'required|numeric',
            'batch.selling' => 'required|numeric',
            'batch.code' => 'required|string|max:255|unique:batches,batch_code',
            'batch.effective_from' => 'required|date',
            'batch.effective_to' => 'required|date',
        ]);

        DB::beginTransaction();
        try {

            $product = Product::find($request->batch['product']);
            Batch::where('product_id', $product->id)->where('status', 1)->update([
                'status' => 0,
                'effective_to' => Carbon::parse($request->batch['effective_from'])->toDateString()
            ]);

            $data = Batch::create([
                'product_id' => $request->batch['product'],
                'batch_code' => $request->batch['code'],
                'retail_price' => $request->batch['retail'],
                'selling_price' => $request->batch['selling'],
                'effective_from' => Carbon::parse($request->batch['effective_from'])->toDateString(),
                'effective_to' => Carbon::parse($request->batch['effective_to'])->toDateString(),
                'status' => true,
            ]);

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

    public function show(Request $request, Batch $batch) :JsonResponse
    {
        try {

            return response()->json([
                'result' => true,
                'data' => $batch,
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

    public function update(Request $request, Batch $batch): JsonResponse
    {
        $request->validate([
            'batch.retail' => 'required|numeric',
            'batch.selling' => 'required|numeric',
            'batch.effective_from' => 'required|date',
            'batch.effective_to' => 'required|date',
        ]);

        DB::beginTransaction();

        try {
            $data = $batch->update([
                'retail_price' => $request->batch['retail'],
                'selling_price' => $request->batch['selling'],
                'effective_from' =>  $request->batch['effective_from'],
                'effective_to' =>  $request->batch['effective_to'],
            ]);

            DB::commit();
            return response()->json([
                'result' => true,
                'data' => $data,
                'message' => null,
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'result' => false,
                'message' => $th->getMessage(),
                'data' => null,
            ], 500);
        }
    }

    public function filter(Request $request): JsonResponse
    {
        try {
            $data = Batch::leftJoin('products as p', 'p.id', 'batches.product_id')
                ->leftJoin('product_retailers as r', 'r.id', 'p.retailer_id')
                ->when(!is_null($request->filter), fn($query) =>
                    $query->when(!is_null($request->filter['product_id']),fn($q) =>
                        $q->where('p.id', $request->filter['product_id'])
                    )
                )
                ->select(['batches.id', 'batches.batch_code'])
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

    public function get_code(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);
        try {
            $product = Product::find($request->product_id);

            $data = Batch::where('product_id', $request->product_id)->latest()->first();
            if($data){
                $parts = explode('-', $data->batch_code);
                $data = $product->code.'-'.($parts[1]+1);
            } else {
                $data = $product->code.'-1';
            }

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
