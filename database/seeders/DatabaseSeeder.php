<?php

namespace Database\Seeders;

use App\Models\NavHeader;
use App\Models\NavItem;
use App\Models\NavSubItem;
use App\Models\NavType;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $adminRole = Role::create(['name' => 'Admin']);
        Role::create(['name' => 'User']);

        $user = User::factory()->create([
            'name' => 'Developer Admin',
            'email' => 'dev@admin.com',
            'password' => Hash::make(123),
            'role_id' => $adminRole->id
        ]);

        $nav_header_create = Permission::create(['name' => 'nav_header-create']);
        $nav_header_view = Permission::create(['name' => 'nav_header-view']);

        $nav_item_create = Permission::create(['name' => 'nav_item-create']);
        $nav_item_view = Permission::create(['name' => 'nav_item-view']);

        $nav_sub_item_create = Permission::create(['name' => 'nav_sub_item-create']);
        $nav_sub_item_view = Permission::create(['name' => 'nav_sub_item-view']);

        $home_view = Permission::create(['name' => 'home-view']);

        NavType::create(['name' => 'View']);
        NavType::create(['name' => 'Create']);
        NavType::create(['name' => 'Edit']);
        NavType::create(['name' => 'Approve']);
        NavType::create(['name' => 'Reject']);

        $nav_header = NavHeader::create([
            'display_name' => 'Permissions',
            'code' => 'permissions',
            'icon' => 'pi pi-lock'
        ]);

        $nav_headers = NavItem::create([
            'nav_header_id' => $nav_header->id,
            'display_name' => 'Navigation Menu Headers',
            'code' => 'nav_headers'
        ]);

        $nav_items = NavItem::create([
            'nav_header_id' => $nav_header->id,
            'display_name' => 'Navigation Menus',
            'code' => 'nav_items'
        ]);

        $nav_sub_items = NavItem::create([
            'nav_header_id' => $nav_header->id,
            'display_name' => 'Navigation Sub Menu',
            'code' => 'nav_sub_items'
        ]);

        NavSubItem::insert([
            [
                'nav_item_id' => $nav_headers->id,
                'permission_id' => $nav_header_create->id,
                'nav_type_id' => 2,
                'display_name' => 'Create',
                'url' => '/permissions/headers/create',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nav_item_id' => $nav_headers->id,
                'permission_id' => $nav_header_view->id,
                'nav_type_id' => 1,
                'display_name' => 'View',
                'url' => '/permissions/headers/index',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nav_item_id' => $nav_items->id,
                'permission_id' => $nav_item_create->id,
                'nav_type_id' => 2,
                'display_name' => 'Create',
                'url' => '/permissions/items/create',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nav_item_id' => $nav_items->id,
                'permission_id' => $nav_item_view->id,
                'nav_type_id' => 1,
                'display_name' => 'View',
                'url' => '/permissions/items/index',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nav_item_id' => $nav_sub_items->id,
                'permission_id' => $nav_sub_item_create->id,
                'nav_type_id' => 2,
                'display_name' => 'Create',
                'url' => '/permissions/sub_items/create',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nav_item_id' => $nav_sub_items->id,
                'permission_id' => $nav_sub_item_view->id,
                'nav_type_id' => 1,
                'display_name' => 'View',
                'url' => '/permissions/sub_items/index',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        $user->assignRole('Admin');
        $adminRole->givePermissionTo([
            $nav_header_create->name,
            $nav_header_view->name,
            $nav_item_create->name,
            $nav_item_view->name,
            $nav_sub_item_create->name,
            $nav_sub_item_view->name,
            $home_view->name
        ]);
    }
}
