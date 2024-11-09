@extends('layoutdashboard.main')
@section('container')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>Tambah Data {{ $title }} {{ $grupsoal_nama }} {{ $modul->nama_modul }}</h4>
            </div>
            <form action="/soal/store" method="post" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-2">
                            <input type="hidden" name="slug" id="slug" value="{{ $slug }}">
                            <input type="hidden" name="grupsoal_slug" id="grupsoal_slug" value="{{ $grupsoal_slug }}">
                            <input type="hidden" name="grup_soal_id" id="grup_soal_id" value="{{ $grupsoal_id }}">
                            <h5 class="card-title">Pertanyaan</h5>
                        </div>
                        <div class="col-md-10 mb-4">
                            @error('pertanyaan')
                            <p class="text-danger">{{ $message }}</p>
                            @enderror
                            <input id="pertanyaan" type="hidden" name="pertanyaan" value="{{ old('pertanyaan') }}"
                                class="@error('pertanyaan') is-invalid @enderror">
                            <trix-editor input="pertanyaan"></trix-editor>
                        </div>
                        <div class="col-md-2">
                            <h5 class="card-title mt-2">Gambar</h5>
                        </div>
                        <div class="col-md-10 mb-4">
                            @error('gambar')
                            <p class="text-danger">{{ $message }}</p>
                            @enderror
                            <img class="img-preview img-fluid mb-3 col-sm-5">
                            <div class="mb-3">
                                <input class="form-control @error('gambar') is-invalid @enderror" type="file"
                                    name="gambar" id="gambar" onchange="previewImage()">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <h5 class="card-title mt-2">Opsi A</h5>
                        </div>
                        <div class="col-md-10 mb-4">
                            @error('opsi_a')
                            <p class="text-danger">{{ $message }}</p>
                            @enderror
                            <input id="opsi_a" type="hidden" name="opsi_a" value="{{ old('opsi_a') }}"
                                class="@error('opsi_a') is-invalid @enderror">
                            <trix-editor input="opsi_a"></trix-editor>
                        </div>
                        <div class="col-md-2">
                            <h5 class="card-title mt-2">Opsi B</h5>
                        </div>
                        <div class="col-md-10 mb-4">
                            @error('opsi_b')
                            <p class="text-danger">{{ $message }}</p>
                            @enderror
                            <input id="opsi_b" type="hidden" name="opsi_b" value="{{ old('opsi_b') }}"
                                class="@error('opsi_b') is-invalid @enderror">
                            <trix-editor input="opsi_b"></trix-editor>
                        </div>
                        <div class="col-md-2">
                            <h5 class="card-title mt-2">Opsi C</h5>
                        </div>
                        <div class="col-md-10 mb-4">
                            @error('opsi_c')
                            <p class="text-danger">{{ $message }}</p>
                            @enderror
                            <input id="opsi_c" type="hidden" name="opsi_c" value="{{ old('opsi_c') }}"
                                class="@error('opsi_c') is-invalid @enderror">
                            <trix-editor input="opsi_c"></trix-editor>
                        </div>
                        <div class="col-md-2">
                            <h5 class="card-title mt-2">Opsi D</h5>
                        </div>
                        <div class="col-md-10 mb-2">
                            @error('opsi_d')
                            <p class="text-danger">{{ $message }}</p>
                            @enderror
                            <input id="opsi_d" type="hidden" name="opsi_d" value="{{ old('opsi_d') }}"
                                class="@error('opsi_d') is-invalid @enderror">
                            <trix-editor input="opsi_d"></trix-editor>
                        </div>
                        <div class="col-md-2">
                            <h5 class="card-title mt-2">Opsi E</h5>
                        </div>
                        <div class="col-md-10 mb-2">
                            @error('opsi_e')
                            <p class="text-danger">{{ $message }}</p>
                            @enderror
                            <input id="opsi_e" type="hidden" name="opsi_e" value="{{ old('opsi_e') }}"
                                class="@error('opsi_e') is-invalid @enderror">
                            <trix-editor input="opsi_e"></trix-editor>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-2">
                            <h5 class="card-title mt-2">Jawaban</h5>
                        </div>
                        <div class="col-md-2">
                            <select class="custom-select" id="jawaban" name="jawaban">
                                @if(old('jawaban') == "opsi_a")
                                <option value="opsi_a" selected>Opsi A</option>
                                <option value="opsi_b">Opsi B</option>
                                <option value="opsi_c">Opsi C</option>
                                <option value="opsi_d">Opsi D</option>
                                <option value="opsi_e">Opsi E</option>
                                @elseif(old('jawaban') == "opsi_b")
                                <option value="opsi_a">Opsi A</option>
                                <option value="opsi_b" selected>Opsi B</option>
                                <option value="opsi_c">Opsi C</option>
                                <option value="opsi_d">Opsi D</option>
                                <option value="opsi_e">Opsi E</option>
                                @elseif(old('jawaban') == "opsi_c")
                                <option value="opsi_a">Opsi A</option>
                                <option value="opsi_b">Opsi B</option>
                                <option value="opsi_c" selected>Opsi C</option>
                                <option value="opsi_d">Opsi D</option>
                                <option value="opsi_e">Opsi E</option>
                                @elseif(old('jawaban') == "opsi_d")
                                <option value="opsi_a">Opsi A</option>
                                <option value="opsi_b">Opsi B</option>
                                <option value="opsi_c">Opsi C</option>
                                <option value="opsi_d" selected>Opsi D</option>
                                <option value="opsi_e">Opsi E</option>
                                @elseif(old('jawaban') == "opsi_e")
                                <option value="opsi_a">Opsi A</option>
                                <option value="opsi_b">Opsi B</option>
                                <option value="opsi_c">Opsi C</option>
                                <option value="opsi_d">Opsi D</option>
                                <option value="opsi_d" selected>Opsi E</option>
                                @else
                                <option value="opsi_a">Opsi A</option>
                                <option value="opsi_b">Opsi B</option>
                                <option value="opsi_c">Opsi C</option>
                                <option value="opsi_d">Opsi D</option>
                                <option value="opsi_e">Opsi E</option>
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-2">
                            <h5 class="card-title mt-2">Bobot</h5>
                        </div>
                        <div class="col-md-2">
                            <select class="custom-select @error('bobot') is-invalid @enderror" id="bobot" name="bobot"
                                value="{{ old('bobot') }}" required>
                                <?php
                                            for ($i = 1; $i <= 2; $i++){ ?>
                                @if(old('bobot') == $i )
                                <option value="<?= $i ?>" selected><?= $i ?></option>
                                @else
                                <option value="<?= $i ?>"><?= $i ?></option>
                                @endif
                                <?php
                                                }
                                            ?>
                            </select>
                            @error('bobot')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>
                    <!-- end section -->
                    <div class="card-footer mr-3 mb-3 mt-0">
                        <a class="ml-1 btn btn-danger float-right" href="/soal/{{ $grupsoal_slug }}">Batal</a>
                        <button class="btn btn-primary float-right" type="submit">Tambah</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    const nama_modul = document.querySelector('#nama_modul');
    const nama_grup = document.querySelector('#nama_grup');
    const slug_modul = document.querySelector('#slug_modul');
    const slug = document.querySelector('#slug');

    nama_grup.addEventListener('change', function () {
        fetch('/grupsoal/create/' + slug_modul.value + '/checkSlug?nama_grup=' + nama_grup.value + ' ' +
                nama_modul.value)
            .then(response => response.json())
            .then(data => slug.value = data.slug)
    });
</script>
@endsection