<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\Grup_dosen;
use App\Models\Jabatan;
use App\Models\Golongan;
use App\Models\Prodi;
use App\Models\Modul;
use App\Models\User;
use Illuminate\Http\Request;
use \Cviebrock\EloquentSluggable\Services\SlugService;

class DosenController extends Controller
{
    //menampilkan halaman index dosen
    public function index()
    {
        return view('fakultas.dosen.index', [
            "title" => "Dosen",
            "post" => Dosen::with(['prodi','golongan','jabatan'])->latest()->filter(request(['search','dosen']))->paginate(10)
        ]);
    }
    
    //menampilkan halaman GRUP MODUL PER DOSEN
    public function grupmodul()
    {
        return view('fakultas.dosen.grupmodul', [
            "title" => "Grup Dosen",
            "post" => Modul::latest()->filter(request(['search','modul']))->paginate(8)  
        ]);
    }

    //menampilkan halaman GRUP MODUL PER DOSEN
    public function index_grupdosen(Request $request, Modul $modul)
    {
        return view('fakultas.dosen.grupindex',[
            "title" => "Grup Dosen",
            "slug" => $modul->slug,
            "modul" => $modul->nama_modul,
            "modul_id" => $modul->id,
            "ketua" => User::where('id', $modul->user_id)->get(),
            "post" => Grup_dosen::with('dosen')->latest()->where('modul_id', $modul->id)->paginate(100)
        ]);
    }

    //menampilkan halaman tambah data dosen
    public function create()
    {
        $prodi = Prodi::all(); 
        $jabatanId = old('jabatan_id') ?: Jabatan::min('id');
        $golongan = Golongan::where('jabatan_id', $jabatanId)->get();

        return view('fakultas.dosen.create', [
            "title" => "Dosen",
            "jabatan" => Jabatan::all(),
            "golongan" => $golongan,
            "prodi" => $prodi
        ]);
    }

    //menampilkan halaman tambah data dosen
    public function creategrupdos(Request $request, Modul $modul)
    { 
        $nipketua = User::where('id', $modul->user_id)->value('nip');
        $grupdos = Grup_dosen::where('modul_id', $modul->id)->get();
        $existingDosenIds = $grupdos->pluck('dosen_id')->toArray(); // Ambil ID dosen yang sudah ada di grup
        return view('fakultas.dosen.creategrupdos', [
            "title" => "Anggota Dosen",
            "modul" => $modul->nama_modul,
            "modul_id" => $modul->id,
            "slug" => $modul->slug,
            "grupdosen" => $grupdos,
            "userNIP" => $nipketua,
            "dosen" => Dosen::latest()->paginate(100),
            "existingDosenIds" => $existingDosenIds // Kirim ID dosen yang sudah ada ke view
        ]);
    }

    // menambahkan data grup dosen ke dalam database
    public function storegrup(Request $request)
    {
        $validatedData = $request->validate([
            'dosen_id' => 'required|array', // Pastikan dosen_id adalah array
            'modul_id' => 'required'
        ]);

        $modul_id = $validatedData['modul_id'];
        $dosen_ids = $validatedData['dosen_id'];

        foreach ($dosen_ids as $dosen_id) {
            Grup_dosen::create([
                'modul_id' => $modul_id,
                'dosen_id' => $dosen_id
            ]);
        }

        return redirect('/dosen'.'/'.'grupdosen'.'/'.$request['slug'])->with('success', 'Data Berhasil Ditambahkan!');
    }

    //menambahkan data dosen ke database
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'prodi_id' => 'required',
            'nip' => 'required|min:16|max:18|regex:/^[0-9]+$/|unique:App\Models\Dosen',
            'nama_dos' => 'required|min:3|max:60',
            'slug' => 'required|unique:App\Models\Dosen',
            'jabatan_id' => 'required',
            'golongan_id' => 'required',
            'jenis_kel' => 'required|min:4|max:9',
            'email' => 'required|email|max:60|min:6|unique:App\Models\Dosen'
        ]);
        
        Dosen::create($validatedData);
        return redirect('/dosen')->with('success', 'Data Berhasil Ditambahkan!');
    }

    //menampilkan halaman edit dosen
    public function edit(Dosen $dosen)
    {
        $prodi = Prodi::all();
        $jabatanId = old('jabatan_id') ?: Jabatan::min('id');
        $golongan = Golongan::where('jabatan_id', $jabatanId)->get();
        return view('fakultas.dosen.edit', [
            "title" => "Dosen",
            "post" => $dosen,
            "jabatan" => Jabatan::all(),
            "golongan" => $golongan,
            "prodi" => $prodi
        ]);
    }

    //mengubah data dosen di database
    public function update(Request $request, Dosen $dosen)
    {
        $rules = [
            'prodi_id' => 'required',
            'nama_dos' => 'required|min:3|max:255',
            'jabatan_id' => 'required',
            'golongan_id' => 'required',
            'jenis_kel' => 'required|min:4|max:9'
        ];

        if($request->nip != $dosen->nip){
            $rules['nip'] = 'required|min:16|max:18|regex:/^[0-9]+$/|unique:App\Models\Dosen';
        }
        if($request->slug != $dosen->slug){
            $rules['slug'] = 'required|unique:App\Models\Dosen';
        }
        if($request->email != $dosen->email){
            $rules['email'] = 'required|email|max:60|min:6|unique:App\Models\Dosen';
        }

        $validatedData = $request->validate($rules);
        Dosen::where('id', $dosen->id)
            ->update($validatedData);
        return redirect('/dosen')->with('success', 'Data Berhasil DiUbah!');
    }

    //menghapus data dosen di database
    public function deleteanggota(Request $request)
    {
        $validatedData = $request->validate([
            'dosen_id' => 'required',
            'modul_id' => 'required',
        ]);
    
        // Cari grup_dosen berdasarkan dosen_id dan modul_id
        $dosen = Dosen::where('slug', $request->dosen_id)->firstOrFail();
        $grup_dosen = Grup_dosen::where('dosen_id', $dosen->id)
                                ->where('modul_id', $request->modul_id)
                                ->firstOrFail();
    
        // Hapus data
        $grup_dosen->delete();

        return redirect('/dosen/grupdosen/' . $request->slug)->with('success', 'Data Berhasil DiHapus!');
    }

    //menghapus data dosen di database
    public function destroy(Dosen $dosen)
    {
        Dosen::destroy($dosen->id);
        return redirect('/dosen')->with('success', 'Data Berhasil DiHapus!');
    }

    //cekslug
    public function checkSlug(Request $request)
    {
        $slug = SlugService::createSlug(Dosen::class, 'slug', $request->nama_dos);
        return response()->json(['slug' => $slug ]);
    }

    public function getGolongan(Request $request)
{
    $jabatanId = $request->query('jabatan_id');
    $golongan = Golongan::where('jabatan_id', $jabatanId)->get();
    return response()->json($golongan);
}
}