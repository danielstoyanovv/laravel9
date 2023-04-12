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
            <div>
            @if($order['payment_method'] == 'Stripe' and  $order['status']  != 'REFUND' and !is_null($order['payment_data']))
                <h5>{{ __('Order refund') }}</h5>
                <form method="post" action="{{ route('stripe_refund') }}">
                @csrf
                <input type="hidden" name="paymentNumber" value="{{ $order['payment_data'] }}">
                {{ __('Amount') }}

                @if($order['status'] == 'PARTLY REFUND')
                    <input type="text" name="amount" value="{{ $order['total'] - $order['refund_amount'] }}" }}>
                @else
                    <input type="text" name="amount" value="{{ $order['total'] }}" }}>

                @endif
                <input type="submit" value="{{ 'Refund payment' }}">
                </form>
            @endif
            </div>

        @endif
    @endif
@endsection
