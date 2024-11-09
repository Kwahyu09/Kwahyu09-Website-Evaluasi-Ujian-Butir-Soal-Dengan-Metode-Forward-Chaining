@extends('layoutdashboard.main')
@section('container')
<div class="card">
    <div class="card-body">
        <h5 class="mb-2">Data {{ $title }} Kelas {{ $nama_kelas }}</h5>
        <div class="d-flex justify-content-start">
            <a class="ml-1 btn btn-danger float-right mr-3" href="/kelasmahasiswa"><- Kembali</a>
            <a href="/mahasiswa/create/{{ $kelas }}" class="btn btn-primary mr-3">
                <span class="fe fe-plus-circle fe-12 mr-2"></span>Tambah <i class="bi bi-plus-circle"></i>
            </a>
            <a href="/mahasiswa/import/{{ $kelas }}" class="btn btn-success">
                Import Data <i class="bi bi-file-earmark-arrow-down"></i>
            </a>
        </div>
        @if ($post->count())
        <div class="d-flex justify-content-end mb-2">
            <div class="col-md-4">
                <form action="{{ url()->full() }}">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="Cari.." name="search"
                            value="{{ request('search') }}">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="flash-data" data-flashdata="{{ session('success') }}">
                </div>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col" tyle="width: 50px; padding: 8px;">No.</th>
                            <th scope="col">NPM</th>
                            <th scope="col">Nama</th>
                            <th scope="col">Usename</th>
                            <th scope="col">Email</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($post as $pos)
                        <tr>
                            <td tyle="width: 50px; padding: 8px;">{{ $loop->iteration }}.</td>
                            <td>{{ $pos->npm }}</td>
                            <td>{{ $pos->nama }}</td>
                            <td>{{ $pos->username }}</td>
                            <td>{{ $pos->email }}</td>
                            <td>
                                <a href="/mahasiswa/{{ $pos->username }}/edit" class="btn btn-primary btn-action mr-1"
                                    data-toggle="tooltip" title="Ubah">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                                <a href="/mahasiswa/{{ $pos->username }}/delete"
                                    class="btn btn-danger btn-action mr-1 tombol-hapus" data-toggle="tooltip"
                                    title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
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