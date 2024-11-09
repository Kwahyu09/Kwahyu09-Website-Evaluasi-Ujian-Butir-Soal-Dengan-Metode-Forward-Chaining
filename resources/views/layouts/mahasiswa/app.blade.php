    @include('layouts.mahasiswa.header')
    <body class="{{ $class ?? '' }}">
        @auth()
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
            @include('layouts.mahasiswa.sidebar')
        @endauth

        <div class="main-content" id="panel">
            @include('layouts.mahasiswa.topnavbar')
            @yield('content')
        </div>

        @guest()
            @include('layouts.footers.guest')
        @endguest
  
        @stack('js')

        <!-- Argon JS -->
        <!-- <script src="{{ asset('argon') }}/js/argon.js?v=1.0.0"></script> -->

        
    </body>
</html>
