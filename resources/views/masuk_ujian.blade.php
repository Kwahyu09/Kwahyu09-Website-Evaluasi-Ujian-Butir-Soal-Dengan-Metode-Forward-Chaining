@extends('layoutdashboard.main')
@section('container')
<div class="row">
    @if(session('ujian_selesai'))
    <div class="alert alert-info">
        Anda sudah menyelesaikan ujian ini.
    </div>
    @else
    @if ($jam > $ujian->waktu_selesai)
    <div class="col-md-12 col-sm-12 mb-3 mt-3 d-flex justify-content-center">
        <div class="card">
            <div class="card-body">
                <form action="/selesaiujian" action="post">
                    <div class="form-group text-center">
                        <label for="" class="text-center">Waktu Ujian Sudah Habis</label><br>
                        <label for="" class="text-center">Klik Tombol selesai ujian untuk menyelesaikan
                            ujian</label><br>
                    </div>
                    <input type="hidden" name="ujian_id" id="ujian_id" value="{{ $ujian->id }}">
                    <input type="hidden" name="totalbobot" id="totalbobot" value="{{ $totalbobot }}">
                    <input type="hidden" name="user_id" id="user_id" value="{{ Auth::user()->id }}">
                    <div class="card-footer mr-3 mb-3 mt-0 d-flex justify-content-center">
                        <button class="btn btn-danger mt-3" type="submit">Selesai Ujian</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @else
    @if($soal)
    <div class="col-md-7 col-sm-10 mb-3 ml-3">
        <div class="card">
            <div class="card-body">
                @foreach ($soal as $s)
                <div class="soal" id="soal-{{ $loop->iteration }}" style="display: none;">
                    @php
                    $soal_id = $s->id;
                    $evaluasi_exists = $evaluasi->contains('soal_id', $soal_id);
                    @endphp
                    @if ($evaluasi->count() > 0 && $evaluasi_exists)
                    <form method="post"
                        action="{{ route('ujian-mahasiswa-update', ['id' => $evaluasi->where('soal_id', $soal_id)->first()->id]) }}">
                        <!-- Konten form -->
                        @method('put')
                        @csrf
                    @else
                        <form method="POST" action="{{ route('ujian-mahasiswa-tambah') }}">
                            <!-- Konten form -->
                            @csrf
                    @endif
                            <div class="form-row">
                                <input type="hidden" name="soal_id" id="soal_id" value="{{ $s->id }}">
                                <input type="hidden" name="skor" id="skor" value="{{ $s->bobot }}">
                                <input type="hidden" name="slug" id="slug" value="{{ $ujian->slug }}">
                                <input type="hidden" name="ujian_id" id="ujian_id" value="{{ $ujian->id }}">
                                <input type="hidden" name="page" id="page" value="{{ $loop->iteration }}">
                                <input type="hidden" name="pt" id="pt" value="{{ $jumlahsoal }}">
                                <input type="hidden" name="user_id" id="user_id"
                                    value="{{ Auth::user()->id }}">
                                <div class="col-md-0">
                                    <p>{{ $loop->iteration }}.</p>
                                </div>
                                <div class="col-md-11 mb-3">
                                    {!! $s->pertanyaan !!}
                                </div>
                            </div>
                            <div class="form-group">
                                @if($s->gambar)
                                <img class="mb-3" style="border: 1px solid black;"
                                    src="{{ asset('storage/' . $s->gambar) }}" alt="Gambar" width="500px">
                                @endif
                                <div class="form-check mb-2">
                                    <input id="jawabana" class="form-check-input" type="radio" @foreach($evaluasi as $eval)
                                        @if($eval->soal_id == $s->id && $eval->jawaban == $s->opsi_a)
                                    checked
                                    @endif
                                    @endforeach
                                    name="jawaban"
                                    value="{{ $s->opsi_a }}">
                                    @if (preg_match('/^gambar-soal\//', $s->opsi_a))
                                    <img class="mb-2" style="border: 1px solid black;"
                                        src="{{ asset('storage/' . $s->opsi_a) }}" alt="Gambar" width="500px">
                                    @else
                                    <label class="form-check-label">{!! $s->opsi_a !!}</label>
                                    @endif
                                </div>
                                <div class="form-check mb-2">
                                    <input id="jawabanb" class="form-check-input" type="radio" @foreach($evaluasi as $eval)
                                        @if($eval->soal_id == $s->id && $eval->jawaban == $s->opsi_b)
                                    checked
                                    @endif
                                    @endforeach
                                    name="jawaban"
                                    value="{{ $s->opsi_b }}">
                                    @if (preg_match('/^gambar-soal\//', $s->opsi_b))
                                    <img class="mb-2" style="border: 1px solid black;"
                                        src="{{ asset('storage/' . $s->opsi_b) }}" alt="Gambar" width="500px">
                                    @else
                                    <label class="form-check-label">{!! $s->opsi_b !!}</label>
                                    @endif
                                </div>
                                <div class="form-check mb-2">
                                    <input id="jawabanc" class="form-check-input" type="radio" name="jawaban"
                                        @foreach($evaluasi as $eval) @if($eval->soal_id == $s->id && $eval->jawaban ==
                                    $s->opsi_c)
                                    checked
                                    @endif
                                    @endforeach
                                    value="{{ $s->opsi_c }}">
                                    @if (preg_match('/^gambar-soal\//', $s->opsi_c))
                                    <img class="mb-2" style="border: 1px solid black;"
                                        src="{{ asset('storage/' . $s->opsi_c) }}" alt="Gambar" width="500px">
                                    @else
                                    <label class="form-check-label">{!! $s->opsi_c !!}</label>
                                    @endif
                                </div>
                                <div class="form-check mb-2">
                                    <input id="jawaband" class="form-check-input" type="radio" name="jawaban" @foreach($evaluasi as
                                        $eval) @if($eval->soal_id == $s->id && $eval->jawaban == $s->opsi_d)
                                    checked
                                    @endif
                                    @endforeach
                                    value="{{ $s->opsi_d }}">
                                    @if (preg_match('/^gambar-soal\//', $s->opsi_d))
                                    <img class="mb-2" style="border: 1px solid black;"
                                        src="{{ asset('storage/' . $s->opsi_d) }}" alt="Gambar" width="500px">
                                    @else
                                    <label class="form-check-label">{!! $s->opsi_d !!}</label>
                                    @endif
                                </div>
                                <div class="form-check">
                                    <input id="jawabane" class="form-check-input" type="radio" name="jawaban" @foreach($evaluasi as
                                        $eval) @if($eval->soal_id == $s->id && $eval->jawaban == $s->opsi_e)
                                    checked
                                    @endif
                                    @endforeach
                                    value="{{ $s->opsi_e }}">
                                    @if (preg_match('/^gambar-soal\//', $s->opsi_e))
                                    <img class="mb-2" style="border: 1px solid black;"
                                        src="{{ asset('storage/' . $s->opsi_e) }}" alt="Gambar" width="500px">
                                    @else
                                    <label class="form-check-label">{!! $s->opsi_e !!}</label>
                                    @endif
                                </div>
                                @error('jawaban')
                                <p class="text-danger">
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>
                            <div class="form-group mr-3 mb-1 mt-0 d-flex justify-content-center">
                        <button class="btn btn-primary" data-soal="{{ $loop->iteration }}" type="submit" style="display: none;">Tambah
                            Jawaban</button>
                        </form>
                        @if ($evaluasi->count() > 0 && $evaluasi_exists)
                            <form class="ragu" action="/update-ragu-ragu" method="POST">
                                <div class="form-check mb-2 tombol-ragu justify-content-center">
                                    @csrf
                                    <input type="hidden" name="page" value="{{ $loop->iteration }}">
                                    <input type="hidden" name="soal_id" value="{{ $s->id }}">
                                    <input type="hidden" name="slug" value="{{ $ujian->slug }}">
                                    <input type="hidden" name="ujian_id" value="{{ $ujian->id }}">
                                    <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                                    <input id="raguragu-{{ $s->id }}" class="raguragu" type="checkbox" name="raguragu"
                                        @if ($evaluasi->where('soal_id', $s->id)->where('user_id', Auth::user()->id)->first()->ragu) checked @endif>
                                    <label for="raguragu-{{ $s->id }}" class="form-check-label">Ragu-ragu</label>
                                    <button class="btn btn-primary" data-soal="{{ $loop->iteration }}" type="submit" style="display: none;">Tambah ragu-ragu</button>
                                </div>
                            </form>
                        @endif
                    </div>
                    <div class="form-group d-flex justify-content-center" style="display: inline-flex;">
                        <form method="POST" action="{{ route('soal-sebelumnya') }}">
                            @csrf
                            <input type="hidden" name="page" id="page" value="{{ $loop->iteration }}">
                            <input type="hidden" name="pt" id="pt" value="{{ $jumlahsoal }}">
                            <input type="hidden" name="slug" id="slug" value="{{ $ujian->slug }}">
                            <button class="mr-2 btn btn-primary" data-soal="{{ $loop->iteration }}" type="submit"><- Sebelumnya</button>
                        </form>
                        <form method="POST" class="kos" action="/evaluasi/sesudah">
                            @csrf
                            <input type="hidden" name="page" id="page" value="{{ $loop->iteration }}">
                            <input type="hidden" name="pt" id="pt" value="{{ $jumlahsoal }}">
                            <input type="hidden" name="slug" id="slug" value="{{ $ujian->slug }}">
                            <button class="btn btn-primary" data-soal="{{ $loop->iteration }}" type="submit" type="submit">Berikutnya-></button>
                        </form>
                    </div>
            </div>
            @endforeach
        </div>
    </div>
    <div class="col-md-4 col-sm-5 mb-5 card-fixed">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-center mb-3">
                    <h5 class="mt-3">Sisa Waktu Pengerjaan :</h5>
                </div>
                <div class="d-flex justify-content-center mb-0">
                    <label for="" id="clock" style="font-size: 35px; color: #000000; -webkit-text-stroke: 3px #050505;
                                                            text-shadow: 4px 4px 10px #fbfbfb,
                                                            4px 4px 20px #36D6FE,
                                                            4px 4px 30px#021013,
                                                            4px 4px 40px #36D6FE;">
                    </label><br>
                </div>
                <div class="d-flex justify-content-center">
                    <form id="form-selesai" action="/selesaiujian" action="post">
                        <input type="hidden" name="ujian_id" id="ujian_id" value="{{ $ujian->id }}">
                        <input type="hidden" name="totalbobot" id="totalbobot" value="{{ $totalbobot }}">
                        <input type="hidden" name="user_id" id="user_id" value="{{ Auth::user()->id }}">
                        <div class="card-footer mr-3 mb-0 mt-0 d-flex justify-content-center">
                            <button id="tombol-selesai" class="btn btn-danger mt-3" type="submit">Selesai Ujian</button>
                        </div>
                        <div class="kotak-container">
                            @php
                                $jumlahSoal = count($soal);
                                $kotakPerBaris = 10; // Jumlah kotak soal per baris (bisa disesuaikan)
                                $jumlahBaris = ceil($jumlahSoal / $kotakPerBaris); // Hitung jumlah baris yang diperlukan
                            @endphp
                            @for ($baris = 0; $baris < $jumlahBaris; $baris++)
                                @for ($i = ($baris * $kotakPerBaris) + 1; $i <= min(($baris + 1) * $kotakPerBaris, $jumlahSoal); $i++)
                                    @php
                                        $evaluasiData = $evaluasi->firstWhere('soal_id', $soal[$i-1]->id);
                                        $evaluasi_exists = !is_null($evaluasiData);
                                        $isRagu = $evaluasi_exists && $evaluasiData->ragu;
                                    @endphp
                                    <a style="text-decoration:none" href="#soal-{{ $i }}"
                                        class="kotak-soal @if ($evaluasi_exists) {{ $isRagu ? 'kuning' : 'biru' }} @endif">
                                        {{ $i }}
                                    </a>
                                @endfor
                            @endfor
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @else
    <p class="text-center fs-4">Tidak Ada Data
        {{ $title }}</p>
    @endif
    @endif
    @endif
</div>
<script>
    document.querySelectorAll('.raguragu').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            this.form.submit();
        });
    });
</script>
<script>

document.addEventListener("DOMContentLoaded", function() {
        const radioButtons = document.querySelectorAll('.form-check-input[type="radio"]');
        radioButtons.forEach(function(radioButton) {
            radioButton.addEventListener('click', function() {
                this.form.submit();
            });
        });
    });

    // Tangkap tombol Selesai Ujian menggunakan ID
    var tombolSelesai = document.getElementById('tombol-selesai');

    // Tambahkan event listener untuk tombol diklik
    tombolSelesai.addEventListener('click', function (event) {
        event.preventDefault(); // Mencegah pengiriman form secara langsung

        // Tampilkan SweetAlert2 konfirmasi
        Swal.fire({
            title: 'Konfirmasi',
            text: 'Apakah Anda yakin ingin mengakhiri ujian?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Selesai',
            cancelButtonText: 'Tidak'
        }).then((result) => {
            if (result.isConfirmed) {
                // Jika pengguna mengklik "Yes", kirimkan form secara programatik
                document.getElementById('form-selesai').submit();
            }
        });
    });

    function getServerTime() {
        return $.ajax({
            async: false
        }).getResponseHeader("Date");
    }

    function realtimeClock() {
        var currentTime = new Date();
        var endTime = new Date(
            currentTime.toDateString() + " " + "{{$ujian->waktu_selesai}}"
        );

        var timeRemaining = endTime - currentTime;
        if (timeRemaining <= 0) {
            // Jika waktu berakhir, Anda dapat menambahkan logika atau tindakan yang sesuai di sini.
            // Misalnya, menghentikan penghitungan waktu atau mengirimkan data ke server.
            document.getElementById("clock").innerHTML = "Waktu telah berakhir";
            return;
        }

        var hours = Math.floor((timeRemaining / (1000 * 60 * 60)) % 24);
        var minutes = Math.floor((timeRemaining / 1000 / 60) % 60);
        var seconds = Math.floor((timeRemaining / 1000) % 60);

        hours = ("0" + hours).slice(-2);
        minutes = ("0" + minutes).slice(-2);
        seconds = ("0" + seconds).slice(-2);

        document.getElementById("clock").innerHTML =
            hours + " : " + minutes + " : " + seconds;

        setTimeout(realtimeClock, 500);
    }
    // Tambahkan event listener untuk setiap kotak soal
    document.querySelectorAll('.kotak-soal').forEach(function (kotak) {
        kotak.addEventListener('click', function () {
            // Dapatkan ID soal dari atribut href
            var soalId = this.getAttribute('href').substring(1);

            // Sembunyikan semua soal
            document.querySelectorAll('.soal').forEach(function (soal) {
                soal.style.display = 'none';
            });

            // Tampilkan soal yang sesuai dengan ID yang dipilih
            document.getElementById(soalId).style.display = 'block';
        });
    });
    // Tampilkan soal nomor 1 saat halaman dimuat
    document.getElementById('soal-1').style.display = 'block';

    document.querySelectorAll('.kotak-soal').forEach(function (kotak) {
        kotak.addEventListener('click', function () {
            // Dapatkan ID soal dari atribut href
            var soalId = this.getAttribute('href').substring(1);

            // Sembunyikan semua soal
            document.querySelectorAll('.soal').forEach(function (soal) {
                soal.style.display = 'none';
            });

            // Tampilkan soal yang sesuai dengan ID yang dipilih
            document.getElementById(soalId).style.display = 'block';
        });
    });
    document.addEventListener("DOMContentLoaded", function () {
        // Fungsi untuk menampilkan soal sesuai dengan hash URL
        function tampilkanSoal() {
            var hash = window.location.hash; // Mendapatkan hash URL (contoh: "#soal-10")

            // Jika tidak ada hash URL, tampilkan soal nomor 1
            if (!hash) {
                document.getElementById('soal-1').style.display = 'block';
            } else {
                // Coba untuk mendapatkan nomor soal dari hash URL
                var nomorSoal = parseInt(hash.replace("#soal-", ""));

                // Periksa apakah nomor soal valid (angka)
                if (!isNaN(nomorSoal)) {
                    // Sembunyikan semua soal
                    document.querySelectorAll('.soal').forEach(function (soal) {
                        soal.style.display = 'none';
                    });

                    // Tampilkan soal yang sesuai dengan nomor soal dari hash URL
                    var soalId = 'soal-' + nomorSoal;
                    document.getElementById(soalId).style.display = 'block';
                }
            }
        }

        // Panggil fungsi tampilkanSoal saat halaman dimuat
        tampilkanSoal();

        // Tambahkan event listener untuk mengikuti perubahan hash URL
        window.addEventListener("hashchange", tampilkanSoal);
    });
</script>
@endsection