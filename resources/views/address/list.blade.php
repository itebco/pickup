@extends('layouts.app')

@section('page-title', __('address.addresses'))
@section('page-heading', __('address.addresses'))

@section('breadcrumbs')
    <li class="breadcrumb-item active">
        @lang('address.addresses')
    </li>
@stop

@section('content')

@include('partials.messages')

<div class="card">
    <div class="card-body">

        <form action="" method="GET" id="addresses-form" class="pb-2 mb-3 border-bottom-light">
            <div class="row my-3 flex-md-row flex-column-reverse">
                <div class="col-md-6 mt-md-0 mt-2">
                    <div class="input-group custom-search-form">
                        <input type="text"
                               class="form-control input-solid"
                               name="search"
                               value="{{ Request::get('search') }}"
                               placeholder="@lang('Search for addresses...')">

                            <span class="input-group-append">
                                @if (Request::has('search') && Request::get('search') != '')
                                    <a href="{{ route('addresses.index') }}"
                                           class="btn btn-light d-flex align-items-center text-muted"
                                           role="button">
                                        <i class="fas fa-times"></i>
                                    </a>
                                @endif
                                <button class="btn btn-light" type="submit" id="search-addresses-btn">
                                    <i class="fas fa-search text-muted"></i>
                                </button>
                            </span>
                    </div>
                </div>

                <div class="col-md-6">
                    <a href="{{ route('addresses.create') }}" class="btn btn-primary btn-rounded float-right">
                        <i class="fas fa-plus mr-2"></i>
                        @lang('address.add_address')
                    </a>
                </div>
            </div>
        </form>

        <div class="table-responsive" id="addresses-table-wrapper" style="min-height: 200px;">
            <table class="table table-borderless table-striped">
                <thead>
                <tr>
                    <th></th>
                    <th class="min-width-100">@lang('address.labels.owner_name')</th>
                    <th class="min-width-100">@lang('address.labels.tel')</th>
                    <th class="min-width-100">@lang('address.postal_code')</th>
                    <th class="min-width-100">@lang('address.address')</th>
                    <th class="min-width-80">@lang('address.type_label')</th>
                    <th class="text-center min-width-150">@lang('app.action')</th>
                </tr>
                </thead>
                <tbody>
                    @if (count($addresses))
                        @foreach ($addresses as $address)
                            <tr>
                                <td></td>
                                <td>{{ $address->owner_name }}</td>
                                <td>{{ $address->tel }}</td>
                                <td>{{ $address->post_code }}</td>
                                <td>{{ $address->state }} {{ $address->city }} {{ $address->ward }} {{ $address->room_no }}</td>
                                <td>{{ $address->translated_type }}</td>
                                <td class="text-center align-middle">
                                    <a href="{{ route('addresses.edit', $address->id) }}"
                                       class="btn btn-icon edit"
                                       title="@lang('app.edit')"
                                       data-toggle="tooltip" data-placement="top">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <a href="{{ route('addresses.destroy', $address->id) }}"
                                       class="btn btn-icon"
                                       title="@lang('app.delete')"
                                       data-toggle="tooltip"
                                       data-placement="top"
                                       data-method="DELETE"
                                       data-confirm-title="@lang('app.please_confirm')"
                                       data-confirm-text="@lang('app.are_you_sure')"
                                       data-confirm-delete="@lang('app.yes_delete_it')">
                                       <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7"><em>@lang('address.no_addresses_found')</em></td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

{!! $addresses->render() !!}

@stop
