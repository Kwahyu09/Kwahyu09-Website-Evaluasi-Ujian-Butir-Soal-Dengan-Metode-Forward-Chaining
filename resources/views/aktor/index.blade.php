@extends('layoutdashboard.main') @section('container')
    <div class="card">
        <div class="card-body">
            <h5 class="mb-2">Data
                {{ $judul }}</h5>
            <div class="d-flex justify-content-start">
                <a href="/{{ $title }}/create" class="btn btn-primary">Tambah Data
                    <i class="bi bi-plus-circle"></i>
                </a>
            </div>
            @if ($post->count())
                <div class="d-flex justify-content-end mb-2">
                    <div class="col-md-4">
                        <form action="/{{ $title }}">
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
                                    <th style="width: 50px; padding: 8px;">No.</th>
                                    @if ($title == 'staff')
                                        <th scope="col">NIK</th>
                                    @elseif($title == 'ketua')
                                        <th scope="col">NIP</th>
                                    @endif
                                    <th scope="col">Nama</th>
                                    <th scope="col">Usename</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($post as $pos)
                                    <tr>
                                        <td tyle="width: 50px; padding: 8px;">{{ ($post->currentPage() - 1) * $post->links()->paginator->perPage() + $loop->iteration }}.
                                        </td>
                                        @if ($title == 'staff')
                                            <td>{{ $pos->nik }}</td>
                                        @elseif($title == 'ketua')
                                            <td>{{ $pos->nip }}</td>
                                        @endif
                                        <td>{{ $pos->nama }}</td>
                                        <td>{{ $pos->username }}</td>
                                        <td>{{ $pos->email }}</td>
                                        <td>
                                            <a href="/{{ $title }}/{{ $pos->username }}/edit"
                                                class="btn btn-primary btn-action mr-1" data-toggle="tooltip"
                                                title="Ubah">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                            <a href="/{{ $title }}/{{ $pos->username }}/delete"
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
                <p class="text-center fs-4">Tidak Ditemukan Data
                    {{ $judul }}</p>
            @endif
            <div class="mt-1 d-flex justify-content-end">
                {{ $post->links() }}
            </div>
        </div>
    </div>
@endsection
