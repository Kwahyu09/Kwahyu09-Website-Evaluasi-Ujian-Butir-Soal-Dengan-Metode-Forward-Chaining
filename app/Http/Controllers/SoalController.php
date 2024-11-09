<?php

namespace App\Http\Controllers;

use App\Models\Grup_soal;
use App\Models\Modul;
use App\Models\soal;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SoalImport;

class SoalController extends Controller
{
    // menampilkan menu soal
    public function index(Grup_soal $grup_soal)
    {
        return view('grupsoal.soal.soal',[
            "title" => "soal",
            "slug" => $grup_soal->slug,
            "grup" => $grup_soal->nama_grup,
            "post" => $grup_soal->soal
        ]);
    }

    // menampilkan menu tambah data soal
    public function create(Grup_soal $grup_soal)
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $kode_unik = '';
        for ($i = 0; $i < 8; $i++) {
            $kode_unik .= $characters[random_int(0, strlen($characters) - 1)];
        }

        return view('grupsoal.soal.create',[
            "title" => "Soal",
            "slug" => $kode_unik,
            "nama_grup" => $grup_soal->nama_grup,
            "grupsoal_id" => $grup_soal->id,
            "grupsoal_nama" => $grup_soal->nama_grup,
            "modul" => Modul::find($grup_soal->modul_id,['nama_modul']),
            "grupsoal_slug" => $grup_soal->slug
        ]);
    }

    // menampilkan tambah data soal jika jawaban bergambar
    public function create_gambar(Grup_soal $grup_soal)
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $kode_unik = '';
        for ($i = 0; $i < 8; $i++) {
            $kode_unik .= $characters[random_int(0, strlen($characters) - 1)];
        }

        return view('grupsoal.soal.creategambar',[
            "title" => "Soal",
            "slug" => $kode_unik,
            "nama_grup" => $grup_soal->nama_grup,
            "grupsoal_id" => $grup_soal->id,
            "grupsoal_nama" => $grup_soal->nama_grup,
            "modul" => Modul::find($grup_soal->modul_id,['nama_modul']),
            "grupsoal_slug" => $grup_soal->slug
        ]);
    }

    // method untuk mengimmport data soal
    public function ImportExel(Request $request)
    {
        // validasi
		$this->validate($request, [
			'file' => 'required'
		]);
 
		// menangkap file excel
		$file = $request->file('file');
 
		// membuat nama file unik
		$nama_file = rand().$file->getClientOriginalName();
 
		// upload ke folder file_user di dalam folder public
		$file->move('file_user',$nama_file);
 
		// import data
		Excel::import(new SoalImport, public_path('/file_user/'.$nama_file));
 
 
		// alihkan halaman kembali
		return redirect('/soal'.'/'.$request->slug_grup)->with('success','Data Mahasiswa Berhasil Diimport!');
    }

    // menampilkan halaman import data soal
    public function createImport(Grup_soal $grup_soal)
    {
        return view('grupsoal.soal.import',[
            "title" => "Soal",
            "nama_grup" => $grup_soal->nama_grup,
            "grupsoal_id" => $grup_soal->id,
            "grupsoal_nama" => $grup_soal->nama_grup,
            "modul" => Modul::find($grup_soal->modul_id,['nama_modul']),
            "grupsoal_slug" => $grup_soal->slug
        ]);
    }

    // menambahkan data soal ke dalam database
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'pertanyaan' => 'required|min:2',
            'grup_soal_id' => 'required',
            'slug' => 'required|min:3|max:8|unique:App\Models\Soal',
            'gambar' => 'image|file|max:5120',
            'opsi_a' => 'required',
            'opsi_b' => 'required',
            'opsi_c' => 'required',
            'opsi_d' => 'required',
            'opsi_e' => 'required',
            'bobot' => 'required'
        ]);
 
        if($request->file('gambar')){
            $validatedData['gambar'] = $request->file('gambar')->store('gambar-soal');
        }

        if($request['jawaban'] == "opsi_a"){
            $validatedData['jawaban'] = $request['opsi_a'];
        }elseif($request['jawaban'] == "opsi_b"){
            $validatedData['jawaban'] = $request['opsi_b'];
        }elseif($request['jawaban'] == "opsi_c"){
            $validatedData['jawaban'] = $request['opsi_c'];
        }elseif($request['jawaban'] == "opsi_d"){
            $validatedData['jawaban'] = $request['opsi_d'];
        }else{
            $validatedData['jawaban'] = $request['opsi_e'];
        }

        soal::create($validatedData);
        return redirect('/soal'.'/'.$request['grupsoal_slug'])->with('success', 'Data Berhasil Ditambahkan!');
    }

    // menambahkan data soal dengan jawaban ada gambar ke database
    public function storegambar(Request $request)
    {
        $validatedData = $request->validate([
            'pertanyaan' => 'required|min:2|max:255',
            'grup_soal_id' => 'required',
            'slug' => 'required|min:3|max:8|unique:App\Models\Soal',
            'gambar' => 'image|file|max:5120',
            'opsi_a' => 'image|file|max:5120',
            'opsi_b' => 'image|file|max:5120',
            'opsi_c' => 'image|file|max:5120',
            'opsi_d' => 'image|file|max:5120',
            'opsi_e' => 'image|file|max:5120',
            'bobot' => 'required'
        ]);

        if($request->file('gambar')){
            $validatedData['gambar'] = $request->file('gambar')->store('gambar-soal');
        }
        if($request->file('opsi_a')){
            $validatedData['opsi_a'] = $request->file('opsi_a')->store('gambar-soal');
        }
        if($request->file('opsi_b')){
            $validatedData['opsi_b'] = $request->file('opsi_b')->store('gambar-soal');
        }
        if($request->file('opsi_c')){
            $validatedData['opsi_c'] = $request->file('opsi_c')->store('gambar-soal');
        }
        if($request->file('opsi_d')){
            $validatedData['opsi_d'] = $request->file('opsi_d')->store('gambar-soal');
        }
        if($request->file('opsi_e')){
            $validatedData['opsi_e'] = $request->file('opsi_e')->store('gambar-soal');
        }

        if($request['jawaban'] == "opsi_a"){
            $validatedData['jawaban'] = $request->file('opsi_a')->store('gambar-soal');
        }elseif($request['jawaban'] == "opsi_b"){
            $validatedData['jawaban'] = $request->file('opsi_b')->store('gambar-soal');
        }elseif($request['jawaban'] == "opsi_c"){
            $validatedData['jawaban'] = $request->file('opsi_c')->store('gambar-soal');
        }elseif($request['jawaban'] == "opsi_d"){
            $validatedData['jawaban'] = $request->file('opsi_d')->store('gambar-soal');
        }else{
            $validatedData['jawaban'] = $request->file('opsi_e')->store('gambar-soal');
        }

        soal::create($validatedData);
        return redirect('/soal'.'/'.$request['grupsoal_slug'])->with('success', 'Data Berhasil Ditambahkan!');
    }

    // menampilkan halaman edit soal
    public function edit(soal $soal)
    {
        return view('grupsoal.soal.edit',[
            "title" => "Soal",
            "grupsoal_slug" => $soal->grup_soal->slug,
            "post" => $soal
        ]);
    }

    // mengubah data soal didalam database
    public function update(Request $request, soal $soal)
    {
        $rules = [
            'pertanyaan' => 'required|min:2',
            'grup_soal_id' => 'required',
            'slug' => 'required',
            'opsi_a' => 'required',
            'opsi_b' => 'required',
            'opsi_c' => 'required',
            'opsi_d' => 'required',
            'opsi_e' => 'required',
            'bobot' => 'required'
        ];

        $validatedData = $request->validate($rules);

        if($request->file('gambar')){
            if($request->oldGambar){
                Storage::delete($request->oldGambar);
            }
            $validatedData['gambar'] = $request->file('gambar')->store('gambar-soal');
        }

        if($request['jawaban'] == "opsi_a"){
            $validatedData['jawaban'] = $request['opsi_a'];
        }elseif($request['jawaban'] == "opsi_b"){
            $validatedData['jawaban'] = $request['opsi_b'];
        }elseif($request['jawaban'] == "opsi_c"){
            $validatedData['jawaban'] = $request['opsi_c'];
        }elseif($request['jawaban'] == "opsi_d"){
            $validatedData['jawaban'] = $request['opsi_d'];
        }else{
            $validatedData['jawaban'] = $request['opsi_e'];
        }
        
        Soal::where('id', $soal->id)
            ->update($validatedData);
        return redirect('/soal'.'/'.$soal->grup_soal->slug)->with('success', 'Data Berhasil DiUbah!');
    }

    public function updategambar(Request $request, soal $soal)
    {
        $rules = [
            'pertanyaan' => 'required|min:2|max:255',
            'grup_soal_id' => 'required',
            'slug' => 'required',
            'gambar' => 'image|file|max:5120',
            'opsi_a' => 'image|file|max:5120',
            'opsi_b' => 'image|file|max:5120',
            'opsi_c' => 'image|file|max:5120',
            'opsi_d' => 'image|file|max:5120',
            'opsi_e' => 'image|file|max:5120',
            'bobot' => 'required'
        ];
        
        $validatedData = $request->validate($rules);

        if($request->file('gambar')){
            if($request->oldGambar){
                Storage::delete($request->oldGambar);
            }
            $validatedData['gambar'] = $request->file('gambar')->store('gambar-soal');
        }
        if($request->file('opsi_a')){
            if($request->oldOpsie){
                Storage::delete($request->oldOpsia);
            }
            $validatedData['opsi_a'] = $request->file('opsi_a')->store('gambar-soal');
        }
        if($request->file('opsi_b')){
            if($request->oldOpsib){
                Storage::delete($request->oldOpsib);
            }
            $validatedData['opsi_b'] = $request->file('opsi_b')->store('gambar-soal');
        }
        if($request->file('opsi_c')){
            if($request->oldOpsic){
                Storage::delete($request->oldOpsic);
            }
            $validatedData['opsi_c'] = $request->file('opsi_c')->store('gambar-soal');
        }
        if($request->file('opsi_d')){
            if($request->oldOpsid){
                Storage::delete($request->oldOpsid);
            }
            $validatedData['opsi_d'] = $request->file('opsi_d')->store('gambar-soal');
        }
        if($request->file('opsi_e')){
            if($request->oldOpsie){
                Storage::delete($request->oldOpsie);
            }
            $validatedData['opsi_e'] = $request->file('opsi_e')->store('gambar-soal');
        }

        if($request['jawaban'] == "opsi_a"){
            $validatedData['jawaban'] = $request->file('opsi_a')->store('gambar-soal');
        }elseif($request['jawaban'] == "opsi_b"){
            $validatedData['jawaban'] = $request->file('opsi_b')->store('gambar-soal');
        }elseif($request['jawaban'] == "opsi_c"){
                $validatedData['jawaban'] = $request->file('opsi_c')->store('gambar-soal');
        }elseif($request['jawaban'] == "opsi_d"){
            $validatedData['jawaban'] = $request->file('opsi_d')->store('gambar-soal');
        }else{
            $validatedData['jawaban'] = $request->file('opsi_e')->store('gambar-soal');
        }

        Soal::where('id', $soal->id)
            ->update($validatedData);
        return redirect('/soal'.'/'.$soal->grup_soal->slug)->with('success', 'Data Berhasil DiUbah!');
    }

    // menghapus soal beserta gambar didalam sistem
    public function destroy(soal $soal)
    {
        if($soal->gambar){
            Storage::delete($soal->gambar);
        }
        if(preg_match('/^gambar-soal\//', $soal->opsi_a)){
            Storage::delete($soal->opsi_a);
        }
        if(preg_match('/^gambar-soal\//', $soal->opsi_b)){
            Storage::delete($soal->opsi_b);
        }
        if(preg_match('/^gambar-soal\//', $soal->opsi_c)){
            Storage::delete($soal->opsi_c);
        }
        if(preg_match('/^gambar-soal\//', $soal->opsi_d)){
            Storage::delete($soal->opsi_d);
        }
        if(preg_match('/^gambar-soal\//', $soal->opsi_e)){
            Storage::delete($soal->opsi_e);
        }
        if(preg_match('/^gambar-soal\//', $soal->jawaban)){
            Storage::delete($soal->jawaban);
        }
        soal::destroy($soal->id);
        return back()->with('success', 'Data Berhasil DiHapus!');
    }
}
