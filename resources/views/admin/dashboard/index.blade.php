@extends('layouts.admin')
@section('title', __('Orders'))
@section('content')
    <div class="container">
        <h3>{{ __('Welcome, Admin') }}</h3>
        <div class="row">
            <div class="col-sm">
                <h5>{{ __('Last Orders') }}</h5>
                @if ($orders)
                    <div class="table">
                        <table class="table table-borderless table-striped">
                            <tr>
                                <th class="table-light">{{ __('id') }}</th>
                                <th class="table-light">{{ __('Total') }}</th>
                                <th class="table-light">{{ __('Status') }}</th>
                                <th class="table-light">{{ __('Order date') }}</th>
                                <th class="table-light">{{ __('Payment method') }}</th>
                                <th class="table-light">{{ __('Actions') }}</th>
                            </tr>
                            @foreach ($orders as $order)
                                <tr>
                                    <td class="">{{ $order['id'] }}</td>
                                    <td class="">{{ $order['total'] }}</td>
                                    <td class="">{{ $order['status'] }}</td>
                                    <td class="">{{ $order['created_at'] }}</td>
                                    <td class="">{{ $order['payment_method'] }}</td>
                                    <td class="">
                                        <a href="{{ route('showOrder', $order['id']) }}">{{ __('Details') }}</a>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                @endif
            </div>
            <div class="col-sm">
                <h5>{{ 'Last products' }}</h5>
                @if ($products)
                    <div class="table-responsive">
                        <table class="table table-borderless table-striped">
                            <tr>
                                <th class="table-light">{{ __('id') }}</th>
                                <th class="table-light">{{ __('Name') }}</th>
                                <th class="table-light">{{ __('Price') }}</th>
                                <th class="table-light">{{ __('Actions') }}</th>
                            </tr>
                            @foreach ($products as $product)
                                <tr>
                                    <td class="">{{ $product['id'] }}</td>
                                    <td class="">{{ $product['name'] }}</td>
                                    <td class="">{{ $product['price'] }}</td>
                                    <td class="">
                                        <a href="{{ route('getUpdateProduct', $product['id']) }}">{{ __('Update') }}</a>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                @endif
            </div>
    </div>
@endsection
