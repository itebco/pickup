@extends('layouts.app')

@section('page-title', __('address.edit_address'))
@section('page-heading', __('address.edit_address'))

@section('breadcrumbs')
    <li class="breadcrumb-item">
        <a href="{{ route('addresses.index') }}">@lang('address.addresses')</a>
    </li>
    <li class="breadcrumb-item active">
        @lang('address.edit_address')
    </li>
@stop

@section('content')
    @include('partials.messages')

    <div class="card">
        <div class="card-body">
            <form action="{{ route('addresses.update', $address->id) }}" method="POST" id="address-form">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 offset-md-3">
                        @if(auth()->user()->role_id == \App\Models\Role::CUSTOMER_ROLE_ID)
                            <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                        @else
                            <div class="form-group">
                                <label for="user_id">@lang('address.labels.customer_id')</label>
                                <select name="user_id" id="user_id" class="form-control" required>
                                    <option value="">@lang('address.select_customer')</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ old('user_id', $address->user_id) == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->first_name }} {{ $customer->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="form-group">
                            <label for="owner_name">@lang('address.labels.owner_name')</label>
                            <input type="text" name="owner_name" value="{{ old('owner_name', $address->owner_name) }}" id="owner_name"
                                   class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="tel">@lang('address.labels.tel')</label>
                            <input type="text" name="tel" value="{{ old('tel', $address->tel) }}" id="tel"
                                   class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="post_code">@lang('address.labels.post_code')</label>
                            <input type="text" name="post_code" value="{{ old('post_code', $address->post_code) }}" id="post_code"
                                   class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="state">@lang('address.labels.state')</label>
                            <input type="text" name="state" value="{{ old('state', $address->state) }}" id="state"
                                   class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="city">@lang('address.labels.city')</label>
                            <input type="text" name="city" value="{{ old('city', $address->city) }}" id="city"
                                   class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="ward">@lang('address.labels.ward')</label>
                            <input type="text" name="ward" value="{{ old('ward', $address->ward) }}" id="ward"
                                   class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="room_no">@lang('address.labels.room_no')</label>
                            <input type="text" name="room_no" value="{{ old('room_no', $address->room_no) }}" id="room_no"
                                   class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="type">@lang('address.type_label')</label>
                            <select name="type" id="type" class="form-control" required>
                                <option value="">@lang('address.select_type')</option>
                                <option value="mansion" {{ $address->type == 'mansion' ? 'selected' : '' }}>
                                    @lang('address.mansion')
                                </option>
                                <option value="apartment" {{ $address->type == 'apartment' ? 'selected' : '' }}>
                                    @lang('address.apartment')
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 offset-md-3">
                        <button type="submit" class="btn btn-primary">
                            @lang('address.update_address')
                        </button>
                        <a href="{{ route('addresses.index') }}" class="btn btn-secondary">
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
    {!! JsValidator::formRequest('App\Http\Requests\Address\UpdateAddressRequest', '#address-form') !!}
@stop
