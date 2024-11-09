@extends('layoutdashboard.main') @section('container')
<div class="card">
    <div class="card-body">
        <h5 class="mb-2">Data
            {{ $title }}</h5>
        <div class="d-flex justify-content-start">
            <a href="/ujian/create" class="btn btn-primary">Tambah Data
                <i class="bi bi-plus-circle"></i>
            </a>
        </div>
        @if ($post->count())
        <div class="d-flex justify-content-end mb-2">
            <div class="col-md-4">
                <form action="/ujian">
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
                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped" id="sortable-table">
                                <thead>
                                    <tr>
                                        <th style="width: 30px; padding: 8px;">No.</th>
                                        <th style="width: 50px; padding: 8px;">Kode Ujian</th>
                                        <th style="width: 200px; padding: 8px;">Nama Ujian</th>
                                        <th style="width: 30px; padding: 10px;">Kelas</th>
                                        <th style="width: 30px; padding: 10px;">Modul</th>
                                        <th style="width: 30px; padding: 10px;">Grup Soal</th>
                                        <th style="width: 30px; padding: 10px;">Tanggal</th>
                                        <th style="width: 30px; padding: 10px;">Waktu Mulai</th>
                                        <th style="width: 30px; padding: 10px;">Waktu Selesai</th>
                                        <th style="width: 30px; padding: 8px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($post as $pos)
                                    <tr>
                                        <td style="width: 30px; padding: 8px;">{{ ($post->currentPage() - 1)  * $post->links()->paginator->perPage() + $loop->iteration }}.
                                        </td>
                                        <td style="width: 50px; padding: 8px;">{{ $pos->kd_ujian }}</td>
                                        <td style="width: 100px; white-space: nowrap; padding: 8px;">{{ $pos->nama_ujian }}</td>
                                        <td style="width: 30px; padding: 10px;">{{ $pos->kelas->nama_kelas }} {{ $pos->kelas->tahun_ajaran }}</td>
                                        <td style="width: 30px; padding: 10px;">{{ $pos->modul->nama_modul }}</td>
                                        <td style="width: 30px; padding: 10px;">{{ $pos->grup_soal->nama_grup }}</td>
                                        <td style="width: 30px; white-space: nowrap; padding: 10px;">{{ $pos->tanggal }}</td>
                                        <td style="width: 30px; padding: 10px;">{{ $pos->waktu_mulai }}</td>
                                        <td style="width: 30px; padding: 10px;">{{ $pos->waktu_selesai }}</td>
                                        <td style="width: 50px; white-space: nowrap; padding: 8px;">
                                            <a href="/ujian/{{ $pos->slug }}/edit"
                                                class="btn btn-primary btn-action mr-1" data-toggle="tooltip"
                                                title="Ubah">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                            <a href="/ujian/{{ $pos->slug }}/delete"
                                                class="btn btn-danger btn-action mr-1 tombol-hapus"
                                                data-toggle="tooltip" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @else
        <p class="text-center fs-4">Tidak Ada Data
            {{ $title }}</p>
        @endif
        <div class="d-flex justify-content-end">
            {{ $post->links() }}
        </div>
    </div>
</div>
@endsection