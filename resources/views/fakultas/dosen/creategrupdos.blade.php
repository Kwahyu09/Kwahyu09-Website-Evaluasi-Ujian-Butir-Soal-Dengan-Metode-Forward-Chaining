@extends('layoutdashboard.main') @section('container')
    <div class="card">
        <div class="card-body">
            <h5 class="mb-2">Tambah Data
                {{ $title }} Modul : {{ $modul }}</h5>
            @if ($dosen->count())
                <div class="row mt-4">
                    <div class="col-12">
                        <form action="/dosen/store/grupdosen" method="post">
                            @csrf
                            <input type="hidden" name="modul_id" id="modul_id" value="{{ $modul_id }}">
                            <input type="hidden" name="slug" id="slug" value="{{ $slug }}">
                            <div class="flash-data" data-flashdata="{{ session('success') }}">
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped" id="sortable-table">
                                    <thead>
                                        <tr style="background-color: lightslategray;">
                                            <th>No</th>
                                            <th>NIP</th>
                                            <th>Nama</th>
                                            <th>Email</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($dosen as $dos)
                                            <tr>
                                                <td>
                                                    {{ ($dosen->currentPage() - 1) * $dosen->links()->paginator->perPage() + $loop->iteration }}
                                                </td>
                                                <td>{{ $dos->nip }}</td>
                                                <td>{{ $dos->nama_dos }}</td>
                                                <td>{{ $dos->email }}</td>
                                                <td>
                                                    @if ($dos->nip == $userNIP)
                                                        <input class="form-check-input" type="checkbox" disabled>
                                                        <span class="text-muted">Ketua Modul</span>
                                                    @elseif (in_array($dos->id, $existingDosenIds))
                                                        <input class="form-check-input" type="checkbox" disabled>
                                                        <span class="text-muted">Sudah ada di grup</span>
                                                    @else
                                                        <input class="form-check-input" type="checkbox" name="dosen_id[]"
                                                            value="{{ $dos->id }}">
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer mt-0">
                                <a class="ml-1 btn btn-danger float-right"
                                    href="/dosen/grupdosen/{{ $slug }}">Batal</a>
                                <button class="btn btn-primary float-right" type="submit">Tambah</button>
                            </div>
                        </form>
                    </div>
                </div>
            @else
                <p class="text-center fs-4">Tidak Ada Data
                    {{ $title }}</p>
            @endif
            <div class="d-flex justify-content-end">
                {{ $dosen->links() }}
            </div>
        </div>
    </div>
@endsection
