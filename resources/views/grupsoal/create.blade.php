@extends('layoutdashboard.main')
@section('container')
<div class="row">
    <div class="col-12 col-md-6 col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4>Tambah Data {{ $title }}</h4>
            </div>
            <form action="/grupsoal/store" method="post">
                @csrf
                <input type="hidden" name="slug_modul" id="slug_modul" value="{{ $slug_modul }}">
                <input type="hidden" name="nama_modul" id="nama_modul" value="{{ $nama_modul }}">
                <input type="hidden" name="user_id" id="user_id" value="{{ auth()->user()->id }}">
                <div class="card-body">
                    <div class="form-group">
                        <input type="hidden" name="modul_id" id="modul_id" value="{{ $modul_id }}">
                        <label for="nama_grup">Nama Grup</label>
                        <input type="text" name="nama_grup"
                            class="form-control @error('nama_grup') is-invalid @enderror" id="nama_grup" required
                            value="{{ old('nama_grup') }}">
                        @error('nama_grup')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="slug">Slug</label>
                        <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror"
                            id="slug" value="{{ old('slug') }}" readonly>
                        @error('nama_modul')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="card-footer mr-3 mb-3 mt-0">
                        <a class="ml-1 btn btn-danger float-right" href="/grupsoal/{{ $slug_modul }}">Batal</a>
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