<nav class="navbar navbar-vertical fixed-left navbar-expand-md navbar-light bg-white" id="sidenav-main">
    <div class="container-fluid">
        <!-- Toggler -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#sidenav-collapse-main"
            aria-controls="sidenav-main" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon">
            </span>
        </button>
        <a class="navbar-brand pt-0">
            Sahabat CPNS
        </a>

        <div class="collapse navbar-collapse" id="sidenav-collapse-main">
            <!-- Collapse header -->
            <div class="navbar-collapse-header ">
                <div class="row">
                    <div class="col-6 collapse-brand">
                        <a>
                            Sahabat CPNS
                        </a>
                    </div>
                    <div class="col-6 collapse-close">
                        <button type="button" class="navbar-toggler" data-toggle="collapse"
                            data-target="#sidenav-collapse-main" aria-controls="sidenav-main" aria-expanded="false"
                            aria-label="Toggle sidenav">
                        </button>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <ul class="navbar-nav disabledsidebar" id="sidebarclient">
                <?php
                
                use App\Models\Peserta;
                use Illuminate\Support\Facades\Auth;
                
                $peserta = Peserta::where('id_peserta', Auth::user()->id_peserta)->first();
                
                ?>
                @if ($peserta->status)
                    <li class="nav-item ">
                        <a class="nav-link {{ request()->is('client-dashboard') ? 'active' : '' }}"
                            href="{{ route('client-dashboard') }}">
                            <i class="ni ni-tv-2 text-primary">
                            </i> {{ __('Dashboard') }}
                        </a>
                    </li>
                    <li>
                        <a class="nav-link {{ request()->is('client-datavideo') ? 'active' : '' }}"
                            href="{{ route('client-datavideo') }}">
                            <i class="fas fa-video">
                            </i>
                            <span>Video
                            </span>
                        </a>
                    </li>

                    <li>
                        <a class="nav-link {{ request()->is('client-datamateri') ? 'active' : '' }}"
                            href="{{ route('client-datamateri') }}">
                            <i class="fas fa-newspaper">
                            </i>
                            <span>Materi
                            </span>
                        </a>
                    </li>



                    <li class="nav-item">
                        <a class="nav-link" href="#navbar-3" data-toggle="collapse" role="button" aria-expanded="true"
                            aria-controls="navbar-examples">
                            <i class="fas fa-archive" style="color: #f4645f;"></i>
                            <span class="nav-link-text" style="color: #000000;">{{ __('Latihan') }}</span>
                        </a>

                        <div class="collapse" id="navbar-3">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->is('client-datalthnpeserta') ? 'active' : '' }}"
                                        href="{{ route('client-datalthnpeserta') }}">
                                        {{ __('Kerjakan Latihan') }}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->is('client-datahasilthnpeserta') ? 'active' : '' }}"
                                        href="{{ route('client-datahasilthnpeserta') }}">
                                        {{ __('Hasil') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="#navbar-4" data-toggle="collapse" role="button" aria-expanded="true"
                            aria-controls="navbar-examples">
                            <i class="fas fa-archive" style="color: #f4645f;"></i>
                            <span class="nav-link-text" style="color: #000000;">{{ __('Tryout') }}</span>
                        </a>

                        <div class="collapse" id="navbar-4">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->is('client-datatopeserta') ? 'active' : '' }}"
                                        href="{{ route('client-datatopeserta') }}">
                                        {{ __('Kerjakan Tryout') }}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->is('client-datahasiltopeserta') ? 'active' : '' }}"
                                        href="{{ route('client-datahasiltopeserta') }}">
                                        {{ __('Hasil') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif

                <li class="nav-item">
                    <a class="nav-link" href="#navbar-5" data-toggle="collapse" role="button" aria-expanded="true"
                        aria-controls="navbar-examples">
                        <i class="fas fa-cogs"></i>
                        <span class="nav-link-text" style="color: #000000;">{{ __('Settings') }}</span>
                    </a>

                    <div class="collapse" id="navbar-5">
                        <ul class="nav nav-sm flex-column">


                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('client-profil') ? 'active' : '' }}"
                                    href="{{ route('client-profil') }}">
                                    {{ __('Profile') }}
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('client-password') ? 'active' : '' }}"
                                    href="{{ route('client-password') }}">
                                    {{ __('Security') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li>
                    <a class="nav-link " href="{{ route('logout') }}"
                        onclick="event.preventDefault();
                    document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout
                        </span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
