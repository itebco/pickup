@extends('layouts.app')

@section('page-title', __('package.edit_package'))
@section('page-heading', __('package.edit_package'))

@section('breadcrumbs')
    <li class="breadcrumb-item">
        <a href="{{ route('packages.index') }}">@lang('package.packages')</a>
    </li>
    <li class="breadcrumb-item active">
        @lang('package.edit_package')
    </li>
@stop

@section('styles')
    <style>
        .disabled-date {
            background-color: #dee2e6 !important;
            opacity: 0.6;
        }
        #pickup_date[readonly] {
            background-color: #fff;
            cursor: pointer;
        }
    </style>
@endsection

@section('content')
    @include('partials.messages')

    <div class="card">
        <div class="card-body">
            <form action="{{ route('packages.update', $package->id) }}" method="POST" id="package-form">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 offset-md-3">
                        @if(auth()->user()->role_id == \App\Models\Role::CUSTOMER_ROLE_ID)
                            <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                        @else
                            <div class="form-group">
                                <label for="user_id">@lang('package.labels.customer_id')</label>
                                <select name="user_id" id="user_id" class="form-control" required>
                                    <option value="">@lang('package.select_customer')</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ old('user_id', $package->user_id) == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->first_name }} {{ $customer->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="form-group">
                            <label for="address_id">@lang('package.labels.address_id')</label>
                            <select name="address_id" id="address_id" class="form-control" required>
                                <option value="">@lang('package.select_address')</option>
                                @foreach($addresses as $address)
                                    <option value="{{ $address->id }}" {{ old('address_id', $package->address_id) == $address->id ? 'selected' : '' }}>
                                        {{ $address->owner_name }} - {{ $address->state }} {{ $address->city }} {{ $address->ward }} {{ $address->room_no }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="pickup_date">@lang('package.labels.pickup_date')</label>
                            <input type="text" name="pickup_date" value="{{ old('pickup_date', $package->pickup_date) }}" id="pickup_date"
                                   class="form-control" required readonly>
                        </div>

                        <div class="form-group">
                            <label for="pickup_time">@lang('package.labels.pickup_time')</label>
                            <select name="pickup_time" id="pickup_time" class="form-control" required>
                                <option value="">@lang('package.select_pickup_time')</option>
                                @foreach($pickUpTimes as $time)
                                    <option value="{{ $time }}" {{ old('pickup_time', $package->pickup_time) == $time ? 'selected' : '' }}>
                                        {{ $time }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="quantity">@lang('package.labels.quantity')</label>
                            <input type="number" name="quantity" value="{{ old('quantity', $package->quantity) }}" id="quantity"
                                   class="form-control" required min="1" max="100000">
                        </div>

                        <div class="form-group">
                            <label for="method">@lang('package.method_label')</label>
                            <select name="method" id="method" class="form-control" required>
                                <option value="">@lang('package.select_method')</option>
                                <option value="pickup" {{ $package->method == 'pickup' ? 'selected' : '' }}>
                                    @lang('package.pickup')
                                </option>
                                <option value="delivery" {{ $package->method == 'delivery' ? 'selected' : '' }}>
                                    @lang('package.delivery')
                                </option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="status">@lang('package.status_label')</label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="">@lang('package.select_status')</option>
                                <option value="pending" {{ $package->status == 'pending' ? 'selected' : '' }}>
                                    @lang('package.pending')
                                </option>
                                <option value="done" {{ $package->status == 'done' ? 'selected' : '' }}>
                                    @lang('package.done')
                                </option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="remark">@lang('package.labels.remark')</label>
                            <textarea name="remark" id="remark" class="form-control" rows="3">{{ old('remark', $package->remark) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 offset-md-3">
                        <button type="submit" class="btn btn-primary">
                            @lang('package.update_package')
                        </button>
                        <a href="{{ route('packages.index') }}" class="btn btn-secondary">
                            @lang('app.cancel')
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop

@section('scripts')
    <script src="{{ asset('assets/js/as/btn.js') }}"></script>
    {!! JsValidator::formRequest('App\Http\Requests\Package\UpdatePackageRequest', '#package-form') !!}

    <script>
        $(document).ready(function() {
            // Initialize datepicker for pickup_date with orientation set to bottom and disabled dates
            $('#pickup_date').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true,
                orientation: 'bottom',
                beforeShowDay: function(date) {
                    // Get the day and month from the date
                    const day = String(date.getDate()).padStart(2, '0');
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const formattedDate = month + '-' + day;

                    // Get today's date for comparison
                    const today = new Date();
                    today.setHours(0, 0, 0, 0); // Set time to 00:00:00 for accurate comparison
                    const selectedDate = new Date(date);
                    selectedDate.setHours(0, 0, 0, 0); // Set time to 00:00 for accurate comparison

                    // Check if selected date is less than or equal to today
                    const isPastDate = selectedDate <= today;

                    // Check if this date is in the disableDates array
                    let isConfigDisabled = false;
                    @php
                        $disableDates = config('setting.package.disable_pickup_dates');
                    @endphp
                    @foreach($disableDates as $disableDate)
                        if (formattedDate === '{{ $disableDate }}') {
                            isConfigDisabled = true;
                        }
                    @endforeach

                    // Return the result with CSS class for disabled dates
                    if (isPastDate || isConfigDisabled) {
                        return {
                            enabled: false,
                            classes: 'disabled-date'
                        };
                    } else {
                        return {
                            enabled: true
                        };
                    }
                }
            });

            // Function to update address dropdown based on selected customer
            function updateAddressDropdown() {
                const selectedCustomerId = $('#user_id').val();

                // Clear current address options
                $('#address_id').empty();
                $('#address_id').append('<option value="">@lang('package.select_address')</option>');

                // If current user is not a customer, populate addresses based on selected customer
                @if($currentUser->role_id != \App\Models\Role::CUSTOMER_ROLE_ID)
                    if (selectedCustomerId) {
                        @foreach($customers as $customer)
                            if (selectedCustomerId == {{ $customer->id }}) {
                                @foreach($customer->addresses as $address)
                                    $('#address_id').append('<option value="{{ $address->id }}">{{ addslashes($address->owner_name . " - " . $address->state . " " . $address->city . " " . $address->ward . " " . $address->room_no) }}</option>');
                                @endforeach
                            }
                        @endforeach
                    }
                @endif
            }

            // Call the function when customer dropdown changes
            $('#user_id').on('change', function() {
                updateAddressDropdown();
            });

            // Initialize address dropdown based on current customer selection
            @if($currentUser->role_id != \App\Models\Role::CUSTOMER_ROLE_ID)
                updateAddressDropdown();
            @endif
        });
    </script>
@stop
