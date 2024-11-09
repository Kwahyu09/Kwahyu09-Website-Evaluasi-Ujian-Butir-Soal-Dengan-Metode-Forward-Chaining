@extends('layoutdashboard.main')
@section('container')
    <div class="row">
        <div class="col-12 col-md-6 col-lg-6">
                <div class="card">
                  <div class="card-header">
                    <h4>Tambah Data {{ $title }}</h4>
                  </div>
                  <form action="{{ route('modul.store') }}" method="post">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="kd_modul">Kode Modul</label>
                            <input type="text" name="kd_modul" class="form-control @error('kd_modul') is-invalid @enderror" id="kd_modul" required value="{{ old('kd_modul') }}">
                            @error('kd_modul')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="nama_modul">Nama Modul</label>
                            <input type="text" name="nama_modul" class="form-control @error('nama_modul') is-invalid @enderror" id="nama_modul" required value="{{ old('nama_modul') }}">
                            @error('nama_modul')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="nama_modul">Ketua Modul</label>
                            <select name="user_id" class="custom-select  @error('user_id') is-invalid @enderror" id="user_id" 
                                            required="required"
                                            value="{{ old('nama_user') }}">
                                        @foreach ($post as $pos)
                                            @if(old('user_id') == $pos->id)
                                                <option value="{{ $pos->id }}" selected>{{ $pos->nama }}</option>                                               
                                            @else
                                                <option value="{{ $pos->id }}">{{ $pos->nama }}</option>
                                            @endif
                                        @endforeach
                                        </select>
                            @error('nama_modul')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <input type="hidden" name="slug" class="form-control @error('slug') is-invalid @enderror" id="slug" value="{{ old('slug') }}">
                        <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="semester">Semester</label>
                                     <select name="semester" class="custom-select  @error('semester') is-invalid @enderror" id="semester">
                                    <?php
                                    for ($i = 1; $i <=14; $i++): ?>
                                        @if(old('semester') == $i)
                                            <option value="<?=$i;?>" selected><?=$i;?></option>
                                        @else
                                            <option value="<?=$i;?>"><?=$i;?></option>
                                        @endif
                                    <?php endfor; ?>
                                    </select>
                                    @error('semester')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="sks">Sks</label>
                                     <select name="sks" class="custom-select  @error('sks') is-invalid @enderror" id="sks">
                                    <?php
                                    for ($i = 1; $i <=6; $i++): ?>
                                        @if(old('sks') == $i)
                                            <option value="<?=$i;?>" selected><?=$i;?></option>
                                        @else
                                            <option value="<?=$i;?>"><?=$i;?></option>
                                        @endif
                                    <?php endfor; ?>
                                    </select>
                                    @error('sks')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="card-footer mb-3 mt-0">
                                <a class="ml-1 btn btn-danger float-right" href="/modul">Batal</a>
                                <button class="btn btn-primary float-right" type="submit">Tambah</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

<script>
    const nama_modul = document.querySelector('#nama_modul');
    const slug = document.querySelector('#slug');

    nama_modul.addEventListener('change', function(){
        fetch('/modul/create/checkSlug?nama_modul=' + nama_modul.value)
        .then(response => response.json())
        .then(data => slug.value = data.slug)
    });
    
</script>
@endsection