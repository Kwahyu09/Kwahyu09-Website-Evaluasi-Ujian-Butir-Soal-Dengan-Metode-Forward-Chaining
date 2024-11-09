<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="icon" href="{{ asset('/backend/assets/img/logounib.png') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-- General CSS Files -->
    <link rel="stylesheet" href="{{ asset('/backend/assets/modules/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/backend/assets/modules/fontawesome/css/all.min.css') }}">

    <!-- Template CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('/backend/assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('/backend/assets/css/components.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('/css/style.css') }}">

    {{-- Trik EDITOR --}}
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.0/dist/trix.css">
    <script type="text/javascript" src="https://unpkg.com/trix@2.0.0/dist/trix.umd.min.js"></script>
    @if (Auth::user()->role == 'Mahasiswa')
        <script src="{{ asset('/js/jam.js') }}"></script>
    @endif
    <style>
        trix-toolbar [data-trix-button-group="file-tools"] {
            display: none;
        }
    </style>
    <style>
    .tombol-ragu {
        display: inline-flex;
        align-items: flex-end;
        padding: 10px 20px;
        background-color: rgb(255, 255, 0);
        border: 2px solid #ccc;
        border-radius: 5px;
        cursor: pointer;
        user-select: none;
    }

    .tombol-ragu input[type="checkbox"] {
        margin-right: 10px;
    }

    .tombol-ragu input[type="checkbox"]:checked + label {
        font-weight: bold;
    }

    .tombol-ragu:hover {
        background-color: #ffeb3b; /* Warna kuning sedikit lebih gelap saat hover */
    }

    .tombol-ragu:active {
        background-color: #fdd835; /* Warna kuning lebih gelap saat diklik */
    }
    
    .custom-table {
        border-collapse: collapse;
        width: 100%;
    }

    .custom-table th {
        background-color: #646464; /* Warna abu-abu gelap untuk th */
        color: white;
        font-weight: bold;
        padding: 8px;
        text-align: left;
    }

    .custom-table td {
        padding: 8px;
        text-align: left;
    }

    .custom-table tr:nth-child(odd) td {
        background-color: #f2f2f2; /* Warna abu-abu terang untuk baris ganjil */
    }

    .custom-table tr:nth-child(even) td {
        background-color: white; /* Warna putih untuk baris genap */
    }
    </style>
    <style>
        #chartContainer {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        .chart {
            width: 100%;
            height: 300px;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .card-fixed {
            position: fixed;
            top: 40%;
            right: 20px;
            transform: translateY(-50%);
            z-index: 999;
            bottom: -20%;
        }

        .kotak-ragu {
            background-color: yellow !important;
            color: black !important;
        }

        .kotak-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            overflow-y: auto;
            /* Tambahkan properti overflow-y untuk mengizinkan scrolling vertikal */
            max-height: 400px;
            /* Atur ketinggian maksimum untuk memunculkan scroll ketika konten melebihi ukuran ini */
        }

        .kotak-soal {
            width: 30px;
            height: 30px;
            text-align: center;
            line-height: 30px;
            border: 1px solid #000;
            border-radius: 5px;
            text-decoration: none;
            color: #000;
            background-color: #ccc;
            cursor: pointer;
            margin-bottom: 10px;
        }

        .biru {
            background-color: blue;
            color: white;
        }
        .kuning {
            background-color: yellow;
            color: black;
        }

        #watch {
            color: rgb(252, 150, 65);
            position: absolute;
            z-index: 1;
            height: 40px;
            width: 700px;
            overflow: show;
            margin: auto;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
            font-size: 10vw;
            -webkit-text-stroke: 3px rgb(210, 65, 36);
            text-shadow: 4px 4px 10px rgba(210, 65, 36, 0.4),
                4px 4px 20px rgba(210, 45, 26, 0.4),
                4px 4px 30px rgba(210, 25, 16, 0.4),
                4px 4px 40px rgba(210, 15, 06, 0.4);
        }
    </style>

    <!-- Start GA -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-94034622-3"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('backend/js', new Date());

        gtag('config', 'UA-94034622-3');
    </script>

    <title>evaluasi-ujian | {{ $title }}</title>
</head>
@if (Auth::user()->role == 'Mahasiswa')

    <body onload="realtimeClock()">
    @else

        <body>
@endif
<div id="app">
    <div class="main-wrapper main-wrapper-1">
        @include('partial.navbar')
        <div class="main-sidebar sidebar-style-2">
            @include('partial.sidebare')
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <section class="section">
                <div class="section-header">
                    <h3>Fakultas Kedokteran dan Ilmu Kesehatan UNIB</h3>
                </div>
                @yield('container')

        </div>
        </section>
    </div>
</div>
</div>
<!-- General JS Scripts -->
<script src="https://code.jquery.com/jquery-3.7.0.slim.js"
    integrity="sha256-7GO+jepT9gJe9LB4XFf8snVOjX3iYNb0FHYr5LI1N5c=" crossorigin="anonymous"></script>
<script src="{{ asset('/dist/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset('/dist/myscript.js') }}"></script>
<script src="{{ asset('/backend/assets/modules/jquery.min.js') }}"></script>
<script src="{{ asset('/backend/assets/modules/popper.js') }}"></script>
<script src="{{ asset('/backend/assets/modules/tooltip.js') }}"></script>
<script src="{{ asset('/backend/assets/modules/bootstrap/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('/backend/assets/modules/nicescroll/jquery.nicescroll.min.js') }}"></script>
<script src="{{ asset('/backend/assets/modules/moment.min.js') }}"></script>
<script src="{{ asset('/backend/assets/js/stisla.js') }}"></script>
<!-- Template JS File -->
<script src="{{ asset('/backend/assets/js/scripts.js') }}"></script>
<script src="{{ asset('/backend/assets/js/custom.js') }}"></script>
</body>
<!-- Page Specific JS File -->
@if (!Auth::user()->role == 'Mahasiswa')
    <script src="{{ asset('/backend/assets/js/page/index-0.js') }}"></script>
@endif

</html>