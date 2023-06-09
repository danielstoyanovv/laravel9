@extends('layouts.shop')
@section('title', 'Shop search list')
@section('content')
   <h3>{{ __('Shop search list') }}</h3>
    @if ($products)
        @foreach ($products as $product)
            <div class="w-100 border">
                <div class="col text-center">
                    <p>{{ $product['name'] }}</p>

                    <span class="fw-bold">{{ $product['price'] }}</span>

                    <form method="post" action="{{ route('addToCart') }}">
                        @csrf
                        <input type="hidden" name="product" value="{{ $product['id'] }}">
                        <input type="hidden" name="price" value="{{ $product['price'] }}">
                        <input placeholder="{{ 'Qty' }}" type="text" name="qty" required>
                        <button type="submit" class="btn btn-success">{{ __('Add to cart') }}</button>
                    </form>
                </div>
            </div>
        @endforeach
    @endif
@endsection
