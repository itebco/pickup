@extends('layouts.app')

@section('page-title', __('address.view_address'))
@section('page-heading', __('address.view_address'))

@section('breadcrumbs')
    <li class="breadcrumb-item">
        <a href="{{ route('addresses.index') }}">@lang('address.addresses')</a>
    </li>
    <li class="breadcrumb-item active">
        @lang('address.view_address')
    </li>
@stop

@section('content')
    @include('partials.messages')

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 offset-md-3">
                    <div class="form-group">
                        <label for="customer_id">@lang('address.labels.customer_id')</label>
                        <input type="text" name="customer_id" value="{{ $address->customer ? $address->customer->first_name . ' ' . $address->customer->last_name : '' }}" id="customer_id"
                               class="form-control" readonly>
                    </div>

                    <div class="form-group">
                        <label for="owner_name">@lang('address.labels.owner_name')</label>
                        <input type="text" name="owner_name" value="{{ $address->owner_name }}" id="owner_name"
                               class="form-control" readonly>
                    </div>

                    <div class="form-group">
                        <label for="tel">@lang('address.labels.tel')</label>
                        <input type="text" name="tel" value="{{ $address->tel }}" id="tel"
                               class="form-control" readonly>
                    </div>

                    <div class="form-group">
                        <label for="post_code">@lang('address.labels.post_code')</label>
                        <input type="text" name="post_code" value="{{ $address->post_code }}" id="post_code"
                               class="form-control" readonly>
                    </div>

                    <div class="form-group">
                        <label for="state">@lang('address.labels.state')</label>
                        <input type="text" name="state" value="{{ $address->state }}" id="state"
                               class="form-control" readonly>
                    </div>

                    <div class="form-group">
                        <label for="city">@lang('address.labels.city')</label>
                        <input type="text" name="city" value="{{ $address->city }}" id="city"
                               class="form-control" readonly>
                    </div>

                    <div class="form-group">
                        <label for="ward">@lang('address.labels.ward')</label>
                        <input type="text" name="ward" value="{{ $address->ward }}" id="ward"
                               class="form-control" readonly>
                    </div>

                    <div class="form-group">
                        <label for="type">@lang('address.type_label')</label>
                        <input type="text" name="type" value="{{ $address->translated_type }}" id="type"
                               class="form-control" readonly>
                    </div>

                    <div class="form-group">
                        <label for="room_no">@lang('address.labels.room_no')</label>
                        <input type="text" name="room_no" value="{{ $address->room_no }}" id="room_no"
                               class="form-control" readonly>
                    </div>

                    <div class="form-group">
                        <label for="created_at">@lang('address.created_at')</label>
                        <input type="text" name="created_at" value="{{ $address->created_at->format('Y-m-d H:i:s') }}" id="created_at"
                               class="form-control" readonly>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 offset-md-3">
                    <a href="{{ route('addresses.index') }}" class="btn btn-secondary">
                        @lang('app.back')
                    </a>
                    <a href="{{ route('addresses.edit', $address->id) }}" class="btn btn-primary">
                        @lang('app.edit')
                    </a>
                    @if(auth()->user()->role_id == 1 || (auth()->user()->role_id == 2 && $address->created_by == auth()->user()->id))
                    <a href="{{ route('addresses.destroy', $address->id) }}"
                       class="btn btn-danger"
                       title="@lang('address.delete_address')"
                       data-toggle="tooltip"
                       data-placement="top"
                       data-method="DELETE"
                       data-confirm-title="@lang('app.please_confirm')"
                       data-confirm-text="@lang('app.are_you_sure')"
                       data-confirm-delete="@lang('app.yes_delete_it')">
                        @lang('app.delete')
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop

