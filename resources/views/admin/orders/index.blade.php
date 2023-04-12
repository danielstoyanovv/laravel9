@extends('layouts.admin')
@section('title', __('Orders'))
@section('content')
    <h3>{{ __('Orders') }}</h3>
    @if ($orders)
        <div class="table-responsive">
            <table class="table table-borderless table-striped">
                <tr>
                    <th class="table-light">{{ __('id') }}</th>
                    <th class="table-light">{{ __('Total') }}</th>
                    <th class="table-light">{{ __('Status') }}</th>
                    <th class="table-light">{{ __('Payment method') }}</th>
                    <th class="table-light">{{ __('Actions') }}</th>
                </tr>
                @foreach ($orders as $order)
                    <tr>
                        <td class="">{{ $order['id'] }}</td>
                        <td class="">{{ $order['total'] }}</td>
                        <td class="">{{ $order['status'] }}</td>
                        <td class="">{{ $order['payment_method'] }}</td>
                        <td class="">
                            <a href="{{ route('showOrder', $order['id']) }}">{{ __('Details') }}</a>
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
        <div class="d-felx justify-content-center pagination">
            {{ $orders->links() }}
        </div>
    @endif
@endsection
