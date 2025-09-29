<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\User;
use App\Http\Requests\Address\CreateAddressRequest;
use App\Http\Requests\Address\UpdateAddressRequest;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AddressController extends Controller
{
    public function index(Request $request)
    {
        $query = Address::query();

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
        $customers = User::where('role_id', Role::CUSTOMER_ROLE_ID)->get(); // Only customers
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
        return view('address.view', compact('address'));
    }

    public function edit(Address $address)
    {
        $customers = User::where('role_id', Role::CUSTOMER_ROLE_ID)->get(); // Only customers
        return view('address.edit', compact('address', 'customers'));
    }

    public function update(UpdateAddressRequest $request, Address $address)
    {
        $address->update($request->validated());

        return redirect()->route('addresses.index')->with('success', __('address.address_updated_successfully'));
    }

    public function destroy(Address $address)
    {
        $address->delete();

        return redirect()->route('addresses.index')->with('success', __('address.address_deleted_successfully'));
    }
}
