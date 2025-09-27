<tr>
    <td style="width: 40px;">
        <a href="{{ route('customers.show', $customer) }}">
            <img
                class="rounded-circle img-responsive"
                width="40"
                src="{{ $customer->present()->avatar ?: asset('img/user.png') }}"
                alt="{{ $customer->present()->nameOrEmail }}">
        </a>
    </td>
    <td class="align-middle">
        <a href="{{ route('customers.show', $customer) }}">
            {{ $customer->present()->nameOrEmail ?: __('N/A') }}
        </a>
    </td>
    <td class="align-middle">{{ $customer->email ?: __('N/A') }}</td>
    <td class="align-middle">{{ $customer->created_at->format(config('app.date_format')) }}</td>
    <td class="align-middle">
        <span class="badge badge-lg badge-{{ $customer->present()->labelClass }}">
            {{ trans("app.status.{$customer->status->value}") }}
        </span>
    </td>
    <td class="text-center align-middle">
        <div class="dropdown show d-inline-block">
            <a class="btn btn-icon"
               href="#" role="button" id="dropdownMenuLink"
               data-toggle="dropdown"
               aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-ellipsis-h"></i>
            </a>

            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink">
                <a href="{{ route('customers.show', $customer) }}" class="dropdown-item my-1 text-gray-500">
                    <i class="fas fa-eye mr-2"></i>
                    @lang('customer.view_customer')
                </a>

                <a href="{{ route('customers.edit', $customer->id) }}" class="dropdown-item my-1 text-gray-500">
                    <i class="fas fa-edit mr-2"></i>
                    @lang('customer.edit_customer')
                </a>

                @if(auth()->user()->role_id == 1 || (auth()->user()->role_id == 2 && $customer->created_by == auth()->user()->id))
                <a href="{{ route('customers.destroy', $customer->id) }}"
                    class="dropdown-item my-1 text-gray-500"
                    title="@lang('customer.delete_customer')"
                    data-toggle="tooltip"
                    data-placement="top"
                    data-method="DELETE"
                    data-confirm-title="@lang('customer.please_confirm')"
                    data-confirm-text="@lang('customer.are_you_sure_delete_customer')"
                    data-confirm-delete="@lang('customer.yes_delete_it')">
                    <i class="fas fa-trash mr-2"></i>
                    @lang('customer.delete')
                </a>
                @endif
            </div>
        </div>

        @if(! (auth()->user()->role_id == 1 || (auth()->user()->role_id == 2 && $customer->created_by == auth()->user()->id)))
        <a href="{{ route('customers.edit', $customer->id) }}"
           class="btn btn-icon edit"
           title="@lang('customer.edit_customer')"
           data-toggle="tooltip" data-placement="top">
            <i class="fas fa-edit"></i>
        </a>
        @endif
    </td>
</tr>
