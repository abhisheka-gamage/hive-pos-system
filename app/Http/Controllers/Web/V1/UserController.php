<?php

namespace App\Http\Controllers\Web\V1;

use App\Http\Controllers\Controller;
use App\Models\NavHeader;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
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
}
