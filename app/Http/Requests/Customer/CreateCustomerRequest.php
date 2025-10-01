<?php

namespace App\Http\Requests\Customer;

use App\Http\Requests\Request;

class CreateCustomerRequest extends Request
{
    public function rules(): array
    {
        $rules = [
            'email' => 'required|email|unique:users,email',
            'username' => 'nullable|string|min:2|max:255|unique:users,username',
            'password' => 'required|min:8|confirmed',
            'birthday' => 'nullable|date',
            'role_id' => 'required|exists:roles,id',
            'verified' => 'boolean',
        ];

        if ($this->get('country_id')) {
            $rules += ['country_id' => 'exists:countries,id'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'email.required' => __('validation.required', ['attribute' => __('customer.email')]),
            'email.email' => __('validation.email', ['attribute' => __('customer.email')]),
            'email.unique' => __('validation.unique', ['attribute' => __('customer.email')]),
            'username.string' => __('validation.string', ['attribute' => __('customer.username')]),
            'username.min' => __('validation.min.string', ['attribute' => __('customer.username'), 'min' => 2]),
            'username.max' => __('validation.max.string', ['attribute' => __('customer.username'), 'max' => 25]),
            'username.unique' => __('validation.unique', ['attribute' => __('customer.username')]),
            'password.required' => __('validation.required', ['attribute' => __('customer.password')]),
            'password.min' => __('validation.min.string', ['attribute' => __('customer.password'), 'min' => 8]),
            'password.confirmed' => __('validation.confirmed', ['attribute' => __('customer.password')]),
            'birthday.date' => __('validation.date', ['attribute' => __('customer.birthday')]),
            'role_id.required' => __('validation.required', ['attribute' => __('customer.role')]),
            'role_id.exists' => __('validation.exists', ['attribute' => __('customer.role')]),
            'verified.boolean' => __('validation.boolean', ['attribute' => __('customer.verified')]),
            'country_id.exists' => __('validation.exists', ['attribute' => __('customer.country')]),
        ];
    }
}
