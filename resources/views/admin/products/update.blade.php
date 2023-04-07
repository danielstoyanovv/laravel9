@extends('layouts.admin')
@section('title', 'Update product')
@section('content')
    @if (count($errors) > 0)
        <ul class="list-group">
            @foreach ($errors->all() as $error)
                <li class="list-group-item list-group-item-danger">{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <h3>{{ 'Update product' }}</h3>
    <form method="POST" action="{{ route('postUpdateProduct', ['id' => $product['id']]) }}" enctype="multipart/form-data">
        @csrf
        <div class="mb-3 mt-3">
            <input class="form-control" type="text" name="name" placeholder="{{ 'Name' }}" value="{{ $product['name'] }}">
        </div>
        <div class="mb-3 mt-3">
            <textarea class="form-control" name="description">{{ $product['description'] }}</textarea>
        </div>
        <div class="mb-3 mt-3">
            <input class="form-control" type="text" name="price" value="{{ $product['price'] }}" placeholder="{{ 'Price' }}" >
        </div>
        <div class="mb-3 mt-3">
            <input type="file" name="image">
        </div>
        @if(!empty($product->getImages))
            @foreach($product->getImages as $image)
                <img src="{{ url($image->path) }}" width="150px">
            @endforeach
        @endif
        <div class="mb-3 mt-3">
            <input type="submit" class="btn btn-primary form-control" value="{{ 'Update' }}">
        </div>
    </form>
@endsection
