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
                            <th style="width: 200px">@lang('Action')</th>
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
                                <td>{{ $customer->created_at->format('M d, Y') }}</td>
                                <td>
                                    <a href="{{ route('customers.edit', $customer->id) }}"
                                       class="btn btn-primary btn-sm">
                                        <i class="fas fa-edit"></i> @lang('Edit')
                                    </a>
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
