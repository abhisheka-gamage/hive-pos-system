<?php

namespace App\Http\Controllers\Web\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $roles = Role::when(!is_null($request->search), fn($q) =>
                $q->where('name' , 'like', "%{$request->search}%")
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

    public function show(Request $request, Role $role) :JsonResponse
    {
        try {
            $role = $role->load('permissions');

            return response()->json([
                'result' => true,
                'data' => $role,
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

    public function store(Request $request) :JsonResponse
    {
        $validated = $request->validate([
            'role.name' => 'required'
        ]);
        DB::beginTransaction();
        try {
            Role::create(['name' => $validated['role']['name']]);
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

    public function update(Request $request, Role $role): JsonResponse
    {
        DB::beginTransaction();
        try {
            $permission_types = $request->permissions;

            $permissionsToGive = [];
            $permissionsToRemove = [];

            foreach ($permission_types as $type) {
                foreach ($type['data'] as $permission) {
                    if (!empty($permission['checked'])) {
                        if (!$role->hasPermissionTo($permission['name'])) {
                            $permissionsToGive[] = $permission['name'];
                        }
                    } else {
                        if ($role->hasPermissionTo($permission['name'])) {
                            $permissionsToRemove[] = $permission['name'];
                        }
                    }
                }
            }

            if (!empty($permissionsToGive)) {
                $role->givePermissionTo($permissionsToGive);
            }

            if (!empty($permissionsToRemove)) {
                $role->revokePermissionTo($permissionsToRemove);
            }

            DB::commit();
            return response()->json([
                'result' => true,
                'data' => $role,
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
}
