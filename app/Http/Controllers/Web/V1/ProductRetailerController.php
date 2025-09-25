<?php

namespace App\Http\Controllers\Web\V1;

use App\Http\Controllers\Controller;
use App\Models\ProductRetailer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductRetailerController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $data = ProductRetailer::when(!is_null($request->search), fn($q) =>
                $q->where('name' , 'like', "%{$request->search}%")
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
            'retailer.name' => 'required|string|max:255',
            'retailer.code' => 'required|string|max:255|unique:product_retailers,code',
        ]);

        DB::beginTransaction();
        try {

            $data = ProductRetailer::create([
                'name' => $request->retailer['name'],
                'code' => $request->retailer['code'],
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

    public function show(Request $request, ProductRetailer $retailer) :JsonResponse
    {
        try {

            return response()->json([
                'result' => true,
                'data' => $retailer,
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

    public function update(Request $request, ProductRetailer $retailer): JsonResponse
    {
        $request->validate([
            'retailer.name' => 'required|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            $data = $retailer->update([
                'name' => $request->retailer['name'],
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
            $data = ProductRetailer::select(['id', 'name'])->get();

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
