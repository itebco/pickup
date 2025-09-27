<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class CustomerPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create the Customer role
        $customerRole = Role::updateOrCreate(
            ['name' => 'Customer'],
            [
                'display_name' => 'Customer',
                'description' => 'Customer create packing',
                'removable' => false
            ]
        );

        // Insert or update the customer permission
        $customerPermission = Permission::updateOrCreate(
            ['name' => 'customers.manage'],
            [
                'display_name' => 'Manage Customers',
                'description' => 'Manage customer accounts',
                'removable' => false
            ]
        );

        // Find the User role and attach the permission
        $userRole = Role::where('name', 'User')->first();
        
        if ($userRole) {
            // Check if the permission is already attached
            $existingPermission = $userRole->permissions()
                ->where('permission_id', $customerPermission->id)
                ->first();
                
            if (!$existingPermission) {
                // Use the attachPermission method which will handle cache invalidation
                $userRole->attachPermission($customerPermission);
            }
        }
    }
}
