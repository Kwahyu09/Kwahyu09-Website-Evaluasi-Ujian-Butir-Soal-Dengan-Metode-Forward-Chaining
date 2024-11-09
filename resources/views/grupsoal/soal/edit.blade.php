@extends('layoutdashboard.main')
@section('container')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>Ubah Data {{ $title }}</h4>
            </div>
            @if (preg_match('/^gambar-soal\//', $post->opsi_a))
                <form action="/soal/{{ $post->slug }}/updategambar" method="post" enctype="multipart/form-data">
            @else
                <form action="/soal/{{ $post->slug }}/update" method="post" enctype="multipart/form-data">
            @endif
                @method('put')
                @csrf
                <div class="card-body">
                    <div class="row mb-4">
                        <input type="hidden" name="slug" id="slug" value="{{ $post->slug }}">
                        <input type="hidden" name="oldGambar" id="oldGambar" value="{{ $post->gambar }}">
                        <input type="hidden" name="grup_soal_id" id="grup_soal_id" value="{{ $post->grup_soal_id }}">
                        <div class="col-md-2">
                            <h5 class="card-title">Pertanyaan</h5>
                        </div>
                        <div class="col-md-10 mb-4">
                            @error('pertanyaan')
                            <p class="text-danger">{{ $message }}</p>
                            @enderror
                            <input id="pertanyaan" type="hidden" name="pertanyaan"
                                value="{{ old('pertanyaan',$post->pertanyaan) }}">
                            <trix-editor input="pertanyaan"></trix-editor>
                        </div>
                        <div class="col-md-2">
                            <h5 class="card-title mt-2">Gambar</h5>
                        </div>
                        <div class="col-md-10 mb-4">
                            @error('gambar')
                            <p class="text-danger">{{ $message }}</p>
                            @enderror
                            @if($post->gambar)
                            <img src="{{ asset('storage/'. $post->gambar) }}"
                                class="img-preview img-fluid mb-3 col-sm-5 d-block">
                            @else
                            <img class="img-preview img-fluid mb-3 col-sm-5">
                            @endif
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
                            @if(preg_match('/^gambar-soal\//', $post->opsi_a))
                            <input type="hidden" name="oldOpsia" id="oldOpsia" value="{{ $post->opsi_a }}">
                            <img src="{{ asset('storage/' . $post->opsi_a) }}" alt="Gambar"
                                class="img-previewa img-fluid mb-3 col-sm-5">
                            <div class="mb-3">
                                <input class="form-control @error('opsi_a') is-invalid @enderror" type="file"
                                    name="opsi_a" id="opsi_a" onchange="previewImagea()">
                            </div>
                            @else
                            <input id="opsi_a" type="hidden" name="opsi_a" value="{{ old('opsi_a',$post->opsi_a) }}">
                            <trix-editor input="opsi_a"></trix-editor>
                            @endif
                        </div>
                        <div class="col-md-2">
                            <h5 class="card-title mt-2">Opsi B</h5>
                        </div>
                        <div class="col-md-10 mb-4">
                            @error('opsi_b')
                            <p class="text-danger">{{ $message }}</p>
                            @enderror
                            @if(preg_match('/^gambar-soal\//', $post->opsi_b))
                            <input type="hidden" name="oldOpsib" id="oldOpsib" value="{{ $post->opsi_b }}">
                            <img src="{{ asset('storage/' . $post->opsi_b) }}" alt="Gambar"
                                class="img-previewb img-fluid mb-3 col-sm-5">
                            <div class="mb-3">
                                <input class="form-control @error('opsi_b') is-invalid @enderror" type="file"
                                    name="opsi_b" id="opsi_b" onchange="previewImageb()">
                            </div>
                            @else
                            <input id="opsi_b" type="hidden" name="opsi_b" value="{{ old('opsi_b',$post->opsi_b) }}">
                            <trix-editor input="opsi_b"></trix-editor>
                            @endif
                        </div>
                        <div class="col-md-2">
                            <h5 class="card-title mt-2">Opsi C</h5>
                        </div>
                        <div class="col-md-10 mb-4">
                            @error('opsi_c')
                            <p class="text-danger">{{ $message }}</p>
                            @enderror
                            @if(preg_match('/^gambar-soal\//', $post->opsi_c))
                            <input type="hidden" name="oldOpsic" id="oldOpsic" value="{{ $post->opsi_c }}">
                            <img src="{{ asset('storage/' . $post->opsi_c) }}" alt="Gambar"
                                class="img-previewc img-fluid mb-3 col-sm-5">
                            <div class="mb-3">
                                <input class="form-control @error('opsi_c') is-invalid @enderror" type="file"
                                    name="opsi_c" id="opsi_c" onchange="previewImagec()">
                            </div>
                            @else
                            <input id="opsi_c" type="hidden" name="opsi_c" value="{{ old('opsi_c',$post->opsi_c) }}">
                            <trix-editor input="opsi_c"></trix-editor>
                            @endif
                        </div>
                        <div class="col-md-2">
                            <h5 class="card-title mt-2">Opsi D</h5>
                        </div>
                        <div class="col-md-10 mb-2">
                            @error('opsi_d')
                            <p class="text-danger">{{ $message }}</p>
                            @enderror
                            @if(preg_match('/^gambar-soal\//', $post->opsi_d))
                            <input type="hidden" name="oldOpsid" id="oldOpsid" value="{{ $post->opsi_d }}">
                            <img src="{{ asset('storage/' . $post->opsi_d) }}" alt="Gambar"
                                class="img-previewd img-fluid mb-3 col-sm-5">
                            <div class="mb-3">
                                <input class="form-control @error('opsi_d') is-invalid @enderror" type="file"
                                    name="opsi_d" id="opsi_d" onchange="previewImaged()">
                            </div>
                            @else
                            <input id="opsi_d" type="hidden" name="opsi_d" value="{{ old('opsi_d',$post->opsi_d) }}">
                            <trix-editor input="opsi_d"></trix-editor>
                            @endif
                        </div>
                        <div class="col-md-2">
                            <h5 class="card-title mt-2">Opsi E</h5>
                        </div>
                        <div class="col-md-10 mb-2">
                            @error('opsi_e')
                            <p class="text-danger">{{ $message }}</p>
                            @enderror
                            @if(preg_match('/^gambar-soal\//', $post->opsi_e))
                            <input type="hidden" name="oldOpsie" id="oldOpsie" value="{{ $post->opsi_e }}">
                            <img src="{{ asset('storage/' . $post->opsi_e) }}" alt="Gambar"
                                class="img-previewe img-fluid mb-3 col-sm-5">
                            <div class="mb-3">
                                <input class="form-control @error('opsi_e') is-invalid @enderror" type="file"
                                    name="opsi_e" id="opsi_e" onchange="previewImagee()">
                            </div>
                            @else
                            <input id="opsi_e" type="hidden" name="opsi_e" value="{{ old('opsi_e',$post->opsi_e) }}">
                            <trix-editor input="opsi_e"></trix-editor>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-2">
                            <h5 class="card-title mt-2">Jawaban</h5>
                        </div>
                        <div class="col-md-2">
                            <select class="custom-select" id="jawaban" name="jawaban">
                                @if($post->jawaban == $post->opsi_a)
                                <option value="opsi_a" selected>Opsi A</option>
                                <option value="opsi_b">Opsi B</option>
                                <option value="opsi_c">Opsi C</option>
                                <option value="opsi_d">Opsi D</option>
                                <option value="opsi_e">Opsi E</option>
                                @elseif($post->jawaban == $post->opsi_b))
                                <option value="opsi_a">Opsi A</option>
                                <option value="opsi_b" selected>Opsi B</option>
                                <option value="opsi_c">Opsi C</option>
                                <option value="opsi_d">Opsi D</option>
                                <option value="opsi_e">Opsi E</option>
                                @elseif($post->jawaban == $post->opsi_c)
                                <option value="opsi_a">Opsi A</option>
                                <option value="opsi_b">Opsi B</option>
                                <option value="opsi_c" selected>Opsi C</option>
                                <option value="opsi_d">Opsi D</option>
                                <option value="opsi_e">Opsi E</option>
                                @elseif($post->jawaban == $post->opsi_d)
                                <option value="opsi_a">Opsi A</option>
                                <option value="opsi_b">Opsi B</option>
                                <option value="opsi_c">Opsi C</option>
                                <option value="opsi_d" selected>Opsi D</option>
                                <option value="opsi_e">Opsi E</option>
                                @elseif($post->jawaban == $post->opsi_e)
                                <option value="opsi_a">Opsi A</option>
                                <option value="opsi_b">Opsi B</option>
                                <option value="opsi_c">Opsi C</option>
                                <option value="opsi_d">Opsi D</option>
                                <option value="opsi_e" selected>Opsi E</option>
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
                                @if(old('bobot',$post->bobot) == $i )
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
                        <button class="btn btn-primary float-right" type="submit">Ubah</button>
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

    function previewImage() {
        const gambar = document.querySelector('#gambar');
        const imgPreview = document.querySelector('.img-preview');

        imgPreview.style.display = 'block';

        const oFReader = new FileReader();
        oFReader.readAsDataURL(gambar.files[0]);

        oFReader.onload = function (oFREvent) {
            imgPreview.src = oFREvent.target.result;
        }
    }
</script>
@endsection