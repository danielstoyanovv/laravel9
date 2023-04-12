@extends('layouts.admin')
@section('title', __('Order'))
@section('content')
    <h3>{{ __('Order') }}</h3>
    @if ($order)
        <div class="order-details">
            <p>
                {{ __('id') }}: {{ $order['id'] }}
                </p>
            <p>
                {{ __('Total') }}: {{ $order['total'] }}
            </p>
            <p>
                {{ __('Status') }}: {{ $order['status'] }}
            </p>
            <p>
                {{ __('Payment method') }}: {{ $order['payment_method'] }}
            </p>
        </div>
        @if($order->getOrderItem)
            <h5>{{ __('Order items') }}</h5>

            <div class="table-responsive">
                    <table class="table table-borderless table-striped">
                        <tr>
                            <th class="table-light">{{ __('Name') }}</th>
                            <th class="table-light">{{ __('Price') }}</th>
                            <th class="table-light">{{ __('Qty') }}</th>


                        </tr>
                        @foreach ($order->getOrderItem as $item)
                            <tr>
                                <td class="">{{ $item->product->getAttributes()['name'] }}</td>
                                <td class="">{{ $item['price'] }}</td>
                                <td class="">{{ $item['qty'] }}</td>

                            </tr>
                        @endforeach
                    </table>
                </div>
        @endif
    @endif
@endsection
