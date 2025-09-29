<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\User;
use App\Models\Address;
use App\Http\Requests\Package\CreatePackageRequest;
use App\Http\Requests\Package\UpdatePackageRequest;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PackageController extends Controller
{
    public function index(Request $request)
    {
        $currentUser = Auth::user();
        $query = Package::query();

        // If current user is a customer (role = 3), only get their packages
        if ($currentUser->role_id == Role::CUSTOMER_ROLE_ID) {
            $query->whereHas('address', function($q) use ($currentUser) {
                $q->where('user_id', $currentUser->id);
            });
        }
        // If current user is a user (role = 2), only get packages of customers they created
        elseif ($currentUser->role_id == Role::USER_ROLE_ID) {
            // Get the IDs of customers (role = 3) that were created by this user
            $customerIds = User::where('role_id', Role::CUSTOMER_ROLE_ID)
                               ->where('created_by', $currentUser->id)
                               ->pluck('id');
            $query->whereHas('address', function($q) use ($customerIds) {
                $q->whereIn('user_id', $customerIds);
            });
        }

        // Filter by search
        if ($request->has('search') && $request->get('search') != '') {
            $search = $request->get('search');
            $query->where('package_code', 'LIKE', "%{$search}%")
                //   ->orWhereHas('address', function($q) use ($search, $currentUser, $customerIds) {
                //       $q->where('owner_name', 'LIKE', "%{$search}%");
                //       if ($currentUser->role_id == Role::CUSTOMER_ROLE_ID) {
                //           $q->where('user_id', $currentUser->id);
                //       }
                //       elseif ($currentUser->role_id == Role::USER_ROLE_ID) {
                //           $q->whereIn('user_id', $customerIds);
                //       }
                //   })
                //   ->orWhere('status', 'LIKE', "%{$search}%")
                  ->orWhere('method', 'LIKE', "%{$search}%");
        }

        // Filter by pickup_date from
        if ($request->get('pickup_date_from')) {
            $query->where('pickup_date', '>=', $request->get('pickup_date_from'));
        }

        // Filter by pickup_date to
        if ($request->get('pickup_date_to')) {
            $query->where('pickup_date', '<=', $request->get('pickup_date_to'));
        }

        // Filter by status
        if ($request->get('status') && $request->get('status') != 'all') {
            $query->where('status', $request->get('status'));
        }

        $packages = $query->orderBy('id', 'desc')->paginate(15)->appends(request()->query());

        return view('package.list', compact('packages'));
    }

    public function create()
    {
        $currentUser = Auth::user();
        $pickUpTimes = config('setting.package.pick_up_times');

        if ($currentUser->role_id == Role::CUSTOMER_ROLE_ID) {
            // If current user is a customer (role = 3), only get their information and their addresses
            $customer = $currentUser;
            $addresses = $customer->addresses;
            $customers = collect([$customer]); // Create a collection with just this user
        }
        elseif ($currentUser->role_id == Role::USER_ROLE_ID) {
            // If current user is a user (role = 2), only get customers they created and their addresses
            $customerIds = User::where('role_id', Role::CUSTOMER_ROLE_ID)
                               ->where('created_by', $currentUser->id)
                               ->pluck('id');
            $customers = User::where('role_id', Role::CUSTOMER_ROLE_ID)
                             ->where('created_by', $currentUser->id)
                             ->with('addresses')
                             ->get();
            // Only get addresses of customers they created
            $addresses = Address::whereIn('user_id', $customerIds)->get();
        }
        else {
            // For other roles, get all customers with their addresses using eager loading
            $customers = User::where('role_id', Role::CUSTOMER_ROLE_ID)->with('addresses')->get();
            $addresses = Address::all(); // All addresses for all customers
        }

        return view('package.add', compact('customers', 'addresses', 'currentUser', 'pickUpTimes'));
    }

    public function store(CreatePackageRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = Auth::user()->id;
        Package::create($data);

        return redirect()->route('packages.index')->with('success', __('package.package_added_successfully'));
    }

    public function show(Package $package)
    {
        // disable route
        abort(404);
        return view('package.view', compact('package'));
    }

    public function edit(Package $package)
    {
        $currentUser = Auth::user();

        // Check if package status is done or pickup date is in the past
        $currentDate = now()->format('Y-m-d');
        $pickupDate = $package->pickup_date;

        if ($package->status === Package::STATUS_DONE || $pickupDate <= $currentDate) {
            abort(403, __('package.package_cannot_be_edited'));
        }

        // Check if the current user is a customer and if the package belongs to them
        if ($currentUser->role_id == Role::CUSTOMER_ROLE_ID) {
            // Verify that the package belongs to an address owned by the current user
            $isPackageOwnedByUser = $package->address->user_id == $currentUser->id;

            if (!$isPackageOwnedByUser) {
                abort(403, __('package.unauthorized_to_edit_package'));
            }
        }
        // Check if the current user is a user and if the package belongs to a customer they created
        elseif ($currentUser->role_id == Role::USER_ROLE_ID) {
            // Get the IDs of customers (role = 3) that were created by this user
            $customerIds = User::where('role_id', Role::CUSTOMER_ROLE_ID)
                               ->where('created_by', $currentUser->id)
                               ->pluck('id');
            // Verify that the package belongs to one of the customers created by the current user
            $isPackageOwnedByCreatedCustomer = in_array($package->address->user_id, $customerIds->toArray());

            if (!$isPackageOwnedByCreatedCustomer) {
                abort(403, __('package.unauthorized_to_edit_package'));
            }
        }

        $pickUpTimes = config('setting.package.pick_up_times');

        if ($currentUser->role_id == Role::CUSTOMER_ROLE_ID) {
            // If current user is a customer (role = 3), only get their information and their addresses
            $customer = $currentUser;
            $addresses = $customer->addresses;
            $customers = collect([$customer]); // Create a collection with just this user
        }
        elseif ($currentUser->role_id == Role::USER_ROLE_ID) {
            // If current user is a user (role = 2), only get customers they created and their addresses
            $customers = User::where('role_id', Role::CUSTOMER_ROLE_ID)
                             ->where('created_by', $currentUser->id)
                             ->with('addresses')
                             ->get();
            // Only get addresses of customers they created
            $addresses = Address::whereIn('user_id', $customerIds)->get();
        }
        else {
            // For other roles, get all customers with their addresses using eager loading
            $customers = User::where('role_id', Role::CUSTOMER_ROLE_ID)->with('addresses')->get();
            $addresses = Address::all(); // All addresses for all customers
        }

        return view('package.edit', compact('package', 'customers', 'addresses', 'currentUser', 'pickUpTimes'));
    }

    public function update(UpdatePackageRequest $request, Package $package)
    {
        $currentUser = Auth::user();

        // Check if package status is done or pickup date is in the past
        $currentDate = now()->format('Y-m-d');
        $pickupDate = $package->pickup_date;

        if ($package->status === Package::STATUS_DONE || $pickupDate <= $currentDate) {
            abort(403, __('package.package_cannot_be_edited'));
        }

        // Check if the current user is a customer and if the package belongs to them
        if ($currentUser->role_id == Role::CUSTOMER_ROLE_ID) {
            // Verify that the package belongs to an address owned by the current user
            $isPackageOwnedByUser = $package->address->user_id == $currentUser->id;

            if (!$isPackageOwnedByUser) {
                abort(403, __('package.unauthorized_to_edit_package'));
            }
        }
        // Check if the current user is a user and if the package belongs to a customer they created
        elseif ($currentUser->role_id == Role::USER_ROLE_ID) {
            // Get the IDs of customers (role = 3) that were created by this user
            $customerIds = User::where('role_id', Role::CUSTOMER_ROLE_ID)
                               ->where('created_by', $currentUser->id)
                               ->pluck('id');
            // Verify that the package belongs to one of the customers created by the current user
            $isPackageOwnedByCreatedCustomer = in_array($package->address->user_id, $customerIds->toArray());

            if (!$isPackageOwnedByCreatedCustomer) {
                abort(403, 'Unauthorized to edit this package');
            }
        }

        $package->update($request->validated());

        return redirect()->route('packages.index')->with('success', __('package.package_updated_successfully'));
    }

    public function destroy(Package $package)
    {
        $currentUser = Auth::user();

        // Check if package status is done or pickup date is in the past
        $currentDate = now()->format('Y-m-d');
        $pickupDate = $package->pickup_date;

        if ($package->status === Package::STATUS_DONE || $pickupDate <= $currentDate) {
            abort(403, __('package.package_cannot_be_deleted'));
        }

        // Check if the current user is a customer and if the package belongs to them
        if ($currentUser->role_id == Role::CUSTOMER_ROLE_ID) {
            // Verify that the package belongs to an address owned by the current user
            $isPackageOwnedByUser = $package->address->user_id == $currentUser->id;

            if (!$isPackageOwnedByUser) {
                abort(403, __('package.unauthorized_to_delete_package'));
            }
        }
        // Check if the current user is a user and if the package belongs to a customer they created
        elseif ($currentUser->role_id == Role::USER_ROLE_ID) {
            // Get the IDs of customers (role = 3) that were created by this user
            $customerIds = User::where('role_id', Role::CUSTOMER_ROLE_ID)
                               ->where('created_by', $currentUser->id)
                               ->pluck('id');
            // Verify that the package belongs to one of the customers created by the current user
            $isPackageOwnedByCreatedCustomer = in_array($package->address->user_id, $customerIds->toArray());

            if (!$isPackageOwnedByCreatedCustomer) {
                abort(403, 'Unauthorized to delete this package');
            }
        }

        $package->delete();

        return redirect()->route('packages.index')->with('success', __('package.package_deleted_successfully'));
    }
}
