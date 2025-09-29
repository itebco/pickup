<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\User;
use App\Models\Package;
use App\Http\Requests\Address\CreateAddressRequest;
use App\Http\Requests\Address\UpdateAddressRequest;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function index(Request $request)
    {
        $currentUser = Auth::user();
        $query = Address::query();

        // If current user is a customer (role = 3), only get their addresses
        if ($currentUser->role_id == Role::CUSTOMER_ROLE_ID) {
            $query->where('user_id', $currentUser->id);
        }
        // If current user is a user (role = 2), only get addresses of customers they created
        elseif ($currentUser->role_id == Role::USER_ROLE_ID) {
            // Get the IDs of customers (role = 3) that were created by this user
            $customerIds = User::where('role_id', Role::CUSTOMER_ROLE_ID)
                               ->where('created_by', $currentUser->id)
                               ->pluck('id');
            $query->whereIn('user_id', $customerIds);
        }

        if ($request->has('search') && $request->get('search') != '') {
            $search = $request->get('search');
            $query->where('owner_name', 'LIKE', "%{$search}%")
                  ->orWhere('tel', 'LIKE', "%{$search}%")
                  ->orWhere('state', 'LIKE', "%{$search}%")
                  ->orWhere('city', 'LIKE', "%{$search}%")
                  ->orWhere('ward', 'LIKE', "%{$search}%")
                  ->orWhere('room_no', 'LIKE', "%{$search}%");
        }

        $addresses = $query->orderBy('id', 'desc')->paginate(15)->appends(request()->query());

        return view('address.list', compact('addresses'));
    }

    public function create()
    {
        $currentUser = Auth::user();

        if ($currentUser->role_id == Role::CUSTOMER_ROLE_ID) {
            // If current user is a customer (role = 3), only get their information
            $customers = collect([$currentUser]);
        }
        elseif ($currentUser->role_id == Role::USER_ROLE_ID) {
            // If current user is a user (role = 2), only get customers they created
            $customers = User::where('role_id', Role::CUSTOMER_ROLE_ID)
                             ->where('created_by', $currentUser->id)
                             ->get();
        }
        else {
            // For other roles, get all customers
            $customers = User::where('role_id', Role::CUSTOMER_ROLE_ID)->get(); // Only customers
        }

        return view('address.add', compact('customers'));
    }

    public function store(CreateAddressRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = Auth::user()->id;
        Address::create($data);

        return redirect()->route('addresses.index')->with('success', __('address.address_added_successfully'));
    }

    public function show(Address $address)
    {
        // disable route
        abort(404);
    }

    public function edit(Address $address)
    {
        $currentUser = Auth::user();

        // Check if the current user is a customer and if the address belongs to them
        if ($currentUser->role_id == Role::CUSTOMER_ROLE_ID) {
            // Verify that the address belongs to the current user
            $isAddressOwnedByUser = $address->user_id == $currentUser->id;

            if (!$isAddressOwnedByUser) {
                abort(403, __('address.unauthorized_to_edit_address'));
            }
        }
        // Check if the current user is a user and if the address belongs to a customer they created
        elseif ($currentUser->role_id == Role::USER_ROLE_ID) {
            // Get the IDs of customers (role = 3) that were created by this user
            $customerIds = User::where('role_id', Role::CUSTOMER_ROLE_ID)
                               ->where('created_by', $currentUser->id)
                               ->pluck('id');
            // Verify that the address belongs to one of the customers created by the current user
            $isAddressOwnedByCreatedCustomer = in_array($address->user_id, $customerIds->toArray());

            if (!$isAddressOwnedByCreatedCustomer) {
                abort(403, __('address.unauthorized_to_edit_address'));
            }
        }

        // Check if address has packages with status 'Done' or pickup date <= current date
        if ($this->hasRestrictedPackages($address)) {
            abort(403, __('address.address_cannot_be_edited_due_to_packages'));
        }

        if ($currentUser->role_id == Role::CUSTOMER_ROLE_ID) {
            // If current user is a customer (role = 3), only get their information
            $customers = collect([$currentUser]); // Create a collection with just this user
        }
        elseif ($currentUser->role_id == Role::USER_ROLE_ID) {
            // If current user is a user (role = 2), only get customers they created
            $customers = User::where('role_id', Role::CUSTOMER_ROLE_ID)
                             ->where('created_by', $currentUser->id)
                             ->get();
        }
        else {
            // For other roles, get all customers
            $customers = User::where('role_id', Role::CUSTOMER_ROLE_ID)->get(); // Only customers
        }

        return view('address.edit', compact('address', 'customers'));
    }

    public function update(UpdateAddressRequest $request, Address $address)
    {
        $currentUser = Auth::user();

        // Check if the current user is a customer and if the address belongs to them
        if ($currentUser->role_id == Role::CUSTOMER_ROLE_ID) {
            // Verify that the address belongs to the current user
            $isAddressOwnedByUser = $address->user_id == $currentUser->id;

            if (!$isAddressOwnedByUser) {
                abort(403, __('address.unauthorized_to_edit_address'));
            }
        }
        // Check if the current user is a user and if the address belongs to a customer they created
        elseif ($currentUser->role_id == Role::USER_ROLE_ID) {
            // Get the IDs of customers (role = 3) that were created by this user
            $customerIds = User::where('role_id', Role::CUSTOMER_ROLE_ID)
                               ->where('created_by', $currentUser->id)
                               ->pluck('id');
            // Verify that the address belongs to one of the customers created by the current user
            $isAddressOwnedByCreatedCustomer = in_array($address->user_id, $customerIds->toArray());

            if (!$isAddressOwnedByCreatedCustomer) {
                abort(403, __('address.unauthorized_to_edit_address'));
            }
        }

        // Check if address has packages with status 'Done' or pickup date <= current date
        if ($this->hasRestrictedPackages($address)) {
            abort(403, __('address.address_cannot_be_edited_due_to_packages'));
        }

        $address->update($request->validated());

        return redirect()->route('addresses.index')->with('success', __('address.address_updated_successfully'));
    }

    public function destroy(Address $address)
    {
        $currentUser = Auth::user();

        // Check if the current user is a customer and if the address belongs to them
        if ($currentUser->role_id == Role::CUSTOMER_ROLE_ID) {
            // Verify that the address belongs to the current user
            $isAddressOwnedByUser = $address->user_id == $currentUser->id;

            if (!$isAddressOwnedByUser) {
                abort(403, __('address.unauthorized_to_delete_address'));
            }
        }
        // Check if the current user is a user and if the address belongs to a customer they created
        elseif ($currentUser->role_id == Role::USER_ROLE_ID) {
            // Get the IDs of customers (role = 3) that were created by this user
            $customerIds = User::where('role_id', Role::CUSTOMER_ROLE_ID)
                               ->where('created_by', $currentUser->id)
                               ->pluck('id');
            // Verify that the address belongs to one of the customers created by the current user
            $isAddressOwnedByCreatedCustomer = in_array($address->user_id, $customerIds->toArray());

            if (!$isAddressOwnedByCreatedCustomer) {
                abort(403, __('address.unauthorized_to_delete_address'));
            }
        }

        // Check if address has packages with status 'Done' or pickup date <= current date
        if ($this->hasRestrictedPackages($address)) {
            abort(403, __('address.address_cannot_be_deleted_due_to_packages'));
        }

        $address->delete();

        return redirect()->route('addresses.index')->with('success', __('address.address_deleted_successfully'));
    }

    /**
     * Check if address has packages with status 'Done' or pickup date <= current date
    */
    public function hasRestrictedPackages(Address $address)
    {
        $currentDate = now()->format('Y-m-d');
        return $address->packages()->where(function($query) use ($currentDate) {
            $query->where('status', Package::STATUS_DONE)
                  ->orWhere('pickup_date', '<=', $currentDate);
        })->exists();
    }
}
