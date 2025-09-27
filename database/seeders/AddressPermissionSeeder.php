<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class AddressPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insert or update the address permission
        $addressPermission = Permission::updateOrCreate(
            ['name' => 'addresses.manage'],
            [
                'display_name' => 'Manage Addresses',
                'description' => 'Manage address records',
                'removable' => false
            ]
        );

        // Find the User role (role_id = 2) and attach the permission
        $userRole = Role::where('id', 2)->first();
        if ($userRole) {
            // Check if the permission is already attached
            $existingPermission = $userRole->permissions()
                ->where('permission_id', $addressPermission->id)
                ->first();

            if (!$existingPermission) {
                // Use the attachPermission method which will handle cache invalidation
                $userRole->attachPermission($addressPermission);
            }
        }

        // Find the Admin role (role_id = 1) and attach the permission
        $adminRole = Role::where('id', 1)->first();
        if ($adminRole) {
            // Check if the permission is already attached
            $existingPermission = $adminRole->permissions()
                ->where('permission_id', $addressPermission->id)
                ->first();

            if (!$existingPermission) {
                // Use the attachPermission method which will handle cache invalidation
                $adminRole->attachPermission($addressPermission);
            }
        }

        // Find the Customer role (role_id = 3) and attach the permission
        $customerRole = Role::where('id', 3)->first();
        if ($customerRole) {
            // Check if the permission is already attached
            $existingPermission = $customerRole->permissions()
                ->where('permission_id', $addressPermission->id)
                ->first();

            if (!$existingPermission) {
                // Use the attachPermission method which will handle cache invalidation
                $customerRole->attachPermission($addressPermission);
            }
        }
    }
}
