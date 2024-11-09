@extends('layoutdashboard.main')
@section('container')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-12">
                <h5 class="mb-2">Data Evaluasi Butir Soal</h5>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <div class="d-flex justify-content-start">
                            <form action="/evaluasi/soal" method="post">
                                @csrf
                                <input type="hidden" name="slug" value="{{ $slug }}">
                                <input type="hidden" name="id_ujian" value="{{ $id_ujian }}">
                                <button class=" btn btn-danger float-right" type="submit"><- Kembali</button>
                            </form>
                            <br>
                        </div>
                        <div class="d-flex mt-4">
                            <p><b> <h5>{{ $nosoal }}.</h5> {!! $datasoal->pertanyaan !!}</b></p>
                        </div>
                    </div>
                    @if($soal->isNotEmpty())
                    <div class="form-group col-md-6">
                        <form action="/evaluasi/butirsoal" method="post">
                            @csrf
                            <input type="hidden" name="slug" value="{{ $slug }}">
                            <input type="hidden" name="ujian_id" value="{{ $id_ujian }}">
                            <input type="hidden" name="nosoal" value="{{ $nosoal }}">
                            <input type="hidden" name="soal_id" value="{{ $soal_id }}">
                            <button class="btn btn-primary ml-3" type="submit">Lihat Evaluasi Soal</button>
                        </form>
                        <br>
                        @if($kesimpulan->isNotEmpty())
                            @foreach ($kesimpulan as $kes)
                                <h6 class="ml-3">Soal Kategori : {{ $kes->basis_pengetahuan->keterangan }} 
                                </h6>
                            @endforeach
                        @endif
                    </div>
                    @endif
                </div><br>
                @if($datasoal->gambar)
                <img class="mb-2" style="border: 1px solid black;" src="{{ asset('storage/' . $datasoal->gambar) }}"
                    alt="Gambar" width="300px">
                @endif
                <form action="">
                    <input type="hidden" class="label-input" value="{!! $datasoal->opsi_a !!}">
                    <input type="hidden" class="label-input" value="{!! $datasoal->opsi_b !!}">
                    <input type="hidden" class="label-input" value="{!! $datasoal->opsi_c !!}">
                    <input type="hidden" class="label-input" value="{!! $datasoal->opsi_d !!}">
                    <input type="hidden" class="label-input" value="{!! $datasoal->opsi_e !!}">
                    <input type="hidden" class="data-input" value="{{ $opsia }}">
                    <input type="hidden" class="data-input" value="{{ $opsib }}">
                    <input type="hidden" class="data-input" value="{{ $opsic }}">
                    <input type="hidden" class="data-input" value="{{ $opsid }}">
                    <input type="hidden" class="data-input" value="{{ $opsie }}">
                </form>
                @if (preg_match('/^gambar-soal\//', $datasoal->jawaban))
                Jawaban : <h6>{{-- Cek apakah jawaban siswa adalah salah satu dari opsi --}}
                    @if($datasoal->jawaban == $datasoal->opsi_a)
                        A .
                    @elseif($datasoal->jawaban == $datasoal->opsi_b)
                        B .
                    @elseif($datasoal->jawaban == $datasoal->opsi_c)
                        C .
                    @elseif($datasoal->jawaban == $datasoal->opsi_d)
                        D .
                    @elseif($datasoal->jawaban == $datasoal->opsi_e)
                        E . 
                    @else
                        Tidak ada jawaban yang sesuai
                    @endif
                <img class="mb-2" style="border: 1px solid black;"
                    src="{{ asset('storage/' . $datasoal->jawaban) }}" alt="Gambar" width="300px"></h6>
                @else
                <h6 class="d-inline-flex">Jawaban :
                    {{-- Cek apakah jawaban siswa adalah salah satu dari opsi --}}
                    @if($datasoal->jawaban == $datasoal->opsi_a)
                        A .
                    @elseif($datasoal->jawaban == $datasoal->opsi_b)
                        B .
                    @elseif($datasoal->jawaban == $datasoal->opsi_c)
                        C .
                    @elseif($datasoal->jawaban == $datasoal->opsi_d)
                        D .
                    @elseif($datasoal->jawaban == $datasoal->opsi_e)
                        E . 
                    @else
                        Tidak ada jawaban yang sesuai
                    @endif
                     {!! $datasoal->jawaban !!}</h6>
                @endif
            </div>
        </div>
        <div>
            <canvas id="chartContainer" width="400" height="400"></canvas>
        </div>
        <div>
            @if (preg_match('/^gambar-soal\//', $datasoal->opsi_a))
            <img class="mb-2 ml-3" style="border: 1px solid black;" src="{{ asset('storage/' . $datasoal->opsi_a) }}"
                alt="Gambar" width="220px">
            @endif
            @if (preg_match('/^gambar-soal\//', $datasoal->opsi_b))
            <img class="mb-2 ml-1" style="border: 1px solid black;" src="{{ asset('storage/' . $datasoal->opsi_b) }}"
                alt="Gambar" width="220px">
            @endif
            @if (preg_match('/^gambar-soal\//', $datasoal->opsi_c))
            <img class="mb-2 ml-1" style="border: 1px solid black;" src="{{ asset('storage/' . $datasoal->opsi_c) }}"
                alt="Gambar" width="220px">
            @endif
            @if (preg_match('/^gambar-soal\//', $datasoal->opsi_d))
            <img class="mb-2 ml-1" style="border: 1px solid black;" src="{{ asset('storage/' . $datasoal->opsi_d) }}"
                alt="Gambar" width="220px">
            @endif
            @if (preg_match('/^gambar-soal\//', $datasoal->opsi_e))
            <img class="mb-2 ml-1" style="border: 1px solid black;" src="{{ asset('storage/' . $datasoal->opsi_e) }}"
                alt="Gambar" width="220px">
            @endif
        </div>
        @if ($soal->count())
        <div class="row mt-3">
            <div class="col-12">
                <div class="flash-data" data-flashdata="{{ session('success') }}">
                </div>
                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped" id="sortable-table">
                                <thead>
                                    <tr style="background-color: lightslategray;">
                                        <th style="width: 50px">No</th>
                                        <th>Npm Mahasiswa</th>
                                        <th>Nama Mahasiswa</th>
                                        <th>Jawaban</th>
                                        <th>Skor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($soal as $s)
                                    <tr>
                                        <td style="width: 50px">{{ $loop->iteration }}</td>
                                        <td>{{ $s->user->npm }}</td>
                                        <td>{{ $s->user->nama }}</td>
                                        <td>
                                            {{-- Cek apakah jawaban siswa adalah salah satu dari opsi --}}
                                            @if($s->jawaban == $datasoal->opsi_a)
                                                Opsi A
                                            @elseif($s->jawaban == $datasoal->opsi_b)
                                                Opsi B
                                            @elseif($s->jawaban == $datasoal->opsi_c)
                                                Opsi C
                                            @elseif($s->jawaban == $datasoal->opsi_d)
                                                Opsi D
                                            @elseif($s->jawaban == $datasoal->opsi_e)
                                                Opsi E
                                            @else
                                                Tidak ada jawaban yang sesuai
                                            @endif
                                        </td>
                                        <td>{{ $s->skor }}</td>
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
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        var ctx = document.getElementById("chartContainer").getContext("2d");

        // Mengambil elemen input type hidden untuk data dan label
        var dataInputs = document.getElementsByClassName("data-input");
        var labelInputs = document.getElementsByClassName("label-input");

        // Mendapatkan data dan label dari input type hidden
        var data = [];
        var labels = [];

        for (var i = 0; i < dataInputs.length; i++) {
            data.push(dataInputs[i].value);
            var labelValue = labelInputs[i].getAttribute("value");

            // Jika label diawali dengan 'gambar-soal/', gunakan teks 'Gambar' sebagai placeholder
            if (labelValue.startsWith('gambar-soal/')) {
                labels.push('Gambar');
            } else {
                // Membersihkan tag HTML dari teks label sebelum menambahkannya ke dalam array labels
                var cleanLabel = labelValue.replace(/(<([^>]+)>)/gi, ""); // Membersihkan tag HTML
                // Jika tidak diawali dengan 'gambar-soal/', tambahkan teks biasa ke array labels
                labels.push(cleanLabel);
            }
        }

        // Membuat objek diagram batang
        var barChart = new Chart(ctx, {
            type: "bar",
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: "rgba(0, 123, 255, 0.5)",
                    borderColor: "rgba(0, 123, 255, 1)",
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });

</script>
@endsection
