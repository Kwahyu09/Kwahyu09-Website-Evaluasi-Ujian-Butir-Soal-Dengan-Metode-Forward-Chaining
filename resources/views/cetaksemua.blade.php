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
        /* Print styles */
        @media print {
            th, td {
                -webkit-print-color-adjust: exact; 
                print-color-adjust: exact; 
            }
        }
    </style>
    <title class="mt-5">Cetak Semua Evaluasi Soal {{ $ujian->nama_ujian }}</title>
</head>

<body>
    <div class="form-group mt-5 d-flex justify-content-center">
        <h3 class="center">Laporan Hasil Evaluasi Semua Soal {{ $ujian->nama_ujian }}</h3> <!-- Center the heading -->
        <div class="header-section ml-auto">
            <p>
                @if(isset($tk1) && isset($tk2) && isset($tk3))
                <h6>Perserntase Klasifikasi Soal pada Ujian Ini:</h6>
                <label>Persentase Sukar : {{ $tk1 }}%</label></br>
                <label>Persentase Sedang : {{ $tk2 }}%</label></br>
                <label>Persentase Mudah : {{ $tk3 }}%</label>
                @endif
            </p>
        </div>

        <table class="static" rules="all" border="1px" align="center">
            <thead>
                <tr>
                    <th style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">Soal No.</th>
                    <th style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">Opsi A</th>
                    <th style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">Opsi B</th>
                    <th style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">Opsi C</th>
                    <th style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">Opsi D</th>
                    <th style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">Opsi E</th>
                    <th style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">Klasifikasi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($soal as $s)
                    <tr>
                        <td style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle;">
                            {{ ($soal->currentPage() - 1)  * $soal->links()->paginator->perPage() + $loop->iteration }}</td>
                            
                        @php
                            // Default background color is green
                            $bgColorA = 'green';
                            $bgColorB = 'green';
                            $bgColorC = 'green';
                            $bgColorD = 'green';
                            $bgColorE = 'green';

                            $basis_pengetahuan_id = $klasifikasiMap[$s->id]->basis_pengetahuan_id ?? null;
                            if ($basis_pengetahuan_id == 17) {
                                $bgColorA = 'red';
                                $bgColorB = 'red';
                                $bgColorC = 'red';
                                $bgColorD = 'red';
                                $bgColorE = 'red';
                            } else {
                                $analisisValues = $analisisMap[$s->id] ?? [];

                                // Check for specific values in the analysis data array
                                if (in_array(3, $analisisValues) || in_array(12, $analisisValues)) {
                                    $bgColorA = 'red';
                                }
                                if (in_array(4, $analisisValues) || in_array(13, $analisisValues)) {
                                    $bgColorB = 'red';
                                }
                                if (in_array(5, $analisisValues) || in_array(14, $analisisValues)) {
                                    $bgColorC = 'red';
                                }
                                if (in_array(6, $analisisValues) || in_array(15, $analisisValues)) {
                                    $bgColorD = 'red';
                                }
                                if (in_array(7, $analisisValues) || in_array(16, $analisisValues)) {
                                    $bgColorE = 'red';
                                }
                            }
                            if(!isset($klasifikasiMap[$s->id])){
                                $bgColorA = '';
                                $bgColorB = '';
                                $bgColorC = '';
                                $bgColorD = '';
                                $bgColorE = '';
                            }
                        @endphp

                        <td style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle; background-color: {{ $bgColorA }};"></td>
                        <td style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle; background-color: {{ $bgColorB }};"></td>
                        <td style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle; background-color: {{ $bgColorC }};"></td>
                        <td style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle; background-color: {{ $bgColorD }};"></td>
                        <td style="padding: 3px; border: 1px solid black; text-align: center; vertical-align: middle; background-color: {{ $bgColorE }};"></td>
                        
                        
                    <td style="width: 50%; padding: 3px; border: 1px solid black; text-align: start; vertical-align: middle;">
                        @if(isset($klasifikasiMap[$s->id]))
                                {{ $klasifikasiMap[$s->id]->basis_pengetahuan->keterangan }}
                                @php
                                $basis_pengetahuan_id = $klasifikasiMap[$s->id]->basis_pengetahuan_id ?? null;
                                @endphp
                                @if($basis_pengetahuan_id !== 17)
                                <b>{{ $tk[$s->id]->basis_pengetahuan->keterangan }}</b>
                                @endif
                        @else
                            <b><i>Belum Ada Klasifikasi</i></b>
                        @endif
                    </td>
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