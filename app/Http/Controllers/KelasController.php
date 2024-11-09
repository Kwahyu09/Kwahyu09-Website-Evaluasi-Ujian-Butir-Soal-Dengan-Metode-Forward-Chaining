<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kelas;
use App\Models\Prodi;
use Illuminate\Http\Request;
use \Cviebrock\EloquentSluggable\Services\SlugService;

class KelasController extends Controller
{
    // menampilkan menu kelas
    public function index()
    {
        return view('fakultas.kelas.index', [
            "title" => "Kelas",
            "post" => Kelas::with('prodi')
                        ->orderBy('tahun_ajaran', 'desc')  // Mengurutkan berdasarkan kolom tahun secara descending
                        ->filter(request(['search']))
                        ->paginate(10)
        ]);
    }

    // menampilkan menu tambah kelas
    public function create()
    {
        $prodi = Prodi::all();
        return view('fakultas.kelas.create',[
            "title" => "Kelas",
            "prodi" => $prodi
        ]);
    }

    // menambahkan data kelas ke database
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama_kelas' => 'required|max:30',
            'tahun_ajaran' => 'required|min:4|max:4',
            'slug' => 'required|max:255|unique:App\Models\Kelas',
            'prodi_id' => 'required'
        ]);
        Kelas::create($validatedData);

        return redirect('/kelas')->with('success', 'Data Kelas Berhasil Ditambahkan!');
    }

    // menampiilkan menu mahasiswa
    public function show(Request $request, Kelas $kelas)
    {
        $search = $request->get('search');

        return view('mahasiswa.index',[
            'title' => 'Mahasiswa',
            'kelas' => $kelas->slug,
            'nama_kelas' => $kelas->nama_kelas,
            'post' => User::where('kelas_id', $kelas->id)->where(function ($query) use ($search) {
                $query->where('username', 'like', '%'. $search .'%')
            ->orWhere('nama', 'like', '%' . $search . '%')
            ->orWhere('npm', 'like', '%' . $search . '%')
            ->orWhere('email', 'like', '%' . $search . '%');
            })->orderBy('npm', 'asc')->get()
        ]);
    }

    // menampilkan menu edit kelas
    public function edit(Kelas $kelas)
    {
        $prodi = Prodi::all();
        return view('fakultas.kelas.edit', [
            "title" => "Kelas",
            "post" => $kelas,
            "prodi" => $prodi
        ]);
    }

    // mengubah data kelas di database
    public function update(Request $request, Kelas $kelas)
    {
        $rules = [
            'nama_kelas' => 'required|max:30',
            'tahun_ajaran' => 'required',
            'prodi_id' => 'required'
        ];

        if($request->slug != $kelas->slug){
            $rules['slug'] = 'required|max:255';
        }

        $validatedData = $request->validate($rules);
        Kelas::where('id', $kelas->id)
            ->update($validatedData);
        return redirect('/kelas')->with('success', 'Data Berhasil DiUbah!');
    }

    // menghapus data kelas di database
    public function destroy(Kelas $kelas)
    {
        Kelas::destroy($kelas->id);
        return redirect('/kelas')->with('success', 'Data Berhasil DiHapus!');
    }

    public function checkSlug(Request $request)
{
    $nama_kelas = $request->nama_kelas;
    $tahun_ajaran = $request->tahun_ajaran;
    $prodi_id = $request->prodi_id;

    // Fetch the prodi name based on prodi_id
    $prodi = Prodi::find($prodi_id);
    $prodi_name = $prodi ? $prodi->nama_prodi : '';

    // Combine the parameters to create a full string for slug
    $full_name = $nama_kelas . ' ' . $tahun_ajaran . ' ' . $prodi_name;

    $slug = SlugService::createSlug(Kelas::class, 'slug', $full_name);
    return response()->json(['slug' => $slug ]);
}

    // menampilkan menu mahasiswa berdasarkan kelas
    public function kelas_mahasiswa()
    {
        return view('mahasiswa.kelas',[
            "title" => "Kelas Mahasiswa",
            "slug" => "kelasmahasiswa",
            "post" => Kelas::with('prodi')->orderBy('tahun_ajaran', 'desc')->latest()->filter(request(['search']))->paginate(8)
        ]);
    }
}
