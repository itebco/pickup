<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class PackagePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insert or update the package permission
        $packagePermission = Permission::updateOrCreate(
            ['name' => 'packages.manage'],
            [
                'display_name' => 'Manage Packages',
                'description' => 'Manage package records',
                'removable' => false
            ]
        );

        // Find the User role (role_id = 2) and attach the permission
        $userRole = Role::where('id', 2)->first();
        if ($userRole) {
            // Check if the permission is already attached
            $existingPermission = $userRole->permissions()
                ->where('permission_id', $packagePermission->id)
                ->first();

            if (!$existingPermission) {
                // Use the attachPermission method which will handle cache invalidation
                $userRole->attachPermission($packagePermission);
            }
        }

        // Find the Admin role (role_id = 1) and attach the permission
        $adminRole = Role::where('id', 1)->first();
        if ($adminRole) {
            // Check if the permission is already attached
            $existingPermission = $adminRole->permissions()
                ->where('permission_id', $packagePermission->id)
                ->first();

            if (!$existingPermission) {
                // Use the attachPermission method which will handle cache invalidation
                $adminRole->attachPermission($packagePermission);
            }
        }

        // Find the Customer role (role_id = 3) and attach the permission
        $customerRole = Role::where('id', 3)->first();
        if ($customerRole) {
            // Check if the permission is already attached
            $existingPermission = $customerRole->permissions()
                ->where('permission_id', $packagePermission->id)
                ->first();

            if (!$existingPermission) {
                // Use the attachPermission method which will handle cache invalidation
                $customerRole->attachPermission($packagePermission);
            }
        }
    }
}
