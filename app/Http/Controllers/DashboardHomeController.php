<?php

namespace App\Http\Controllers;

use App\Models\soal;
use App\Models\User;
use App\Models\Dosen;
use App\Models\Kelas;
use App\Models\Modul;
use App\Models\Ujian;
use App\Models\Grup_soal;
use App\Http\Controllers\Controller;

class DashboardHomeController extends Controller
{
    //menampilkan halaman home
    public function index()
    {
        $staf = User::where('role', 'Staf')->count();
        $ketua = User::where('role', 'Ketua')->count();
        $dosen = Dosen::count();
        $mahasiswa = User::where('role', 'Mahasiswa')->count();
        $kelas = Kelas::count();
        $modul = Modul::count();
        $grupsoal = Grup_soal::count();
        $soal = soal::count();
        $ujian = Ujian::count();
        if(auth()->user()->role == "Ketua"){
            $modul = Modul::where('user_id', auth()->user()->id)->count();
            $grupsoal = Grup_soal::where('user_id', auth()->user()->id)->count();
            $ujian = Ujian::where('user_id', auth()->user()->id)->count();
            $soal = Soal::whereHas('grup_soal', function($query){
                $query->where('user_id', auth()->user()->id);
            })->count();
        }
        return view('home', [
            "nama" => "Krisna Wahyudi",
            "title" => "Home",
            "dosen" => $dosen,
            "staf" => $staf,
            "ketua" => $ketua,
            "kelas" => $kelas,
            "mahasiswa" => $mahasiswa,
            "modul" => $modul,
            "grupsoal" => $grupsoal,
            "soal" => $soal,
            "ujian" => $ujian
        ]);
    }
}
