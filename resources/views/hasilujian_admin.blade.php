@extends('layoutdashboard.main') @section('container')
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <div class="row align-items-center my-2">
                    <div class="col">
                        @if ($ujian->count())
                        <h3>Hasil Ujian (Laporan Nilai)</h3>
                        <h4> Modul : {{ $namamodul }}</h4>
                        <br>
                        <h5>Silahkan Pilih Ujian :
                        </h5>
                        <br>
                        <form action="{{ route('hasilujian.hasil_ujian') }}" method="post">
                            @csrf
                            <div class="input-group mb-3">
                                <select class="custom-select" id="ujian_id" name="ujian_id">
                                    @foreach ($ujian as $uji)
                                    <option value="{{ $uji->id }}">{{ $uji->nama_ujian }}</option>
                                    @endforeach
                                </select>
                            </div>
                    </div>
                    <div class="col-auto my-2">
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                        <button type="submit" class="btn btn-info mr-5 mt-4">
                            <span class="bi bi-arrow-up-right-circle fe-12 mr-2"></span>Lihat</button>
                        </form>
                    </div>
                    @else
                        <p class="text-center fs-4">Tidak Ada Data
                            {{ $title }}</p>
                    @endif
                </div>
                <!-- end section -->
            </div>
            <!-- .col-12 -->
        </div>
        <!-- .row -->
    </div>
</div>
@endsection