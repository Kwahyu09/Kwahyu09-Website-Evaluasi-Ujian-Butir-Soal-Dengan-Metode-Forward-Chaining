@extends('layoutdashboard.main')

@section('container')
    <div class="card">
        <div class="card-body">
            <h5 class="mb-2">Data {{ $judul }}</h5>
            <hr>
            <div class="row mb-12">
                <div class="col-md-12">
                    <h6>Data KA</h6>
                    <div class="flash-data" data-flashdata="{{ session('success') }}">
                    </div>
                    @if ($ka->count())
                        <table class="custom-table" style="width:100%; border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ka as $item)
                                    <tr>
                                        <td>{{ $item->kode }}</td>
                                        <td>{{ $item->keterangan }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-center fs-4">Tidak Ditemukan Data {{ $judul }} KA</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
