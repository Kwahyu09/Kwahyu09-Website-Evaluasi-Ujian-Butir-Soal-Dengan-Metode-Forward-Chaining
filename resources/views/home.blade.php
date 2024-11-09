@extends('layoutdashboard.main')

@section('container')
<h6>Selamat Datang {{ Auth::user()->nama }} !</h6><br>
<div class="row">
    <div class="flash-data" data-flashdata="{{ session('success') }}">
    </div>
    @if (Auth::user()->role == 'Admin')
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
            <div class="card-icon bg-primary">
                <i class="far fa-user"></i>
            </div>
            <div class="card-wrap">
                <div class="card-header">
                    <h4>Total Staff</h4>
                </div>
                <div class="card-body">
                    {{ $staf }}
                </div>
            </div>
        </div>
    </div>
    @endif
    @if (Auth::user()->role == 'Admin' || Auth::user()->role == 'Staf')
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
            <div class="card-icon bg-primary">
                <i class="far fa-user"></i>
            </div>
            <div class="card-wrap">
                <div class="card-header">
                    <h4>Total Ketua</h4>
                </div>
                <div class="card-body">
                    {{ $ketua }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
            <div class="card-icon bg-primary">
                <i class="far fa-user"></i>
            </div>
            <div class="card-wrap">
                <div class="card-header">
                    <h4>Total Dosen</h4>
                </div>
                <div class="card-body">
                    {{ $dosen }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
            <div class="card-icon bg-danger">
                <i class="far fa-newspaper"></i>
            </div>
            <div class="card-wrap">
                <div class="card-header">
                    <h4>Total Kelas</h4>
                </div>
                <div class="card-body">
                    {{ $kelas }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
            <div class="card-icon bg-danger">
                <i class="far fa-user"></i>
            </div>
            <div class="card-wrap">
                <div class="card-header">
                    <h4>Total Mahasiswa</h4>
                </div>
                <div class="card-body">
                    {{ $mahasiswa }}
                </div>
            </div>
        </div>
    </div>
    @endif
    @if (Auth::user()->role == 'Admin' || Auth::user()->role == 'Ketua' || Auth::user()->role == 'Staf')
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
            <div class="card-icon bg-warning">
                <i class="far fa-newspaper"></i>
            </div>
            <div class="card-wrap">
                <div class="card-header">
                    <h4>Total Modul</h4>
                </div>
                <div class="card-body">
                    {{ $modul }}
                </div>
            </div>
        </div>
    </div>
    @endif
    @if (Auth::user()->role == 'Admin' || Auth::user()->role == 'Ketua')
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
            <div class="card-icon bg-warning">
                <i class="far fa-file"></i>
            </div>
            <div class="card-wrap">
                <div class="card-header">
                    <h4>Total Grup Soal</h4>
                </div>
                <div class="card-body">
                    {{ $grupsoal }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
            <div class="card-icon bg-success">
                <i class="far fa-file"></i>
            </div>
            <div class="card-wrap">
                <div class="card-header">
                    <h4>Total Soal</h4>
                </div>
                <div class="card-body">
                    {{ $soal }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
            <div class="card-icon bg-success">
                <i class="far fa-file"></i>
            </div>
            <div class="card-wrap">
                <div class="card-header">
                    <h4>Total Ujian</h4>
                </div>
                <div class="card-body">
                    {{ $ujian }}
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection