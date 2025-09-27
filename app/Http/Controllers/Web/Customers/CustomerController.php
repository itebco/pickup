<?php

namespace App\Http\Controllers\Web\Customers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\CreateCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use App\Models\User;
use App\Repositories\Country\CountryRepository;
use App\Repositories\Role\RoleRepository;
use App\Repositories\User\UserRepository;
use App\Support\Enum\UserStatus;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function __construct(private readonly UserRepository $users)
    {
        $this->middleware(function ($request, $next) {
            $user = Auth::check() ? Auth::user() : null;

            // Check if user has permission to access customer management
            if (!$user) {
                abort(403, 'Unauthorized');
            }

            // Allow access based on permission or role:
            // Has 'customers.manage' permission - full access
            // Role 1 (admin) - full access
            // Role 2 (user) - limited access to their own created customers
            // Other roles - no access
            if ($user->hasPermission('customers.manage')) {
                return $next($request);
            }

            abort(404); // Other roles get 404
        });
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $currentUser = Auth::user();
        $roleFilter = 3; // Always filter for role 3 (customers)
        $createdByFilter = null;

        // If current user is role 2 (user), only show customers created by them
        if ($currentUser && $currentUser->role_id == 2) {
            $createdByFilter = $currentUser->id;
        }

        $customers = $this->users->paginate($perPage = 20, $request->search, $request->status, $roleFilter, $createdByFilter);

        $statuses = ['' => __('customer.all')] + UserStatus::lists();

        return view('customer.list', compact('customers', 'statuses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(CountryRepository $countryRepository, RoleRepository $roleRepository): View
    {
        return view('customer.add', [
            'countries' => $this->parseCountries($countryRepository),
            'roles' => $roleRepository->lists(),
            'statuses' => UserStatus::lists(),
        ]);
    }

    /**
     * Parse countries into an array that also has a blank
     * item as first element, which will allow users to
     * leave the country field unpopulated.
     */
    private function parseCountries(CountryRepository $countryRepository): array
    {
        return [0 => __('customer.select_country')] + $countryRepository->lists()->toArray();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateCustomerRequest $request): RedirectResponse
    {
        // When customer is created by administrator, we will set their
        // status to Active by default and role to 3 for customer role.
        $data = $request->all() + [
            'status' => UserStatus::ACTIVE,
            'email_verified_at' => now(),
            'role_id' => 3, // Assign role_id 3 for customer role
        ];

        $data['force_password_change'] = !!setting('password-change.enabled');

        if (! data_get($data, 'country_id')) {
            $data['country_id'] = null;
        }

        // Username should be updated only if it is provided.
        if (! data_get($data, 'username')) {
            $data['username'] = null;
        }

        // Set created_by to current user's ID
        $data['created_by'] = Auth::id();

        $this->users->create($data);

        return redirect()->route('customers.index')
            ->withSuccess(__('customer.created_successfully'));
    }

    /**
     * Display the specified resource.
     */
    public function show(User $customer): View
    {
        if (!$this->canEditCustomer($customer)) {
            abort(403, __('customer.no_permission_edit'));
        }

        return view('customer.view', compact('customer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(
        User $customer,
        CountryRepository $countryRepository,
        RoleRepository $roleRepository
    ): View {
        if (!$this->canEditCustomer($customer)) {
            abort(403, __('customer.no_permission_edit'));
        }

        return view('customer.edit', [
            'edit' => true,
            'customer' => $customer,
            'countries' => $this->parseCountries($countryRepository),
            'roles' => $roleRepository->lists(),
            'statuses' => UserStatus::lists(),
            'socialLogins' => $this->users->getUserSocialLogins($customer->id),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerRequest $request, User $customer): RedirectResponse
    {
        if (!$this->canEditCustomer($customer)) {
            abort(403, __('customer.no_permission_edit'));
        }

        $data = $request->all();

        // Ensure role_id remains 3 for customers and cannot be changed through the form
        $data['role_id'] = 3;

        if (! data_get($data, 'country_id')) {
            $data['country_id'] = null;
        }

        // Username should be updated only if it is provided.
        if (! data_get($data, 'username')) {
            $data['username'] = null;
        }

        // If password is provided, hash it
        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        } else {
            unset($data['password']);
        }

        $this->users->update($customer->id, $data);

        return redirect()->route('customers.index')
            ->withSuccess(__('customer.updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $customer): RedirectResponse
    {
        $currentUser = Auth::user();

        // Check if current user is trying to delete themselves
        if ($customer->id === $currentUser->id) {
            return redirect()->route('customers.index')
                ->withErrors(__('customer.cannot_delete_yourself'));
        }

        if (!$this->canEditCustomer($customer)) {
            abort(403, __('customer.no_permission_delete'));
        }

        $this->users->delete($customer->id);

        return redirect()->route('customers.index')
            ->withSuccess(__('customer.deleted_successfully'));
    }

    /**
     * Determine if the current user can edit the given customer.
     * Role 1 (admin) can delete any customer
     * Role 2 (user) can only delete customers they created
     */
    public function canEditCustomer(User $customer): bool
    {
        $currentUser = Auth::user();

        // Role 1 (admin) can edit any customer
        if ($currentUser->role_id == 1) {
            return true;
        }

        // Role 2 (user) can only edit customers they created
        if ($currentUser->role_id == 2 && $customer->created_by == $currentUser->id) {
            return true;
        }

        return false;
    }
}
