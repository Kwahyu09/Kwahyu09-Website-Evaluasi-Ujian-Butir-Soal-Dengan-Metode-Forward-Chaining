@extends('layoutdashboard.main')
@section('container')
    <div class="card">
        <div class="card-body">
            <h5 class="mb-2">Data Perhitungan Evaluasi Butir Soal</h5>
            <div class="form-row">
                <div class="form-group col-md-6 mt-3">
                    <div class="d-flex justify-content-start">
                        <form action="/evaluasi/show" method="post" class="mr-2">
                            @csrf
                            <input type="hidden" name="slug" value="{{ $slug }}">
                            <input type="hidden" name="ujian_id" value="{{ $id_ujian }}">
                            <input type="hidden" name="nosoal" value="{{ $nosoal }}">
                            <input type="hidden" name="soal_id" value="{{ $soal_id }}">
                            <button class="btn btn-danger" type="submit"><- Kembali</button>
                        </form>
                        <form action="/evaluasi/hitung" method="POST" class="mr-2">
                            @csrf
                                <input type="hidden" name="slug" value="{{ $slug }}">
                                <input type="hidden" name="ujian_id" value="{{ $id_ujian }}">
                                <input type="hidden" name="nosoal" value="{{ $nosoal }}">
                                <input type="hidden" name="soal_id" value="{{ $soal_id }}">
                                <button class="btn btn-success" type="submit">Analisis Soal</button>
                        </form>
                        <form action="{{ route('resetdata') }}" method="POST" class="mr-2">
                            @csrf
                            <input type="hidden" name="slug" value="{{ $slug }}">
                            <input type="hidden" name="ujian_id" value="{{ $id_ujian }}">
                            <input type="hidden" name="nosoal" value="{{ $nosoal }}">
                            <input type="hidden" name="soal_id" value="{{ $soal_id }}">
                            <button class="btn btn-primary" type="submit">Reset Data</button>
                        </form>
                    </div>
                </div>
            </div>
            <h6 class="mb-1">Soal No : {{ $nosoal }}</h6> 
            @if($kesimpulan->isNotEmpty())
            @foreach ($kesimpulan as $kes)                                       
                <h6>Kesimpulan Klasifikasi : {{ $kes->basis_pengetahuan->keterangan }}</h6>
            @endforeach
        @endif
        </br>
        <input type="checkbox" id="lihatrumus" name="lihatrumus"> Lihat Rumus Perhitungan
            <div class="row" id="rumus" style="display: none;">
                <h3 class="text-center">Rumus</h3>
                <div class="form-row text-center mb-0">
                    <div class="form-group col-md-4">
                        <label for="inputAddress">Daya Pembeda</label> </br>
                        <img class="text-center" src="/rumus/dayapembeda.png" alt="Daya Pembeda" width="40%">
                        <p style="font-size: 12px;" class="text-start">DP = daya pembeda soal </br>
                            Ka = banyak mahasiswa pada kelompok atas yang menjawab benar</br>
                            Kb = banyak mahasiswa pada kelompok bawah yang menjawab benar</br>
                            Na = banyak mahasiswa pada kelompok atas</br>
                            Nb = banyak mahasiswa pada kelompok bawah</br>
                            </p>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputAddress2">Tingkat Kesukaran Soal</label></br>
                        <img class="text-center" src="/rumus/tingkatkesukaran.png" alt="Tingkat Kesukaran" width="25%">
                        <p style="font-size: 12px;" class="text-start ml-5">TK = tingkat kesukaran </br> 
                            JB = banyak mahasiswa yang menjawab benar</br>
                            N  = banyak mahasiswa
                            </p>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputAddress2">Penyebaran Pilihan Jawaban</label></br>
                        <img class="text-center" src="/rumus/ppj.png" alt="Tingkat Kesukaran" width="30%">
                        <p style="font-size: 12px;" class="text-start ml-1">Ppj	= penyebaran jawaban untuk pilihan jawaban soal tertentu</br>
                            Jpj	= banyak mahasiswa yang memilih pilihan jawaban tertentu</br>
                            N   = banyak mahasiswa
                            </p>
                    </div>
                </div>
                <div class="form-row text-center mt-0">
                    <div class="form-group col-md-4">
                        <table class="custom-table" style="width:90%; border-collapse: collapse; border: 1px solid black;">
                            <thead>
                                <!-- Baris kedua untuk Kolom Pengecoh -->
                                <tr>
                                    <th style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">Kriteria Daya Pembeda</th>
                                    <th style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">DP > 0,25</td>
                                    <td style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">Diterima</td>
                                </tr>
                                <tr>
                                    <td style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;"> 0 < DP < 0,25</td>
                                    <td style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">Diperbaiki</td>
                                </tr>
                                <tr>
                                    <td style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;"> DP ≤ 0 </td>
                                    <td style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">Ditolak</td>
                                </tr>
                            </tbody>                                
                        </table>
                        <p style="text-align:start">
                            Daya pembeda Pengecoh baik jika memiliki nilai negatif(-)
                        </p>
                    </div>
                    <div class="form-group col-md-4">
                        <table class="custom-table" style="width:90%; border-collapse: collapse; border: 1px solid black;">
                            <thead>
                                <!-- Baris kedua untuk Kolom Pengecoh -->
                                <tr>
                                    <th style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">Kriteria Tingkat Kesukaran</th>
                                    <th style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">TK < 0,3</td>
                                    <td style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">Sukar</td>
                                </tr>
                                <tr>
                                    <td style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;"> 0,3 ≤ TK ≤ 0,7</td>
                                    <td style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">Sedang</td>
                                </tr>
                                <tr>
                                    <td style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;"> DP > 0,7 </td>
                                    <td style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">Mudah</td>
                                </tr>
                            </tbody>                                
                        </table>
                    </div>
                    <div class="form-group col-md-4">
                        <table class="custom-table" style="width:90%; border-collapse: collapse; border: 1px solid black;">
                            <thead>
                                <!-- Baris kedua untuk Kolom Pengecoh -->
                                <tr>
                                    <th style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">Kriteria Penyebaran Pilihan Jawaban</th>
                                    <th style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">PPJ ≥ 0,025</td>
                                    <td style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">Pengecoh Berfungsi</td>
                                </tr>
                            </tbody>                                
                        </table>
                    </div>
                </div>
            </div>
        </br>
            @if($kelompok_mahasiswa->isNotEmpty())
                <input type="checkbox" id="lihatkelompok" name="kelompoksiswa"> Lihat Data Kelompok Mahasiswa
                <div class="row mt-2" id="kelompokTable" style="display: none;">
                    <div class="col-md-12">
                        <div class="flash-data" data-flashdata="{{ session('success') }}">
                        </div>
                        <table class="custom-table" style="width:100%;">
                            <thead>
                                <tr>
                                    <th style="padding: 3px;">No</th>
                                    <th style="padding: 3px;">Npm</th>
                                    <th style="padding: 3px;">Nama Mahasiswa</th>
                                    <th style="padding: 3px;">Skor Akhir</th>
                                    <th style="padding: 3px;">Skor Soal</th>
                                    <th style="padding: 3px;">Kelompok</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($kelompok_mahasiswa as $index => $kel)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $kel->user->npm }}</td>
                                    <td>{{ $kel->user->nama }}</td>
                                    <td>{{ $kel->nilai }}</td>
                                    <td>{{ $skor_evaluasi[$index] }}</td>
                                    <td>{{ $kel->kelompok }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </br>
            <input type="checkbox" id="perhitungan" name="perhitungan"> Lihat Perhitungan
                <div class="row mt-2" id="lihatperhitungan" style="display: none;">
                    <div class="form-row mt-1">
                        <div class="form-group col-md-4">
                            <label class="text-start">Diketahui : </label> </br>
                            <p style="font-size: 12px;" class="text-center">
                                <table class="custom-table" style="width:100%; border-collapse: collapse; border: 1px solid black;">
                                    <thead>
                                        <!-- Baris pertama untuk Daya Pembeda -->
                                        <tr>
                                            <th rowspan="3" style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">Kelompok</th>
                                            <th colspan="6" style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">Daya Pembeda Soal</th>
                                        </tr>
                                        <!-- Baris kedua untuk Kolom Pengecoh -->
                                        <tr>
                                            <th rowspan="2" style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">Soal</th>
                                            <th colspan="5" style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">Pengecoh</th>
                                        </tr>
                                        <!-- Baris ketiga untuk A, B, C, D, E -->
                                        <tr>
                                            <th style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">A</th>
                                            <th style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">B</th>
                                            <th style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">C</th>
                                            <th style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">D</th>
                                            <th style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">E</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">Kelompok Atas (Ka)</td>
                                            <td style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">{{ $Ka }}</td>
                                            <td style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">{{ $dp_a_atas }}</td>
                                            <td style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">{{ $dp_b_atas }}</td>
                                            <td style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">{{ $dp_c_atas }}</td>
                                            <td style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">{{ $dp_d_atas }}</td>
                                            <td style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">{{ $dp_e_atas }}</td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">Kelompok Bawah (Kb)</td>
                                            <td style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">{{ $Kb }}</td>
                                            <td style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">{{ $dp_a_bawah }}</td>
                                            <td style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">{{ $dp_b_bawah }}</td>
                                            <td style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">{{ $dp_c_bawah }}</td>
                                            <td style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">{{ $dp_d_bawah }}</td>
                                            <td style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">{{ $dp_e_bawah }}</td>
                                        </tr>
                                    </tbody>                                
                                </table>
                                </p>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="inputAddress2"></label></br>
                            <p style="font-size: 12px;" class="text-start ml-5"> 
                                Banyak Mahasiswa Yang Menjawab Benar (JB) = {{ $JB }}</br>
                                Jumlah Mahasiswa (N) = {{ $N }}</br>
                                </p>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="inputAddress2"></label></br>
                            <p style="font-size: 12px;" class="text-start ml-1">
                                <table class="custom-table" style="width:100%; border-collapse: collapse; border: 1px solid black;">
                                    <thead>
                                        <!-- Baris kedua untuk Kolom Pengecoh -->
                                        <tr>
                                            <th rowspan="2" style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">No. Soal</th>
                                            <th colspan="5" style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">Penyebaran Pilihan Jawaban</th>
                                        </tr>
                                        <!-- Baris ketiga untuk A, B, C, D, E -->
                                        <tr>
                                            <th style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">A</th>
                                            <th style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">B</th>
                                            <th style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">C</th>
                                            <th style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">D</th>
                                            <th style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">E</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">{{ $nosoal }}</td>
                                            <td style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">{{ $ppj_a }}</td>
                                            <td style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">{{ $ppj_b }}</td>
                                            <td style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">{{ $ppj_c }}</td>
                                            <td style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">{{ $ppj_d }}</td>
                                            <td style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">{{ $ppj_e }}</td>
                                        </tr>
                                    </tbody>                                
                                </table>
                                </p>
                        </div>
                    </div>
                    <form action="/evaluasi/analisisnilai" method="POST" class="mr-2">
                        @csrf
                            <input type="hidden" name="slug" value="{{ $slug }}">
                            <input type="hidden" name="ujian_id" value="{{ $id_ujian }}">
                            <input type="hidden" name="nosoal" value="{{ $nosoal }}">
                            <input type="hidden" name="soal_id" value="{{ $soal_id }}">
                            <input type="hidden" name="Ka" value="{{ $Ka }}">
                            <input type="hidden" name="dp_a_atas" value="{{ $dp_a_atas }}">
                            <input type="hidden" name="dp_b_atas" value="{{ $dp_b_atas }}">
                            <input type="hidden" name="dp_c_atas" value="{{ $dp_c_atas }}">
                            <input type="hidden" name="dp_d_atas" value="{{ $dp_d_atas }}">
                            <input type="hidden" name="dp_e_atas" value="{{ $dp_e_atas }}">
                            <input type="hidden" name="Kb" value="{{ $Kb }}">
                            <input type="hidden" name="dp_a_bawah" value="{{ $dp_a_bawah }}">
                            <input type="hidden" name="dp_b_bawah" value="{{ $dp_b_bawah }}">
                            <input type="hidden" name="dp_c_bawah" value="{{ $dp_c_bawah }}">
                            <input type="hidden" name="dp_d_bawah" value="{{ $dp_d_bawah }}">
                            <input type="hidden" name="dp_e_bawah" value="{{ $dp_e_bawah }}">
                            <input type="hidden" name="Jb" value="{{ $JB }}">
                            <input type="hidden" name="N" value="{{ $N }}">
                            <input type="hidden" name="ppj_a" value="{{ $ppj_a }}">
                            <input type="hidden" name="ppj_b" value="{{ $ppj_b }}">
                            <input type="hidden" name="ppj_c" value="{{ $ppj_c }}">
                            <input type="hidden" name="ppj_d" value="{{ $ppj_d }}">
                            <input type="hidden" name="ppj_e" value="{{ $ppj_e }}">
                            <button class="btn btn-success" type="submit">Analisis Nilai</button>
                    </form>
                    @if($analisisnilai->isNotEmpty())
                    <div class="d-flex align-items-center mt-3">
                        <input class="text-start mt-2" type="checkbox" id="analisisnilai" name="kelompoksiswa"> 
                        Lihat Data Analisis Nilai
                    </div>
                        <div class="row text-start mt-3 " id="analisisnilai1" style="display: none;">
                            <p style="font-size: 12px;" class="text-center">
                                <table class="custom-table" style="width:100%; border-collapse: collapse; border: 1px solid black;">
                                    <thead>
                                        <!-- Baris pertama untuk Daya Pembeda -->
                                        <tr>
                                            <th rowspan="4" style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">Soal No.</th>
                                            <th colspan="6" style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">Daya Pembeda Soal</th>
                                            <th rowspan="4" style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">Tingkat Kesukaran</th>
                                            <th colspan="5" rowspan="3" style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">Penyebaran Pilihan Jawaban(Pengecoh)</th>
                                        </tr>
                                        <!-- Baris kedua untuk Kolom Pengecoh -->
                                        <tr>
                                            <th rowspan="3" style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">Soal</th>
                                            <th colspan="5" style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">Pengecoh</th>
                                        </tr>
                                        <tr>
                                            <th rowspan="2" style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">A</th>
                                            <th rowspan="2" style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">B</th>
                                            <th rowspan="2" style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">C</th>
                                            <th rowspan="2" style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">D</th>
                                            <th rowspan="2" style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">E</th>
                                        </tr>
                                        <!-- Baris ketiga untuk A, B, C, D, E -->
                                        <tr>
                                            <th style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">A</th>
                                            <th style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">B</th>
                                            <th style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">C</th>
                                            <th style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">D</th>
                                            <th style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">E</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($analisisnilai as $nilai)
                                        <tr>
                                            <td style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">{{ $nosoal }}.</td>
                                            <td style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">{{ $nilai->dp_soal }}</td>
                                            <td style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">{{ $nilai->dp_opsia }}</td>
                                            <td style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">{{ $nilai->dp_opsib }}</td>
                                            <td style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">{{ $nilai->dp_opsic }}</td>
                                            <td style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">{{ $nilai->dp_opsid }}</td>
                                            <td style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">{{ $nilai->dp_opsie }}</td>
                                            <td style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">{{ $nilai->tk_soal }}</td>
                                            <td style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">{{ $nilai->ppj_opsia }}</td>
                                            <td style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">{{ $nilai->ppj_opsib }}</td>
                                            <td style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">{{ $nilai->ppj_opsic }}</td>
                                            <td style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">{{ $nilai->ppj_opsid }}</td>
                                            <td style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">{{ $nilai->ppj_opsie }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>                                
                                </table>
                                <label for="">Klasifikasi : </label>
                                @if($analisisklasifikasi->isNotEmpty())
                                <ul class="ml-3">
                                    @foreach ($analisisklasifikasi as $analisis)                                       
                                        <li>
                                            {{ $analisis->basis_pengetahuan->kode }} - 
                                            {{ $analisis->basis_pengetahuan->keterangan }}
                                        </li>
                                    @endforeach
                                </ul>
                                @endif
                                </p>
                        </div>
                    @else
                        <p style="font-size: 12px;" class="text-start mt-3">Belum Ada Data Analisis Nilai </br>
                            Klik Analisis Nilai terlebih dahulu
                        </p>
                    @endif
                </div>
            @else
                <p style="font-size: 12px;" class="text-start mt-3">Belum Ada Data Perhitungan </br>
                    Klik Analisis Soal terlebih dahulu
                    </p>
            @endif
        </div>
    </div>
    <script>
        document.getElementById('lihatrumus').addEventListener('change', function() {
            var table = document.getElementById('rumus');
            if (this.checked) {
                table.style.display = 'block';
            } else {
                table.style.display = 'none';
            }
        });
    </script>
    <script>
        document.getElementById('lihatkelompok').addEventListener('change', function() {
            var table = document.getElementById('kelompokTable');
            if (this.checked) {
                table.style.display = 'block';
            } else {
                table.style.display = 'none';
            }
        });
        document.getElementById('perhitungan').addEventListener('change', function() {
            var table = document.getElementById('lihatperhitungan');
            if (this.checked) {
                table.style.display = 'block';
            } else {
                table.style.display = 'none';
            }
        });
        document.getElementById('analisisnilai').addEventListener('change', function() {
            var table = document.getElementById('analisisnilai1');
            if (this.checked) {
                table.style.display = 'block';
            } else {
                table.style.display = 'none';
            }
        });
    </script>
@endsection