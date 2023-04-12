<!DOCTYPE html>
<html>
<head>
    @include('includes.head')
</head>
<body>
    @include('includes.shopHeader')
    <main class="products">
        @yield('content')
    </main>
    @include('includes.footer')
</body>
</html>
