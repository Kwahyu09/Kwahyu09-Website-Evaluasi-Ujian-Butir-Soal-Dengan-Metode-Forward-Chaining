@extends('layoutdashboard.main')
@section('container')

<head>
    <meta http-equiv="Cache-Control" content="no-store" />
    <meta http-equiv="Pragma" content="no-store" />
    <meta http-equiv="Expires" content="0" />
</head>
<div class="row">
    <div class="col-md-6 col-sm-6 mb-3">
        <div class="card">
            <div class="card-body">
                <h3 class="text-center mt-3 mb-5">Nilai Ujian Anda :</h3>
                <h1 class="text-center mt-5 mb-5">{{ $total }}</h1>
                <div class="mt-3 mb-5 d-flex justify-content-center">
                    <a href="ujian-mahasiswa" class="btn btn-primary">Tutup</a>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    window.addEventListener('popstate', function (event) {
        window.history.pushState(null, null, window.location.href);
    });
</script>
@endsection