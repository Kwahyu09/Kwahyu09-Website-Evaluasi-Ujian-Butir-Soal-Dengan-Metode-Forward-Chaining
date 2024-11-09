@extends('layoutdashboard.main') @section('container')
    <div class="card">
        <div class="card-body">
            <h5 class="mb-2">Data
                {{ $title }} Modul : {{ $modul }}</h5>
            <div class="d-flex justify-content-start mt-3">
                <a class="ml-1 btn btn-danger float-right mr-3" href="/dosen/grupmodul"><- Kembali</a>
                        <a href="/dosen/grupdosen/create/{{ $slug }}" class="btn btn-primary">Tambah Anggota
                            <i class="bi bi-plus-circle"></i>
                        </a>
            </div>
            @if ($ketua->count())
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="flash-data" data-flashdata="{{ session('success') }}">
                        </div>
                        <div class="card">
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-striped" id="sortable-table">
                                        <thead>
                                            <tr style="background-color: lightslategray;">
                                                <th>No</th>
                                                <th>NIP/NIK</th>
                                                <th>Nama</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($ketua as $ket)
                                                <tr>
                                                    <td>
                                                        1.
                                                    </td>
                                                    <td>{{ $ket->nip }}</td>
                                                    <td>{{ $ket->nama }}</td>
                                                    <td><b>Ketua Modul</b></td>
                                                    <td> -
                                                    </td>
                                                </tr>
                                            @endforeach
                                            @foreach ($post as $pos)
                                                <tr>
                                                    <td>
                                                        {{ ($post->currentPage() - 1) * $post->links()->paginator->perPage() + $loop->iteration + 1 }}
                                                    </td>
                                                    <td>{{ $pos->dosen->nip }}</td>
                                                    <td>{{ $pos->dosen->nama_dos }}</td>
                                                    <td><b>Anggota</b></td>
                                                    <td>
                                                        <form action="{{ route('hapusanggotagrupdosen') }}" method="POST"
                                                            class="d-inline form-hapusdosen">
                                                            @csrf
                                                            <input type="hidden" name="modul_id"
                                                                value="{{ $modul_id }}">
                                                            <input type="hidden" name="slug"
                                                                value="{{ $slug }}">
                                                            <input type="hidden" name="dosen_id"
                                                                value="{{ $pos->dosen->slug }}">
                                                            <button type="submit"
                                                                class="btn btn-danger btn-action mr-1 tombol-hapusdosen"
                                                                data-toggle="tooltip" title="Hapus">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
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
            {{-- <div class="d-flex justify-content-end">
            {{ $post->links() }}
        </div> --}}
        </div>
    </div>
@endsection