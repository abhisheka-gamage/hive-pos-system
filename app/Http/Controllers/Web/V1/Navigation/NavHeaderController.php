<?php

namespace App\Http\Controllers\Web\V1\Navigation;

use App\Http\Controllers\Controller;
use App\Models\NavHeader;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NavHeaderController extends Controller
{
        public function index(Request $request): JsonResponse
    {
        try {
            $roles = NavHeader::when(!is_null($request->search), fn($q) =>
                $q->where('display_name' , 'like', "%{$request->search}%")
            )
            ->paginate(request('entries', 10));

            return response()->json([
                'result' => true,
                'data' => $roles,
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

    public function filter(Request $request): JsonResponse
    {
        try {
            $headers = NavHeader::select(['id', 'code'])->get();

            return response()->json([
                'result' => true,
                'data' => $headers,
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

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'permission.name' => 'required',
            'permission.code' => 'required',
            'permission.icon' => 'nullable'
        ]);
        try {
            DB::beginTransaction();

            NavHeader::create([
                'display_name' => $validated['permission']['name'],
                'code' => $validated['permission']['code'],
                'icon' => $validated['permission']['icon']
            ]);

            DB::commit();
            return response()->json([
                'result' => true,
                'message' => 'Saved successfully',
                'data' => null
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
}
