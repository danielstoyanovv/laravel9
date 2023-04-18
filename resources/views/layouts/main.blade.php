<!DOCTYPE html>
<html>
<head>
    @include('includes.head')
</head>
<body>
    @include('includes.header')
    <div class='notifications'>
        @include("alerts")
    </div>
    <main class="py-4">
        @yield('content')
    </main>
    @include('includes.footer')
</body>
</html>
