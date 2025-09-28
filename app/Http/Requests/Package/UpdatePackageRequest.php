<?php

namespace App\Http\Requests\Package;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Package;

class UpdatePackageRequest extends FormRequest
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
        $packageId = $this->route('package');
        $pickUpTimes = config('setting.package.pick_up_times');
        $pickUpTimesString = implode(',', $pickUpTimes);

        return [
            'user_id' => 'required|exists:users,id',
            'address_id' => 'required|exists:addresses,id',
            'package_code' => 'nullable|string|max:255|unique:packages,package_code,' . $packageId,
            'pickup_date' => 'required|date',
            'pickup_time' => 'required|in:' . $pickUpTimesString,
            'quantity' => 'required|integer|min:1',
            'method' => 'required|in:pickup,delivery',
            'status' => 'required|in:pending,done',
            'remark' => 'nullable|string|max:500',
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
            'user_id.required' => __('package.validation.customer_id_required'),
            'user_id.exists' => __('package.validation.customer_id_exists'),
            'address_id.required' => __('package.validation.address_id_required'),
            'address_id.exists' => __('package.validation.address_id_exists'),
            'pickup_date.required' => __('package.validation.pickup_date_required'),
            'pickup_date.date' => __('package.validation.pickup_date_date'),
            'pickup_time.required' => __('package.validation.pickup_time_required'),
            'pickup_time.date_format' => __('package.validation.pickup_time_format'),
            'quantity.required' => __('package.validation.quantity_required'),
            'quantity.integer' => __('package.validation.quantity_integer'),
            'quantity.min' => __('package.validation.quantity_min'),
            'method.required' => __('package.validation.method_required'),
            'method.in' => __('package.validation.method_in'),
            'status.required' => __('package.validation.status_required'),
            'status.in' => __('package.validation.status_in'),
            'package_code.unique' => __('package.validation.package_code_unique'),
        ];
    }
}
