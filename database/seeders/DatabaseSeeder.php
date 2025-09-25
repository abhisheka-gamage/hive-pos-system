<?php

namespace Database\Seeders;

use App\Models\NavHeader;
use App\Models\NavItem;
use App\Models\NavSubItem;
use App\Models\NavType;
use App\Models\StockMovementReferances;
use App\Models\StockMovementType;
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

        $adminRole = Role::create(['name' => 'Super Admin']);
        Role::create(['name' => 'User']);

        $user = User::factory()->create([
            'name' => 'Developer Admin',
            'email' => 'dev@test.com',
            'password' => Hash::make(123),
        ]);

        $nav_header_create = Permission::create(['name' => 'nav_header-create']);
        $nav_header_view = Permission::create(['name' => 'nav_header-view']);

        $nav_item_create = Permission::create(['name' => 'nav_item-create']);
        $nav_item_view = Permission::create(['name' => 'nav_item-view']);

        $nav_sub_item_create = Permission::create(['name' => 'nav_sub_item-create']);
        $nav_sub_item_view = Permission::create(['name' => 'nav_sub_item-view']);

        $user_roles_create = Permission::create(['name' => 'user_roles-create']);
        $user_roles_view = Permission::create(['name' => 'user_roles-view']);

        $users_create = Permission::create(['name' => 'users-create']);
        $users_view = Permission::create(['name' => 'users-view']);

        $retailers_create = Permission::create(['name' => 'product_retailers-create']);
        $retailers_view   = Permission::create(['name' => 'product_retailers-view']);

        $products_create = Permission::create(['name' => 'products-create']);
        $products_view   = Permission::create(['name' => 'products-view']);

        $batches_create = Permission::create(['name' => 'product_batches-create']);
        $batches_view   = Permission::create(['name' => 'product_batches-view']);

        $stocks_create = Permission::create(['name' => 'product_stocks-create']);
        $stocks_view   = Permission::create(['name' => 'product_stocks-view']);

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

        $nav_header_users = NavHeader::create([
            'display_name' => 'Users',
            'code' => 'users',
            'icon' => 'pi pi-user'
        ]);

        $nav_header_products = NavHeader::create([
            'display_name' => 'Products',
            'code' => 'products',
            'icon' => 'pi pi-shopping-bag'
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

        $user_roles = NavItem::create([
            'nav_header_id' => $nav_header_users->id,
            'display_name' => 'User Roles',
            'code' => 'user_roles'
        ]);

        $users = NavItem::create([
            'nav_header_id' => $nav_header_users->id,
            'display_name' => 'Users',
            'code' => 'users'
        ]);

        $retailers = NavItem::create([
            'nav_header_id' => $nav_header_products->id,
            'display_name' => 'Retailers',
            'code' => 'product_retailers'
        ]);

        $products = NavItem::create([
            'nav_header_id' => $nav_header_products->id,
            'display_name' => 'Products',
            'code' => 'products'
        ]);

        $batches = NavItem::create([
            'nav_header_id' => $nav_header_products->id,
            'display_name' => 'Batches',
            'code' => 'product_batches'
        ]);

        $stocks = NavItem::create([
            'nav_header_id' => $nav_header_products->id,
            'display_name' => 'Stocks',
            'code' => 'product_stocks'
        ]);

        NavSubItem::insert([

            //Nav Headers
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

            //Nav Items
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

            // Nav Sub Items
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
            ],

            // User Roles
            [
                'nav_item_id' => $user_roles->id,
                'permission_id' => $user_roles_create->id,
                'nav_type_id' => 2,
                'display_name' => 'Add Roles',
                'url' => '/users/roles/create',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nav_item_id' => $user_roles->id,
                'permission_id' => $user_roles_view->id,
                'nav_type_id' => 1,
                'display_name' => 'View Roles',
                'url' => '/users/roles/index',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Users
            [
                'nav_item_id' => $users->id,
                'permission_id' => $users_create->id,
                'nav_type_id' => 2,
                'display_name' => 'Add Users',
                'url' => '/users/user/create',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nav_item_id' => $users->id,
                'permission_id' => $users_view->id,
                'nav_type_id' => 1,
                'display_name' => 'View Users',
                'url' => '/users/user/index',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            //Retailers
            [
                'nav_item_id'   => $retailers->id,
                'permission_id' => $retailers_create->id,
                'nav_type_id'   => 2,
                'display_name'  => 'Add Retailers',
                'url'           => '/products/retailers/create',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'nav_item_id'   => $retailers->id,
                'permission_id' => $retailers_view->id,
                'nav_type_id'   => 1,
                'display_name'  => 'View Retailers',
                'url'           => '/products/retailers/index',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],

            // Products
            [
                'nav_item_id'   => $products->id,
                'permission_id' => $products_create->id,
                'nav_type_id'   => 2,
                'display_name'  => 'Add Products',
                'url'           => '/products/product/create',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'nav_item_id'   => $products->id,
                'permission_id' => $products_view->id,
                'nav_type_id'   => 1,
                'display_name'  => 'View Products',
                'url'           => '/products/product/index',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],

            // Batches
            [
                'nav_item_id'   => $batches->id,
                'permission_id' => $batches_create->id,
                'nav_type_id'   => 2,
                'display_name'  => 'Add Batches',
                'url'           => '/products/batches/create',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'nav_item_id'   => $batches->id,
                'permission_id' => $batches_view->id,
                'nav_type_id'   => 1,
                'display_name'  => 'View Batches',
                'url'           => '/products/batches/index',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],

            // Stocks
            [
                'nav_item_id'   => $stocks->id,
                'permission_id' => $stocks_create->id,
                'nav_type_id'   => 2,
                'display_name'  => 'Add Stocks',
                'url'           => '/products/stocks/create',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'nav_item_id'   => $stocks->id,
                'permission_id' => $stocks_view->id,
                'nav_type_id'   => 1,
                'display_name'  => 'View Stocks',
                'url'           => '/products/stocks/index',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ]);

        $user->assignRole('Super Admin');
        $adminRole->givePermissionTo([
            $nav_header_create->name,
            $nav_header_view->name,
            $nav_item_create->name,
            $nav_item_view->name,
            $nav_sub_item_create->name,
            $nav_sub_item_view->name,
            $home_view->name,
            $user_roles_create->name,
            $user_roles_view->name,
            $users_create->name,
            $users_view->name,
            $retailers_create->name,
            $retailers_view->name,
            $products_create->name,
            $products_view->name,
            $batches_create->name,
            $batches_view->name,
            $stocks_create->name,
            $stocks_view->name,
        ]);

        StockMovementType::insert([
            [
                'id' => 1,
                'name' => 'Increment',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Decrement',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Adjustment',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        StockMovementReferances::insert([
            [
                'id' => 1,
                'name' => 'sold',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'purchased',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'returned',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'name' => 'expired',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
