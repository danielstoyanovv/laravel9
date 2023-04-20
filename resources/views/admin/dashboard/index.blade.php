@extends('layouts.admin')
@section('title', __('Orders'))
@section('content')
    <div class="container">
        <h3>{{ __('Welcome, Admin') }}</h3>
        <div class="row">
            <div class="col-sm">
                <h5>{{ __('Last orders') }}</h5>
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
                                        <div class="dropdown">
                                            <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                {{ __('Select') }}
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('showOrder', $order['id']) }}">{{ __('Details') }}</a>
                                                </li>
                                            </ul>
                                        </div>                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                @endif
            </div>
            <div class="col-sm">
                <h5>{{ 'Last products' }}</h5>
                @if ($products)
                    <div class="table">
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
                                        <div class="dropdown">
                                            <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                {{ __('Select') }}
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('products.edit', $product['id']) }}">{{ __('Update') }}</a>
                                                </li>
                                            </ul>
                                        </div>                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                @endif
            </div>
    </div>
@endsection
