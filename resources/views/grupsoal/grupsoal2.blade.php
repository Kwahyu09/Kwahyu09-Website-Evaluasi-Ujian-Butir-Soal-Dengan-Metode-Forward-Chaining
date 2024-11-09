@extends('layoutdashboard.main')@section('container')
<h5>Data
    {{ $title }}
    Modul
    {{ $modul }}</h5>
<div class="d-flex justify-content-start mb-4 mt-3">
    <div class="d-flex justify-content-start">
        <a class="ml-1 btn btn-danger float-right mr-3" href="/grupsoal"><- Kembali</a>
        <a href="/grupsoal/create/{{ $slug }}" class="btn btn-primary">Tambah Data
            <i class="bi bi-plus-circle"></i>
        </a>
    </div>
</div>
<div class="flash-data" data-flashdata="{{ session('success') }}">
</div>
@if ($post->count())
<div class="container">
    <div class="row">
        @foreach ($post as $pos)
        <div class="col-md-3">
            <a style="text-decoration:none" href="/soal/{{ $pos->slug }}">
                <div class="card shadow mb-4">
                    <div class="card-body text-center text-dark">
                        <div class="card-text my-2">
                            <h6></h6>
                            <br>
                            <h5>{{ $pos->nama_grup }}</h5>
                        </div>
                    </div>
                    <!-- ./card-text -->
                    <div class="card-footer">
                        <div class="row align-items-center justify-content-between text-dark">
                            <div class="col-auto">
                                <a href="/soal/{{ $pos->slug }}" class="btn btn-primary stretched-link">Lihat Soal</a>
                            </div>
                            <div class="col-auto">
                                <div class="file-action">
                                    <a href="/grupsoal/{{ $pos->slug }}/edit" class="btn btn-primary btn-action mr-1"
                                        data-toggle="tooltip" title="Ubah">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                    <a href="/grupsoal/{{ $pos->slug }}/delete"
                                        class="btn btn-danger btn-action mr-1 tombol-hapus" data-toggle="tooltip"
                                        title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-footer -->
                </div>
            </a>
        </div>
        @endforeach
    </div>
</div>
@else
<p class="text-center fs-4">Tidak Ada Data
    {{ $title }}</p>
@endif
<div class="d-flex justify-content-end">
    {{ $post->links() }}
</div>

@endsection