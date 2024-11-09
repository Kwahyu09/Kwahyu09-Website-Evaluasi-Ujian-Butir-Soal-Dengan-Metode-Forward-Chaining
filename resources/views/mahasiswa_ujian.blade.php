@extends('layoutdashboard.main') 
@section('container')
<div class="row">
    <div class="col-12 col-md-6 col-lg-6">
        <div class="card">
            <div class="card-body">
                <h3 class="mb-3">Ujian Mahasiswa</h3>
                <div class="flash-dataeror" data-flashdataeror="{{ session('success') }}">
                </div>
                <form action="{{ route('ujian-data') }}" method="POST">
                    @csrf

                    <label for="kd_ujian">Silahkan Pilih Ujian :</label>
                    <div class="input-group mb-3">
                        <select class="custom-select @error('kd_ujian') is-invalid @enderror" name="id_ujian" id="id_ujian" required="required">
                            @foreach ($post as $pos)
                            <option value="{{ $pos->id }}">{{ $pos->nama_ujian }}</option>
                            @endforeach
                        </select>
                        @error('ujian_id')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="kd_ujian">Kode Ujian</label>
                        <input type="text" name="kd_ujian" class="form-control @error('kd_ujian') is-invalid @enderror"
                            id="kd_ujian" required="required" value="{{ old('kd_ujian') }}">
                        @error('kd_ujian')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="card-footer mr-3 mb-3 mt-0 d-flex justify-content-end">
                        <input type="submit" value="Masuk Ujian" class="btn btn-primary">
                    </div>
                </form>
                <!-- end section -->
            </div>
            <!-- .col-12 -->
        </div>
        <!-- .row -->
    </div>
</div>
@endsection