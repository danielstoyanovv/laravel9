@extends('layouts.admin')
@section('title', 'Create new product')
@section('content')
    @if (count($errors) > 0)
        <ul class="list-group">
            @foreach ($errors->all() as $error)
                <li class="list-group-item list-group-item-danger">{{ $error }}</li>
            @endforeach
        </ul>
    @endif
    <h3>{{ 'Create new product' }}</h3>
    <form method="POST" action="{{ route('storeProduct') }}" enctype="multipart/form-data">
        @csrf
        <div class="mb-3 mt-3">
            <input class="form-control" type="text" name="name" placeholder="{{ 'Name' }}" >
        </div>
        <div class="mb-3 mt-3">
            <textarea class="form-control" name="description"></textarea>
        </div>
        <div class="mb-3 mt-3">
            <input class="form-control" type="text" name="price" placeholder="{{ 'Price' }}" >
        </div>
        <div class="mb-3 mt-3">
            <input type="file" name="image">
        </div>
        <div class="mb-3 mt-3">
            <input class="btn btn-primary form-control" type="submit" value="{{ 'Create' }}">
        </div>
    </form>
@endsection
