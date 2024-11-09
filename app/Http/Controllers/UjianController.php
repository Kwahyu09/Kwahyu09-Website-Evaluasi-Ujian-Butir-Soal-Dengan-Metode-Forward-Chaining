<?php

namespace App\Http\Controllers;

use App\Models\Ujian;
use App\Models\Grup_soal;
use App\Models\Kelas;
use App\Models\Modul;
use Illuminate\Http\Request;
use \Cviebrock\EloquentSluggable\Services\SlugService;

class UjianController extends Controller
{
    // menampilkan menu ujian
    public function index()
    {
        $ujian = Ujian::with(['modul','grup_soal','kelas'])->latest()->filter(request(['search']))->paginate(10);

        if(auth()->user()->role == "Ketua"){
            $ujian = Ujian::with(['modul','grup_soal','kelas'])->where('user_id', auth()->user()->id)->latest()->filter(request(['search']))->paginate(10);
        }
        return view('ujian.index', [
            "title" => "Ujian",
            "post" => $ujian
        ]);
    }

    //menampilkan menu tambah ujian
    public function create()
    {
        $modul = Modul::orderBy('nama_modul', 'asc')->get();
        $grupsoal = Grup_soal::orderBy('nama_grup', 'asc')->get();
        $kelas = Kelas::orderBy('tahun_ajaran', 'desc')->orderBy('nama_kelas', 'asc')->get();
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $kode_unik = '';
        for ($i = 0; $i < 8; $i++) {
            $kode_unik .= $characters[random_int(0, strlen($characters) - 1)];
        }

        if(auth()->user()->role == "Ketua"){
            $modul = Modul::where('user_id', auth()->user()->id)->latest()->filter(request(['search']))->paginate(1000);
            $grupsoal = Grup_soal::where('user_id', auth()->user()->id)->latest()->filter(request(['search']))->paginate(1000);
        }
        return view('ujian.create',[
            "title" => "Ujian",
            "kd_ujian" => $kode_unik,
            "modul" => $modul,
            "grup_soal" => $grupsoal,
            "kelas" => $kelas
        ]); 
    }

    // menambahkan data ujian ke database
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required',
            'kd_ujian' => 'required|min:8|max:8',
            'nama_ujian' => 'required|min:5|max:255',
            'kelas_id' => 'required',
            'modul_id' => 'required',
            'grup_soal_id' => 'required',
            'slug' => 'required|min:5|max:255|unique:App\Models\Ujian',
            'acak_soal' => 'required', 
            'tanggal' => 'required',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'required'
        ]);

        Ujian::create($validatedData);
        return redirect('/ujian')->with('success', 'Data Berhasil Ditambahkan!');
    }

    public function getGrupSoalByModul($modul_id)
    {
        $grup_soal = Grup_soal::where('modul_id', $modul_id)->get();

        return response()->json($grup_soal);
    }

    // menampilkan menu edit ujian
    public function edit(Ujian $ujian)
    {
        $modul = Modul::orderBy('nama_modul', 'asc')->get();
        $grupsoal = Grup_soal::orderBy('nama_grup', 'asc')->get();
        $kelas = Kelas::orderBy('tahun_ajaran', 'desc')->orderBy('nama_kelas', 'asc')->get();

        if(auth()->user()->role == "Ketua"){
            $modul = Modul::where('user_id', auth()->user()->id)->latest()->filter(request(['search']))->paginate(1000);
            $grupsoal = Grup_soal::where('user_id', auth()->user()->id)->latest()->filter(request(['search']))->paginate(1000);
        }


         return view('ujian.edit',[
            "title" => "Ujian",
            "post" => $ujian,
            "modul" => $modul,
            "grup_soal" => $grupsoal,
            "kelas" => $kelas
        ]);
    }

    //mengubah data ujian didatabase
    public function update(Request $request, Ujian $ujian)
    {
        $rules = [
            'user_id' => 'required',
            'kd_ujian' => 'required|min:6|max:8',
            'nama_ujian' => 'required|min:5|max:60',
            'kelas_id' => 'required',
            'modul_id' => 'required|max:30',
            'grup_soal_id' => 'required|max:255',
            'acak_soal' => 'required',
            'tanggal' => 'required',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'required'
        ];

        if($request->slug != $ujian->slug){
            $rules['slug'] = 'required|min:5|max:50|unique:App\Models\Ujian';
        }

        $validatedData = $request->validate($rules);
        Ujian::where('id', $ujian->id)
            ->update($validatedData);
        return redirect('/ujian')->with('success', 'Data Berhasil DiUbah!');
    }

    //menghapus data ujian
    public function destroy(Ujian $ujian)
    {
        Ujian::destroy($ujian->id);
        return back()->with('success', 'Data Berhasil DiHapus!');
    }

    // membuat slug data ujian
    public function checkSlug(Request $request)
    {
        $slug = SlugService::createSlug(Ujian::class, 'slug', $request->nama_ujian);
        return response()->json(['slug' => $slug ]);
    }
}
