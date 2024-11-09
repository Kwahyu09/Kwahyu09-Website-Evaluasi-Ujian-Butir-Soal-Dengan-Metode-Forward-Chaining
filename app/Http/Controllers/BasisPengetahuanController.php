<?php

namespace App\Http\Controllers;

use App\Models\Basis_pengetahuan;
use Illuminate\Http\Request;

class BasisPengetahuanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    //menampilkan halaman variabel
    //menampilkan halaman home staff
    public function index()
    {
        $dp = Basis_pengetahuan::where('kode', 'LIKE', 'DP%')->get();
        $tk = Basis_pengetahuan::where('kode', 'LIKE', 'TK%')->get();
        $ppj = Basis_pengetahuan::where('kode', 'LIKE', 'PPJ%')->get();
        
        return view('variabel.index', [
            "title" => "Basis Pengetahuan",
            "judul" => "Variabel Basis Pengetahuan",
            "dp" => $dp,
            "tk" => $tk,
            "ppj" => $ppj
        ]);
    }
    public function index_kes()
    {
        $ka = Basis_pengetahuan::where('kode', 'LIKE', 'KA%')->get();
        
        return view('variabel.index2', [
            "title" => "Basis Pengetahuan",
            "judul" => "kesimpulan",
            "ka" => $ka
        ]);
    }
}
