<?php

namespace App\Http\Controllers\Web\V1;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $data = Product::with('retailer')
            ->when(!is_null($request->search_filter['search']), fn($q) =>
                $q->where('name' , 'like', "%{$request->search_filter['search']}%")
                ->orWhere('code' , 'like', "%{$request->search_filter['search']}%")
            )
            ->when(!is_null($request->search_filter['retailer']), fn($q) =>
                $q->whereHas('retailer', fn($r) =>
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
            'product.name' => 'required|string|max:255',
            'product.code' => 'required|string|max:255|unique:product_retailers,code',
            'product.barcode' => 'nullable',
            'product.retail' => 'required|numeric',
            'product.selling' => 'required|numeric',
            'product.retailer' => 'required|numeric',
        ]);

        DB::beginTransaction();
        try {

            $data = Product::create([
                'name' => $request->product['name'],
                'code' => $request->product['code'],
                'barcode' => $request->product['barcode'],
                'retailer_id' => $request->product['retailer'],
                'status' => true
            ]);

            Batch::create([
                'product_id' => $data->id,
                'batch_code' => $data->code.'-1',
                'retail_price' => $request->product['retail'],
                'selling_price' => $request->product['selling'],
                'effective_from' => today(),
                'effective_to' => '9999-12-31',
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

    public function show(Request $request, Product $product) :JsonResponse
    {
        try {

            return response()->json([
                'result' => true,
                'data' => $product,
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

    public function update(Request $request, Product $product): JsonResponse
    {
        $request->validate([
            'product.name' => 'required|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            $data = $product->update([
                'name' => $request->product['name'],
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
            $data = Product::leftJoin('product_retailers as r', 'r.id', 'products.retailer_id')
                ->when(!is_null($request->filter), fn($query) =>
                    $query->when(!is_null($request->filter['retailer_id']),fn($q) =>
                        $q->where('r.id', $request->filter['retailer_id'])
                    )
                )
                ->select(['products.id', 'products.name'])
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
