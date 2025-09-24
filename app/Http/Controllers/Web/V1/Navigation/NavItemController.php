<?php

namespace App\Http\Controllers\Web\V1\Navigation;

use App\Http\Controllers\Controller;
use App\Models\NavItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NavItemController extends Controller
{
      public function index(Request $request): JsonResponse
    {
        try {
            $items = NavItem::when(!is_null($request->search), fn($q) =>
                $q->where('display_name' , 'like', "%{$request->search}%")
            )
            ->with('parent')->paginate(request('entries', 10));

            return response()->json([
                'result' => true,
                'data' => $items,
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
            $items = NavItem::when(isset($request->parent), fn($q)=>
                $q->where('nav_header_id', $request->parent)
            )->select(['id', 'code'])->get();

            return response()->json([
                'result' => true,
                'data' => $items,
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
            'permission.parent' => 'required'
        ]);
        try {
            DB::beginTransaction();

            NavItem::create([
                'nav_header_id' => $validated['permission']['parent'],
                'code' => $validated['permission']['code'],
                'display_name' => $validated['permission']['name']
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
