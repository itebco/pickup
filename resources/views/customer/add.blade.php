@extends('layouts.app')

@section('page-title', __('customer.add_customer'))
@section('page-heading', __('customer.add_customer'))

@section('breadcrumbs')
    <li class="breadcrumb-item">
        <a href="{{ route('customers.index') }}">@lang('customer.customers')</a>
    </li>
    <li class="breadcrumb-item active">
        @lang('customer.add_customer')
    </li>
@stop

@section('content')
    @include('partials.messages')

    <div class="card">
        <div class="card-body">
            <form action="{{ route('customers.store') }}" method="POST" id="customer-form">
                @csrf

                <div class="row">
                    <div class="col-md-6 offset-md-3">
                        <div class="form-group">
                            <input type="hidden" name="role_id" value="3" id="role_id" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="first_name">@lang('customer.first_name')</label>
                            <input type="text" name="first_name" value="{{ old('first_name') }}" id="first_name"
                                   class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="last_name">@lang('customer.last_name')</label>
                            <input type="text" name="last_name" value="{{ old('last_name') }}" id="last_name"
                                   class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="username">@lang('customer.username')</label>
                            <input type="text" name="username" value="{{ old('username') }}" id="username"
                                   class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="email">@lang('customer.email')</label>
                            <input type="email" name="email" value="{{ old('email') }}" id="email"
                                   class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="password">@lang('customer.password')</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation">@lang('customer.confirm_password')</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="phone">@lang('customer.phone')</label>
                            <input type="text" name="phone" value="{{ old('phone') }}" id="phone"
                                   class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="status">@lang('customer.status')</label>
                            <select name="status" id="status" class="form-control" required>
                                @foreach(\App\Support\Enum\UserStatus::lists() as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 offset-md-3">
                        <button type="submit" class="btn btn-primary">
                            @lang('customer.add_customer')
                        </button>
                        <a href="{{ route('customers.index') }}" class="btn btn-secondary">
                            @lang('customer.cancel')
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop

@section('scripts')
    <script src="{{ asset('assets/js/as/btn.js') }}"></script>
    {!! JsValidator::formRequest('App\Http\Requests\Customer\CreateCustomerRequest', '#customer-form') !!}
@stop
