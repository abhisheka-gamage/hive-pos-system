<?php

namespace App\Http\Controllers\Web\V1\Navigation;

use App\Http\Controllers\Controller;
use App\Models\NavSubItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class NavSubItemController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $sub_items = NavSubItem::when(!is_null($request->search), fn($q) =>
                $q->where('display_name' , 'like', "%{$request->search}%")
            )
            ->with(['parent.parent', 'permissions.roles', 'types'])->paginate(request('entries', 10));

            return response()->json([
                'result' => true,
                'data' => $sub_items,
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
            $sub_items = NavSubItem::select(['id', 'code'])->get();

            return response()->json([
                'result' => true,
                'data' => $sub_items,
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
            'permission.type' => 'required',
            'permission.permission_name' => 'required',
            'permission.url' => 'nullable',
            'permission.parent' => 'required',
            'permission.type' => 'required',
        ]);
        try {
            DB::beginTransaction();

            $permission = Permission::create(['name' => $validated['permission']['permission_name']]);

            NavSubItem::create([
                'nav_item_id' => $validated['permission']['parent'],
                'permission_id' => $permission->id,
                'nav_type_id' => $validated['permission']['type'],
                'display_name' => $validated['permission']['name'],
                'url' => $validated['permission']['url']
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
