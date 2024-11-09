<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Hash;

class UserImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new User([
            'kelas_id' => $row[0], 
            'npm' => $row[1],
            'nama' => $row[2],
            'username' => $row[3], 
            'role' => $row[4], 
            'email' => $row[5], 
            'password' => Hash::make($row[6]),
        ]);
    }
}
