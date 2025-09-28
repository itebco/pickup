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
        $query = Package::query();

        if ($request->has('search') && $request->get('search') != '') {
            $search = $request->get('search');
            $query->where('package_code', 'LIKE', "%{$search}%")
                  ->orWhereHas('address', function($q) use ($search) {
                      $q->where('owner_name', 'LIKE', "%{$search}%");
                  })
                  ->orWhere('status', 'LIKE', "%{$search}%")
                  ->orWhere('method', 'LIKE', "%{$search}%");
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
        } else {
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
        $pickUpTimes = config('setting.package.pick_up_times');

        if ($currentUser->role_id == Role::CUSTOMER_ROLE_ID) {
            // If current user is a customer (role = 3), only get their information and their addresses
            $customer = $currentUser;
            $addresses = $customer->addresses;
            $customers = collect([$customer]); // Create a collection with just this user
        } else {
            // For other roles, get all customers with their addresses using eager loading
            $customers = User::where('role_id', Role::CUSTOMER_ROLE_ID)->with('addresses')->get();
            $addresses = Address::all(); // All addresses for all customers
        }

        return view('package.edit', compact('package', 'customers', 'addresses', 'currentUser', 'pickUpTimes'));
    }

    public function update(UpdatePackageRequest $request, Package $package)
    {
        $package->update($request->validated());

        return redirect()->route('packages.index')->with('success', __('package.package_updated_successfully'));
    }

    public function destroy(Package $package)
    {
        $package->delete();

        return redirect()->route('packages.index')->with('success', __('package.package_deleted_successfully'));
    }
}
