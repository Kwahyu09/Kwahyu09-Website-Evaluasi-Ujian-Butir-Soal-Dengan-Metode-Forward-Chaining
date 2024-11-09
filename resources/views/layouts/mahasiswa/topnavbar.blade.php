@auth()
    @include('layouts.client.nav.auth')
@endauth
    
@guest()
    @include('layouts.client.nav.guest')
@endguest