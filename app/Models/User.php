<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

        public static function getUserPermissions(){
        $user = Auth::user();
        return $user ? $user->load('roles', 'permissions')->pluck('name'): null;
    }

    public static function getUserNavData()
    {
        $user = Auth::user();

        if (!$user) {
            return collect();
        }

        $roleIds = $user->roles->pluck('id');

        $navbar = NavHeader::with([
            'children' => function ($query) use ($roleIds) {
                $query->whereHas('children', function ($q) use ($roleIds) {
                    $q->whereNotNull('url')
                    ->whereHas('permissions.roles', function ($r) use ($roleIds) {
                        $r->whereIn('id', $roleIds);
                    });
                })->with([
                    'children' => function ($q) use ($roleIds) {
                        $q->whereNotNull('url')
                        ->whereHas('permissions.roles', function ($r) use ($roleIds) {
                            $r->whereIn('id', $roleIds);
                        });
                    }
                ]);
            },
            'children.children.permissions'
        ])
        ->whereHas('children.children', function ($query) use ($roleIds) {
            $query->whereNotNull('url')
                ->whereHas('permissions.roles', function ($q) use ($roleIds) {
                    $q->whereIn('id', $roleIds);
                });
        })
        ->get();

        return $navbar;
    }
}
