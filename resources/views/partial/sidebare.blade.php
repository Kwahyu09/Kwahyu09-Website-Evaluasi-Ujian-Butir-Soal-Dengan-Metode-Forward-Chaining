<aside id="sidebar-wrapper">
    <div class="sidebar-brand">
        <img src="{{ asset('backend/assets/img/logounib.png') }}" width="50"><br>
        <h6>FKIK UNIB</h6>
        @if (Auth::user()->role == 'Mahasiswa')
        <a href="">Sistem Informasi Ujian</a>
        @else
        <a href="/">Evaluasi Ujian</a>
        @endif
    </div>
    <div class="sidebar-brand sidebar-brand-sm">
        @if (Auth::user()->role == 'Mahasiswa')
        <a href="/">Ujian</a>
        @else
        <a href="/">Eval</a>
        @endif
    </div>
    <ul class="sidebar-menu">
        @if(Request::is('masuk-ujian*') || Request::is('selesaiujian*'))
        <li class="menu-header"></li>
        @else
        <li class="menu-header">Menu</li>
        @endif
        <li class="{{ Request::is('/') ? 'active' : '' }}">
            @if(Request::is('masuk-ujian*') || Request::is('selesaiujian*'))
            <a href="" class="nav-link"></a>
            @else
            <a href="/" class="nav-link"><i class="fas fa-fire"></i><span>Home</span></a>
            @endif
        </li>
        @if (Auth::user()->role == 'Admin')
        <li class="{{ Request::is('staff*') || Request::is('ketua*') ? 'active' : '' }}">
            <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-columns"></i>
                <span>Manajemen Akun</span></a>
            <ul class="dropdown-menu">
                <li><a class="nav-link " href="/staff">Staff</a></li>
                <li><a class="nav-link" href="/ketua">Ketua</a></li>
            </ul>
        </li>
        @endif
        @if(Auth::user()->role == 'Staf')
        <li class="{{ Request::is('ketua*') ? 'active' : '' }}">
            <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-columns"></i>
                <span>Manajemen Akun</span></a>
            <ul class="dropdown-menu">
                <li><a class="nav-link" href="/ketua">Ketua</a></li>
            </ul>
        </li>
        @endif
        @if (Auth::user()->role == 'Admin' || Auth::user()->role == 'Staf')
        <li
            class="{{ Request::is('dosen*') ||Request::is('kelas*') || Request::is('mahasiswa*') || Request::is('modul*') ? 'active' : '' }}">
            <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-columns"></i> <span>Data
                    Fakultas</span></a>
            <ul class="dropdown-menu">
                <li><a class="nav-link" href="/dosen">Dosen</a></li>
                <li><a class="nav-link" href="/kelas">Kelas</a></li>
                <li><a class="nav-link" href="/kelasmahasiswa">Mahasiswa</a></li>
                <li><a class="nav-link" href="/modul">Modul</a></li>
            </ul>
        </li>
        @endif
        @if (Auth::user()->role == 'Admin' || Auth::user()->role == 'Ketua')
        <li class="{{ Request::is('grupsoal*') || Request::is('grupsoal2*') || Request::is('soal*') ? 'active' : '' }}">
            <a href="/grupsoal" data-toggle="nav-link"><i class="far fa-file"></i><span>Soal</span></a>
        </li>
        <li class="{{ Request::is('ujian*') ? 'active' : '' }}">
            <a href="/ujian" data-toggle="nav-link"><i class="far fa-file"></i> <span>Ujian</span></a>
        </li>
        <li class="{{ Request::is('hasilujian*') ? 'active' : '' }}">
            <a href="/hasilujian" data-toggle="nav-link"><i class="far fa-file"></i> <span>Hasil Ujian</span></a>
        </li>
        <li class="{{ Request::is('evaluasi*') ? 'active' : '' }}">
            <a href="/evaluasi" data-toggle="nav-link"><i class="far fa-file"></i><span>Evaluasi</span></a>
        </li>
        @endif
        @if (Auth::user()->role == 'Admin')
        <li class="{{ Request::is('variabel*') || Request::is('kesimpulan*') || Request::is('basispengetahuan*') ? 'active' : '' }}">
            <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-columns"></i> <span>Basis Pengetahuan</span></a>
            <ul class="dropdown-menu">
                <li><a class="nav-link" href="/variabel">Variabel</a></li>
                <li><a class="nav-link" href="/kesimpulan">Kesimpulan</a></li>
            </ul>
        </li>
        @endif
        @if (Auth::user()->role == 'Mahasiswa')
        <li class="{{ Request::is('profile*') ? 'active' : '' }}">
            @if(Request::is('masuk-ujian*') || Request::is('selesaiujian*'))
            <a href="" data-toggle="nav-link"></a>
            @else
            <a href="/profile/{{ Auth::user()->username }}/edit" data-toggle="nav-link">
                <i class="far fa-user"></i> <span>Profile</span></a>
            @endif
        </li>
        <li class="{{ Request::is('ujian-mahasiswa*') || Request::is('ujian-data*') ? 'active' : '' }}">
            @if(Request::is('masuk-ujian*') || Request::is('selesaiujian*'))
            <a href="" data-toggle="nav-link"></a>
            @else
            <a href="/ujian-mahasiswa" data-toggle="nav-link"><i class="far fa-file"></i><span>Ujian</span></a>
            @endif
        </li>
        <li>
            @if(Request::is('masuk-ujian*') || Request::is('selesaiujian*'))
            @else
            <form action="/logout" method="post">
                @csrf
                <button type="submit" class="dropdown-item text-danger ml-4 mt-5"><i
                    class="bi bi-box-arrow-down-left mr-3">   </i> Logout</button>
            </form>
            @endif
        </li>
        @endif
    </ul>
</aside>