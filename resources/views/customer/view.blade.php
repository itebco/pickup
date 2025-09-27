@extends('layouts.app')

@section('page-title', __('customer.view_customer'))
@section('page-heading', __('customer.view_customer'))

@section('breadcrumbs')
    <li class="breadcrumb-item">
        <a href="{{ route('customers.index') }}">@lang('customer.customers')</a>
    </li>
    <li class="breadcrumb-item active">
        @lang('customer.view_customer')
    </li>
@stop

@section('content')
    @include('partials.messages')

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 offset-md-3">
                    <div class="form-group">
                        <label for="first_name">@lang('customer.first_name')</label>
                        <input type="text" name="first_name" value="{{ $customer->first_name }}" id="first_name"
                               class="form-control" readonly>
                    </div>

                    <div class="form-group">
                        <label for="last_name">@lang('customer.last_name')</label>
                        <input type="text" name="last_name" value="{{ $customer->last_name }}" id="last_name"
                               class="form-control" readonly>
                    </div>

                    <div class="form-group">
                        <label for="email">@lang('customer.email')</label>
                        <input type="email" name="email" value="{{ $customer->email }}" id="email"
                               class="form-control" readonly>
                    </div>

                    <div class="form-group">
                        <label for="phone">@lang('customer.phone')</label>
                        <input type="text" name="phone" value="{{ $customer->phone }}" id="phone"
                               class="form-control" readonly>
                    </div>

                    <div class="form-group">
                        <label for="status">@lang('customer.status')</label>
                        <input type="text" name="status" value="{{ $customer->status->name() }}" id="status"
                               class="form-control" readonly>
                    </div>

                    <div class="form-group">
                        <label for="role">@lang('customer.role')</label>
                        <input type="text" name="role" value="{{ $customer->role->display_name }}" id="role"
                               class="form-control" readonly>
                    </div>

                    <div class="form-group">
                        <label for="created_at">@lang('customer.created_at')</label>
                        <input type="text" name="created_at" value="{{ $customer->created_at->format('Y-m-d H:i:s') }}" id="created_at"
                               class="form-control" readonly>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 offset-md-3">
                    <a href="{{ route('customers.index') }}" class="btn btn-secondary">
                        @lang('customer.back')
                    </a>
                    <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-primary">
                        @lang('customer.edit')
                    </a>
                    @if(auth()->user()->role_id == 1 || (auth()->user()->role_id == 2 && $customer->created_by == auth()->user()->id))
                    <a href="{{ route('customers.destroy', $customer->id) }}"
                       class="btn btn-danger"
                       title="@lang('customer.delete_customer')"
                       data-toggle="tooltip"
                       data-placement="top"
                       data-method="DELETE"
                       data-confirm-title="@lang('customer.please_confirm')"
                       data-confirm-text="@lang('customer.are_you_sure_delete_customer')"
                       data-confirm-delete="@lang('customer.yes_delete_it')">
                        @lang('customer.delete')
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop
