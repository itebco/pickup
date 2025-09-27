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

        <form action="" method="GET" id="customers-form" class="pb-2 mb-3 border-bottom-light">
            <div class="row my-3 flex-md-row flex-column-reverse">
                <div class="col-md-4 mt-md-0 mt-2">
                    <div class="input-group custom-search-form">
                        <input type="text"
                               class="form-control input-solid"
                               name="search"
                               value="{{ Request::get('search') }}"
                               placeholder="@lang('Search for customers...')">

                            <span class="input-group-append">
                                @if (Request::has('search') && Request::get('search') != '')
                                    <a href="{{ route('customers.index') }}"
                                           class="btn btn-light d-flex align-items-center text-muted"
                                           role="button">
                                        <i class="fas fa-times"></i>
                                    </a>
                                @endif
                                <button class="btn btn-light" type="submit" id="search-customers-btn">
                                    <i class="fas fa-search text-muted"></i>
                                </button>
                            </span>
                    </div>
                </div>

                <div class="col-md-2 mt-2 mt-md-0">
                    <select name="status" id="status" class="form-control input-solid">
                        <option value="">@lang('All Statuses')</option>
                        @foreach($statuses as $key => $value)
                            <option value="{{ $key }}" {{ Request::get('status') == $key ? 'selected' : '' }}>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <a href="{{ route('customers.create') }}" class="btn btn-primary btn-rounded float-right">
                        <i class="fas fa-plus mr-2"></i>
                        @lang('customer.add_customer')
                    </a>
                </div>
            </div>
        </form>

        <div class="table-responsive" id="customers-table-wrapper" style="min-height: 200px;">
            <table class="table table-borderless table-striped">
                <thead>
                <tr>
                    <th></th>
                    <th class="min-width-80">@lang('customer.name')</th>
                    <th class="min-width-100">@lang('customer.email')</th>
                    <th class="min-width-80">@lang('customer.created')</th>
                    <th class="min-width-80">@lang('customer.status')</th>
                    <th class="text-center min-width-150">@lang('app.action')</th>
                </tr>
                </thead>
                <tbody>
                    @if (count($customers))
                        @foreach ($customers as $customer)
                            @include('customer.partials.row')
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6"><em>@lang('customer.no_customers_found')</em></td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

{!! $customers->render() !!}

@stop

@section('scripts')
    <script>
        $("#status").change(function () {
            $("#customers-form").submit();
        });
    </script>
@stop
