<?php

namespace App\Http\Controllers\Web\Users;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Events\User\Deleted;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\CreateUserRequest;
use App\Models\User;
use App\Repositories\Country\CountryRepository;
use App\Repositories\Role\RoleRepository;
use App\Repositories\User\UserRepository;
use App\Support\Enum\UserStatus;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{
    public function __construct(private readonly UserRepository $users)
    {
    }

    public function index(Request $request): View
    {
        $users = $this->users->paginate($perPage = 20, $request->search, $request->status);

        $statuses = ['' => __('All')] + UserStatus::lists();

        return view('user.list', compact('users', 'statuses'));
    }

    public function show(User $user): View
    {
        return view('user.view', compact('user'));
    }

    public function create(CountryRepository $countryRepository, RoleRepository $roleRepository): View
    {
        return view('user.add', [
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
        return [0 => __('Select a Country')] + $countryRepository->lists()->toArray();
    }

    public function store(CreateUserRequest $request): RedirectResponse
    {
        // When user is created by administrator, we will set his
        // status to Active by default.
        $data = $request->all() + [
            'status' => UserStatus::ACTIVE,
            'email_verified_at' => now(),
        ];

        $data['force_password_change'] = !!setting('password-change.enabled');

        if (! data_get($data, 'country_id')) {
            $data['country_id'] = null;
        }

        // Username should be updated only if it is provided.
        if (! data_get($data, 'username')) {
            $data['username'] = null;
        }

        $data['created_by'] = Auth::id();

        $this->users->create($data);

        return redirect()->route('users.index')
            ->withSuccess(__('User created successfully.'));
    }

    public function edit(User $user, CountryRepository $countryRepository, RoleRepository $roleRepository): View
    {
        return view('user.edit', [
            'edit' => true,
            'user' => $user,
            'countries' => $this->parseCountries($countryRepository),
            'roles' => $roleRepository->lists(),
            'statuses' => UserStatus::lists(),
            'socialLogins' => $this->users->getUserSocialLogins($user->id),
        ]);
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->is(auth()->user())) {
            return redirect()->route('users.index')
                ->withErrors(__('You cannot delete yourself.'));
        }

        $this->users->delete($user->id);

        event(new Deleted($user));

        return redirect()->route('users.index')
            ->withSuccess(__('User deleted successfully.'));
    }
}
