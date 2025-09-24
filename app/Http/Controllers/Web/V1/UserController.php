<?php

namespace App\Http\Controllers\Web\V1;

use App\Http\Controllers\Controller;
use App\Models\NavHeader;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $roles = User::with('roles')
            ->when(!is_null($request->search), fn($q) =>
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

    public function auth_index(Request $request): JsonResponse
    {
        try {

            $user = Auth::user()->load('roles', 'permissions');

            return response()->json($user);

        } catch (\Throwable $th) {

            return response()->json([
                'message' => $th->getMessage()
            ],500);
        }
    }

    public function user_permissions(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $roleIds = $user->roles->pluck('id');

            $navbar = NavHeader::with(['children.children' => function($query) use ($roleIds) {
                $query->whereNotNull('url')
                    ->whereHas('permissions.roles', function($q) use ($roleIds) {
                        $q->whereIn('id', $roleIds);
                    });
            }, 'children.children.permissions.roles'])
            ->get();

            return response()->json($navbar);

        } catch (\Throwable $th) {
            throw $th;
            return response()->json(['message' => $th->getMessage(), 'line' => $th->getLine(), 'file'=> $th->getFile()],500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'user.name' => 'required|string|max:255',
            'user.email' => 'required|string|email|max:255|unique:users,email',
            'user.password' => ['required', 'confirmed', Password::defaults()],
            'user.role' => 'required|exists:roles,id',
        ]);

        DB::beginTransaction();
        try {
            $role = Role::find($request->user['role']);

            $user = User::create([
                'name' => $request->user['name'],
                'email' => $request->user['email'],
                'password' => Hash::make($request->string('password')),
            ]);

            $user->assignRole($role->name);

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

    public function show(Request $request, User $user) :JsonResponse
    {
        try {
            $users = $user->load('roles');

            return response()->json([
                'result' => true,
                'data' => $users,
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

    public function update(Request $request, User $user): JsonResponse
    {
        $request->validate([
            'user.name' => 'required|string|max:255',
            'role' => 'required|exists:roles,id',
        ]);

        DB::beginTransaction();

        try {
            $role = Role::findOrFail($request->role);

            $user->update([
                'name' => $request->user['name'],
            ]);

            $user->roles()->detach();
            $user->assignRole($role->name);

            DB::commit();
            return response()->json([
                'result' => true,
                'data' => $user->load('roles'),
                'message' => 'User updated successfully.',
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

}
