<?php

namespace App\Http\Requests\Customer;

use Illuminate\Validation\Rule;
use App\Http\Requests\Request;
use App\Support\Enum\UserStatus;

class UpdateCustomerRequest extends Request
{
    public function rules(): array
    {
        // $user = $this->user();
        $user = $this->route('customer');

        return [
            'email' => 'email|unique:users,email,'.$user->id.',id',
            'username' => 'nullable|string|min:2|max:255|unique:users,username,'.$user->id.',id',
            'password' => 'nullable|min:8|confirmed',
            'birthday' => 'nullable|date',
            'role_id' => 'exists:roles,id',
            'country_id' => 'exists:countries,id',
            'status' => Rule::in(array_keys(UserStatus::lists())),
        ];
    }

    public function messages(): array
    {
        return [
            'email.email' => __('validation.email', ['attribute' => __('customer.email')]),
            'email.unique' => __('validation.unique', ['attribute' => __('customer.email')]),
            'username.string' => __('validation.string', ['attribute' => __('customer.username')]),
            'username.min' => __('validation.min.string', ['attribute' => __('customer.username'), 'min' => 2]),
            'username.max' => __('validation.max.string', ['attribute' => __('customer.username'), 'max' => 25]),
            'username.unique' => __('validation.unique', ['attribute' => __('customer.username')]),
            'password.min' => __('validation.min.string', ['attribute' => __('customer.password'), 'min' => 8]),
            'password.confirmed' => __('validation.confirmed', ['attribute' => __('customer.password')]),
            'birthday.date' => __('validation.date', ['attribute' => __('customer.birthday')]),
            'role_id.exists' => __('validation.exists', ['attribute' => __('customer.role')]),
            'country_id.exists' => __('validation.exists', ['attribute' => __('customer.country')]),
            'status.in' => __('validation.in', ['attribute' => __('customer.status')]),
        ];
    }
}
