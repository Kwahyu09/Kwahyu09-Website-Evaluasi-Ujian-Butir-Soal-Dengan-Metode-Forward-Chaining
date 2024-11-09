@extends('layoutdashboard.main')
@section('container')
<div class="row">
    <div class="col-12 col-md-6 col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4>Tambah Data {{ $title }}</h4>
            </div>
            <form action="/kelas/store" method="post">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label for="tahun_ajaran">Tahun</label>
                        <select name="tahun_ajaran" class="custom-select  @error('tahun_ajaran') is-invalid @enderror"
                            id="tahun_ajaran">
                            <?php
                                    for ($year = (int)date('Y'); 1990 <= $year; $year--): ?>
                            @if(old('tahun_ajaran') == $year)
                            <option value="<?=$year;?>" selected><?=$year;?></option>
                            @else
                            <option value="<?=$year;?>"><?=$year;?></option>
                            @endif
                            <?php endfor; ?>
                        </select>
                        @error('tahun_ajaran')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <input type="hidden" name="slug" class="form-control @error('slug') is-invalid @enderror" id="slug"
                        value="{{ old('slug') }}">
                    <div class="form-group">
                        <label for="nama_kelas">Nama Kelas</label>
                        <input type="text" name="nama_kelas"
                            class="form-control @error('nama_kelas') is-invalid @enderror" id="nama_kelas" required
                            value="{{ old('nama_kelas') }}">
                        @error('nama_kelas')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="prodi_id">Prodi</label>
                        <select class="custom-select @error('prodi_id') is-invalid @enderror" id="prodi_id"
                            name="prodi_id" required>
                            @foreach ($prodi as $prod)
                            <option value="{{ $prod->id }}">{{ $prod->nama_prodi }}</option>
                            @endforeach
                        </select>
                        @error('prodi')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="card-footer mb-3 mt-0">
                        <a class="ml-1 btn btn-danger float-right" href="/kelas">Batal</a>
                        <button class="btn btn-primary float-right" type="submit">Tambah</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const nama_kelas = document.querySelector('#nama_kelas');
    const slug = document.querySelector('#slug');
    const tahun_ajaran = document.querySelector('#tahun_ajaran');

    nama_kelas.addEventListener('change', function () {
        fetch('/kelas/create/checkSlug?nama_kelas=' + nama_kelas.value + ' ' + tahun_ajaran.value)
            .then(response => response.json())
            .then(data => slug.value = data.slug)
    });
</script>
@endsection