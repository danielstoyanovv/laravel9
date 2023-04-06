@extends('layouts.admin')
@section('title', 'All products')
@section('content')
    <h3>{{ 'All products' }}</h3>
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
        <div class="d-felx justify-content-center">
            {{ $products->links() }}
        </div>
    @endif
@endsection
