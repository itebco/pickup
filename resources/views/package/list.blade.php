@extends('layouts.app')

@section('page-title', __('package.packages'))
@section('page-heading', __('package.packages'))

@section('breadcrumbs')
    <li class="breadcrumb-item active">
        @lang('package.packages')
    </li>
@stop

@section('styles')
    <style>
        input[type=checkbox] {
            width: 16px;
            height: 16px;
            cursor: pointer;
            vertical-align: middle;
        }

        input[type=checkbox]:checked {
            accent-color: #495057;
        }

        .fixed-buttons-container {
            position: fixed;
            bottom: 25px;
            right: 25px;
            z-index: 25;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
    </style>
@endsection

@section('content')

@include('partials.messages')

<div class="card">
    <div class="card-body">
        <form action="" method="GET" id="packages-form" class="pb-2 mb-3 border-bottom-light">
            <!-- Fixed buttons container at bottom right -->
            <div class="fixed-buttons-container">
                @if(auth()->user()->role_id == \App\Models\Role::ADMIN_ROLE_ID)
                    <button type="button" id="export-selected-btn" class="btn btn-primary btn-rounded">
                        <i class="fas fa-eye mr-2"></i>
                        @lang('app.preview')
                    </button>
                @endif
                <a href="{{ route('packages.create') }}" class="btn btn-primary btn-rounded">
                    <i class="fas fa-plus mr-2"></i>
                    @lang('package.add_package')
                </a>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="search_inline">@lang('package.labels.free_search')</label>
                        <div class="input-group custom-search-form">
                            <input type="text"
                                   class="form-control input-solid"
                                   name="search"
                                   id="search_inline"
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
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label for="pickup_date_from">@lang('package.labels.pickup_date_from')</label>
                        <input type="date"
                               class="form-control input-solid"
                               name="pickup_date_from"
                               id="pickup_date_from"
                               value="{{ Request::get('pickup_date_from') }}">
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label for="pickup_date_to">@lang('package.labels.pickup_date_to')</label>
                        <input type="date"
                               class="form-control input-solid"
                               name="pickup_date_to"
                               id="pickup_date_to"
                               value="{{ Request::get('pickup_date_to') }}">
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label for="status">@lang('package.labels.status')</label>
                        <select name="status" id="status" class="form-control input-solid">
                            <option value="">@lang('app.all')</option>
                            <option value="pending" {{ Request::get('status') == 'pending' ? 'selected' : '' }}>
                                @lang('package.status.pending')
                            </option>
                            <option value="done" {{ Request::get('status') == 'done' ? 'selected' : '' }}>
                                @lang('package.status.done')
                            </option>
                        </select>
                    </div>
                </div>
           </div>
       </form>

        <div class="table-responsive" id="packages-table-wrapper" style="min-height: 200px;">
            <table class="table table-borderless table-striped">
                <thead>
                <tr>
                    <th class="align-middle"><input type="checkbox" id="select-all"></th>
                    <th class="min-width-100">@lang('package.labels.package_code')</th>
                    <th class="min-width-10">@lang('package.labels.owner_name')</th>
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
                                <td class="align-middle"><input type="checkbox" class="package-checkbox" name="package_ids[]" value="{{ $package->id }}"></td>
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
        // Search when fields change
        $("#status, #pickup_date_from, #pickup_date_to").change(function () {
            $("#packages-form").submit();
        });

        // Checkbox functionality
        $("#select-all").click(function () {
            $(".package-checkbox").prop("checked", this.checked);
        });

        $(".package-checkbox").click(function () {
            if (!this.checked) {
                $("#select-all").prop("checked", false);
            }
            // Check if all checkboxes are checked to update select-all
            if ($(".package-checkbox:checked").length == $(".package-checkbox").length) {
                $("#select-all").prop("checked", true);
            }
        });

        // Handle export selected button click
        $("#export-selected-btn").click(function() {
            const selectedPackageIds = [];
            $(".package-checkbox:checked").each(function() {
                selectedPackageIds.push($(this).val());
            });

            if (selectedPackageIds.length > 0) {
                const url = '{{ route("packages.show.selected") }}';
                window.location.href = url + '?pids=' + btoa(selectedPackageIds.join(','));
            } else {
                alert('@lang('package.no_packages_selected_for_display')');
            }
        });
    </script>
@stop
