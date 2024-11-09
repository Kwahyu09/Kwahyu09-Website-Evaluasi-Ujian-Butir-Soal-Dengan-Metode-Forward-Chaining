@extends('layoutdashboard.main')
@section('container')
    <div class="row">
        <div class="col-12 col-md-6 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h4>Tambah Data {{ $title }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('ujian.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="kd_ujian" class="form-control @error('kd_ujian') is-invalid @enderror"
                            id="kd_ujian" value="{{ $kd_ujian }}">
                        <input type="hidden" name="user_id" class="form-control @error('user_id') is-invalid @enderror"
                            id="user_id" value="{{ auth()->user()->id }}">
                        <div class="form-group">
                            <label for="nama_ujian">Nama {{ $title }}</label>
                            <input type="text" name="nama_ujian"
                                class="form-control @error('nama_ujian') is-invalid @enderror" id="nama_ujian"
                                required="required" value="{{ old('nama_ujian') }}">
                            @error('nama_ujian')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="slug" class="form-control @error('slug') is-invalid @enderror"
                                id="slug" readonly>
                            @error('slug')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="modul_id">Modul</label>
                                    <select class="custom-select" id="modul_id" name="modul_id">
                                        @foreach ($modul as $m)
                                            <option value="{{ $m->id }}"
                                                {{ old('modul') == $m->id ? 'selected' : '' }}>{{ $m->nama_modul }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('modul_id')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="grup_soal_id">Grup Soal</label>
                                    <select class="custom-select" id="grup_soal_id" name="grup_soal_id">
                                        @if (old('grup_soal_id'))
                                            @foreach ($grup_soal as $grup)
                                                @if (old('grup_soal_id') == $grup->id)
                                                    <option value="{{ $grup->id }}" selected>{{ $grup->nama_grup }}
                                                    </option>
                                                @else
                                                    <option value="{{ $grup->id }}">{{ $grup->nama_grup }}</option>
                                                @endif
                                            @endforeach
                                        @else
                                            <option value="">Pilih Modul terlebih dahulu</option>
                                        @endif
                                    </select>
                                    @error('grup_soal_id')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="kelas_id">Kelas</label>
                                    <select class="custom-select" id="kelas_id" name="kelas_id">
                                        @foreach ($kelas as $k)
                                            <option value="{{ $k->id }}"
                                                {{ old('kelas') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}
                                                {{ $k->tahun_ajaran }}</option>
                                        @endforeach
                                    </select>
                                    @error('kelas_id')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <input type="hidden" name="acak_soal"
                                    class="form-control @error('acak_soal') is-invalid @enderror" id="acak_soal"
                                    value="Y">
                                <div class="col-md-6">
                                    <label for="tanggal">Tanggal</label>
                                    <input type="date" name="tanggal"
                                        class="form-control  @error('tanggal') is-invalid @enderror" id="tanggal"
                                        required="required" value="{{ old('tanggal') }}">
                                    @error('tanggal')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="waktu_mulai">Waktu Mulai</label>
                                    <input type="time" name="waktu_mulai"
                                        class="form-control  @error('waktu_mulai') is-invalid @enderror" id="waktu_mulai"
                                        required="required" value="{{ old('waktu_mulai') }}">
                                    @error('waktu_mulai')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="waktu_selesai">Waktu Selesai</label>
                                    <input type="time" name="waktu_selesai"
                                        class="form-control  @error('waktu_selesai') is-invalid @enderror"
                                        id="waktu_selesai" required="required" value="{{ old('waktu_selesai') }}">
                                    @error('waktu_selesai')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="card-footer mb-3 mt-0">
                            <a class="ml-1 btn btn-danger float-right" href="/ujian">Batal</a>
                            <button class="btn btn-primary float-right" type="submit">Tambah</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const nama_ujian = document.querySelector('#nama_ujian');
        const slug = document.querySelector('#slug');

        nama_ujian.addEventListener('change', function() {
            fetch('/ujian/create/checkSlug?nama_ujian=' + encodeURIComponent(nama_ujian.value))
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok ' + response.statusText);
                    }
                    return response.json();
                })
                .then(data => {
                    slug.value = data.slug;
                    console.log('Slug fetched:', data.slug);
                })
                .catch(error => console.error('Error fetching slug:', error));
        });

        const modulSelect = document.querySelector('#modul_id');
        const grupSoalSelect = document.querySelector('#grup_soal_id');

        function loadGrupSoal(modulId) {
            fetch(`/ujian/getGrupSoal/${modulId}`)
                .then(response => response.json())
                .then(data => {
                    grupSoalSelect.innerHTML = '';
                    if (data.length > 0) {
                        data.forEach(grup => {
                            const option = document.createElement('option');
                            option.value = grup.id;
                            option.textContent = grup.nama_grup;
                            grupSoalSelect.appendChild(option);
                        });
                    } else {
                        const option = document.createElement('option');
                        option.value = '';
                        option.textContent = 'Tidak ada grup soal';
                        grupSoalSelect.appendChild(option);
                    }
                })
                .catch(error => console.error('Error fetching grup soal:', error));
        }

        modulSelect.addEventListener('change', function() {
            const modulId = modulSelect.value;
            loadGrupSoal(modulId);
        });

        document.addEventListener('DOMContentLoaded', function() {
            const initialModulId = modulSelect.value;
            if (initialModulId) {
                loadGrupSoal(initialModulId);
            }
        });
    </script>
@endsection