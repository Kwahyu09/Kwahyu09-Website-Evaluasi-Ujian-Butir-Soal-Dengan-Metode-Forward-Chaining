@extends('layoutdashboard.main')
@section('container')
    <h5 class="mt-4 mb-4">Data
        {{ $title }}
        Berdasarkan Modul</h5>
    @if ($post->count())
        <div class="container">
            <div class="row">
                @foreach ($post as $pos)
                    <div class="col-md-3">
                        <a style="text-decoration:none" href="/hasilujian/{{ $pos->slug }}">
                            <div class="card shadow">
                                <div class="card-body text-center text-dark">
                                    <div class="card-text">
                                        <h5>Modul</h5>
                                        <br>
                                        <br>
                                        <h6>{{ $pos->nama_modul }}</h6>
                                    </div>
                                </div>
                                <!-- ./card-text -->
                                <div class="card-footer">
                                    <div class="text-center text-dark col-auto">
                                        <h6>Semester :
                                            {{ $pos->semester }}
                                            , Sks :
                                            {{ $pos->sks }}</h6>
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
        <p class="text-center fs-4">Tidak Ditemukan Data Modul</p>
    @endif
    <div class="d-flex justify-content-end">
        {{ $post->links() }}
    </div>
@endsection