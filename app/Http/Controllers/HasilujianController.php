<?php

namespace App\Http\Controllers;

use App\Models\Ujian;
use App\Models\Evaluasi;
use App\Models\Modul;
use App\Models\HasilUjian;
use Illuminate\Http\Request;

class HasilujianController extends Controller
{
    //menampilkan halaman hasil ujian memilih ujian index
    public function index()
    {
        $post = Modul::latest()->filter(request(['search','modul']))->paginate(8);

        if(auth()->user()->role == "Ketua"){
            $post = Modul::where('user_id', auth()->user()->id)->latest()->filter(request(['search','modul']))->paginate(8);
        }
        return view('hasilujian_modul', [
            "title" => "Hasil Ujian",
            "post" => $post
        ]);
    }

    public function indexhasil(Modul $modul)
    {
        $idModul = $modul->id;
        $ujian = Ujian::where('modul_id',$idModul)->get();
        
        if(auth()->user()->role == "Ketua"){
            $ujian = Ujian::where('user_id', auth()->user()->id)->where('modul_id',$idModul)->get();
        }
        
        $namaujian = Ujian::where('modul_id',$idModul)->value('nama_ujian');
        $namamodul =  $modul->nama_modul;

        return view('hasilujian_admin', [
            "title" => "Hasil Ujian",
            "ujian" => $ujian,
            "namaujian" => $namaujian,
            "namamodul" => $namamodul,
        ]);
    }

    // menampilkan hasil ujian detai dimana terlihat nama dan nilai mahasiswa
    public function hasil(Request $request)
    {
        $id_ujian = $request->ujian_id;
        // Mengambil data hasil ujian dan mengurutkannya berdasarkan kolom npm di tabel user
        $hasil_ujian = HasilUjian::with('user')
            ->where('ujian_id', $id_ujian)
            ->join('users', 'hasil_ujians.user_id', '=', 'users.id')
            ->orderBy('users.npm')
            ->select('hasil_ujians.*') // pastikan hanya mengambil kolom dari tabel hasil_ujian
            ->get();

        $ujian = Ujian::find($id_ujian);

        return view('hasilujian', [
            "title" => "Hasil Ujian",
            "hasil" => $hasil_ujian,
            "ujian" => $ujian
        ]);
    }

    //cetak
    public function cetak(Request $request)
    {
        // Logika pencetakan
        $id_ujian = $request->ujian_id;
        $hasil_ujian =  HasilUjian::with('user')
            ->where('ujian_id', $id_ujian)
            ->join('users', 'hasil_ujians.user_id', '=', 'users.id')
            ->orderBy('users.npm')
            ->select('hasil_ujians.*') // pastikan hanya mengambil kolom dari tabel hasil_ujian
            ->get();
        $ujian = Ujian::find($id_ujian);

        return view('cetak', [
            "hasil" => $hasil_ujian,
            "ujian" => $ujian
        ]);
    }

    // menampilkan hasil ujian untuk mahasiswa
    public function hasil_ujianmhs(){
        return view('hasil_ujian',  [
            "title" => "Ujian Mahasiswa",
            "total" => session('hasilmahasiswa')
        ]);
    }

    // method untuk menghitung hasil ujian
    public function selesai_ujian(Request $request)
    {
        $validatedData = $request->validate([
            'ujian_id' => 'required',
            'user_id' => 'required'
        ]);

        $totalbobot = $request['totalbobot'];
        $nilaimhs = Evaluasi::where('ujian_id', $request->ujian_id)
                        ->where('user_id', $request->user_id)
                       ->sum('skor');
        $nilai = ($nilaimhs / $totalbobot) * 100;
        // menggunakan format dibelakang koma diambil 2 angka setelah koma
        $nilaiFormatted = number_format($nilai, 2);

        // hapus nilai koma jika bilangan bulat
        $validatedData['nilai'] = rtrim(rtrim($nilaiFormatted, '0'), '.'); 

        HasilUjian::create($validatedData);
        // Setel sesi 'ujian_selesai' menjadi true
        session()->put('ujian_selesai', true);

        return redirect()->route('hasil-ujianmhs')->with('hasilmahasiswa', $validatedData['nilai'])->header('Cache-Control', 'no-cache, no-store, must-revalidate');
    }
}
