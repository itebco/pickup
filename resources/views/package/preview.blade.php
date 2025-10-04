@extends('layouts.blank')

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

        <!-- Fixed buttons container at bottom right -->
        <div class="fixed-buttons-container">
            @if(auth()->user()->role_id == \App\Models\Role::ADMIN_ROLE_ID)
                <a class="btn btn-primary btn-rounded" href="{{ route('packages.index') }}">
                    <i class="fas fa-eye mr-2"></i>
                    <span>@lang('package.packages')</span>
                </a>
            @endif
            <button id="export-csv-btn" class="btn btn-primary btn-rounded">
                <i class="fas fa-download mr-2"></i>
                <span>@lang('app.export_csv')</span>
            </button>
        </div>

        <!-- Hidden form for CSV export -->
        <form id="export-csv-form" class="d-none" method="POST" action="{{ route('packages.export-csv') }}">
            @csrf
            <input type="hidden" name="pids" value="{{ request()->get('pids') }}">
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th class="min-width-100">伝票区分</th>
                    <th class="min-width-200">依頼店（精算店）コード</th>
                    <th style="min-width: 210px;">荷送人コード（顧客)枝番込</th>
                    <th class="min-width-100">総個数</th>
                    <th class="min-width-100">代引金</th>
                    <th class="min-width-150">代引決済区分</th>
                    <th class="min-width-100">保険金</th>
                    <th class="min-width-100">書込み金</th>
                    <th class="min-width-100">立替金</th>
                    <th class="min-width-200">依頼主コード　枝番込</th>
                    <th class="min-width-100">便種</th>
                    <th class="min-width-150">集荷予定日付</th>
                    <th class="min-width-200">集荷予定時刻FROM</th>
                    <th class="min-width-150">集荷予定時刻TO</th>
                    <th class="min-width-150">荷送人郵便番号</th>
                    <th class="min-width-150">荷送人電話番号</th>
                    <th style="min-width: 500px;">荷送人住所１～３</th>
                    <th class="min-width-200">荷送人名称１～３</th>
                    <th class="min-width-150">荷受人郵便番号</th>
                    <th class="min-width-150">荷受人電話番号</th>
                    <th class="min-width-150">荷受人住所１～３</th>
                    <th class="min-width-150">荷受人名称１～３</th>
                    <th class="min-width-150">注文主郵便番号</th>
                    <th class="min-width-150">注文主電話番号</th>
                    <th class="min-width-150">注文主住所１～３</th>
                    <th class="min-width-150">注文主名称１～３</th>
                    <th class="min-width-150">送り状編集１～３</th>
                    <th class="min-width-150">配達指定日付</th>
                    <th class="min-width-150">配達指定時刻</th>
                    <th class="min-width-200">営止着店（精算店）コード</th>
                    <th style="min-width: 300px;">顧客管理番号</th>
                    <th class="min-width-100">記事欄1</th>
                    <th class="min-width-100">記事欄2</th>
                    <th class="min-width-100">記事欄3</th>
                    <th class="min-width-100">記事欄4</th>
                    <th class="min-width-100">記事欄5</th>
                </tr>
                </thead>
                <tbody>
                    @if (count($packages))
                        @foreach ($packages as $package)
                            <tr>
                                <td>1</td>
                                <td>1010</td>
                                <td>18228216005</td>
                                <td>{{ $package->quantity }}</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>1751000</td>
                                <td>0</td>
                                <td>{{ \Carbon\Carbon::parse($package->pickup_date)->format('Y-m-d') }}</td>
                                <td>{{ explode('-', $package->pickup_time)[0] ?? '' }}</td>
                                <td>{{ explode('-', $package->pickup_time)[1] ?? '' }}</td>
                                <td>{{ $package->address->post_code ?? '' }}</td>
                                <td>{{ $package->address->tel ?? '' }}</td>
                                <td>{{ $package->address->state . ' ' . $package->address->city . ' ' . $package->address->ward . ' ' . $package->address->room_no ?? '' }}</td>
                                <td>{{ $package->address->owner_name ?? '' }}</td>
                                <td>2860117</td>
                                <td>344006368</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>{{ $package->package_code }}</td>
                                <td title="{{ $package->remark }}">{{ strlen($package->remark) > 30 ? substr($package->remark, 0, 30) . '...' : $package->remark }}</td>
                                <td>{{ config('setting.prefecture_mapping')[$package->address->state] ?? '' }}</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="12"><em>@lang('package.no_packages_found')</em></td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $('#export-csv-btn').on('click', function () {
                $('#export-csv-form').submit();
            });
        });
    </script>
@endsection
