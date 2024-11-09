<?php

namespace App\Http\Controllers;

use App\Models\Modul;
use App\Models\User;
use Illuminate\Http\Request;
use \Cviebrock\EloquentSluggable\Services\SlugService;

class ModulController extends Controller
{
    // menampilkan halaman modul
    public function index()
    {
        return view('fakultas.modul.index', [
            "title" => "Modul",
            "post" => Modul::with('user')->latest()->filter(request(['search']))->paginate(10)
        ]);
    }

    // menampilkan halaman tambah modul
    public function create()
    {
        return view('fakultas.modul.create',[
            "title" => "Modul",
            "post" => User::all()->where('role','Ketua')
        ]);
    }

    // menambahkan data modul ke database
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'kd_modul' => 'required|unique:App\Models\modul',
            'nama_modul' => 'required|min:1|max:30|unique:App\Models\modul',
            'slug' => 'required|unique:App\Models\modul',
            'semester' => 'required',
            'sks' => 'required',
            'user_id' => 'required'
        ]);

        modul::create($validatedData);

        return redirect('/modul')->with('success', 'Data Modul Berhasil Ditambahkan!');
    }

    // menampilkan data grupsoal ketika modul diklik
    public function show(modul $modul)
    {
        return view('grupsoal.grupsoal2', [
            "title" => "Grup Soal",
            'modul' => $modul->nama_modul,
            'post' => $modul->grup_soal
        ]);
    }

    // menampilkan menu edit modul
    public function edit(modul $modul)
    {
        return view('fakultas.modul.edit', [
            "title" => "Modul",
            "post" => $modul,
            "user" => User::all()->where('role','Ketua')
        ]);
    }

    // mengubah data modul didatabase
    public function update(Request $request, modul $modul)
    {
        $rules = [
            'semester' => 'required',
            'sks' => 'required',
            'user_id' => 'required'
        ];

        if($request->slug != $modul->slug){
            $rules['slug'] = 'required|unique:App\Models\modul';
        }
        if($request->nama_modul != $modul->nama_modul){
            $rules['nama_modul'] = 'required|min:1|max:255|unique:App\Models\modul';
        }
        if($request->kd_modul != $modul->kd_modul){
            $rules['kd_modul'] = 'required|unique:App\Models\modul';
        }

        $validatedData = $request->validate($rules);
        modul::where('id', $modul->id)
            ->update($validatedData);
        return redirect('/modul')->with('success', 'Data Berhasil DiUbah!');
    }

    // mengahapus data modul
    public function destroy(modul $modul)
    {
        Modul::destroy($modul->id);
        return redirect('/modul')->with('success', 'Data Berhasil DiHapus!');
    }

    // method untuk membuat checkslug
    public function checkSlug(Request $request)
    {
        $slug = SlugService::createSlug(Modul::class, 'slug', $request->nama_modul);
        return response()->json(['slug' => $slug ]);
    }
}
