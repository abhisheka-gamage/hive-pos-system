<?php

namespace App\Http\Controllers\Web\V1;

use App\Http\Controllers\Controller;
use App\Models\NavSubItem;
use App\Models\NavType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $permissions = NavType::with(['menus.permissions'])->get();

            return response()->json([
                'result' => true,
                'data' => $permissions,
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

    //type
    public function type_filter(): JsonResponse
    {
        try {
            $nav_types = NavType::select(['id', 'name'])->get();

            return response()->json([
                'result' => true,
                'data' => $nav_types,
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
