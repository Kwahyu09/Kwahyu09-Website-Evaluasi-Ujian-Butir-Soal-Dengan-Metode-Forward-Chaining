<?php

namespace App\Http\Controllers;

use App\Models\Analisis_klasifikasi;
use App\Models\Evaluasi;
use App\Models\Grup_soal;
use App\Models\HasilUjian;
use App\Models\Ujian;
use App\Models\Analisis_nilai;
use App\Models\Kelompok_mahasiswa;
use App\Models\Kesimpulan_analisis;
use App\Models\Modul;
use App\Models\Soal;
use Illuminate\Http\Request;


class EvaluasiController extends Controller
{
    //menampilkan menu evaluasi index
    public function indexmodul()
    {
        $post = Modul::latest()->filter(request(['search','modul']))->paginate(8);

        if(auth()->user()->role == "Ketua"){
            $post = Modul::where('user_id', auth()->user()->id)->latest()->filter(request(['search','modul']))->paginate(8);
        }
        return view('evaluasi_modul', [
            "title" => "Evaluasi Ujian",
            "post" => $post
        ]);
    }
    //menampilkan menu evaluasi index
    public function index(Modul $modul)
    {
        $idModul = $modul->id;
        $ujian = Ujian::where('modul_id',$idModul)->get();
        
        if(auth()->user()->role == "Ketua"){
            $ujian = Ujian::where('user_id', auth()->user()->id)->where('modul_id',$idModul)->get();
        }

        $slug = $modul->slug;
        $namamodul = $modul->nama_modul;

        return view('evaluasiujian', [
            "title" => "Evaluasi Ujian",
            "post" => $ujian,
            "namamodul" => $namamodul,
            "slug" => $slug
        ]);
    }

    public function updateRaguRaguSession(Request $request)
    {
        // Update the 'ragu' column in the 'evaluasi' table
            $evaluasi = evaluasi::where('user_id', $request->user_id)
                ->where('ujian_id', $request->ujian_id)
                ->where('soal_id', $request->soal_id)
                ->first();

        if ($evaluasi) {
            $evaluasi->ragu = $request->has('raguragu');
            $evaluasi->save();
        }

        return redirect('/masuk-ujian'.'/'.$request->slug.'#soal-'.$request->page);
    }

    // menampilkan menu evaluasi persoal
    public function soalEvaluasi(Request $request)
    {
        $id_ujian = $request->id_ujian;
        $ujian = Ujian::find($id_ujian);
        
        $id = $ujian->grup_soal_id;
        $grup = Grup_soal::where('id', $id)->get();
        $id_grup = $grup[0]['id'];
        $slug = $request->slug;
        $soal = Soal::latest()->where('grup_soal_id',$id_grup)->paginate(300);
        return view('evaluasi', [
            "title" => "Evaluasi",
            "soal" => $soal,
            "ujian" => $ujian,
            "id_ujian" => $id_ujian,
            "slug" => $slug
        ]);
    }

    // menampilkan menu evaluasi persoal
    public function evaluasibutirsoal(Request $request)
    {
        $nosoal = $request->nosoal;
        $id_ujian = $request->ujian_id;
        $id_soal = $request->soal_id;
        $slug = $request->slug;

        // Mengambil data kelompok mahasiswa yang baru ditambahkan
        $kelompokMahasiswa = Kelompok_mahasiswa::where('ujian_id', $id_ujian)
            ->orderBy('id', 'asc')
            ->get();

        // Mengecek apakah data sudah ada di tabel kelompok_mahasiswas
        $existingData = Kelompok_mahasiswa::where('ujian_id', $id_ujian)->exists();

        if (!$existingData) {
            // Mengambil data kelompok mahasiswa yang sudah ada
            $kelompokMahasiswa = Kelompok_mahasiswa::where('ujian_id', $id_ujian)->get();
            $analisisNilai = Analisis_nilai::where('ujian_id', $id_ujian)->where('soal_id', $id_soal)->get();
            $analisisklasifikasi = Analisis_klasifikasi::with('basis_pengetahuan')->where('ujian_id', $id_ujian)->where('soal_id', $id_soal)->get();
            $kesimpulan = Kesimpulan_analisis::with('basis_pengetahuan')->where('ujian_id', $id_ujian)->where('soal_id', $id_soal)->get();

            return view('evaluasibutirsoal', [
                "title" => "Evaluasi Butir Soal",
                "id_ujian" => $id_ujian,
                "analisisnilai" => $analisisNilai,
                "kesimpulan" => $kesimpulan,
                "analisisklasifikasi" => $analisisklasifikasi,
                "kelompok_mahasiswa" => $kelompokMahasiswa,
                "slug" => $slug,
                "soal_id" => $id_soal,
                "nosoal" => $nosoal
            ]);
            
        }
        else{

            // Mengambil data evaluasi yang sesuai dengan kriteria
            $evaluasi = Evaluasi::where('ujian_id', $id_ujian)
            ->where('soal_id', $id_soal)
            ->whereIn('user_id', $kelompokMahasiswa->pluck('user_id'))
            ->orderByRaw("FIELD(user_id, ".implode(",", $kelompokMahasiswa->pluck('user_id')->toArray()).")")
            ->get(['user_id', 'skor']);

            // Menyusun skor dari evaluasi berdasarkan urutan kelompok mahasiswa
            $skorEvaluasi = $kelompokMahasiswa->map(function($kel) use ($evaluasi) {
                return $evaluasi->firstWhere('user_id', $kel->user_id)->skor ?? null;
            });

            // dd($request);

            // Menghitung Ka dan Kb
            $Ka = $kelompokMahasiswa->where('kelompok', 'Atas')->filter(function($kel) use ($evaluasi) {
                return $evaluasi->firstWhere('user_id', $kel->user_id)->skor != 0;
            })->count();

            $Kb = $kelompokMahasiswa->where('kelompok', 'Bawah')->filter(function($kel) use ($evaluasi) {
                return $evaluasi->firstWhere('user_id', $kel->user_id)->skor != 0;
            })->count();
            
            // Menghitung N
            $N = $kelompokMahasiswa->count();

            // Menghitung JB
            $JB = $evaluasi->filter(function($eval) {
                return $eval->skor != 0;
            })->count();

            // Menghitung N
            $N = $kelompokMahasiswa->count();

            // Mengambil data soal
            $soal = Soal::find($id_soal);
            // Mengambil user_id dari kelompok mahasiswa atas dan bawah
            $userIdsAtas = $kelompokMahasiswa->where('kelompok', 'Atas')->pluck('user_id');
            $userIdsBawah = $kelompokMahasiswa->where('kelompok', 'Bawah')->pluck('user_id');

            // Menghitung jumlah jawaban berdasarkan opsi dan kelompok
            $dp_a_atas = Evaluasi::where('ujian_id', $id_ujian)
            ->where('soal_id', $id_soal)
            ->where('jawaban', $soal->opsi_a)
            ->whereIn('user_id', $userIdsAtas)
            ->count();

            $dp_a_bawah = Evaluasi::where('ujian_id', $id_ujian)
            ->where('soal_id', $id_soal)
            ->where('jawaban', $soal->opsi_a)
            ->whereIn('user_id', $userIdsBawah)
            ->count();

            $dp_b_atas = Evaluasi::where('ujian_id', $id_ujian)
                ->where('soal_id', $id_soal)
                ->where('jawaban', $soal->opsi_b)
                ->whereIn('user_id', $userIdsAtas)
                ->count();

            $dp_b_bawah = Evaluasi::where('ujian_id', $id_ujian)
                ->where('soal_id', $id_soal)
                ->where('jawaban', $soal->opsi_b)
                ->whereIn('user_id', $userIdsBawah)
                ->count();

            $dp_c_atas = Evaluasi::where('ujian_id', $id_ujian)
                ->where('soal_id', $id_soal)
                ->where('jawaban', $soal->opsi_c)
                ->whereIn('user_id', $userIdsAtas)
                ->count();

            $dp_c_bawah = Evaluasi::where('ujian_id', $id_ujian)
                ->where('soal_id', $id_soal)
                ->where('jawaban', $soal->opsi_c)
                ->whereIn('user_id', $userIdsBawah)
                ->count();

            $dp_d_atas = Evaluasi::where('ujian_id', $id_ujian)
                ->where('soal_id', $id_soal)
                ->where('jawaban', $soal->opsi_d)
                ->whereIn('user_id', $userIdsAtas)
                ->count();

            $dp_d_bawah = Evaluasi::where('ujian_id', $id_ujian)
                ->where('soal_id', $id_soal)
                ->where('jawaban', $soal->opsi_d)
                ->whereIn('user_id', $userIdsBawah)
                ->count();

            $dp_e_atas = Evaluasi::where('ujian_id', $id_ujian)
                ->where('soal_id', $id_soal)
                ->where('jawaban', $soal->opsi_e)
                ->whereIn('user_id', $userIdsAtas)
                ->count();

            $dp_e_bawah = Evaluasi::where('ujian_id', $id_ujian)
                ->where('soal_id', $id_soal)
                ->where('jawaban', $soal->opsi_e)
                ->whereIn('user_id', $userIdsBawah)
                ->count();

            $userIds = $userIdsAtas->merge($userIdsBawah);

            $ppj_a = Evaluasi::where('ujian_id',$id_ujian)->where('soal_id',$id_soal)->where('jawaban',$soal->opsi_a)->whereIn('user_id', $userIds)->count();
            $ppj_b = Evaluasi::where('ujian_id',$id_ujian)->where('soal_id',$id_soal)->where('jawaban',$soal->opsi_b)->whereIn('user_id', $userIds)->count();
            $ppj_c = Evaluasi::where('ujian_id',$id_ujian)->where('soal_id',$id_soal)->where('jawaban',$soal->opsi_c)->whereIn('user_id', $userIds)->count();
            $ppj_d = Evaluasi::where('ujian_id',$id_ujian)->where('soal_id',$id_soal)->where('jawaban',$soal->opsi_d)->whereIn('user_id', $userIds)->count();
            $ppj_e = Evaluasi::where('ujian_id',$id_ujian)->where('soal_id',$id_soal)->where('jawaban',$soal->opsi_e)->whereIn('user_id', $userIds)->count();
        
            $analisisNilai = Analisis_nilai::where('ujian_id', $id_ujian)->where('soal_id', $id_soal)->get();
            $analisisklasifikasi = Analisis_klasifikasi::with('basis_pengetahuan')->where('ujian_id', $id_ujian)->where('soal_id', $id_soal)->get();
            $kesimpulan = Kesimpulan_analisis::with('basis_pengetahuan')->where('ujian_id', $id_ujian)->where('soal_id', $id_soal)->get();

            return view('evaluasibutirsoal', [
                "title" => "Evaluasi Butir Soal",
                "skor_evaluasi" => $skorEvaluasi,
                "analisisnilai" => $analisisNilai,
                "kesimpulan" => $kesimpulan,
                "analisisklasifikasi" => $analisisklasifikasi,
                "id_ujian" => $id_ujian,
                "kelompok_mahasiswa" => $kelompokMahasiswa,
                "slug" => $slug,
                "soal_id" => $id_soal,
                "nosoal" => $nosoal,
                "Ka" => $Ka,
                "Kb" => $Kb,
                "JB" => $JB,
                "N" => $N,
                "dp_a_atas" => $dp_a_atas,
                "dp_a_bawah" => $dp_a_bawah,
                "dp_b_atas" => $dp_b_atas,
                "dp_b_bawah" => $dp_b_bawah,
                "dp_c_atas" => $dp_c_atas,
                "dp_c_bawah" => $dp_c_bawah,
                "dp_d_atas" => $dp_d_atas,
                "dp_d_bawah" => $dp_d_bawah,
                "dp_e_atas" => $dp_e_atas,
                "dp_e_bawah" => $dp_e_bawah,
                "ppj_a" => $ppj_a,
                "ppj_b" => $ppj_b,
                "ppj_c" => $ppj_c,
                "ppj_d" => $ppj_d,
                "ppj_e" => $ppj_e
            ]);
        }
    }


    // menampilkan menu semua evaluasi
    public function Evaluasisemua(Request $request)
    {
        $id_ujian = $request->ujian_id;
        $slug = $request->slug;
        $ujian = Ujian::find($id_ujian);
        
        $id = $ujian->grup_soal_id;
        $grup = Grup_soal::where('id', $id)->get();
        $id_grup = $grup[0]['id'];
        $slug = $request->slug;
        $soal = Soal::latest()->where('grup_soal_id',$id_grup)->paginate(300);
        $klasifikasi = Kesimpulan_analisis::with('basis_pengetahuan')->where('ujian_id', $id_ujian)->get();
        $analisisList = Analisis_klasifikasi::where('ujian_id', $id_ujian)->get();
        
        $klasifikasiMap = $klasifikasi->keyBy('soal_id');
        // Mengelompokkan analisis berdasarkan soal_id
        $analisisMap = [];
        foreach ($analisisList as $analisis) {
            $analisisMap[$analisis->soal_id][] = $analisis->basis_pengetahuan_id;
        }


        $tk = Analisis_klasifikasi::with('basis_pengetahuan')
        ->where('ujian_id', $id_ujian)
        ->whereIn('basis_pengetahuan_id', [8, 9, 10])
        ->get();
        
        $tkMap = $tk->keyBy('soal_id');

        // Menghitung jumlah masing-masing basis_pengetahuan_id
        $count8 = $tk->where('basis_pengetahuan_id', 8)->count();
        $count9 = $tk->where('basis_pengetahuan_id', 9)->count();
        $count10 = $tk->where('basis_pengetahuan_id', 10)->count();
        
        // Menghitung total jumlah basis_pengetahuan_id yang dicari
        $total = $count8 + $count9 + $count10;
        
        // Menghitung persentase masing-masing basis_pengetahuan_id
        $percentage8 = $total > 0 ? number_format(($count8 / $total) * 100, 1) : 0;
        $percentage9 = $total > 0 ? number_format(($count9 / $total) * 100, 1) : 0;
        $percentage10 = $total > 0 ? number_format(($count10 / $total) * 100, 1) : 0;
        
        return view('semuaevaluasi', [
            "title" => "Evaluasi Semua Soal",
            "soal" => $soal,
            "slug" => $slug,
            "tk1" => $percentage8,
            "tk2" => $percentage9,
            "tk3" => $percentage10,
            "tk" => $tkMap,
            "id_ujian" => $id_ujian,
            "klasifikasiMap" => $klasifikasiMap,
            "analisisMap" => $analisisMap,
        ]);
    }

    //cetak
    public function cetak(Request $request)
    {
        $id_ujian = $request->ujian_id;
        $slug = $request->slug;
        $ujian = Ujian::find($id_ujian);
        
        $id = $ujian->grup_soal_id;
        $grup = Grup_soal::where('id', $id)->get();
        $id_grup = $grup[0]['id'];
        $slug = $request->slug;
        $soal = Soal::latest()->where('grup_soal_id',$id_grup)->paginate(300);
        $klasifikasi = Kesimpulan_analisis::with('basis_pengetahuan')->where('ujian_id', $id_ujian)->get();
        $analisisList = Analisis_klasifikasi::where('ujian_id', $id_ujian)->get();
        
        $klasifikasiMap = $klasifikasi->keyBy('soal_id');
        // Mengelompokkan analisis berdasarkan soal_id
        $analisisMap = [];
        foreach ($analisisList as $analisis) {
            $analisisMap[$analisis->soal_id][] = $analisis->basis_pengetahuan_id;
        }


        $tk = Analisis_klasifikasi::with('basis_pengetahuan')
        ->where('ujian_id', $id_ujian)
        ->whereIn('basis_pengetahuan_id', [8, 9, 10])
        ->get();
        
        $tkMap = $tk->keyBy('soal_id');

        // Menghitung jumlah masing-masing basis_pengetahuan_id
        $count8 = $tk->where('basis_pengetahuan_id', 8)->count();
        $count9 = $tk->where('basis_pengetahuan_id', 9)->count();
        $count10 = $tk->where('basis_pengetahuan_id', 10)->count();
        
        // Menghitung total jumlah basis_pengetahuan_id yang dicari
        $total = $count8 + $count9 + $count10;
        
        $percentage8 = $total > 0 ? number_format(($count8 / $total) * 100, 1) : 0;
        $percentage9 = $total > 0 ? number_format(($count9 / $total) * 100, 1) : 0;
        $percentage10 = $total > 0 ? number_format(($count10 / $total) * 100, 1) : 0;
        
        return view('cetaksemua', [
            "title" => "Evaluasi Semua Soal",
            "ujian" => $ujian,
            "soal" => $soal,
            "slug" => $slug,
            "tk1" => $percentage8,
            "tk2" => $percentage9,
            "tk3" => $percentage10,
            "tk" => $tkMap,
            "id_ujian" => $id_ujian,
            "klasifikasiMap" => $klasifikasiMap,
            "analisisMap" => $analisisMap,
        ]);
    }

    // menambahkan data evaluasi ketika mahasiswa menambahkan jawaban ujiannya
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'soal_id' => 'required',
            'ujian_id' => 'required',
            'user_id' => 'required',
            'jawaban' => 'required'
        ]);
        $soal = Soal::find($request->soal_id);
        if($request->jawaban == $soal->jawaban){
            $validatedData['skor'] = $request->skor;
        }else{
            $validatedData['skor'] = 0;
        }
        if(session('ujian_selesai')){
            return back()->with('success', 'Jawaban Gagal Diubah!');
        }
        evaluasi::create($validatedData);
        if($request->page == $request->pt){
            return redirect('/masuk-ujian'.'/'.$request->slug.'#soal-'.$request->pt);
        }else{
            return redirect('/masuk-ujian'.'/'.$request->slug.'#soal-'.$request->page);
        }
    }

    public function next(Request $request)
    {   
        $pageNext = $request->page - 1;
        if($request->page == $request->pt){
            return redirect('/masuk-ujian'.'/'.$request->slug.'#soal-'.$request->pt);
        }elseif($pageNext == 0){
            return redirect('/masuk-ujian'.'/'.$request->slug.'#soal-1');
        }else{
            return redirect('/masuk-ujian'.'/'.$request->slug.'#soal-'.$pageNext);
        }
    }
    public function sblm(Request $request)
    {   
        $pageNext = $request->page + 1;
        if($request->page == $request->pt){
            return redirect('/masuk-ujian'.'/'.$request->slug.'#soal-'.$request->pt);
        }else{
            return redirect('/masuk-ujian'.'/'.$request->slug.'#soal-'.$pageNext);
        }
    }

    // menampilkan data evaluasi detai berserta grafik jawaban persoal
    public function show(Request $request)
    {
        $nosoal = $request->nosoal;
        $id_ujian = $request->ujian_id;
        $id_soal = $request->soal_id;
        $slug = $request->slug;
        // Mengambil data evaluasi yang sesuai dengan kriteria
        $kelompokMahasiswa = Kelompok_mahasiswa::where('ujian_id', $id_ujian)
            ->orderBy('id', 'asc')
            ->get();
        // Mengambil user_id dari kelompok mahasiswa atas dan bawah
        $userIdsAtas = $kelompokMahasiswa->where('kelompok', 'Atas')->pluck('user_id');
        $userIdsBawah = $kelompokMahasiswa->where('kelompok', 'Bawah')->pluck('user_id');
        $userIds = $userIdsAtas->merge($userIdsBawah);

        $soal = Soal::find($id_soal);
        $eval = Evaluasi::with('user')->where('ujian_id',$id_ujian)->whereIn('user_id', $userIds)->where('soal_id',$id_soal)->get();
        
        $opsi_a = Evaluasi::where('ujian_id',$id_ujian)->where('soal_id',$id_soal)->where('jawaban',$soal->opsi_a)->whereIn('user_id', $userIds)->count();
        $opsi_b = Evaluasi::where('ujian_id',$id_ujian)->where('soal_id',$id_soal)->where('jawaban',$soal->opsi_b)->whereIn('user_id', $userIds)->count();
        $opsi_c = Evaluasi::where('ujian_id',$id_ujian)->where('soal_id',$id_soal)->where('jawaban',$soal->opsi_c)->whereIn('user_id', $userIds)->count();
        $opsi_d = Evaluasi::where('ujian_id',$id_ujian)->where('soal_id',$id_soal)->where('jawaban',$soal->opsi_d)->whereIn('user_id', $userIds)->count();
        $opsi_e = Evaluasi::where('ujian_id',$id_ujian)->where('soal_id',$id_soal)->where('jawaban',$soal->opsi_e)->whereIn('user_id', $userIds)->count();

        $kesimpulan = Kesimpulan_analisis::with('basis_pengetahuan')->where('ujian_id', $id_ujian)->where('soal_id', $id_soal)->get();
      

        return view('evaluasiSoal', [
            "title" => "Evaluasi Ujian",
            "soal" => $eval,
            "kesimpulan" => $kesimpulan,
            "opsia" => $opsi_a,
            "opsib" => $opsi_b,
            "opsic" => $opsi_c,
            "opsid" => $opsi_d,
            "opsie" => $opsi_e,
            "id_ujian" => $id_ujian,
            "slug" => $slug,
            "soal_id" => $id_soal,
            "nosoal" => $nosoal,
            "datasoal" => $soal
        ]);
    }

    // menampilkan data evaluasi detai berserta grafik jawaban persoal
    public function hitung(Request $request)
    {
        $nosoal = $request->nosoal;
        $id_ujian = $request->ujian_id;
        $id_soal = $request->soal_id;
        $slug = $request->slug;

        // Mengecek apakah data sudah ada di tabel kelompok_mahasiswas
        $existingData = Kelompok_mahasiswa::where('ujian_id', $id_ujian)->exists();

        if ($existingData) {
            // Mengambil data kelompok mahasiswa yang sudah ada
            $kelompokMahasiswa = Kelompok_mahasiswa::where('ujian_id', $id_ujian)->get();

            // Kembali ke halaman sebelumnya dengan data kelompok mahasiswa dan request data
            return view('redirectPost', [
                "kelompok_mahasiswa" => $kelompokMahasiswa,
                "nosoal" => $nosoal,
                "id_ujian" => $id_ujian,
                "id_soal" => $id_soal,
                "slug" => $slug
            ]);
        }
        else{

            // Mendapatkan jumlah hasil ujian
            $jmlhHasilUjian = HasilUjian::where('ujian_id', $id_ujian)->count();
            $nilaitengah = ($jmlhHasilUjian / 2);

            // Mendapatkan hasil ujian yang diurutkan berdasarkan nilai
            $hasilUjian = HasilUjian::where('ujian_id', $id_ujian)
                ->orderByRaw('CAST(nilai AS UNSIGNED) DESC')
                ->orderBy('created_at', 'asc')
                ->get();

            // Menentukan kelompok mahasiswa berdasarkan nilai tengah
            foreach ($hasilUjian as $index => $hasil) {
                $kelompok = ($index <= $nilaitengah) ? 'Atas' : 'Bawah';

                // Menambahkan data ke tabel kelompok mahasiswa menggunakan metode create
                Kelompok_mahasiswa::create([
                    'user_id' => $hasil->user_id,
                    'ujian_id' => $hasil->ujian_id,
                    'kelompok' => $kelompok,
                    'nilai' => $hasil->nilai,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Mengambil data kelompok mahasiswa yang baru ditambahkan
            $kelompokMahasiswa = Kelompok_mahasiswa::where('ujian_id', $id_ujian)
                ->orderByRaw('CAST(nilai AS UNSIGNED) DESC')
                ->paginate(300);

            return view('redirectPost',[
                "kelompok_mahasiswa" => $kelompokMahasiswa,
                "nosoal" => $nosoal,
                "id_ujian" => $id_ujian,
                "id_soal" => $id_soal,
                "slug" => $slug
            ]);
        }
    }
    
    public function hitungsemua(Request $request)
    {
        $id_ujian = $request->ujian_id;
        $slug = $request->slug;

        // Mengecek apakah data sudah ada di tabel kelompok_mahasiswas
        $existingData = Kelompok_mahasiswa::where('ujian_id', $id_ujian)->exists();

        if (!$existingData) {
            // Mendapatkan jumlah hasil ujian
            $jmlhHasilUjian = HasilUjian::where('ujian_id', $id_ujian)->count();
            $nilaitengah = ($jmlhHasilUjian / 2);

            // Mendapatkan hasil ujian yang diurutkan berdasarkan nilai
            $hasilUjian = HasilUjian::where('ujian_id', $id_ujian)
                ->orderByRaw('CAST(nilai AS UNSIGNED) DESC')
                ->orderBy('created_at', 'asc')
                ->get();

            // Menentukan kelompok mahasiswa berdasarkan nilai tengah
            foreach ($hasilUjian as $index => $hasil) {
                $kelompok = ($index < $nilaitengah) ? 'Atas' : 'Bawah';

                // Menambahkan data ke tabel kelompok mahasiswa menggunakan metode create
                Kelompok_mahasiswa::create([
                    'user_id' => $hasil->user_id,
                    'ujian_id' => $hasil->ujian_id,
                    'kelompok' => $kelompok,
                    'nilai' => $hasil->nilai,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
        
        // Mendapatkan grup soal yang terkait dengan ujian
        $ujian = Ujian::find($id_ujian);
        $id_grup = $ujian->grup_soal_id;

        // Mengambil semua soal yang terkait dengan grup soal
        $soalList = Soal::where('grup_soal_id', $id_grup)->get();

        // Mengambil data evaluasi yang sesuai dengan kriteria
        $kelompokMahasiswa = Kelompok_mahasiswa::where('ujian_id', $id_ujian)
            ->orderBy('id', 'asc')
            ->get();

        // Loop untuk setiap soal
        foreach ($soalList as $soal) {
            $id_soal = $soal->id;

            // Mengambil data evaluasi yang sesuai dengan kriteria
            $evaluasi = Evaluasi::where('ujian_id', $id_ujian)
            ->where('soal_id', $id_soal)
            ->whereIn('user_id', $kelompokMahasiswa->pluck('user_id'))
            ->orderByRaw("FIELD(user_id, ".implode(",", $kelompokMahasiswa->pluck('user_id')->toArray()).")")
            ->get(['user_id', 'skor']);

            // Mengambil data soal
            $soal = Soal::find($id_soal);
            // Mengambil user_id dari kelompok mahasiswa atas dan bawah
            $userIdsAtas = $kelompokMahasiswa->where('kelompok', 'Atas')->pluck('user_id');
            $userIdsBawah = $kelompokMahasiswa->where('kelompok', 'Bawah')->pluck('user_id');
            $userIds = $userIdsAtas->merge($userIdsBawah);

            // Diketahui N
            $N = $kelompokMahasiswa->count();
            // Diketahui DP SOAL KA DAN KB
            $dp_ka_soal = $kelompokMahasiswa->where('kelompok', 'Atas')->filter(function($kel) use ($evaluasi) {
                        return $evaluasi->firstWhere('user_id', $kel->user_id)->skor != 0;
                        })->count();
            $dp_kb_soal = $kelompokMahasiswa->where('kelompok', 'Bawah')->filter(function($kel) use ($evaluasi) {
                        return $evaluasi->firstWhere('user_id', $kel->user_id)->skor != 0;
                        })->count();
            //Diketahui Dp Opsi A
            $dp_a_atas = Evaluasi::where('ujian_id', $id_ujian)->where('soal_id', $id_soal)->where('jawaban', $soal->opsi_a)
                        ->whereIn('user_id', $userIdsAtas)->count();
            $dp_a_bawah = Evaluasi::where('ujian_id', $id_ujian)->where('soal_id', $id_soal)->where('jawaban', $soal->opsi_a)
                        ->whereIn('user_id', $userIdsBawah)->count();
            //Diketahui Dp Opsi B
            $dp_b_atas = Evaluasi::where('ujian_id', $id_ujian)->where('soal_id', $id_soal)->where('jawaban', $soal->opsi_b)
                        ->whereIn('user_id', $userIdsAtas)->count();
            $dp_b_bawah = Evaluasi::where('ujian_id', $id_ujian)->where('soal_id', $id_soal)->where('jawaban', $soal->opsi_b)
                        ->whereIn('user_id', $userIdsBawah)->count();
            //Diketahui DP Opsi C
            $dp_c_atas = Evaluasi::where('ujian_id', $id_ujian)->where('soal_id', $id_soal)->where('jawaban', $soal->opsi_c)
                        ->whereIn('user_id', $userIdsAtas)->count();
            $dp_c_bawah = Evaluasi::where('ujian_id', $id_ujian)->where('soal_id', $id_soal)->where('jawaban', $soal->opsi_c)
                        ->whereIn('user_id', $userIdsBawah)->count();
            //Diketahui DP Opsi D
            $dp_d_atas = Evaluasi::where('ujian_id', $id_ujian)->where('soal_id', $id_soal)->where('jawaban', $soal->opsi_d)
                        ->whereIn('user_id', $userIdsAtas)->count();
            $dp_d_bawah = Evaluasi::where('ujian_id', $id_ujian)->where('soal_id', $id_soal)->where('jawaban', $soal->opsi_d)
                        ->whereIn('user_id', $userIdsBawah)->count();
            //Diketahui DP Opsi E
            $dp_e_atas = Evaluasi::where('ujian_id', $id_ujian)->where('soal_id', $id_soal)->where('jawaban', $soal->opsi_e)
                        ->whereIn('user_id', $userIdsAtas)->count();
            $dp_e_bawah = Evaluasi::where('ujian_id', $id_ujian)->where('soal_id', $id_soal)->where('jawaban', $soal->opsi_e)
                        ->whereIn('user_id', $userIdsBawah)->count();

            $DpSoal = 2*($dp_ka_soal - $dp_kb_soal) / $N; //Rumus Daya Pembeda Soal
            $Dp_opsia = 2*($dp_a_atas - $dp_a_bawah) / $N; //Rumus Daya Pembeda Pengecoh A
            $Dp_opsib = 2*($dp_b_atas - $dp_b_bawah) / $N; //Rumus Daya Pembeda Pengecoh B
            $Dp_opsic = 2*($dp_c_atas - $dp_c_bawah) / $N; //Rumus Daya Pembeda Pengecoh C
            $Dp_opsid = 2*($dp_d_atas - $dp_d_bawah) / $N; //Rumus Daya Pembeda Pengecoh D
            $Dp_opsie = 2*($dp_e_atas - $dp_e_bawah) / $N; //Rumus Daya Pembeda Pengecoh E

            // Menghitung JB
            $JB = $evaluasi->filter(function($eval) {
                return $eval->skor != 0;
            })->count();
            $Tk = $JB / $N ; //Rumus Tingkat Kesukaran Soal

            //DIketahui PPJ
            $ppj_a = Evaluasi::where('ujian_id',$id_ujian)->where('soal_id',$id_soal)->where('jawaban',$soal->opsi_a)->whereIn('user_id', $userIds)->count();
            $ppj_b = Evaluasi::where('ujian_id',$id_ujian)->where('soal_id',$id_soal)->where('jawaban',$soal->opsi_b)->whereIn('user_id', $userIds)->count();
            $ppj_c = Evaluasi::where('ujian_id',$id_ujian)->where('soal_id',$id_soal)->where('jawaban',$soal->opsi_c)->whereIn('user_id', $userIds)->count();
            $ppj_d = Evaluasi::where('ujian_id',$id_ujian)->where('soal_id',$id_soal)->where('jawaban',$soal->opsi_d)->whereIn('user_id', $userIds)->count();
            $ppj_e = Evaluasi::where('ujian_id',$id_ujian)->where('soal_id',$id_soal)->where('jawaban',$soal->opsi_e)->whereIn('user_id', $userIds)->count();
            
            $ppja = $ppj_a / $N; //Rumus Penyebaran Pilihan Jawaban Pengecoh A
            $ppjb = $ppj_b / $N; //Rumus Penyebaran Pilihan Jawaban Pengecoh B
            $ppjc = $ppj_c / $N; //Rumus Penyebaran Pilihan Jawaban Pengecoh C
            $ppjd = $ppj_d / $N; //Rumus Penyebaran Pilihan Jawaban Pengecoh D
            $ppje = $ppj_e / $N; //Rumus Penyebaran Pilihan Jawaban Pengecoh E
            
            // Mengecek apakah data sudah ada di tabel kelompok_mahasiswas
            $existingData3 = Analisis_nilai::where('ujian_id', $id_ujian)->where('soal_id', $id_soal)->exists();

            if (!$existingData3) {
                Analisis_nilai::create([
                    // Menambahkan data ke tabel kelompok Analisis nilai menggunakan metode create
                    'ujian_id' => $id_ujian,
                    'soal_id' => $id_soal,
                    'dp_soal' => $DpSoal,
                    'dp_opsia' => $Dp_opsia,
                    'dp_opsib' => $Dp_opsib,
                    'dp_opsic' => $Dp_opsic,
                    'dp_opsid' => $Dp_opsid,
                    'dp_opsie' => $Dp_opsie,
                    'tk_soal' => $Tk,
                    'ppj_opsia' => $ppja,
                    'ppj_opsib' => $ppjb,
                    'ppj_opsic' => $ppjc,
                    'ppj_opsid' => $ppjd,
                    'ppj_opsie' => $ppje,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Mengecek apakah data sudah ada di tabel kelompok_mahasiswas
            $existingData4 = Kesimpulan_analisis::where('ujian_id', $id_ujian)->where('soal_id', $id_soal)->exists();

            if (!$existingData4) {
                if($DpSoal <= 0){
                    Analisis_klasifikasi::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 2,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }elseif($soal->jawaban == $soal->opsi_a && $Dp_opsib < 0 && $Dp_opsic < 0 && $Dp_opsid < 0 && $Dp_opsie < 0){
                    Analisis_klasifikasi::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 1,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }elseif($soal->jawaban == $soal->opsi_b && $Dp_opsia < 0 && $Dp_opsic < 0 && $Dp_opsid < 0 && $Dp_opsie < 0){
                    Analisis_klasifikasi::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 1,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }elseif($soal->jawaban == $soal->opsi_c && $Dp_opsib < 0 && $Dp_opsia < 0 && $Dp_opsid < 0 && $Dp_opsie < 0){
                    Analisis_klasifikasi::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 1,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }elseif($soal->jawaban == $soal->opsi_d && $Dp_opsib < 0 && $Dp_opsic < 0 && $Dp_opsia < 0 && $Dp_opsie < 0){
                    Analisis_klasifikasi::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 1,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }elseif(($soal->jawaban == $soal->opsi_e && $Dp_opsib < 0 && $Dp_opsic < 0 && $Dp_opsid < 0 && $Dp_opsia < 0)){
                    Analisis_klasifikasi::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 1,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                
                if($Dp_opsia >= 0 && $soal->jawaban !== $soal->opsi_a){
                    Analisis_klasifikasi::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 3,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                if($Dp_opsib >= 0 && $soal->jawaban !== $soal->opsi_b){
                    Analisis_klasifikasi::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 4,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                if($Dp_opsic >= 0 && $soal->jawaban !== $soal->opsi_c){
                    Analisis_klasifikasi::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 5,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                if($Dp_opsid >= 0 && $soal->jawaban !== $soal->opsi_d){
                    Analisis_klasifikasi::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 6,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                if($Dp_opsie >= 0 && $soal->jawaban !== $soal->opsi_e){
                    Analisis_klasifikasi::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 7,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                
                if($Tk < 0.3) {
                    Analisis_klasifikasi::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 8,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }elseif($Tk > 0.7) {
                    Analisis_klasifikasi::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 10,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }else{
                    Analisis_klasifikasi::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 9,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                if($ppja >= 0.025 && $ppjb >= 0.025 && $ppjc >= 0.025 && $ppjd >= 0.025 & $ppje >= 0.025){
                    Analisis_klasifikasi::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 11,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                if($ppja < 0.025){
                    Analisis_klasifikasi::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 12,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                if($ppjb < 0.025){
                    Analisis_klasifikasi::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 13,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                if($ppjc < 0.025){
                    Analisis_klasifikasi::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 14,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                if($ppjd < 0.025){
                    Analisis_klasifikasi::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 15,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                if($ppje < 0.025){
                    Analisis_klasifikasi::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 16,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                $arrayklasifikasi = [];
                $analisisklasifikasi = Analisis_klasifikasi::where('ujian_id', $id_ujian)->where('soal_id', $id_soal)->get();

                // Mengisi array dengan basis_pengetahuan_id
                foreach ($analisisklasifikasi as $analisis) {
                    $arrayklasifikasi[] = $analisis->basis_pengetahuan_id;
                }

                $kesimpulan = Kesimpulan_analisis::where('ujian_id', $id_ujian)->where('soal_id', $id_soal)->exists();

                // Memeriksa apakah ada basis_pengetahuan_id yang sama dengan 2
                if (in_array(2, $arrayklasifikasi) && !$kesimpulan) {
                    Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 17,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } 
                elseif (empty(array_diff([1, 11, 8], $arrayklasifikasi)) && !$kesimpulan) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 18,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                } 
                elseif (empty(array_diff([1, 11, 9], $arrayklasifikasi)) && !$kesimpulan) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 19,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                } 
                elseif (empty(array_diff([1, 11, 10], $arrayklasifikasi)) && !$kesimpulan) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 20,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                } 
                elseif ((empty(array_diff([3, 4, 5, 11], $arrayklasifikasi)) ||
                    empty(array_diff([12, 13, 14, 1], $arrayklasifikasi)) ||
                    empty(array_diff([3, 4, 14], $arrayklasifikasi)) ||
                    empty(array_diff([4, 5, 12], $arrayklasifikasi)) ||
                    empty(array_diff([3, 5, 13], $arrayklasifikasi)) ||
                    empty(array_diff([12, 13, 5], $arrayklasifikasi)) ||
                    empty(array_diff([13, 14, 3], $arrayklasifikasi)) ||
                    empty(array_diff([12, 14, 4], $arrayklasifikasi))) && !$kesimpulan 
                    && in_array(8, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 66,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                } 
                elseif ((empty(array_diff([3, 4, 5, 11], $arrayklasifikasi)) ||
                    empty(array_diff([12, 13, 14, 1], $arrayklasifikasi)) ||
                    empty(array_diff([3, 4, 14], $arrayklasifikasi)) ||
                    empty(array_diff([4, 5, 12], $arrayklasifikasi)) ||
                    empty(array_diff([3, 5, 13], $arrayklasifikasi)) ||
                    empty(array_diff([12, 13, 5], $arrayklasifikasi)) ||
                    empty(array_diff([13, 14, 3], $arrayklasifikasi)) ||
                    empty(array_diff([12, 14, 4], $arrayklasifikasi))) && !$kesimpulan 
                    && in_array(9, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 67,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                } 
                elseif ((empty(array_diff([3, 4, 5, 11], $arrayklasifikasi)) ||
                    empty(array_diff([12, 13, 14, 1], $arrayklasifikasi)) ||
                    empty(array_diff([3, 4, 14], $arrayklasifikasi)) ||
                    empty(array_diff([4, 5, 12], $arrayklasifikasi)) ||
                    empty(array_diff([3, 5, 13], $arrayklasifikasi)) ||
                    empty(array_diff([12, 13, 5], $arrayklasifikasi)) ||
                    empty(array_diff([13, 14, 3], $arrayklasifikasi)) ||
                    empty(array_diff([12, 14, 4], $arrayklasifikasi))) && !$kesimpulan 
                    && in_array(10, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 68,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                } 
                elseif ((empty(array_diff([3, 4, 6, 11], $arrayklasifikasi)) ||
                    empty(array_diff([12, 13, 15, 1], $arrayklasifikasi)) ||
                    empty(array_diff([3, 4, 15], $arrayklasifikasi)) ||
                    empty(array_diff([4, 6, 12], $arrayklasifikasi)) ||
                    empty(array_diff([3, 6, 13], $arrayklasifikasi)) ||
                    empty(array_diff([12, 13, 6], $arrayklasifikasi)) ||
                    empty(array_diff([13, 15, 3], $arrayklasifikasi)) ||
                    empty(array_diff([12, 15, 4], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(8, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 69,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([3, 4, 6, 11], $arrayklasifikasi)) ||
                    empty(array_diff([12, 13, 15, 1], $arrayklasifikasi)) ||
                    empty(array_diff([3, 4, 15], $arrayklasifikasi)) ||
                    empty(array_diff([4, 6, 12], $arrayklasifikasi)) ||
                    empty(array_diff([3, 6, 13], $arrayklasifikasi)) ||
                    empty(array_diff([12, 13, 6], $arrayklasifikasi)) ||
                    empty(array_diff([13, 15, 3], $arrayklasifikasi)) ||
                    empty(array_diff([12, 15, 4], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(9, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 70,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([3, 4, 6, 11], $arrayklasifikasi)) ||
                    empty(array_diff([12, 13, 15, 1], $arrayklasifikasi)) ||
                    empty(array_diff([3, 4, 15], $arrayklasifikasi)) ||
                    empty(array_diff([4, 6, 12], $arrayklasifikasi)) ||
                    empty(array_diff([3, 6, 13], $arrayklasifikasi)) ||
                    empty(array_diff([12, 13, 6], $arrayklasifikasi)) ||
                    empty(array_diff([13, 15, 3], $arrayklasifikasi)) ||
                    empty(array_diff([12, 15, 4], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(10, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 71,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([3, 4, 7, 11], $arrayklasifikasi)) ||
                    empty(array_diff([12, 13, 16, 1], $arrayklasifikasi)) ||
                    empty(array_diff([3, 4, 16], $arrayklasifikasi)) ||
                    empty(array_diff([4, 7, 12], $arrayklasifikasi)) ||
                    empty(array_diff([3, 7, 13], $arrayklasifikasi)) ||
                    empty(array_diff([12, 13, 7], $arrayklasifikasi)) ||
                    empty(array_diff([13, 16, 3], $arrayklasifikasi)) ||
                    empty(array_diff([12, 16, 4], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(8, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 72,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([3, 4, 7, 11], $arrayklasifikasi)) ||
                    empty(array_diff([12, 13, 16, 1], $arrayklasifikasi)) ||
                    empty(array_diff([3, 4, 16], $arrayklasifikasi)) ||
                    empty(array_diff([4, 7, 12], $arrayklasifikasi)) ||
                    empty(array_diff([3, 7, 13], $arrayklasifikasi)) ||
                    empty(array_diff([12, 13, 7], $arrayklasifikasi)) ||
                    empty(array_diff([13, 16, 3], $arrayklasifikasi)) ||
                    empty(array_diff([12, 16, 4], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(9, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 73,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([3, 4, 7, 11], $arrayklasifikasi)) ||
                    empty(array_diff([12, 13, 16, 1], $arrayklasifikasi)) ||
                    empty(array_diff([3, 4, 16], $arrayklasifikasi)) ||
                    empty(array_diff([4, 7, 12], $arrayklasifikasi)) ||
                    empty(array_diff([3, 7, 13], $arrayklasifikasi)) ||
                    empty(array_diff([12, 13, 7], $arrayklasifikasi)) ||
                    empty(array_diff([13, 16, 3], $arrayklasifikasi)) ||
                    empty(array_diff([12, 16, 4], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(10, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 74,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([3, 5, 6, 11], $arrayklasifikasi)) ||
                    empty(array_diff([12, 14, 15, 1], $arrayklasifikasi)) ||
                    empty(array_diff([3, 5, 15], $arrayklasifikasi)) ||
                    empty(array_diff([5, 6, 12], $arrayklasifikasi)) ||
                    empty(array_diff([3, 6, 14], $arrayklasifikasi)) ||
                    empty(array_diff([12, 14, 6], $arrayklasifikasi)) ||
                    empty(array_diff([14, 15, 3], $arrayklasifikasi)) ||
                    empty(array_diff([12, 15, 5], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(8, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 75,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([3, 5, 6, 11], $arrayklasifikasi)) ||
                    empty(array_diff([12, 14, 15, 1], $arrayklasifikasi)) ||
                    empty(array_diff([3, 5, 15], $arrayklasifikasi)) ||
                    empty(array_diff([5, 6, 12], $arrayklasifikasi)) ||
                    empty(array_diff([3, 6, 14], $arrayklasifikasi)) ||
                    empty(array_diff([12, 14, 6], $arrayklasifikasi)) ||
                    empty(array_diff([14, 15, 3], $arrayklasifikasi)) ||
                    empty(array_diff([12, 15, 5], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(9, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 76,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([3, 5, 6, 11], $arrayklasifikasi)) ||
                    empty(array_diff([12, 14, 15, 1], $arrayklasifikasi)) ||
                    empty(array_diff([3, 5, 15], $arrayklasifikasi)) ||
                    empty(array_diff([5, 6, 12], $arrayklasifikasi)) ||
                    empty(array_diff([3, 6, 14], $arrayklasifikasi)) ||
                    empty(array_diff([12, 14, 6], $arrayklasifikasi)) ||
                    empty(array_diff([14, 15, 3], $arrayklasifikasi)) ||
                    empty(array_diff([12, 15, 5], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(10, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 77,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([3, 5, 7, 11], $arrayklasifikasi)) ||
                    empty(array_diff([12, 14, 16, 1], $arrayklasifikasi)) ||
                    empty(array_diff([3, 5, 16], $arrayklasifikasi)) ||
                    empty(array_diff([5, 7, 12], $arrayklasifikasi)) ||
                    empty(array_diff([3, 7, 14], $arrayklasifikasi)) ||
                    empty(array_diff([12, 14, 7], $arrayklasifikasi)) ||
                    empty(array_diff([14, 16, 3], $arrayklasifikasi)) ||
                    empty(array_diff([12, 16, 5], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(8, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 78,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([3, 5, 7, 11], $arrayklasifikasi)) ||
                    empty(array_diff([12, 14, 16, 1], $arrayklasifikasi)) ||
                    empty(array_diff([3, 5, 16], $arrayklasifikasi)) ||
                    empty(array_diff([5, 7, 12], $arrayklasifikasi)) ||
                    empty(array_diff([3, 7, 14], $arrayklasifikasi)) ||
                    empty(array_diff([12, 14, 7], $arrayklasifikasi)) ||
                    empty(array_diff([14, 16, 3], $arrayklasifikasi)) ||
                    empty(array_diff([12, 16, 5], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(9, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 79,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([3, 5, 7, 11], $arrayklasifikasi)) ||
                    empty(array_diff([12, 14, 16, 1], $arrayklasifikasi)) ||
                    empty(array_diff([3, 5, 16], $arrayklasifikasi)) ||
                    empty(array_diff([5, 7, 12], $arrayklasifikasi)) ||
                    empty(array_diff([3, 7, 14], $arrayklasifikasi)) ||
                    empty(array_diff([12, 14, 7], $arrayklasifikasi)) ||
                    empty(array_diff([14, 16, 3], $arrayklasifikasi)) ||
                    empty(array_diff([12, 16, 5], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(10, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 80,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([3, 6, 7, 11], $arrayklasifikasi)) ||
                    empty(array_diff([12, 15, 16, 1], $arrayklasifikasi)) ||
                    empty(array_diff([3, 6, 16], $arrayklasifikasi)) ||
                    empty(array_diff([6, 7, 12], $arrayklasifikasi)) ||
                    empty(array_diff([3, 7, 15], $arrayklasifikasi)) ||
                    empty(array_diff([12, 15, 7], $arrayklasifikasi)) ||
                    empty(array_diff([15, 16, 3], $arrayklasifikasi)) ||
                    empty(array_diff([12, 16, 6], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(8, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 81,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([3, 6, 7, 11], $arrayklasifikasi)) ||
                    empty(array_diff([12, 15, 16, 1], $arrayklasifikasi)) ||
                    empty(array_diff([3, 6, 16], $arrayklasifikasi)) ||
                    empty(array_diff([6, 7, 12], $arrayklasifikasi)) ||
                    empty(array_diff([3, 7, 15], $arrayklasifikasi)) ||
                    empty(array_diff([12, 15, 7], $arrayklasifikasi)) ||
                    empty(array_diff([15, 16, 3], $arrayklasifikasi)) ||
                    empty(array_diff([12, 16, 6], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(9, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 82,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([3, 6, 7, 11], $arrayklasifikasi)) ||
                    empty(array_diff([12, 15, 16, 1], $arrayklasifikasi)) ||
                    empty(array_diff([3, 6, 16], $arrayklasifikasi)) ||
                    empty(array_diff([6, 7, 12], $arrayklasifikasi)) ||
                    empty(array_diff([3, 7, 15], $arrayklasifikasi)) ||
                    empty(array_diff([12, 15, 7], $arrayklasifikasi)) ||
                    empty(array_diff([15, 16, 3], $arrayklasifikasi)) ||
                    empty(array_diff([12, 16, 6], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(10, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 83,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([4, 5, 6, 11], $arrayklasifikasi)) ||
                    empty(array_diff([13, 14, 15, 1], $arrayklasifikasi)) ||
                    empty(array_diff([4, 5, 15], $arrayklasifikasi)) ||
                    empty(array_diff([5, 6, 13], $arrayklasifikasi)) ||
                    empty(array_diff([4, 6, 14], $arrayklasifikasi)) ||
                    empty(array_diff([13, 14, 6], $arrayklasifikasi)) ||
                    empty(array_diff([14, 15, 4], $arrayklasifikasi)) ||
                    empty(array_diff([13, 15, 5], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(8, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 84,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([4, 5, 6, 11], $arrayklasifikasi)) ||
                    empty(array_diff([13, 14, 15, 1], $arrayklasifikasi)) ||
                    empty(array_diff([4, 5, 15], $arrayklasifikasi)) ||
                    empty(array_diff([5, 6, 13], $arrayklasifikasi)) ||
                    empty(array_diff([4, 6, 14], $arrayklasifikasi)) ||
                    empty(array_diff([13, 14, 6], $arrayklasifikasi)) ||
                    empty(array_diff([14, 15, 4], $arrayklasifikasi)) ||
                    empty(array_diff([13, 15, 5], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(9, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 85,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([4, 5, 6, 11], $arrayklasifikasi)) ||
                    empty(array_diff([13, 14, 15, 1], $arrayklasifikasi)) ||
                    empty(array_diff([4, 5, 15], $arrayklasifikasi)) ||
                    empty(array_diff([5, 6, 13], $arrayklasifikasi)) ||
                    empty(array_diff([4, 6, 14], $arrayklasifikasi)) ||
                    empty(array_diff([13, 14, 6], $arrayklasifikasi)) ||
                    empty(array_diff([14, 15, 4], $arrayklasifikasi)) ||
                    empty(array_diff([13, 15, 5], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(10, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 86,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([4, 6, 7, 11], $arrayklasifikasi)) ||
                    empty(array_diff([13, 15, 16, 1], $arrayklasifikasi)) ||
                    empty(array_diff([4, 6, 16], $arrayklasifikasi)) ||
                    empty(array_diff([6, 7, 13], $arrayklasifikasi)) ||
                    empty(array_diff([4, 7, 15], $arrayklasifikasi)) ||
                    empty(array_diff([13, 15, 7], $arrayklasifikasi)) ||
                    empty(array_diff([15, 16, 4], $arrayklasifikasi)) ||
                    empty(array_diff([13, 16, 6], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(8, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 87,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([4, 6, 7, 11], $arrayklasifikasi)) ||
                    empty(array_diff([13, 15, 16, 1], $arrayklasifikasi)) ||
                    empty(array_diff([4, 6, 16], $arrayklasifikasi)) ||
                    empty(array_diff([6, 7, 13], $arrayklasifikasi)) ||
                    empty(array_diff([4, 7, 15], $arrayklasifikasi)) ||
                    empty(array_diff([13, 15, 7], $arrayklasifikasi)) ||
                    empty(array_diff([15, 16, 4], $arrayklasifikasi)) ||
                    empty(array_diff([13, 16, 6], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(9, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 88,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([4, 6, 7, 11], $arrayklasifikasi)) ||
                    empty(array_diff([13, 15, 16, 1], $arrayklasifikasi)) ||
                    empty(array_diff([4, 6, 16], $arrayklasifikasi)) ||
                    empty(array_diff([6, 7, 13], $arrayklasifikasi)) ||
                    empty(array_diff([4, 7, 15], $arrayklasifikasi)) ||
                    empty(array_diff([13, 15, 7], $arrayklasifikasi)) ||
                    empty(array_diff([15, 16, 4], $arrayklasifikasi)) ||
                    empty(array_diff([13, 16, 6], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(10, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 89,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([5, 6, 7, 11], $arrayklasifikasi)) ||
                    empty(array_diff([14, 15, 16, 1], $arrayklasifikasi)) ||
                    empty(array_diff([5, 6, 16], $arrayklasifikasi)) ||
                    empty(array_diff([6, 7, 14], $arrayklasifikasi)) ||
                    empty(array_diff([5, 7, 15], $arrayklasifikasi)) ||
                    empty(array_diff([14, 15, 7], $arrayklasifikasi)) ||
                    empty(array_diff([15, 16, 5], $arrayklasifikasi)) ||
                    empty(array_diff([14, 16, 6], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(8, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 90,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([5, 6, 7, 11], $arrayklasifikasi)) ||
                    empty(array_diff([14, 15, 16, 1], $arrayklasifikasi)) ||
                    empty(array_diff([5, 6, 16], $arrayklasifikasi)) ||
                    empty(array_diff([6, 7, 14], $arrayklasifikasi)) ||
                    empty(array_diff([5, 7, 15], $arrayklasifikasi)) ||
                    empty(array_diff([14, 15, 7], $arrayklasifikasi)) ||
                    empty(array_diff([15, 16, 5], $arrayklasifikasi)) ||
                    empty(array_diff([14, 16, 6], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(9, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 91,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([5, 6, 7, 11], $arrayklasifikasi)) ||
                    empty(array_diff([14, 15, 16, 1], $arrayklasifikasi)) ||
                    empty(array_diff([5, 6, 16], $arrayklasifikasi)) ||
                    empty(array_diff([6, 7, 14], $arrayklasifikasi)) ||
                    empty(array_diff([5, 7, 15], $arrayklasifikasi)) ||
                    empty(array_diff([14, 15, 7], $arrayklasifikasi)) ||
                    empty(array_diff([15, 16, 5], $arrayklasifikasi)) ||
                    empty(array_diff([14, 16, 6], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(10, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 92,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([4, 5, 7, 11], $arrayklasifikasi)) ||
                    empty(array_diff([13, 14, 16, 1], $arrayklasifikasi)) ||
                    empty(array_diff([4, 5, 16], $arrayklasifikasi)) ||
                    empty(array_diff([5, 7, 13], $arrayklasifikasi)) ||
                    empty(array_diff([4, 7, 14], $arrayklasifikasi)) ||
                    empty(array_diff([13, 14, 7], $arrayklasifikasi)) ||
                    empty(array_diff([14, 16, 4], $arrayklasifikasi)) ||
                    empty(array_diff([13, 16, 5], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(8, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 93,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([4, 5, 7, 11], $arrayklasifikasi)) ||
                    empty(array_diff([13, 14, 16, 1], $arrayklasifikasi)) ||
                    empty(array_diff([4, 5, 16], $arrayklasifikasi)) ||
                    empty(array_diff([5, 7, 13], $arrayklasifikasi)) ||
                    empty(array_diff([4, 7, 14], $arrayklasifikasi)) ||
                    empty(array_diff([13, 14, 7], $arrayklasifikasi)) ||
                    empty(array_diff([14, 16, 4], $arrayklasifikasi)) ||
                    empty(array_diff([13, 16, 5], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(9, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 94,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([4, 5, 7, 11], $arrayklasifikasi)) ||
                    empty(array_diff([13, 14, 16, 1], $arrayklasifikasi)) ||
                    empty(array_diff([4, 5, 16], $arrayklasifikasi)) ||
                    empty(array_diff([5, 7, 13], $arrayklasifikasi)) ||
                    empty(array_diff([4, 7, 14], $arrayklasifikasi)) ||
                    empty(array_diff([13, 14, 7], $arrayklasifikasi)) ||
                    empty(array_diff([14, 16, 4], $arrayklasifikasi)) ||
                    empty(array_diff([13, 16, 5], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(10, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 95,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([3, 4, 11], $arrayklasifikasi)) ||
                    empty(array_diff([12, 13, 1], $arrayklasifikasi)) ||
                    empty(array_diff([3, 13], $arrayklasifikasi)) ||
                    empty(array_diff([12, 4], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(8, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 36,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([3, 4, 11], $arrayklasifikasi)) ||
                    empty(array_diff([12, 13, 1], $arrayklasifikasi)) ||
                    empty(array_diff([3, 13], $arrayklasifikasi)) ||
                    empty(array_diff([12, 4], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(9, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 37,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([3, 4, 11], $arrayklasifikasi)) ||
                    empty(array_diff([12, 13, 1], $arrayklasifikasi)) ||
                    empty(array_diff([3, 13], $arrayklasifikasi)) ||
                    empty(array_diff([12, 4], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(10, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 38,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([3, 5, 11], $arrayklasifikasi)) ||
                    empty(array_diff([12, 14, 1], $arrayklasifikasi)) ||
                    empty(array_diff([3, 14], $arrayklasifikasi)) ||
                    empty(array_diff([12, 5], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(8, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 39,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([3, 5, 11], $arrayklasifikasi)) ||
                    empty(array_diff([12, 14, 1], $arrayklasifikasi)) ||
                    empty(array_diff([3, 14], $arrayklasifikasi)) ||
                    empty(array_diff([12, 5], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(9, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 40,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([3, 5, 11], $arrayklasifikasi)) ||
                    empty(array_diff([12, 14, 1], $arrayklasifikasi)) ||
                    empty(array_diff([3, 14], $arrayklasifikasi)) ||
                    empty(array_diff([12, 5], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(10, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 41,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([3, 6, 11], $arrayklasifikasi)) ||
                    empty(array_diff([12, 15, 1], $arrayklasifikasi)) ||
                    empty(array_diff([3, 15], $arrayklasifikasi)) ||
                    empty(array_diff([12, 6], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(8, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 42,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([3, 6, 11], $arrayklasifikasi)) ||
                    empty(array_diff([12, 15, 1], $arrayklasifikasi)) ||
                    empty(array_diff([3, 15], $arrayklasifikasi)) ||
                    empty(array_diff([12, 6], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(9, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 43,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([3, 6, 11], $arrayklasifikasi)) ||
                    empty(array_diff([12, 15, 1], $arrayklasifikasi)) ||
                    empty(array_diff([3, 15], $arrayklasifikasi)) ||
                    empty(array_diff([12, 6], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(10, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 44,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([3, 7, 11], $arrayklasifikasi)) ||
                    empty(array_diff([12, 16, 1], $arrayklasifikasi)) ||
                    empty(array_diff([3, 16], $arrayklasifikasi)) ||
                    empty(array_diff([12, 7], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(8, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 45,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([3, 7, 11], $arrayklasifikasi)) ||
                    empty(array_diff([12, 16, 1], $arrayklasifikasi)) ||
                    empty(array_diff([3, 16], $arrayklasifikasi)) ||
                    empty(array_diff([12, 7], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(9, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 46,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([3, 7, 11], $arrayklasifikasi)) ||
                    empty(array_diff([12, 16, 1], $arrayklasifikasi)) ||
                    empty(array_diff([3, 16], $arrayklasifikasi)) ||
                    empty(array_diff([12, 7], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(10, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 47,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([4, 5, 11], $arrayklasifikasi)) ||
                    empty(array_diff([13, 14, 1], $arrayklasifikasi)) ||
                    empty(array_diff([4, 14], $arrayklasifikasi)) ||
                    empty(array_diff([13, 5], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(8, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 48,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([4, 5, 11], $arrayklasifikasi)) ||
                    empty(array_diff([13, 14, 1], $arrayklasifikasi)) ||
                    empty(array_diff([4, 14], $arrayklasifikasi)) ||
                    empty(array_diff([13, 5], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(9, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 49,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([4, 5, 11], $arrayklasifikasi)) ||
                    empty(array_diff([13, 14, 1], $arrayklasifikasi)) ||
                    empty(array_diff([4, 14], $arrayklasifikasi)) ||
                    empty(array_diff([13, 5], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(10, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 50,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([4, 6, 11], $arrayklasifikasi)) ||
                    empty(array_diff([13, 15, 1], $arrayklasifikasi)) ||
                    empty(array_diff([4, 15], $arrayklasifikasi)) ||
                    empty(array_diff([13, 6], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(8, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 51,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([4, 6, 11], $arrayklasifikasi)) ||
                    empty(array_diff([13, 15, 1], $arrayklasifikasi)) ||
                    empty(array_diff([4, 15], $arrayklasifikasi)) ||
                    empty(array_diff([13, 6], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(9, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 52,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([4, 6, 11], $arrayklasifikasi)) ||
                    empty(array_diff([13, 15, 1], $arrayklasifikasi)) ||
                    empty(array_diff([4, 15], $arrayklasifikasi)) ||
                    empty(array_diff([13, 6], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(10, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 53,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([4, 7, 11], $arrayklasifikasi)) ||
                    empty(array_diff([13, 16, 1], $arrayklasifikasi)) ||
                    empty(array_diff([4, 16], $arrayklasifikasi)) ||
                    empty(array_diff([13, 7], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(8, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 54,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([4, 7, 11], $arrayklasifikasi)) ||
                    empty(array_diff([13, 16, 1], $arrayklasifikasi)) ||
                    empty(array_diff([4, 16], $arrayklasifikasi)) ||
                    empty(array_diff([13, 7], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(9, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 55,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([4, 7, 11], $arrayklasifikasi)) ||
                    empty(array_diff([13, 16, 1], $arrayklasifikasi)) ||
                    empty(array_diff([4, 16], $arrayklasifikasi)) ||
                    empty(array_diff([13, 7], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(10, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 56,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([5, 6, 11], $arrayklasifikasi)) ||
                    empty(array_diff([14, 15, 1], $arrayklasifikasi)) ||
                    empty(array_diff([5, 15], $arrayklasifikasi)) ||
                    empty(array_diff([14, 6], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(8, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 57,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([5, 6, 11], $arrayklasifikasi)) ||
                    empty(array_diff([14, 15, 1], $arrayklasifikasi)) ||
                    empty(array_diff([5, 15], $arrayklasifikasi)) ||
                    empty(array_diff([14, 6], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(9, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 58,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([5, 6, 11], $arrayklasifikasi)) ||
                    empty(array_diff([14, 15, 1], $arrayklasifikasi)) ||
                    empty(array_diff([5, 15], $arrayklasifikasi)) ||
                    empty(array_diff([14, 6], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(10, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 59,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([5, 7, 11], $arrayklasifikasi)) ||
                    empty(array_diff([14, 16, 1], $arrayklasifikasi)) ||
                    empty(array_diff([5, 16], $arrayklasifikasi)) ||
                    empty(array_diff([14, 7], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(8, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 60,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([5, 7, 11], $arrayklasifikasi)) ||
                    empty(array_diff([14, 16, 1], $arrayklasifikasi)) ||
                    empty(array_diff([5, 16], $arrayklasifikasi)) ||
                    empty(array_diff([14, 7], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(9, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 61,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([5, 7, 11], $arrayklasifikasi)) ||
                    empty(array_diff([14, 16, 1], $arrayklasifikasi)) ||
                    empty(array_diff([5, 16], $arrayklasifikasi)) ||
                    empty(array_diff([14, 7], $arrayklasifikasi)) && !$kesimpulan
                    && in_array(10, $arrayklasifikasi))) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 62,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([6, 7, 11], $arrayklasifikasi)) ||
                    empty(array_diff([15, 16, 1], $arrayklasifikasi)) ||
                    empty(array_diff([6, 16], $arrayklasifikasi)) ||
                    empty(array_diff([15, 7], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(8, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 63,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([6, 7, 11], $arrayklasifikasi)) ||
                    empty(array_diff([15, 16, 1], $arrayklasifikasi)) ||
                    empty(array_diff([6, 16], $arrayklasifikasi)) ||
                    empty(array_diff([15, 7], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(9, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 64,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([6, 7, 11], $arrayklasifikasi)) ||
                    empty(array_diff([15, 16, 1], $arrayklasifikasi)) ||
                    empty(array_diff([6, 16], $arrayklasifikasi)) ||
                    empty(array_diff([15, 7], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(10, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 65,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([3, 12], $arrayklasifikasi)) ||
                    empty(array_diff([12, 1], $arrayklasifikasi)) ||
                    empty(array_diff([3, 11], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(8, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 21,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif (in_array(9, $arrayklasifikasi) && (empty(array_diff([3, 12], $arrayklasifikasi)) ||
                    empty(array_diff([12, 1], $arrayklasifikasi)) ||
                    empty(array_diff([3, 11], $arrayklasifikasi))) && !$kesimpulan) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 22,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif (in_array(10, $arrayklasifikasi) && (empty(array_diff([3, 12], $arrayklasifikasi)) ||
                    empty(array_diff([12, 1], $arrayklasifikasi)) ||
                    empty(array_diff([3, 11], $arrayklasifikasi))) && !$kesimpulan) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 23,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([4, 13], $arrayklasifikasi)) ||
                    empty(array_diff([13, 1], $arrayklasifikasi)) ||
                    empty(array_diff([4, 11], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(8, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 24,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([4, 13], $arrayklasifikasi)) ||
                    empty(array_diff([13, 1], $arrayklasifikasi)) ||
                    empty(array_diff([4, 11], $arrayklasifikasi)) && !$kesimpulan
                    && in_array(9, $arrayklasifikasi))) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 25,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([4, 13], $arrayklasifikasi)) ||
                    empty(array_diff([13, 1], $arrayklasifikasi)) ||
                    empty(array_diff([4, 11], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(10, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 26,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([5, 14], $arrayklasifikasi)) ||
                    empty(array_diff([14, 1], $arrayklasifikasi)) ||
                    empty(array_diff([5, 11], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(8, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 27,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([5, 14], $arrayklasifikasi)) ||
                    empty(array_diff([14, 1], $arrayklasifikasi)) ||
                    empty(array_diff([5, 11], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(9, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 28,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([5, 14], $arrayklasifikasi)) ||
                    empty(array_diff([14, 1], $arrayklasifikasi)) ||
                    empty(array_diff([5, 11], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(10, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 29,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([6, 15], $arrayklasifikasi)) ||
                    empty(array_diff([15, 1], $arrayklasifikasi)) ||
                    empty(array_diff([6, 11], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(8, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 30,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([6, 15], $arrayklasifikasi)) ||
                    empty(array_diff([15, 1], $arrayklasifikasi)) ||
                    empty(array_diff([6, 11], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(9, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 31,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([6, 15], $arrayklasifikasi)) ||
                    empty(array_diff([15, 1], $arrayklasifikasi)) ||
                    empty(array_diff([6, 11], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(10, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 32,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([7, 16], $arrayklasifikasi)) ||
                    empty(array_diff([16, 1], $arrayklasifikasi)) ||
                    empty(array_diff([7, 11], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(8, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 33,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([7, 16], $arrayklasifikasi)) ||
                    empty(array_diff([16, 1], $arrayklasifikasi)) ||
                    empty(array_diff([7, 11], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(9, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 34,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
                elseif ((empty(array_diff([7, 16], $arrayklasifikasi)) ||
                    empty(array_diff([16, 1], $arrayklasifikasi)) ||
                    empty(array_diff([7, 11], $arrayklasifikasi))) && !$kesimpulan
                    && in_array(10, $arrayklasifikasi)) {
                        Kesimpulan_analisis::create([
                        'ujian_id' => $id_ujian,
                        'basis_pengetahuan_id' => 35,
                        'soal_id' => $id_soal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        ]);
                }
            }

        }
        return view('redirectSemua',[
            "id_ujian" => $id_ujian,
            "slug" => $slug
        ]);
    }

    // menampilkan data evaluasi detai berserta grafik jawaban persoal
    public function Analisisnilai(Request $request)
    {
        $nosoal = $request->nosoal;
        $id_ujian = $request->ujian_id;
        $id_soal = $request->soal_id;
        $slug = $request->slug;

        // Mengecek apakah data sudah ada di tabel kelompok_mahasiswas
        $existingData = Analisis_nilai::where('ujian_id', $id_ujian)->where('soal_id', $id_soal)->exists();

        if($existingData) {

            // Kembali ke halaman sebelumnya dengan data kelompok mahasiswa dan request data
            return view('redirectPost', [
                "nosoal" => $nosoal,
                "id_ujian" => $id_ujian,
                "id_soal" => $id_soal,
                "slug" => $slug
            ]);
        }
        else{

            $dp_ka_soal = $request->Ka;
            $dp_kb_soal = $request->Kb;
            $N = $request->N;
            $DpSoal = 2*($dp_ka_soal - $dp_kb_soal) / $N;
            
            $dp_a_atas = $request->dp_a_atas;
            $dp_a_bawah = $request->dp_a_bawah;
            $Dp_opsia = 2*($dp_a_atas - $dp_a_bawah) / $N;
            
            $dp_b_atas = $request->dp_b_atas;
            $dp_b_bawah = $request->dp_b_bawah;
            $Dp_opsib = 2*($dp_b_atas - $dp_b_bawah) / $N;
            
            $dp_c_atas = $request->dp_c_atas;
            $dp_c_bawah = $request->dp_c_bawah;
            $Dp_opsic = 2*($dp_c_atas - $dp_c_bawah) / $N;
            
            $dp_d_atas = $request->dp_d_atas;
            $dp_d_bawah = $request->dp_d_bawah;
            $Dp_opsid = 2*($dp_d_atas - $dp_d_bawah) / $N;
            
            $dp_e_atas = $request->dp_e_atas;
            $dp_e_bawah = $request->dp_e_bawah;
            $Dp_opsie = 2*($dp_e_atas - $dp_e_bawah) / $N;

            $Tk = $request->Jb / $N ;
            
            $ppja = $request->ppj_a / $N;
            $ppjb = $request->ppj_b / $N;
            $ppjc = $request->ppj_c / $N;
            $ppjd = $request->ppj_d / $N;
            $ppje = $request->ppj_e / $N;
            
            Analisis_nilai::create([
                // Menambahkan data ke tabel kelompok Analisis nilai menggunakan metode create
                'ujian_id' => $id_ujian,
                'soal_id' => $id_soal,
                'dp_soal' => $DpSoal,
                'dp_opsia' => $Dp_opsia,
                'dp_opsib' => $Dp_opsib,
                'dp_opsic' => $Dp_opsic,
                'dp_opsid' => $Dp_opsid,
                'dp_opsie' => $Dp_opsie,
                'tk_soal' => $Tk,
                'ppj_opsia' => $ppja,
                'ppj_opsib' => $ppjb,
                'ppj_opsic' => $ppjc,
                'ppj_opsid' => $ppjd,
                'ppj_opsie' => $ppje,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $soal = Soal::find($request->soal_id);

            if($DpSoal <= 0){
                Analisis_klasifikasi::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 2,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }elseif($soal->jawaban == $soal->opsi_a && $Dp_opsib < 0 && $Dp_opsic < 0 && $Dp_opsid < 0 && $Dp_opsie < 0){
                Analisis_klasifikasi::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 1,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }elseif($soal->jawaban == $soal->opsi_b && $Dp_opsia < 0 && $Dp_opsic < 0 && $Dp_opsid < 0 && $Dp_opsie < 0){
                Analisis_klasifikasi::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 1,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }elseif($soal->jawaban == $soal->opsi_c && $Dp_opsib < 0 && $Dp_opsia < 0 && $Dp_opsid < 0 && $Dp_opsie < 0){
                Analisis_klasifikasi::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 1,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }elseif($soal->jawaban == $soal->opsi_d && $Dp_opsib < 0 && $Dp_opsic < 0 && $Dp_opsia < 0 && $Dp_opsie < 0){
                Analisis_klasifikasi::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 1,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }elseif(($soal->jawaban == $soal->opsi_e && $Dp_opsib < 0 && $Dp_opsic < 0 && $Dp_opsid < 0 && $Dp_opsia < 0)){
                Analisis_klasifikasi::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 1,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            if($Dp_opsia >= 0 && $soal->jawaban !== $soal->opsi_a){
                Analisis_klasifikasi::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 3,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            if($Dp_opsib >= 0 && $soal->jawaban !== $soal->opsi_b){
                Analisis_klasifikasi::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 4,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            if($Dp_opsic >= 0 && $soal->jawaban !== $soal->opsi_c){
                Analisis_klasifikasi::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 5,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            if($Dp_opsid >= 0 && $soal->jawaban !== $soal->opsi_d){
                Analisis_klasifikasi::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 6,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            if($Dp_opsie >= 0 && $soal->jawaban !== $soal->opsi_e){
                Analisis_klasifikasi::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 7,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            if($Tk < 0.3) {
                Analisis_klasifikasi::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 8,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }elseif($Tk > 0.7) {
                Analisis_klasifikasi::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 10,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }else{
                Analisis_klasifikasi::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 9,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            if($ppja >= 0.025 && $ppjb >= 0.025 && $ppjc >= 0.025 && $ppjd >= 0.025 & $ppje >= 0.025){
                Analisis_klasifikasi::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 11,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            if($ppja < 0.025){
                Analisis_klasifikasi::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 12,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            if($ppjb < 0.025){
                Analisis_klasifikasi::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 13,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            if($ppjc < 0.025){
                Analisis_klasifikasi::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 14,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            if($ppjd < 0.025){
                Analisis_klasifikasi::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 15,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            if($ppje < 0.025){
                Analisis_klasifikasi::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 16,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $arrayklasifikasi = [];
            $analisisklasifikasi = Analisis_klasifikasi::where('ujian_id', $id_ujian)->where('soal_id', $id_soal)->get();

            // Mengisi array dengan basis_pengetahuan_id
            foreach ($analisisklasifikasi as $analisis) {
                $arrayklasifikasi[] = $analisis->basis_pengetahuan_id;
            }

            $kesimpulan = Kesimpulan_analisis::where('ujian_id', $id_ujian)->where('soal_id', $id_soal)->exists();

            // Memeriksa apakah ada basis_pengetahuan_id yang sama dengan 2
            if (in_array(2, $arrayklasifikasi) && !$kesimpulan) {
                Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 17,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } 
            elseif (empty(array_diff([1, 11, 8], $arrayklasifikasi)) && !$kesimpulan) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 18,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            } 
            elseif (empty(array_diff([1, 11, 9], $arrayklasifikasi)) && !$kesimpulan) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 19,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            } 
            elseif (empty(array_diff([1, 11, 10], $arrayklasifikasi)) && !$kesimpulan) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 20,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            } 
            elseif ((empty(array_diff([3, 4, 5, 11], $arrayklasifikasi)) ||
                empty(array_diff([12, 13, 14, 1], $arrayklasifikasi)) ||
                empty(array_diff([3, 4, 14], $arrayklasifikasi)) ||
                empty(array_diff([4, 5, 12], $arrayklasifikasi)) ||
                empty(array_diff([3, 5, 13], $arrayklasifikasi)) ||
                empty(array_diff([12, 13, 5], $arrayklasifikasi)) ||
                empty(array_diff([13, 14, 3], $arrayklasifikasi)) ||
                empty(array_diff([12, 14, 4], $arrayklasifikasi))) && !$kesimpulan 
                && in_array(8, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 66,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            } 
            elseif ((empty(array_diff([3, 4, 5, 11], $arrayklasifikasi)) ||
                empty(array_diff([12, 13, 14, 1], $arrayklasifikasi)) ||
                empty(array_diff([3, 4, 14], $arrayklasifikasi)) ||
                empty(array_diff([4, 5, 12], $arrayklasifikasi)) ||
                empty(array_diff([3, 5, 13], $arrayklasifikasi)) ||
                empty(array_diff([12, 13, 5], $arrayklasifikasi)) ||
                empty(array_diff([13, 14, 3], $arrayklasifikasi)) ||
                empty(array_diff([12, 14, 4], $arrayklasifikasi))) && !$kesimpulan 
                && in_array(9, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 67,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            } 
            elseif ((empty(array_diff([3, 4, 5, 11], $arrayklasifikasi)) ||
                empty(array_diff([12, 13, 14, 1], $arrayklasifikasi)) ||
                empty(array_diff([3, 4, 14], $arrayklasifikasi)) ||
                empty(array_diff([4, 5, 12], $arrayklasifikasi)) ||
                empty(array_diff([3, 5, 13], $arrayklasifikasi)) ||
                empty(array_diff([12, 13, 5], $arrayklasifikasi)) ||
                empty(array_diff([13, 14, 3], $arrayklasifikasi)) ||
                empty(array_diff([12, 14, 4], $arrayklasifikasi))) && !$kesimpulan 
                && in_array(10, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 68,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            } 
            elseif ((empty(array_diff([3, 4, 6, 11], $arrayklasifikasi)) ||
                empty(array_diff([12, 13, 15, 1], $arrayklasifikasi)) ||
                empty(array_diff([3, 4, 15], $arrayklasifikasi)) ||
                empty(array_diff([4, 6, 12], $arrayklasifikasi)) ||
                empty(array_diff([3, 6, 13], $arrayklasifikasi)) ||
                empty(array_diff([12, 13, 6], $arrayklasifikasi)) ||
                empty(array_diff([13, 15, 3], $arrayklasifikasi)) ||
                empty(array_diff([12, 15, 4], $arrayklasifikasi))) && !$kesimpulan
                && in_array(8, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 69,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([3, 4, 6, 11], $arrayklasifikasi)) ||
                empty(array_diff([12, 13, 15, 1], $arrayklasifikasi)) ||
                empty(array_diff([3, 4, 15], $arrayklasifikasi)) ||
                empty(array_diff([4, 6, 12], $arrayklasifikasi)) ||
                empty(array_diff([3, 6, 13], $arrayklasifikasi)) ||
                empty(array_diff([12, 13, 6], $arrayklasifikasi)) ||
                empty(array_diff([13, 15, 3], $arrayklasifikasi)) ||
                empty(array_diff([12, 15, 4], $arrayklasifikasi))) && !$kesimpulan
                && in_array(9, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 70,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([3, 4, 6, 11], $arrayklasifikasi)) ||
                empty(array_diff([12, 13, 15, 1], $arrayklasifikasi)) ||
                empty(array_diff([3, 4, 15], $arrayklasifikasi)) ||
                empty(array_diff([4, 6, 12], $arrayklasifikasi)) ||
                empty(array_diff([3, 6, 13], $arrayklasifikasi)) ||
                empty(array_diff([12, 13, 6], $arrayklasifikasi)) ||
                empty(array_diff([13, 15, 3], $arrayklasifikasi)) ||
                empty(array_diff([12, 15, 4], $arrayklasifikasi))) && !$kesimpulan
                && in_array(10, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 71,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([3, 4, 7, 11], $arrayklasifikasi)) ||
                empty(array_diff([12, 13, 16, 1], $arrayklasifikasi)) ||
                empty(array_diff([3, 4, 16], $arrayklasifikasi)) ||
                empty(array_diff([4, 7, 12], $arrayklasifikasi)) ||
                empty(array_diff([3, 7, 13], $arrayklasifikasi)) ||
                empty(array_diff([12, 13, 7], $arrayklasifikasi)) ||
                empty(array_diff([13, 16, 3], $arrayklasifikasi)) ||
                empty(array_diff([12, 16, 4], $arrayklasifikasi))) && !$kesimpulan
                && in_array(8, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 72,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([3, 4, 7, 11], $arrayklasifikasi)) ||
                empty(array_diff([12, 13, 16, 1], $arrayklasifikasi)) ||
                empty(array_diff([3, 4, 16], $arrayklasifikasi)) ||
                empty(array_diff([4, 7, 12], $arrayklasifikasi)) ||
                empty(array_diff([3, 7, 13], $arrayklasifikasi)) ||
                empty(array_diff([12, 13, 7], $arrayklasifikasi)) ||
                empty(array_diff([13, 16, 3], $arrayklasifikasi)) ||
                empty(array_diff([12, 16, 4], $arrayklasifikasi))) && !$kesimpulan
                && in_array(9, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 73,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([3, 4, 7, 11], $arrayklasifikasi)) ||
                empty(array_diff([12, 13, 16, 1], $arrayklasifikasi)) ||
                empty(array_diff([3, 4, 16], $arrayklasifikasi)) ||
                empty(array_diff([4, 7, 12], $arrayklasifikasi)) ||
                empty(array_diff([3, 7, 13], $arrayklasifikasi)) ||
                empty(array_diff([12, 13, 7], $arrayklasifikasi)) ||
                empty(array_diff([13, 16, 3], $arrayklasifikasi)) ||
                empty(array_diff([12, 16, 4], $arrayklasifikasi))) && !$kesimpulan
                && in_array(10, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 74,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([3, 5, 6, 11], $arrayklasifikasi)) ||
                empty(array_diff([12, 14, 15, 1], $arrayklasifikasi)) ||
                empty(array_diff([3, 5, 15], $arrayklasifikasi)) ||
                empty(array_diff([5, 6, 12], $arrayklasifikasi)) ||
                empty(array_diff([3, 6, 14], $arrayklasifikasi)) ||
                empty(array_diff([12, 14, 6], $arrayklasifikasi)) ||
                empty(array_diff([14, 15, 3], $arrayklasifikasi)) ||
                empty(array_diff([12, 15, 5], $arrayklasifikasi))) && !$kesimpulan
                && in_array(8, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 75,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([3, 5, 6, 11], $arrayklasifikasi)) ||
                empty(array_diff([12, 14, 15, 1], $arrayklasifikasi)) ||
                empty(array_diff([3, 5, 15], $arrayklasifikasi)) ||
                empty(array_diff([5, 6, 12], $arrayklasifikasi)) ||
                empty(array_diff([3, 6, 14], $arrayklasifikasi)) ||
                empty(array_diff([12, 14, 6], $arrayklasifikasi)) ||
                empty(array_diff([14, 15, 3], $arrayklasifikasi)) ||
                empty(array_diff([12, 15, 5], $arrayklasifikasi))) && !$kesimpulan
                && in_array(9, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 76,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([3, 5, 6, 11], $arrayklasifikasi)) ||
                empty(array_diff([12, 14, 15, 1], $arrayklasifikasi)) ||
                empty(array_diff([3, 5, 15], $arrayklasifikasi)) ||
                empty(array_diff([5, 6, 12], $arrayklasifikasi)) ||
                empty(array_diff([3, 6, 14], $arrayklasifikasi)) ||
                empty(array_diff([12, 14, 6], $arrayklasifikasi)) ||
                empty(array_diff([14, 15, 3], $arrayklasifikasi)) ||
                empty(array_diff([12, 15, 5], $arrayklasifikasi))) && !$kesimpulan
                && in_array(10, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 77,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([3, 5, 7, 11], $arrayklasifikasi)) ||
                empty(array_diff([12, 14, 16, 1], $arrayklasifikasi)) ||
                empty(array_diff([3, 5, 16], $arrayklasifikasi)) ||
                empty(array_diff([5, 7, 12], $arrayklasifikasi)) ||
                empty(array_diff([3, 7, 14], $arrayklasifikasi)) ||
                empty(array_diff([12, 14, 7], $arrayklasifikasi)) ||
                empty(array_diff([14, 16, 3], $arrayklasifikasi)) ||
                empty(array_diff([12, 16, 5], $arrayklasifikasi))) && !$kesimpulan
                && in_array(8, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 78,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([3, 5, 7, 11], $arrayklasifikasi)) ||
                empty(array_diff([12, 14, 16, 1], $arrayklasifikasi)) ||
                empty(array_diff([3, 5, 16], $arrayklasifikasi)) ||
                empty(array_diff([5, 7, 12], $arrayklasifikasi)) ||
                empty(array_diff([3, 7, 14], $arrayklasifikasi)) ||
                empty(array_diff([12, 14, 7], $arrayklasifikasi)) ||
                empty(array_diff([14, 16, 3], $arrayklasifikasi)) ||
                empty(array_diff([12, 16, 5], $arrayklasifikasi))) && !$kesimpulan
                && in_array(9, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 79,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([3, 5, 7, 11], $arrayklasifikasi)) ||
                empty(array_diff([12, 14, 16, 1], $arrayklasifikasi)) ||
                empty(array_diff([3, 5, 16], $arrayklasifikasi)) ||
                empty(array_diff([5, 7, 12], $arrayklasifikasi)) ||
                empty(array_diff([3, 7, 14], $arrayklasifikasi)) ||
                empty(array_diff([12, 14, 7], $arrayklasifikasi)) ||
                empty(array_diff([14, 16, 3], $arrayklasifikasi)) ||
                empty(array_diff([12, 16, 5], $arrayklasifikasi))) && !$kesimpulan
                && in_array(10, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 80,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([3, 6, 7, 11], $arrayklasifikasi)) ||
                empty(array_diff([12, 15, 16, 1], $arrayklasifikasi)) ||
                empty(array_diff([3, 6, 16], $arrayklasifikasi)) ||
                empty(array_diff([6, 7, 12], $arrayklasifikasi)) ||
                empty(array_diff([3, 7, 15], $arrayklasifikasi)) ||
                empty(array_diff([12, 15, 7], $arrayklasifikasi)) ||
                empty(array_diff([15, 16, 3], $arrayklasifikasi)) ||
                empty(array_diff([12, 16, 6], $arrayklasifikasi))) && !$kesimpulan
                && in_array(8, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 81,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([3, 6, 7, 11], $arrayklasifikasi)) ||
                empty(array_diff([12, 15, 16, 1], $arrayklasifikasi)) ||
                empty(array_diff([3, 6, 16], $arrayklasifikasi)) ||
                empty(array_diff([6, 7, 12], $arrayklasifikasi)) ||
                empty(array_diff([3, 7, 15], $arrayklasifikasi)) ||
                empty(array_diff([12, 15, 7], $arrayklasifikasi)) ||
                empty(array_diff([15, 16, 3], $arrayklasifikasi)) ||
                empty(array_diff([12, 16, 6], $arrayklasifikasi))) && !$kesimpulan
                && in_array(9, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 82,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([3, 6, 7, 11], $arrayklasifikasi)) ||
                empty(array_diff([12, 15, 16, 1], $arrayklasifikasi)) ||
                empty(array_diff([3, 6, 16], $arrayklasifikasi)) ||
                empty(array_diff([6, 7, 12], $arrayklasifikasi)) ||
                empty(array_diff([3, 7, 15], $arrayklasifikasi)) ||
                empty(array_diff([12, 15, 7], $arrayklasifikasi)) ||
                empty(array_diff([15, 16, 3], $arrayklasifikasi)) ||
                empty(array_diff([12, 16, 6], $arrayklasifikasi))) && !$kesimpulan
                && in_array(10, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 83,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([4, 5, 6, 11], $arrayklasifikasi)) ||
                empty(array_diff([13, 14, 15, 1], $arrayklasifikasi)) ||
                empty(array_diff([4, 5, 15], $arrayklasifikasi)) ||
                empty(array_diff([5, 6, 13], $arrayklasifikasi)) ||
                empty(array_diff([4, 6, 14], $arrayklasifikasi)) ||
                empty(array_diff([13, 14, 6], $arrayklasifikasi)) ||
                empty(array_diff([14, 15, 4], $arrayklasifikasi)) ||
                empty(array_diff([13, 15, 5], $arrayklasifikasi))) && !$kesimpulan
                && in_array(8, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 84,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([4, 5, 6, 11], $arrayklasifikasi)) ||
                empty(array_diff([13, 14, 15, 1], $arrayklasifikasi)) ||
                empty(array_diff([4, 5, 15], $arrayklasifikasi)) ||
                empty(array_diff([5, 6, 13], $arrayklasifikasi)) ||
                empty(array_diff([4, 6, 14], $arrayklasifikasi)) ||
                empty(array_diff([13, 14, 6], $arrayklasifikasi)) ||
                empty(array_diff([14, 15, 4], $arrayklasifikasi)) ||
                empty(array_diff([13, 15, 5], $arrayklasifikasi))) && !$kesimpulan
                && in_array(9, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 85,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([4, 5, 6, 11], $arrayklasifikasi)) ||
                empty(array_diff([13, 14, 15, 1], $arrayklasifikasi)) ||
                empty(array_diff([4, 5, 15], $arrayklasifikasi)) ||
                empty(array_diff([5, 6, 13], $arrayklasifikasi)) ||
                empty(array_diff([4, 6, 14], $arrayklasifikasi)) ||
                empty(array_diff([13, 14, 6], $arrayklasifikasi)) ||
                empty(array_diff([14, 15, 4], $arrayklasifikasi)) ||
                empty(array_diff([13, 15, 5], $arrayklasifikasi))) && !$kesimpulan
                && in_array(10, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 86,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([4, 6, 7, 11], $arrayklasifikasi)) ||
                empty(array_diff([13, 15, 16, 1], $arrayklasifikasi)) ||
                empty(array_diff([4, 6, 16], $arrayklasifikasi)) ||
                empty(array_diff([6, 7, 13], $arrayklasifikasi)) ||
                empty(array_diff([4, 7, 15], $arrayklasifikasi)) ||
                empty(array_diff([13, 15, 7], $arrayklasifikasi)) ||
                empty(array_diff([15, 16, 4], $arrayklasifikasi)) ||
                empty(array_diff([13, 16, 6], $arrayklasifikasi))) && !$kesimpulan
                && in_array(8, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 87,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([4, 6, 7, 11], $arrayklasifikasi)) ||
                empty(array_diff([13, 15, 16, 1], $arrayklasifikasi)) ||
                empty(array_diff([4, 6, 16], $arrayklasifikasi)) ||
                empty(array_diff([6, 7, 13], $arrayklasifikasi)) ||
                empty(array_diff([4, 7, 15], $arrayklasifikasi)) ||
                empty(array_diff([13, 15, 7], $arrayklasifikasi)) ||
                empty(array_diff([15, 16, 4], $arrayklasifikasi)) ||
                empty(array_diff([13, 16, 6], $arrayklasifikasi))) && !$kesimpulan
                && in_array(9, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 88,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([4, 6, 7, 11], $arrayklasifikasi)) ||
                empty(array_diff([13, 15, 16, 1], $arrayklasifikasi)) ||
                empty(array_diff([4, 6, 16], $arrayklasifikasi)) ||
                empty(array_diff([6, 7, 13], $arrayklasifikasi)) ||
                empty(array_diff([4, 7, 15], $arrayklasifikasi)) ||
                empty(array_diff([13, 15, 7], $arrayklasifikasi)) ||
                empty(array_diff([15, 16, 4], $arrayklasifikasi)) ||
                empty(array_diff([13, 16, 6], $arrayklasifikasi))) && !$kesimpulan
                && in_array(10, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 89,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([5, 6, 7, 11], $arrayklasifikasi)) ||
                empty(array_diff([14, 15, 16, 1], $arrayklasifikasi)) ||
                empty(array_diff([5, 6, 16], $arrayklasifikasi)) ||
                empty(array_diff([6, 7, 14], $arrayklasifikasi)) ||
                empty(array_diff([5, 7, 15], $arrayklasifikasi)) ||
                empty(array_diff([14, 15, 7], $arrayklasifikasi)) ||
                empty(array_diff([15, 16, 5], $arrayklasifikasi)) ||
                empty(array_diff([14, 16, 6], $arrayklasifikasi))) && !$kesimpulan
                && in_array(8, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 90,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([5, 6, 7, 11], $arrayklasifikasi)) ||
                empty(array_diff([14, 15, 16, 1], $arrayklasifikasi)) ||
                empty(array_diff([5, 6, 16], $arrayklasifikasi)) ||
                empty(array_diff([6, 7, 14], $arrayklasifikasi)) ||
                empty(array_diff([5, 7, 15], $arrayklasifikasi)) ||
                empty(array_diff([14, 15, 7], $arrayklasifikasi)) ||
                empty(array_diff([15, 16, 5], $arrayklasifikasi)) ||
                empty(array_diff([14, 16, 6], $arrayklasifikasi))) && !$kesimpulan
                && in_array(9, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 91,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([5, 6, 7, 11], $arrayklasifikasi)) ||
                empty(array_diff([14, 15, 16, 1], $arrayklasifikasi)) ||
                empty(array_diff([5, 6, 16], $arrayklasifikasi)) ||
                empty(array_diff([6, 7, 14], $arrayklasifikasi)) ||
                empty(array_diff([5, 7, 15], $arrayklasifikasi)) ||
                empty(array_diff([14, 15, 7], $arrayklasifikasi)) ||
                empty(array_diff([15, 16, 5], $arrayklasifikasi)) ||
                empty(array_diff([14, 16, 6], $arrayklasifikasi))) && !$kesimpulan
                && in_array(10, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 92,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([4, 5, 7, 11], $arrayklasifikasi)) ||
                empty(array_diff([13, 14, 16, 1], $arrayklasifikasi)) ||
                empty(array_diff([4, 5, 16], $arrayklasifikasi)) ||
                empty(array_diff([5, 7, 13], $arrayklasifikasi)) ||
                empty(array_diff([4, 7, 14], $arrayklasifikasi)) ||
                empty(array_diff([13, 14, 7], $arrayklasifikasi)) ||
                empty(array_diff([14, 16, 4], $arrayklasifikasi)) ||
                empty(array_diff([13, 16, 5], $arrayklasifikasi))) && !$kesimpulan
                && in_array(8, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 93,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([4, 5, 7, 11], $arrayklasifikasi)) ||
                empty(array_diff([13, 14, 16, 1], $arrayklasifikasi)) ||
                empty(array_diff([4, 5, 16], $arrayklasifikasi)) ||
                empty(array_diff([5, 7, 13], $arrayklasifikasi)) ||
                empty(array_diff([4, 7, 14], $arrayklasifikasi)) ||
                empty(array_diff([13, 14, 7], $arrayklasifikasi)) ||
                empty(array_diff([14, 16, 4], $arrayklasifikasi)) ||
                empty(array_diff([13, 16, 5], $arrayklasifikasi))) && !$kesimpulan
                && in_array(9, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 94,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([4, 5, 7, 11], $arrayklasifikasi)) ||
                empty(array_diff([13, 14, 16, 1], $arrayklasifikasi)) ||
                empty(array_diff([4, 5, 16], $arrayklasifikasi)) ||
                empty(array_diff([5, 7, 13], $arrayklasifikasi)) ||
                empty(array_diff([4, 7, 14], $arrayklasifikasi)) ||
                empty(array_diff([13, 14, 7], $arrayklasifikasi)) ||
                empty(array_diff([14, 16, 4], $arrayklasifikasi)) ||
                empty(array_diff([13, 16, 5], $arrayklasifikasi))) && !$kesimpulan
                && in_array(10, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 95,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([3, 4, 11], $arrayklasifikasi)) ||
                empty(array_diff([12, 13, 1], $arrayklasifikasi)) ||
                empty(array_diff([3, 13], $arrayklasifikasi)) ||
                empty(array_diff([12, 4], $arrayklasifikasi))) && !$kesimpulan
                && in_array(8, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 36,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([3, 4, 11], $arrayklasifikasi)) ||
                empty(array_diff([12, 13, 1], $arrayklasifikasi)) ||
                empty(array_diff([3, 13], $arrayklasifikasi)) ||
                empty(array_diff([12, 4], $arrayklasifikasi))) && !$kesimpulan
                && in_array(9, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 37,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([3, 4, 11], $arrayklasifikasi)) ||
                empty(array_diff([12, 13, 1], $arrayklasifikasi)) ||
                empty(array_diff([3, 13], $arrayklasifikasi)) ||
                empty(array_diff([12, 4], $arrayklasifikasi))) && !$kesimpulan
                && in_array(10, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 38,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([3, 5, 11], $arrayklasifikasi)) ||
                empty(array_diff([12, 14, 1], $arrayklasifikasi)) ||
                empty(array_diff([3, 14], $arrayklasifikasi)) ||
                empty(array_diff([12, 5], $arrayklasifikasi))) && !$kesimpulan
                && in_array(8, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 39,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([3, 5, 11], $arrayklasifikasi)) ||
                empty(array_diff([12, 14, 1], $arrayklasifikasi)) ||
                empty(array_diff([3, 14], $arrayklasifikasi)) ||
                empty(array_diff([12, 5], $arrayklasifikasi))) && !$kesimpulan
                && in_array(9, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 40,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([3, 5, 11], $arrayklasifikasi)) ||
                empty(array_diff([12, 14, 1], $arrayklasifikasi)) ||
                empty(array_diff([3, 14], $arrayklasifikasi)) ||
                empty(array_diff([12, 5], $arrayklasifikasi))) && !$kesimpulan
                && in_array(10, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 41,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([3, 6, 11], $arrayklasifikasi)) ||
                empty(array_diff([12, 15, 1], $arrayklasifikasi)) ||
                empty(array_diff([3, 15], $arrayklasifikasi)) ||
                empty(array_diff([12, 6], $arrayklasifikasi))) && !$kesimpulan
                && in_array(8, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 42,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([3, 6, 11], $arrayklasifikasi)) ||
                empty(array_diff([12, 15, 1], $arrayklasifikasi)) ||
                empty(array_diff([3, 15], $arrayklasifikasi)) ||
                empty(array_diff([12, 6], $arrayklasifikasi))) && !$kesimpulan
                && in_array(9, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 43,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([3, 6, 11], $arrayklasifikasi)) ||
                empty(array_diff([12, 15, 1], $arrayklasifikasi)) ||
                empty(array_diff([3, 15], $arrayklasifikasi)) ||
                empty(array_diff([12, 6], $arrayklasifikasi))) && !$kesimpulan
                && in_array(10, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 44,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([3, 7, 11], $arrayklasifikasi)) ||
                empty(array_diff([12, 16, 1], $arrayklasifikasi)) ||
                empty(array_diff([3, 16], $arrayklasifikasi)) ||
                empty(array_diff([12, 7], $arrayklasifikasi))) && !$kesimpulan
                && in_array(8, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 45,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([3, 7, 11], $arrayklasifikasi)) ||
                empty(array_diff([12, 16, 1], $arrayklasifikasi)) ||
                empty(array_diff([3, 16], $arrayklasifikasi)) ||
                empty(array_diff([12, 7], $arrayklasifikasi))) && !$kesimpulan
                && in_array(9, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 46,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([3, 7, 11], $arrayklasifikasi)) ||
                empty(array_diff([12, 16, 1], $arrayklasifikasi)) ||
                empty(array_diff([3, 16], $arrayklasifikasi)) ||
                empty(array_diff([12, 7], $arrayklasifikasi))) && !$kesimpulan
                && in_array(10, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 47,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([4, 5, 11], $arrayklasifikasi)) ||
                empty(array_diff([13, 14, 1], $arrayklasifikasi)) ||
                empty(array_diff([4, 14], $arrayklasifikasi)) ||
                empty(array_diff([13, 5], $arrayklasifikasi))) && !$kesimpulan
                && in_array(8, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 48,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([4, 5, 11], $arrayklasifikasi)) ||
                empty(array_diff([13, 14, 1], $arrayklasifikasi)) ||
                empty(array_diff([4, 14], $arrayklasifikasi)) ||
                empty(array_diff([13, 5], $arrayklasifikasi))) && !$kesimpulan
                && in_array(9, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 49,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([4, 5, 11], $arrayklasifikasi)) ||
                empty(array_diff([13, 14, 1], $arrayklasifikasi)) ||
                empty(array_diff([4, 14], $arrayklasifikasi)) ||
                empty(array_diff([13, 5], $arrayklasifikasi))) && !$kesimpulan
                && in_array(10, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 50,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([4, 6, 11], $arrayklasifikasi)) ||
                empty(array_diff([13, 15, 1], $arrayklasifikasi)) ||
                empty(array_diff([4, 15], $arrayklasifikasi)) ||
                empty(array_diff([13, 6], $arrayklasifikasi))) && !$kesimpulan
                && in_array(8, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 51,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([4, 6, 11], $arrayklasifikasi)) ||
                empty(array_diff([13, 15, 1], $arrayklasifikasi)) ||
                empty(array_diff([4, 15], $arrayklasifikasi)) ||
                empty(array_diff([13, 6], $arrayklasifikasi))) && !$kesimpulan
                && in_array(9, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 52,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([4, 6, 11], $arrayklasifikasi)) ||
                empty(array_diff([13, 15, 1], $arrayklasifikasi)) ||
                empty(array_diff([4, 15], $arrayklasifikasi)) ||
                empty(array_diff([13, 6], $arrayklasifikasi))) && !$kesimpulan
                && in_array(10, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 53,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([4, 7, 11], $arrayklasifikasi)) ||
                empty(array_diff([13, 16, 1], $arrayklasifikasi)) ||
                empty(array_diff([4, 16], $arrayklasifikasi)) ||
                empty(array_diff([13, 7], $arrayklasifikasi))) && !$kesimpulan
                && in_array(8, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 54,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([4, 7, 11], $arrayklasifikasi)) ||
                empty(array_diff([13, 16, 1], $arrayklasifikasi)) ||
                empty(array_diff([4, 16], $arrayklasifikasi)) ||
                empty(array_diff([13, 7], $arrayklasifikasi))) && !$kesimpulan
                && in_array(9, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 55,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([4, 7, 11], $arrayklasifikasi)) ||
                empty(array_diff([13, 16, 1], $arrayklasifikasi)) ||
                empty(array_diff([4, 16], $arrayklasifikasi)) ||
                empty(array_diff([13, 7], $arrayklasifikasi))) && !$kesimpulan
                && in_array(10, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 56,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([5, 6, 11], $arrayklasifikasi)) ||
                empty(array_diff([14, 15, 1], $arrayklasifikasi)) ||
                empty(array_diff([5, 15], $arrayklasifikasi)) ||
                empty(array_diff([14, 6], $arrayklasifikasi))) && !$kesimpulan
                && in_array(8, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 57,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([5, 6, 11], $arrayklasifikasi)) ||
                empty(array_diff([14, 15, 1], $arrayklasifikasi)) ||
                empty(array_diff([5, 15], $arrayklasifikasi)) ||
                empty(array_diff([14, 6], $arrayklasifikasi))) && !$kesimpulan
                && in_array(9, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 58,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([5, 6, 11], $arrayklasifikasi)) ||
                empty(array_diff([14, 15, 1], $arrayklasifikasi)) ||
                empty(array_diff([5, 15], $arrayklasifikasi)) ||
                empty(array_diff([14, 6], $arrayklasifikasi))) && !$kesimpulan
                && in_array(10, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 59,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([5, 7, 11], $arrayklasifikasi)) ||
                empty(array_diff([14, 16, 1], $arrayklasifikasi)) ||
                empty(array_diff([5, 16], $arrayklasifikasi)) ||
                empty(array_diff([14, 7], $arrayklasifikasi))) && !$kesimpulan
                && in_array(8, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 60,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([5, 7, 11], $arrayklasifikasi)) ||
                empty(array_diff([14, 16, 1], $arrayklasifikasi)) ||
                empty(array_diff([5, 16], $arrayklasifikasi)) ||
                empty(array_diff([14, 7], $arrayklasifikasi))) && !$kesimpulan
                && in_array(9, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 61,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([5, 7, 11], $arrayklasifikasi)) ||
                empty(array_diff([14, 16, 1], $arrayklasifikasi)) ||
                empty(array_diff([5, 16], $arrayklasifikasi)) ||
                empty(array_diff([14, 7], $arrayklasifikasi))) && !$kesimpulan
                && in_array(10, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 62,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([6, 7, 11], $arrayklasifikasi)) ||
                empty(array_diff([15, 16, 1], $arrayklasifikasi)) ||
                empty(array_diff([6, 16], $arrayklasifikasi)) ||
                empty(array_diff([15, 7], $arrayklasifikasi))) && !$kesimpulan
                && in_array(8, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 63,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([6, 7, 11], $arrayklasifikasi)) ||
                empty(array_diff([15, 16, 1], $arrayklasifikasi)) ||
                empty(array_diff([6, 16], $arrayklasifikasi)) ||
                empty(array_diff([15, 7], $arrayklasifikasi))) && !$kesimpulan
                && in_array(9, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 64,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([6, 7, 11], $arrayklasifikasi)) ||
                empty(array_diff([15, 16, 1], $arrayklasifikasi)) ||
                empty(array_diff([6, 16], $arrayklasifikasi)) ||
                empty(array_diff([15, 7], $arrayklasifikasi))) && !$kesimpulan
                && in_array(10, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 65,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([3, 12], $arrayklasifikasi)) ||
                empty(array_diff([12, 1], $arrayklasifikasi)) ||
                empty(array_diff([3, 11], $arrayklasifikasi))) && !$kesimpulan
                && in_array(8, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 21,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif (in_array(9, $arrayklasifikasi) && (empty(array_diff([3, 12], $arrayklasifikasi)) ||
                empty(array_diff([12, 1], $arrayklasifikasi)) ||
                empty(array_diff([3, 11], $arrayklasifikasi))) && !$kesimpulan) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 22,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif (in_array(10, $arrayklasifikasi) && (empty(array_diff([3, 12], $arrayklasifikasi)) ||
                empty(array_diff([12, 1], $arrayklasifikasi)) ||
                empty(array_diff([3, 11], $arrayklasifikasi))) && !$kesimpulan) { 
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 23,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([4, 13], $arrayklasifikasi)) ||
                empty(array_diff([13, 1], $arrayklasifikasi)) ||
                empty(array_diff([4, 11], $arrayklasifikasi))) && !$kesimpulan
                && in_array(8, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 24,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([4, 13], $arrayklasifikasi)) ||
                empty(array_diff([13, 1], $arrayklasifikasi)) ||
                empty(array_diff([4, 11], $arrayklasifikasi))) && !$kesimpulan
                && in_array(9, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 25,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([4, 13], $arrayklasifikasi)) ||
                empty(array_diff([13, 1], $arrayklasifikasi)) ||
                empty(array_diff([4, 11], $arrayklasifikasi))) && !$kesimpulan
                && in_array(10, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 26,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([5, 14], $arrayklasifikasi)) ||
                empty(array_diff([14, 1], $arrayklasifikasi)) ||
                empty(array_diff([5, 11], $arrayklasifikasi))) && !$kesimpulan
                && in_array(8, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 27,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([5, 14], $arrayklasifikasi)) ||
                empty(array_diff([14, 1], $arrayklasifikasi)) ||
                empty(array_diff([5, 11], $arrayklasifikasi))) && !$kesimpulan
                && in_array(9, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 28,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([5, 14], $arrayklasifikasi)) ||
                empty(array_diff([14, 1], $arrayklasifikasi)) ||
                empty(array_diff([5, 11], $arrayklasifikasi))) && !$kesimpulan
                && in_array(10, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 29,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([6, 15], $arrayklasifikasi)) ||
                empty(array_diff([15, 1], $arrayklasifikasi)) ||
                empty(array_diff([6, 11], $arrayklasifikasi))) && !$kesimpulan
                && in_array(8, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 30,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([6, 15], $arrayklasifikasi)) ||
                empty(array_diff([15, 1], $arrayklasifikasi)) ||
                empty(array_diff([6, 11], $arrayklasifikasi))) && !$kesimpulan
                && in_array(9, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 31,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([6, 15], $arrayklasifikasi)) ||
                empty(array_diff([15, 1], $arrayklasifikasi)) ||
                empty(array_diff([6, 11], $arrayklasifikasi))) && !$kesimpulan
                && in_array(10, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 32,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([7, 16], $arrayklasifikasi)) ||
                empty(array_diff([16, 1], $arrayklasifikasi)) ||
                empty(array_diff([7, 11], $arrayklasifikasi))) && !$kesimpulan
                && in_array(8, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 33,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([7, 16], $arrayklasifikasi)) ||
                empty(array_diff([16, 1], $arrayklasifikasi)) ||
                empty(array_diff([7, 11], $arrayklasifikasi))) && !$kesimpulan
                && in_array(9, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 34,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }
            elseif ((empty(array_diff([7, 16], $arrayklasifikasi)) ||
                empty(array_diff([16, 1], $arrayklasifikasi)) ||
                empty(array_diff([7, 11], $arrayklasifikasi))) && !$kesimpulan
                && in_array(10, $arrayklasifikasi)) {
                    Kesimpulan_analisis::create([
                    'ujian_id' => $id_ujian,
                    'basis_pengetahuan_id' => 35,
                    'soal_id' => $id_soal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    ]);
            }

            // Redirect ke halaman sebelumnya atau halaman yang diinginkan
            return view('redirectPost', [
                "nosoal" => $nosoal,
                "id_ujian" => $id_ujian,
                "id_soal" => $id_soal,
                "slug" => $slug
            ]);
        }
    }    

    // mengubah data evaluasi ketika mahasiswa mengubah jawaban ujiannya
    public function update(Request $request, $id)
    {
        $rules = [
            'soal_id' => 'required',
            'ujian_id' => 'required',
            'user_id' => 'required',
            'jawaban' => 'required'
        ];
        
        $validatedData = $request->validate($rules);

        $soal = Soal::find($request->soal_id);
        if($request->jawaban == $soal->jawaban){
            $validatedData['skor'] = $request->skor;
        }else{
            $validatedData['skor'] = 0;
        }
        if(session('ujian_selesai')){
            return back()->with('success', 'Jawaban Gagal Diubah!');
        }
        evaluasi::where('id', $id)->update($validatedData);
        if($request->page == $request->pt){
            return redirect('/masuk-ujian'.'/'.$request->slug.'#soal-'.$request->pt);
        }else{
            return redirect('/masuk-ujian'.'/'.$request->slug.'#soal-'.$request->page);
        }
    }

    public function reset(Request $request)
    {
        $nosoal = $request->nosoal;
        $id_ujian = $request->ujian_id;
        $id_soal = $request->soal_id;
        $slug = $request->slug;

        // Hapus data dari tabel Kelompok_mahasiswa berdasarkan ujian_id
        Analisis_nilai::where('ujian_id', $request->ujian_id)->where('soal_id', $id_soal)->delete();
        Analisis_klasifikasi::where('ujian_id', $request->ujian_id)->where('soal_id', $id_soal)->delete();
        Kesimpulan_analisis::where('ujian_id', $request->ujian_id)->where('soal_id', $id_soal)->delete();

        // Redirect ke rute Evaluasisbutirsoal dengan data kelompok mahasiswa dan request data
        return view('redirectPost',[
            "nosoal" => $nosoal,
            "id_ujian" => $id_ujian,
            "id_soal" => $id_soal,
            "slug" => $slug
        ]);
    }

    public function resetsemua(Request $request)
    {
        $id_ujian = $request->ujian_id;
        $slug = $request->slug;

        // Hapus data dari tabel Kelompok_mahasiswa berdasarkan ujian_id
        Kelompok_mahasiswa::where('ujian_id', $request->ujian_id)->delete();
        Analisis_nilai::where('ujian_id', $request->ujian_id)->delete();
        Analisis_klasifikasi::where('ujian_id', $request->ujian_id)->delete();
        Kesimpulan_analisis::where('ujian_id', $request->ujian_id)->delete();

        // Redirect ke rute Evaluasisbutirsoal dengan data kelompok mahasiswa dan request data
        return view('redirectSemua',[
            "id_ujian" => $id_ujian,
            "slug" => $slug
        ]);
    }
}