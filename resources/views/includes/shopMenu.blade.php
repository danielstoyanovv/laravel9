<ul class="nav">
    <li><a href="{{ route('homepage') }}">{{  __('Home') }}</a></li>
    <li><a href="{{ route('shop') }}">{{  __('Shop') }}</a></li>
    <li><a href="{{ route('cart') }}">{{  __('Cart') }}</a></li>
    <!-- Authentication Links -->
    @guest
        @if (Route::has('login'))
            <li>
                <a href="{{ route('login') }}">{{ __('Login') }}</a>
            </li>
        @endif

        @if (Route::has('register'))
            <li>
                <a href="{{ route('register') }}">{{ __('Register') }}</a>
            </li>
        @endif
    @else
        <li class="">
            <a id="navbarDropdown" class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                {{ Auth::user()->name }}
            </a>

            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                <a class="dropdown-item" href="{{ route('logout') }}"
                   onclick="event.preventDefault();
                                                 document.getElementById('logout-form').submit();">
                    {{ __('Logout') }}
                </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </li>
    @endguest
    <li class="scroll-to-section"><div class="main-red-button"><a href="#contact">Contact Now</a></div></li>
</ul>
