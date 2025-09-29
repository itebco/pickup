@extends('layouts.app')

@section('page-title', __('package.packages'))
@section('page-heading', __('package.packages'))

@section('breadcrumbs')
    <li class="breadcrumb-item active">
        @lang('package.packages')
    </li>
@stop

@section('content')

@include('partials.messages')

<div class="card">
    <div class="card-body">

        <form action="" method="GET" id="packages-form" class="pb-2 mb-3 border-bottom-light">
            <div class="row my-3 flex-md-row flex-column-reverse">
                <div class="col-md-6 mt-md-0 mt-2">
                    <div class="input-group custom-search-form">
                        <input type="text"
                               class="form-control input-solid"
                               name="search"
                               value="{{ Request::get('search') }}"
                               placeholder="@lang('Search for packages...')">

                            <span class="input-group-append">
                                @if (Request::has('search') && Request::get('search') != '')
                                    <a href="{{ route('packages.index') }}"
                                           class="btn btn-light d-flex align-items-center text-muted"
                                           role="button">
                                        <i class="fas fa-times"></i>
                                    </a>
                                @endif
                                <button class="btn btn-light" type="submit" id="search-packages-btn">
                                    <i class="fas fa-search text-muted"></i>
                                </button>
                            </span>
                    </div>
                </div>

                <div class="col-md-6">
                    <a href="{{ route('packages.create') }}" class="btn btn-primary btn-rounded float-right">
                        <i class="fas fa-plus mr-2"></i>
                        @lang('package.add_package')
                    </a>
                </div>
            </div>
        </form>

        <div class="table-responsive" id="packages-table-wrapper" style="min-height: 200px;">
            <table class="table table-borderless table-striped">
                <thead>
                <tr>
                    <th></th>
                    <th class="min-width-100">@lang('package.labels.package_code')</th>
                    <th class="min-width-100">@lang('package.labels.owner_name')</th>
                    <th class="min-width-100">@lang('package.labels.pickup_date')</th>
                    <th class="min-width-100">@lang('package.labels.pickup_time')</th>
                    <th class="min-width-80">@lang('package.labels.quantity')</th>
                    <th class="min-width-80">@lang('package.labels.method')</th>
                    <th class="min-width-80">@lang('package.labels.status')</th>
                    <th class="min-width-100">@lang('package.labels.remark')</th>
                    <th class="text-center min-width-150">@lang('app.action')</th>
                </tr>
                </thead>
                <tbody>
                    @if (count($packages))
                        @foreach ($packages as $package)
                            <tr>
                                <td></td>
                                <td>{{ $package->package_code }}</td>
                                <td>{{ $package->address->owner_name ?? '' }}</td>
                                <td>{{ \Carbon\Carbon::parse($package->pickup_date)->format('Y-m-d') }}</td>
                                <td>{{ $package->pickup_time }}</td>
                                <td>{{ $package->quantity }}</td>
                                <td>{{ $package->translated_method }}</td>
                                <td>{{ $package->translated_status }}</td>
                                <td title="{{ $package->remark }}">{{ strlen($package->remark) > 30 ? substr($package->remark, 0, 30) . '...' : $package->remark }}</td>
                                <td class="text-center align-middle">
                                    @php
                                        $currentDate = \Carbon\Carbon::now()->format('Y-m-d');
                                        $canEdit = $package->status !== 'done' && $package->pickup_date > $currentDate;
                                    @endphp
                                    @if($canEdit)
                                        <a href="{{ route('packages.edit', $package->id) }}"
                                           class="btn btn-icon edit"
                                           title="@lang('app.edit')"
                                           data-toggle="tooltip" data-placement="top">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @else
                                        <span class="btn btn-icon disabled" title="@lang('package.package_cannot_be_edited')" data-toggle="tooltip" data-placement="top">
                                            <i class="fas fa-edit text-muted"></i>
                                        </span>
                                    @endif

                                    @if($canEdit)
                                        <a href="{{ route('packages.destroy', $package->id) }}"
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
                                    @else
                                        <span class="btn btn-icon disabled" title="@lang('package.package_cannot_be_deleted')" data-toggle="tooltip" data-placement="top">
                                            <i class="fas fa-trash text-muted"></i>
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="10"><em>@lang('package.no_packages_found')</em></td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

{!! $packages->render() !!}

@stop

@section('scripts')
    <script>
        $("#status").change(function () {
            $("#packages-form").submit();
        });
    </script>
@stop
