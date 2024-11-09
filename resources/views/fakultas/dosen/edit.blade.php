@extends('layoutdashboard.main')
@section('container')
    <div class="row">
        <div class="col-12 col-md-6 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h4>Ubah Data {{ $title }}</h4>
                </div>
                <form action="/dosen/{{ $post->slug }}" method="post">
                    @method('put')
                    @csrf
                    <input type="hidden" name="slug" class="form-control @error('slug') is-invalid @enderror" id="slug"
                        value="{{ old('slug', $post->slug) }}">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="nip">NIP</label>
                            <input type="text" name="nip" class="form-control @error('nip') is-invalid @enderror"
                                id="nip" required value="{{ old('nip', $post->nip) }}">
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
                                value="{{ old('nama_dos', $post->nama_dos) }}">
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
                                            {{ old('jabatan_id', $post->jabatan_id) == $jab->id ? 'selected' : '' }}>
                                            {{ $jab->keterangan }}
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
                                <input class="form-check-input" type="radio" name="jenis_kel" id="jenis_kel"
                                    value="Laki-Laki" <?php if ($post->jenis_kel == 'Laki-Laki') {
                                        echo 'checked';
                                    } ?>>
                                <label class="form-check-label" for="jenis_kel">Laki-laki</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="jenis_kel" id="jenis_kel"
                                    value="Perempuan" <?php if ($post->jenis_kel == 'Perempuan') {
                                        echo 'checked';
                                    } ?>>
                                <label class="form-check-label" for="jenis_kel">Perempuan</label>
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
                                    @if (old('prodi_id', $post->prodi_id) == '1')
                                        <option value="1"selected>Kedokteran</option>
                                        <option value="2">Pendidikan Profesi Dokter</option>
                                    @else
                                        <option value="1">Kedokteran</option>
                                        <option value="2"selected>Pendidikan Profesi Dokter</option>
                                    @endif
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
                                    value="{{ old('email', $post->email) }}">
                                @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="card-footer mb-3 mt-0">
                            <a class="ml-1 btn btn-danger float-right" href="/dosen">Batal</a>
                            <button class="btn btn-primary float-right" type="submit">Ubah</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const namaDos = document.querySelector('#nama_dos');
        const slug = document.querySelector('#slug');

        function updateSlug() {
            fetch(`/dosen/create/checkSlug?nama_dos=${namaDos.value}`)
                .then(response => response.json())
                .then(data => slug.value = data.slug);
        }

        namaDos.addEventListener('change', updateSlug);
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const jabatanSelect = document.querySelector('#jabatan_id');
        const golonganSelect = document.querySelector('#golongan_id');
        const selectedGolonganId = {{ old('golongan_id', $post->golongan_id) }};

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
        fetchGolongan(jabatanSelect.value, selectedGolonganId);

        // Update golongan when jabatan changes
        jabatanSelect.addEventListener('change', function() {
            fetchGolongan(this.value);
        });
    });
</script>