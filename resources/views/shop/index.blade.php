@extends('layouts.shop')
@section('title', 'Shop')
@section('content')
   <h3>{{ __('Shop') }}</h3>
    @if ($products)
        <form method="POST" action="{{ route('search') }}">
            @csrf
            <div class="mb-3 mt-3">
                <input class="form-control" type="text" name="product" placeholder="{{ __('Product name') }}" required="true">
            </div>
            <div class="mb-3 mt-3">
                <input class="btn btn-primary form-control" type="submit" value="{{ __('Search') }}">
            </div>
        </form>
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
        <div class="d-felx justify-content-center pagination">
            {{ $products->links() }}
        </div>
    @endif
@endsection
