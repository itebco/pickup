@extends('layouts.app')

@section('page-title', __('Customers'))
@section('page-heading', __('Customers'))

@section('breadcrumbs')
    <li class="breadcrumb-item active">
        @lang('Customers')
    </li>
@stop

@section('content')
    @include('partials.messages')

    <div class="card">
        <div class="card-body">
            <div class="row mb-3 pb-3">
                <div class="col-lg-12">
                    <div class="float-right">
                        <a href="{{ route('customers.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> @lang('Add Customer')
                        </a>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered" id="customers-table">
                    <thead>
                        <tr>
                            <th>@lang('ID')</th>
                            <th>@lang('Name')</th>
                            <th>@lang('Email')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Created')</th>
                            <th style="width: 156px">@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                            <tr>
                                <td>{{ $customer->id }}</td>
                                <td>{{ $customer->present()->nameOrEmail }}</td>
                                <td>{{ $customer->email }}</td>
                                <td>
                                    <span class="badge badge-{{ $customer->present()->labelClass }}">
                                        {{ $customer->status->name }}
                                    </span>
                                </td>
                                <td>{{ $customer->created_at->format('Y-m-d H:i:s') }}</td>
                                <td>
                                    <a href="{{ route('customers.edit', $customer->id) }}"
                                       class="btn btn-primary btn-sm">
                                        <i class="fas fa-edit"></i> @lang('Edit')
                                    </a>
                                    @if(auth()->user()->role_id == 1 || (auth()->user()->role_id == 2 && $customer->created_by == auth()->user()->id))
                                    <a href="{{ route('customers.destroy', $customer->id) }}"
                                       class="btn btn-danger btn-sm"
                                       title="@lang('Delete Customer')"
                                       data-toggle="tooltip"
                                       data-placement="top"
                                       data-method="DELETE"
                                       data-confirm-title="@lang('Please Confirm')"
                                       data-confirm-text="@lang('Are you sure that you want to delete this customer?')"
                                       data-confirm-delete="@lang('Yes, delete it!')">
                                        <i class="fas fa-trash"></i> @lang('Delete')
                                    </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">@lang('No customers found.')</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $customers->render() }}
        </div>
    </div>
@stop
