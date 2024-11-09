@extends('layoutdashboard.main')
@section('container')
    <h5 class="mt-4 mb-4">Data Mahasiswa Berdasarkan Kelas</h5>
    @if ($post->count())
        <div class="container">
            <div class="row">
                @foreach ($post as $pos)
                    <div class="col-md-3">
                        <a style="text-decoration:none" href="/kelas/{{ $pos->slug }}">
                            <div class="card shadow">
                                <div class="card-body text-center text-dark">
                                    <div class="card-text">
                                        <h5>Kelas</h5>
                                        <br>
                                        <br>
                                        <h6>{{ $pos->nama_kelas }}</h6>
                                    </div>
                                </div>
                                <!-- ./card-text -->
                                <div class="card-footer">
                                    <div class="row text-center text-dark">
                                            <h6>
                                                {{ $pos->prodi->nama_prodi }}
                                                <br>
                                                {{ $pos->tahun_ajaran }}
                                            </h6>
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
        <p class="text-center fs-4">Tidak Ada Data Kelas Mahasiswa</p>
    @endif
    <div class="d-flex justify-content-end">
        {{ $post->links() }}
    </div>
@endsection