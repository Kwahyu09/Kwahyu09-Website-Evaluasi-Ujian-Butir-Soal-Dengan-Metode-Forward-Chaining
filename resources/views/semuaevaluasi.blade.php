@extends('layoutdashboard.main')
@section('container')
    <div class="card">
        <div class="card-body">
            <h5 class="mb-1">Data
                {{ $title }}
            </h5>
            <div class="form-row">
                <div class="form-group col-md-2">
                    <div class="d-flex justify-content-start">
                        <form action="/evaluasi/soal" method="post">
                            @csrf
                            <input type="hidden" name="slug" value="{{ $slug }}">
                            <input type="hidden" name="id_ujian" value="{{ $id_ujian }}">
                            <button class=" btn btn-danger float-right" type="submit"><- Kembali</button>
                        </form>
                        <form action="/evaluasi/hitungsemua" method="POST" class="mr-2 ml-2">
                            @csrf
                                <input type="hidden" name="slug" value="{{ $slug }}">
                                <input type="hidden" name="ujian_id" value="{{ $id_ujian }}">
                                <button class="btn btn-success" type="submit">Analisis Semua</button>
                        </form>
                        <form action="{{ route('resetdatasemua') }}" method="POST" class="mr-2">
                            @csrf
                            <input type="hidden" name="slug" value="{{ $slug }}">
                            <input type="hidden" name="ujian_id" value="{{ $id_ujian }}">
                            <button class="btn btn-primary" type="submit">Reset Semua</button>
                        </form>
                        <form method="post" action="{{ route('cetaksemua') }}" id="printForm" target="_blank">
                            @csrf
                            <input type="hidden" name="slug" value="{{ $slug }}">
                            <input type="hidden" name="ujian_id" value="{{ $id_ujian }}">
                            <button class="btn btn-info"><i class="bi bi-printer"></i>Cetak</button>
                        </form>
                    </div>
                </div>
            </div>
            @if(isset($tk1) && isset($tk2) && isset($tk3))
                        <h6>Perserntase Klasifikasi Soal pada Ujian Ini:</h6>
                        <label>Persentase Sukar : {{ $tk1 }}%</label></br>
                        <label>Persentase Sedang : {{ $tk2 }}%</label></br>
                        <label>Persentase Mudah : {{ $tk3 }}%</label>
            @endif
            @if ($soal->count())
                <div class="row mt-1">
                    <div class="col-12">
                        <table class="custom-table"
                            style="width: 100%; border-collapse: collapse; border: 1px solid black;">

                            <thead>
                                <tr>
                                    <th style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">Soal No.</th>
                                    <th style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">Opsi A</th>
                                    <th style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">Opsi B</th>
                                    <th style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">Opsi C</th>
                                    <th style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">Opsi D</th>
                                    <th style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">Opsi E</th>
                                    <th style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">Klasifikasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($soal as $s)
                                    <tr>
                                        <td style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">
                                            {{ ($soal->currentPage() - 1)  * $soal->links()->paginator->perPage() + $loop->iteration }}</td>
                                            
                                        @php
                                            // Default background color is green
                                            $bgColorA = 'green';
                                            $bgColorB = 'green';
                                            $bgColorC = 'green';
                                            $bgColorD = 'green';
                                            $bgColorE = 'green';

                                            $basis_pengetahuan_id = $klasifikasiMap[$s->id]->basis_pengetahuan_id ?? null;
                                            if ($basis_pengetahuan_id == 17) {
                                                $bgColorA = 'red';
                                                $bgColorB = 'red';
                                                $bgColorC = 'red';
                                                $bgColorD = 'red';
                                                $bgColorE = 'red';
                                            } else {
                                                $analisisValues = $analisisMap[$s->id] ?? [];

                                                // Check for specific values in the analysis data array
                                                if (in_array(3, $analisisValues) || in_array(12, $analisisValues)) {
                                                    $bgColorA = 'red';
                                                }
                                                if (in_array(4, $analisisValues) || in_array(13, $analisisValues)) {
                                                    $bgColorB = 'red';
                                                }
                                                if (in_array(5, $analisisValues) || in_array(14, $analisisValues)) {
                                                    $bgColorC = 'red';
                                                }
                                                if (in_array(6, $analisisValues) || in_array(15, $analisisValues)) {
                                                    $bgColorD = 'red';
                                                }
                                                if (in_array(7, $analisisValues) || in_array(16, $analisisValues)) {
                                                    $bgColorE = 'red';
                                                }
                                            }
                                            if(!isset($klasifikasiMap[$s->id])){
                                                $bgColorA = '';
                                                $bgColorB = '';
                                                $bgColorC = '';
                                                $bgColorD = '';
                                                $bgColorE = '';
                                            }
                                        @endphp

                                        <td style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle; background-color: {{ $bgColorA }};"></td>
                                        <td style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle; background-color: {{ $bgColorB }};"></td>
                                        <td style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle; background-color: {{ $bgColorC }};"></td>
                                        <td style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle; background-color: {{ $bgColorD }};"></td>
                                        <td style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle; background-color: {{ $bgColorE }};"></td>
                                        
                                        
                                    <td style="width: 50%; padding: 3px; border: 1px solid black; text-align: start; vertical-align: middle;">
                                        @if(isset($klasifikasiMap[$s->id]))
                                                {{ $klasifikasiMap[$s->id]->basis_pengetahuan->keterangan }}
                                                @php
                                                $basis_pengetahuan_id = $klasifikasiMap[$s->id]->basis_pengetahuan_id ?? null;
                                                @endphp
                                        @else
                                            <b><i>Belum Ada Klasifikasi</i></b>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
        </div>
    @else
        <p class="text-center fs-4">Tidak Ada Data Soal
            {{ $title }}</p>
        @endif
        <div class="d-flex justify-content-end">
            {{ $soal->links() }}
        </div>
    </div>
    </div>
@endsection