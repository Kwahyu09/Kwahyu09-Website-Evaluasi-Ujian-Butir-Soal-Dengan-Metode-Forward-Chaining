@extends('layoutdashboard.main')
@section('container')
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-auto col-md-8">
                <h4 class="mb-2">Data Hasil {{ $ujian->nama_ujian }}</h4>
                <h5>Kelas : {{ $ujian->kelas->nama_kelas }}</h5>
            </div>
            <div class="col-auto col-md-8">
                <div class="input-group mb-3">
                    <form method="post" action="{{ route('cetak') }}" id="printForm" target="_blank">
                        @csrf
                        <input type="hidden" name="ujian_id" id="ujian_id" value="{{ $ujian->id }}">
                        <button class="btn btn-info"><i class="bi bi-printer"></i>Cetak</button>
                    </form>
                </div>
            </div>
        </div>
        @if ($hasil->count())
        <div class="row">
            <div class="col-md-12">
                <div class="flash-data" data-flashdata="{{ session('success') }}">
                </div>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Npm</th>
                            <th>Nama Mahasiswa</th>
                            <th>Skor Akhir</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($hasil as $has)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $has->user->npm }}</td>
                            <td>{{ $has->user->nama }}</td>
                            <td>{{ $has->nilai }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @else
        <p class="text-center fs-4">Tidak Ada Data
            {{ $title }}</p>
        @endif
    </div>
</div>
@endsection