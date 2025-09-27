@extends('layouts.app')

@section('page-title', __('View Customer'))
@section('page-heading', __('View Customer'))

@section('breadcrumbs')
    <li class="breadcrumb-item">
        <a href="{{ route('customers.index') }}">@lang('Customers')</a>
    </li>
    <li class="breadcrumb-item active">
        @lang('View Customer')
    </li>
@stop

@section('content')
    @include('partials.messages')

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 offset-md-3">
                    <div class="form-group">
                        <label for="first_name">@lang('First Name')</label>
                        <input type="text" name="first_name" value="{{ $customer->first_name }}" id="first_name"
                               class="form-control" readonly>
                    </div>

                    <div class="form-group">
                        <label for="last_name">@lang('Last Name')</label>
                        <input type="text" name="last_name" value="{{ $customer->last_name }}" id="last_name"
                               class="form-control" readonly>
                    </div>

                    <div class="form-group">
                        <label for="email">@lang('Email')</label>
                        <input type="email" name="email" value="{{ $customer->email }}" id="email"
                               class="form-control" readonly>
                    </div>

                    <div class="form-group">
                        <label for="phone">@lang('Phone')</label>
                        <input type="text" name="phone" value="{{ $customer->phone }}" id="phone"
                               class="form-control" readonly>
                    </div>

                    <div class="form-group">
                        <label for="status">@lang('Status')</label>
                        <input type="text" name="status" value="{{ $customer->status->name() }}" id="status"
                               class="form-control" readonly>
                    </div>

                    <div class="form-group">
                        <label for="role">@lang('Role')</label>
                        <input type="text" name="role" value="{{ $customer->role->display_name }}" id="role"
                               class="form-control" readonly>
                    </div>

                    <div class="form-group">
                        <label for="created_at">@lang('Created At')</label>
                        <input type="text" name="created_at" value="{{ $customer->created_at->format('M d, Y H:i') }}" id="created_at"
                               class="form-control" readonly>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 offset-md-3">
                    <a href="{{ route('customers.index') }}" class="btn btn-secondary">
                        @lang('Back')
                    </a>
                    <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-primary">
                        @lang('Edit')
                    </a>
                </div>
            </div>
        </div>
    </div>
@stop