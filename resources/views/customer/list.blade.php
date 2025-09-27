@extends('layouts.app')

@section('page-title', __('customer.customers'))
@section('page-heading', __('customer.customers'))

@section('breadcrumbs')
    <li class="breadcrumb-item active">
        @lang('customer.customers')
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
                            <i class="fas fa-plus"></i> @lang('customer.add_customer')
                        </a>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered" id="customers-table">
                    <thead>
                        <tr>
                            <th>@lang('customer.id')</th>
                            <th>@lang('customer.name')</th>
                            <th>@lang('customer.email')</th>
                            <th>@lang('customer.status')</th>
                            <th>@lang('customer.created')</th>
                            <th style="width: 156px">@lang('customer.action')</th>
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
                                        <i class="fas fa-edit"></i> @lang('customer.edit')
                                    </a>
                                    @if(auth()->user()->role_id == 1 || (auth()->user()->role_id == 2 && $customer->created_by == auth()->user()->id))
                                    <a href="{{ route('customers.destroy', $customer->id) }}"
                                       class="btn btn-danger btn-sm"
                                       title="@lang('customer.delete_customer')"
                                       data-toggle="tooltip"
                                       data-placement="top"
                                       data-method="DELETE"
                                       data-confirm-title="@lang('customer.please_confirm')"
                                       data-confirm-text="@lang('customer.are_you_sure_delete_customer')"
                                       data-confirm-delete="@lang('customer.yes_delete_it')">
                                        <i class="fas fa-trash"></i> @lang('customer.delete')
                                    </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">@lang('customer.no_customers_found')</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $customers->render() }}
        </div>
    </div>
@stop
