<?php

namespace App\Imports;

use App\Models\Soal;
use Maatwebsite\Excel\Concerns\ToModel;

class SoalImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Soal([
            'slug' => $row[0], 
            'grup_soal_id' => $row[1],
            'pertanyaan' => $row[2],
            'opsi_a' => $row[3], 
            'opsi_b' => $row[4], 
            'opsi_c' => $row[5], 
            'opsi_d' => $row[6], 
            'opsi_e' => $row[7], 
            'jawaban' => $row[8], 
            'bobot' => $row[9], 
        ]);
    }
}
