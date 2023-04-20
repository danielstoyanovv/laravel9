<!DOCTYPE html>
<html>
<head>
    @include('includes.shopHead')
</head>
<body>
    @include('includes.shopHeader')
    <div class='notifications'>
        @include("alerts")
    </div>
    <main class="products">
        @yield('content')
    </main>
    @include('includes.footer')
</body>
</html>
