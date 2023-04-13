@extends('layouts.shop')
@section('title', 'Cart page')
@section('content')
    <h3>
        {{ __('Cart page') }}
    </h3>
    @if ($cart)
        <table class="table">
            <thead>
                <th>{{ 'Product name' }}</th>
                <th>{{ 'Product price' }}</th>
                <th>{{ 'Qty' }}</th>
                <th>{{ 'Action' }}</th>
            </thead>
            <tbody>
            @foreach($cart->getCartItem as $item)
                <tr>
                    <td>
                        {{ $item->product->getAttributes()['name'] }}
                    </td>
                    <td>
                        {{ $item->getAttributes()['price'] }}
                    </td>
                    <td>
                        {{ $item->getAttributes()['qty'] }}
                    </td>
                    <td>
                        <form method="post" action="{{ route('removeFromCart') }}">
                            @csrf
                            <input type="hidden" name="cart_item_id" value="{{ $item->getAttributes()['id'] }}">
                            <button type="submit" class="btn btn-warning">{{ 'Remove' }}</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <p class="m-2">{{ __('Total') }}: <b>{{ $cart->getTotal() }}</b></p>
        <div class="m-2">
            <form method="post" action="{{  route('checkout')  }}" data-turbo="false">
                @csrf
                <input type="hidden" name="payment_total" value="{{ $cart->getTotal() }}">
                <label for="payment_method">{{ 'Payment method' }}</label>
                <select name="payment_method">
                    <option value="paypal">{{ 'Paypal' }}</option>
                    <option value="stripe">{{ 'Stripe' }}</option>
                    <option value="epay">{{ 'Epay' }}</option>

                </select>
                <button type="submit" class="btn btn-success">{{ 'Pay' }}</button>
            </form>
        </div>
    @else
        {{ __('Your cart is empty') }}
    @endif
@endsection
