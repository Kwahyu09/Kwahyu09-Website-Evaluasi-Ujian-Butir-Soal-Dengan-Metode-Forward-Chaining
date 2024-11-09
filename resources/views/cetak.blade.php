<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta names="viewport" contents="width=device-width, initial-scale-1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        table.static {
            position: relative;
            border: 1px solid #543535;
            width: 80%;
            margin: 0 auto; /* Center the table */
        }

        th, td {
            padding: 4px; /* Add padding for better readability */
        }

        .header-section {
            margin-bottom: 20px;
        }

        .header-section p {
            margin: 5px 0;
            white-space: nowrap; /* Prevent line breaks */
        }

        .header-section b {
            display: inline-block; /* Ensure colon alignment */
            width: 80px; /* Adjust as needed */
        }

        .center {
            text-align: center; /* Center the text */
        }
    </style>
    <title class="mt-5">CETAK DATA HASIL UJIAN</title>
</head>

<body>
    <div class="form-group mt-5 d-flex justify-content-center">
        <h3 class="center">Laporan Hasil Ujian</h3> <!-- Center the heading -->
        <div class="header-section ml-auto">
            <p>Kelas &nbsp; : {{ $ujian->kelas->nama_kelas }} {{ $ujian->kelas->tahun_ajaran }}</p>
            <p>Modul &nbsp;: {{ $ujian->modul->nama_modul }}</p>
            <p>{{ $ujian->nama_ujian }}</p>
        </div>

        <table class="static" rules="all" border="1px" align="center">
            <thead>
                <tr>
                    <th align="center" style="width: 10px">No.</th>
                    <th>Npm</th>
                    <th>Nama Mahasiswa</th>
                    <th align="center">Nilai</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($hasil as $has)
                <tr>
                    <td align="center" style="width: 10px">{{ $loop->iteration }}</td>
                    <td>{{ $has->user->npm }}</td>
                    <td>{{ $has->user->nama }}</td>
                    <td align="center">{{ $has->nilai }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
<script type="text/javascript">
    window.print();
</script>

</html>