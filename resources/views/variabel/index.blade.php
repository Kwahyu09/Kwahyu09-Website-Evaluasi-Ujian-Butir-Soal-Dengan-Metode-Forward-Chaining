@extends('layoutdashboard.main')
@section('container')
    <div class="card">
        <div class="card-body">
            <h5 class="mb-2">Data {{ $judul }}</h5>
            <hr>
            {{-- Baris 1: Tabel DP dan PPJ --}}
            <div class="row mb-3 mt-2">
                <div class="col-md-6">
                    <div class="flash-data" data-flashdata="{{ session('success') }}">
                    </div>
                    <h6>Data Variabel Daya Pembeda (DP)</h6>
                    </form> --}}
                    @if ($dp->count())
                        <table class="custom-table" style="width:100%; border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($dp as $item)
                                    <tr>
                                        <td>{{ $item->kode }}</td>
                                        <td>{{ $item->keterangan }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-center fs-4">Tidak Ditemukan Data DP</p>
                    @endif
                </div>
                <div class="col-md-6">
                    <h6>Data Penyebaran Pilihan Jawaban (PPJ)</h6>
                    @if ($ppj->count())
                        <table class="custom-table" style="width:100%; border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ppj as $item)
                                    <tr>
                                        <td>{{ $item->kode }}</td>
                                        <td>{{ $item->keterangan }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-center fs-4">Tidak Ditemukan Data PPJ</p>
                    @endif
                </div>
            </div>
            {{-- Baris 2: Tabel TK  --}}
            <hr>
            <div class="row mb-6">
                <div class="col-md-6">
                    <h6>Data Tingkat Kesukaran (TK)</h6>
                    </form> --}}
                    @if ($tk->count())
                        <table class="custom-table" style="width:100%; border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tk as $item)
                                    <tr>
                                        <td>{{ $item->kode }}</td>
                                        <td>{{ $item->keterangan }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-center fs-4">Tidak Ditemukan Data TK</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection