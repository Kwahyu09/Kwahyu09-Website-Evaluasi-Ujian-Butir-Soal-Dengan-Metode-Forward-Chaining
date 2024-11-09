@extends('layoutdashboard.main')
@section('container')
    <div class="row">
        <div class="col-12 col-md-6 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h4>Tambah Data {{ $title }}</h4>
                </div>
                <form action="/dosen/store" method="post">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="nip">NIP</label>
                            <input type="hidden" name="slug" class="form-control @error('slug') is-invalid @enderror"
                                id="slug" value="{{ old('slug') }}">
                            <input type="text" name="nip" class="form-control @error('nip') is-invalid @enderror"
                                id="nip" required value="{{ old('nip') }}">
                            <label
                                style="font-family:Arial, Helvetica, sans-serif; font-size:8pt; font-style:italic; border:0pt;">NIP
                                memuat: 18 Karakter dan Harus Angka</label>
                            @error('nip')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="nama_dos">Nama</label>
                            <input type="text" name="nama_dos"
                                class="form-control @error('nama_dos') is-invalid @enderror" id="nama_dos" required
                                value="{{ old('nama_dos') }}">
                            @error('nama_dos')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="jabatan_ids">Jabatan</label>
                                <select class="custom-select @error('jabatan_id') is-invalid @enderror" id="jabatan_id"
                                    name="jabatan_id" required>
                                    @foreach ($jabatan as $jab)
                                        <option value="{{ $jab->id }}"
                                            {{ old('jabatan_id') == $jab->id ? 'selected' : '' }}>{{ $jab->keterangan }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('jabatan_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label for="golongan_id">Pangkat - Golongan</label>
                                <select class="custom-select @error('golongan_id') is-invalid @enderror" id="golongan_id"
                                    name="golongan_id" required>
                                    <!-- Options will be populated dynamically -->
                                </select>
                                @error('golongan_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="">Jenis Kelamin : </label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="jenis_kel" id="jenis_kel_laki"
                                    value="Laki-Laki" {{ old('jenis_kel') == 'Laki-Laki' ? 'checked' : '' }}>
                                <label class="form-check-label" for="jenis_kel_laki">Laki-laki</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="jenis_kel" id="jenis_kel_perempuan"
                                    value="Perempuan" {{ old('jenis_kel') == 'Perempuan' ? 'checked' : '' }}>
                                <label class="form-check-label" for="jenis_kel_perempuan">Perempuan</label>
                            </div>
                            @error('jenis_kel')
                                <p class="text-danger">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="prodi_id">Prodi</label>
                                <select class="custom-select @error('prodi_id') is-invalid @enderror" id="prodi_id"
                                    name="prodi_id" required>
                                    @foreach ($prodi as $prod)
                                        <option value="{{ $prod->id }}"
                                            {{ old('prodi_id') == $prod->id ? 'selected' : '' }}>{{ $prod->nama_prodi }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('prodi_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label for="inputEmail4">Email</label>
                                <input type="email" name="email"
                                    class="form-control  @error('email') is-invalid @enderror" id="email" required
                                    value="{{ old('email') }}">
                                @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="card-footer mb-3 mt-0">
                            <a class="ml-1 btn btn-danger float-right" href="/dosen">Batal</a>
                            <button class="btn btn-primary float-right" type="submit">Tambah</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const nama_dos = document.querySelector('#nama_dos');
        const slug = document.querySelector('#slug');

        nama_dos.addEventListener('input', function() {
            fetch('/dosen/create/checkSlug?nama_dos=' + nama_dos.value)
                .then(response => response.json())
                .then(data => slug.value = data.slug)
        });
    </script>
@endsection
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const jabatanSelect = document.querySelector('#jabatan_id');
        const golonganSelect = document.querySelector('#golongan_id');

        function fetchGolongan(jabatanId, selectedGolonganId = null) {
            fetch(`/dosen/create/golongan?jabatan_id=${jabatanId}`)
                .then(response => response.json())
                .then(data => {
                    golonganSelect.innerHTML = '';
                    data.forEach(gol => {
                        const option = document.createElement('option');
                        option.value = gol.id;
                        option.textContent = `${gol.pangkat} - ${gol.golongan}`;
                        if (selectedGolonganId && selectedGolonganId == gol.id) {
                            option.selected = true;
                        }
                        golonganSelect.appendChild(option);
                    });
                });
        }

        // Load golongan when page loads
        fetchGolongan(jabatanSelect.value, {{ old('golongan_id') }});

        // Update golongan when jabatan changes
        jabatanSelect.addEventListener('change', function() {
            fetchGolongan(this.value);
        });
    });
</script>