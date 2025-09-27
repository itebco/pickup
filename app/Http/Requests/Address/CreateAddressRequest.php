<?php

namespace App\Http\Requests\Address;

use Illuminate\Foundation\Http\FormRequest;

class CreateAddressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_id' => 'required|exists:users,id',
            'owner_name' => 'required|string|max:255',
            'tel' => 'required|string|max:20',
            'post_code' => 'required|string|max:20',
            'state' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'ward' => 'required|string|max:255',
            'type' => 'required|in:mansion,apartment',
            'room_no' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get custom messages for validation errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'user_id.required' => __('address.validation.customer_id_required'),
            'user_id.exists' => __('address.validation.customer_id_exists'),
            'owner_name.required' => __('address.validation.owner_name_required'),
            'tel.required' => __('address.validation.tel_required'),
            'post_code.required' => __('address.validation.post_code_required'),
            'state.required' => __('address.validation.state_required'),
            'city.required' => __('address.validation.city_required'),
            'ward.required' => __('address.validation.ward_required'),
            'type.required' => __('address.validation.type_required'),
            'type.in' => __('address.validation.type_in'),
        ];
    }
}
