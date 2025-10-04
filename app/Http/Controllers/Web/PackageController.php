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
                abort(403, __('package.unauthorized_to_edit_package'));
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
                abort(403, __('package.unauthorized_to_delete_package'));
            }
        }

        $package->delete();

        return redirect()->route('packages.index')->with('success', __('package.package_deleted_successfully'));
    }

    public function exportSelectedPackages(Request $request)
    {
        $currentUser = Auth::user();

        // Check if user is admin (role_id = 1)
        if ($currentUser->role_id != Role::ADMIN_ROLE_ID) {
            abort(403, __('package.unauthorized_to_export_packages'));
        }

        $packageIds = $request->input('pids');
        $packageIds = explode(',', base64_decode($packageIds));

        // Validate that package_ids is an array
        if (!is_array($packageIds) || empty($packageIds)) {
            return redirect()->back()->with('error', __('package.no_packages_selected_for_export'));
        }

        // Get only the packages with the specified IDs
        $packages = Package::with(['address', 'creator'])
                            ->whereIn('id', $packageIds)
                            ->orderBy('id', 'desc')
                            ->get();

        // Extract headers from template
        $templatePath = app_path('Templates/export-package.csv');
        $templateContent = file_get_contents($templatePath);
        $templateLines = explode("\n", $templateContent);
        $templateHeaders = str_getcsv($templateLines[0]); // First line contains headers

        $callback = function() use ($packages, $templateHeaders) {
            $file = fopen('php://output', 'w');
            // Set UTF-8 BOM for proper Japanese character display in Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Output the template headers
            fputcsv($file, $templateHeaders);

            // Add data rows directly without reading headers from template
            foreach ($packages as $package) {
                $pickUpTimeForm = explode('-', $package->pickup_time)[0];
                $pickUpTimeTo = explode('-', $package->pickup_time)[1] ?? '';
                fputcsv($file, [
                    '1', // A: 伝票区分 - Voucher type
                    '1010', // B: 依頼店（精算店）コード - Store code
                    '18228216005', // C: 荷送人コード（顧客)枝番込 - Sender code
                    $package->quantity, // D: 総個数 - Total quantity
                    '', // E: 代引金 - Cash on delivery amount
                    '', // F: 代引決済区分 - COD payment type
                    '', // G: 保険金 - Insurance amount
                    '', // H: 書込み金 - Writing fee
                    '', // I: 立替金 - Advance payment
                    '1751000', // J: 依頼主コード　枝番込 - Requestor code
                    '0', // K: 便種 - Shipping type
                    $package->pickup_date, // L: 集荷予定日付 - Pickup date
                    $pickUpTimeForm, // M: 集荷予定時刻FROM - Pickup time FROM
                    $pickUpTimeTo, // N: 集荷予定時刻TO - Pickup time TO
                    $package->address->post_code, // O: 荷送人郵便番号 - Sender postal code
                    $package->address->tel, // P: 荷送人電話番号 - Sender phone
                    $package->address->state . ' ' . $package->address->city . ' ' . $package->address->ward . ' ' . $package->address->room_no, // Q: 荷送人住所１～３ - Sender address
                    $package->address->owner_name, // R: 荷送人名称１～３ - Sender name
                    '2860117', // S: 荷受人郵便番号 - Recipient postal code
                    '344006368', // T: 荷受人電話番号 - Recipient phone
                    '', // U: 荷受人住所１～３ - Recipient address
                    '', // V: 荷受人名称１～３ - Recipient name
                    '', // W: 注文主郵便番号 - Orderer postal code
                    '', // X: 注文主電話番号 - Orderer phone
                    '', // Y: 注文主住所１～３ - Orderer address
                    '', // Z: 注文主名称１～３ - Orderer name
                    '', // AA: 送り状編集１～３ - Waybill edit
                    '', // AB: 配達指定日付 - Delivery date
                    '', // AC: 配達指定時刻 - Delivery time
                    '', // AD: 営止着店（精算店）コード - Delivery store code
                    $package->package_code, // AE: 顧客管理番号 - Customer management number
                    $package->remark, // AF: 記事欄1 - Note 1
                    '' => config('setting.prefecture_mapping')[$package->address->state] ?? '', // AG: 記事欄2 - Note 2
                    '', // AH: 記事欄3 - Note 3
                    '', // AI: 記事欄4 - Note 4
                    '', // AJ: 記事欄5 - Note 5
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="packages_export_' . date('Y-m-d_H-i-s') . '.csv"',
        ]);
    }

    public function showSelectedPackages(Request $request)
    {
        $currentUser = Auth::user();

        // Check if user is admin (role_id = 1)
        if ($currentUser->role_id != Role::ADMIN_ROLE_ID) {
            abort(403, __('package.unauthorized_to_view_packages'));
        }

        $packageIds = $request->input('pids');
        $packageIds = explode(',', base64_decode($packageIds));

        // Validate that package_ids is an array
        if (!is_array($packageIds) || empty($packageIds)) {
            return redirect()->back()->with('error', __('package.no_packages_selected_for_view'));
        }

        // Get only the packages with the specified IDs
        $packages = Package::with(['address', 'creator'])
                            ->whereIn('id', $packageIds)
                            ->orderBy('id', 'desc')
                            ->get();

        return view('package.preview', compact('packages'));
    }
}
