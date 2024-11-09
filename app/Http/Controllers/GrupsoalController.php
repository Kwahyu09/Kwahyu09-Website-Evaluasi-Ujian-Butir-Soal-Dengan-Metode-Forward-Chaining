<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Modul;
use App\Models\soal;
use App\Models\Grup_soal;
use \Cviebrock\EloquentSluggable\Services\SlugService;

class GrupsoalController extends Controller
{
    //menampilkan index grup soal
    public function index()
    {
        $post = Modul::latest()->filter(request(['search','modul']))->paginate(8);

        if(auth()->user()->role == "Ketua"){
            $post = Modul::where('user_id', auth()->user()->id)->latest()->filter(request(['search','modul']))->paginate(8);
        }
        return view('grupsoal.grupsoal', [
            "title" => "grupsoal",
            "post" => $post
        ]);
    }

    public function show(Request $request, Grup_soal $grup_soal)
    {
        $id_modul = $grup_soal->modul_id;
        $search = $request->get('search');
        $slug_modul = $grup_soal->modul->slug;
        $id_grup = $grup_soal->id;
        $total = Soal::where('grup_soal_id',$grup_soal->id)->sum('bobot');
        return view('grupsoal.soal.soal',[
            "title" => "soal",
            "total" => $total,
            "slug_modul" => $slug_modul,
            "slug" => $grup_soal->slug,
            "grup" => $grup_soal->nama_grup,
            "modul" => Modul::find($id_modul,['nama_modul']),
            "post" => Soal::where('grup_soal_id', $grup_soal->id)->where(function ($query) use ($search) {
                $query->where('pertanyaan', 'like', '%' . $search . '%')
                  ->orWhere('opsi_a', 'like', '%' . $search . '%')
                  ->orWhere('opsi_b', 'like', '%' . $search . '%')
                  ->orWhere('opsi_c', 'like', '%' . $search . '%')
                  ->orWhere('opsi_d', 'like', '%' . $search . '%')
                  ->orWhere('opsi_e', 'like', '%' . $search . '%')
                  ->orWhere('jawaban', 'like', '%' . $search . '%')
                  ->orWhere('bobot', 'like', '%' . $search . '%');
            })->orderBy('id', 'desc')->paginate(100)
        ]);
    }

    public function index_grup(Modul $modul)
    {
        $post = Grup_soal::latest()->where('modul_id', $modul->id)->paginate(8);
        return view('grupsoal.grupsoal2', [
            "title" => "Grup Soal",
            'modul' => $modul->nama_modul,
            'slug' => $modul->slug,
            'post' => $post
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Modul $modul)
    {
        return view('grupsoal.create',[
            "title" => "Grup Soal",
            "modul_id" => $modul->id,
            "nama_modul" => $modul->nama_modul,
            "slug_modul" => $modul->slug

        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required',
            'modul_id' => 'required',
            'nama_grup' => 'required|min:4|max:255',
            'slug' => 'required|min:4|max:255|unique:App\Models\Grup_soal'
        ]);

        Grup_soal::create($validatedData);

        return redirect('/grupsoal'.'/'.$request->slug_modul)->with('success', 'Data Grup Berhasil Ditambahkan!');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Grup_soal $grup_soal)
    {
        $nama_modul = "";
        if ($grup_soal->modul_id == $grup_soal->modul_id){
           $nama_modul = Modul::find($grup_soal->modul_id, ['slug']);        
        }
        return view('grupsoal.edit',[
            "title" => "Grup Soal",
            "post" => $grup_soal,
            "nama_modul" => $nama_modul,
            "slug_modul" => $nama_modul,
            "link" => $grup_soal->modul->slug

        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Grup_soal $grup_soal)
    {
        $rules = [
            'user_id' => 'required',
            'modul_id' => 'required',
            'nama_grup' => 'required|min:4|max:255',
        ];

        if($request->slug != $grup_soal->slug){
            $rules['slug'] = 'required|min:4|max:255|unique:App\Models\Grup_soal';
        }

        $slug = $grup_soal->modul->slug;

        $validatedData = $request->validate($rules);
        Grup_soal::where('id', $grup_soal->id)
            ->update($validatedData);
        return redirect('/grupsoal'.'/'.$slug)->with('success', 'Data Berhasil DiUbah!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Grup_soal $grup_soal)
    {
        Grup_soal::destroy($grup_soal->id);
        return back()->with('success', 'Data Berhasil DiHapus!');
    }

    public function checkSlug(Request $request)
    {
        $slug = SlugService::createSlug(Grup_soal::class, 'slug', $request->nama_grup);

        return response()->json(['slug' => $slug ]);
    }
}
