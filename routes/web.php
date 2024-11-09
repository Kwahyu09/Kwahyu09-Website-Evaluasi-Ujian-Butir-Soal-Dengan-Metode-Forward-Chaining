<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SoalController;
use App\Http\Controllers\AktorController;
use App\Http\Controllers\BasisPengetahuanController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\ModulController;
use App\Http\Controllers\UjianController;
use App\Http\Controllers\EvaluasiController;
use App\Http\Controllers\GrupsoalController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\HasilujianController;
use App\Http\Controllers\DashboardHomeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// Rute profile
Route::middleware(['auth'])->group(function () {
    Route::get('/profile/{user:username}/edit', [AktorController::class, 'edit'])->name('profile');
    Route::put('/Admin/{user:username}', [AktorController::class, 'profile'])->middleware('role:Admin')->name('updateAdmin');
    Route::put('/Staf/{user:username}', [AktorController::class, 'profile'])->name('ProfileStaf');
    Route::put('/Ketua/{user:username}', [AktorController::class, 'profile'])->name('ProfileKetua');
    Route::put('/Mahasiswa/{user:username}', [AktorController::class, 'profile'])->name('ProfileMahasiswa');
});

Route::get('/', [DashboardHomeController::class, 'index'])->middleware(['auth'])->name('home');

//rute staf
Route::middleware(['auth', 'role:Admin'])->group(function () {
    Route::get('/staff', [AktorController::class, 'index_staff'])->name('Staff');
    Route::get('/staff/create', [AktorController::class, 'create_staff'])->name('CreateStaff');
    Route::post('/staff/store', [AktorController::class, 'store_staff'])->name('StoreStaff');
    Route::get('/staff/{user:username}/delete', [AktorController::class, 'destroy_staff'])->name('destroyStaff');
    Route::get('/staff/{user:username}/edit', [AktorController::class, 'edit_staf'])->name('editStaff');
    Route::put('/Staf/{user:username}/update', [AktorController::class, 'update_staf'])->name('updateStaf');
    Route::get('/variabel', [BasispengetahuanController::class, 'index'])->name('variabel');
    Route::get('/kesimpulan', [BasispengetahuanController::class, 'index_kes'])->name('kesimpulan');
});

Route::middleware(['auth', 'role:Admin|Staf'])->group(function () {
    //rute ketua
    Route::get('/ketua', [AktorController::class, 'index_ketua'])->name('Ketua');
    Route::get('/ketua/create', [AktorController::class, 'create_ketua'])->name('CreateKetua');
    Route::post('/ketua/store', [AktorController::class, 'store_ketua'])->name('StoreKetua');
    Route::get('/ketua/{user:username}/delete', [AktorController::class, 'destroy_ketua'])->name('destroyKetua');
    Route::put('/Ketua/{user:username}/update', [AktorController::class,'update_ketua'])->name('updateKetua');
    Route::get('/ketua/{user:username}/edit', [AktorController::class, 'edit_ketua'])->name('editKetua');

    // Rute untuk kelas
    Route::get('/kelas', [KelasController::class,'index'])->name('Kelas');
    Route::get('/kelas/create', [KelasController::class,'create'])->name('createKelas');
    Route::post('/kelas/store', [KelasController::class,'store'])->name('storeKelas');
    Route::get('/kelas/{kelas:slug}/delete', [KelasController::class, 'destroy'])->name('destroyKetua');
    Route::get('/kelas/{kelas:slug}/edit', [KelasController::class, 'edit'])->name('editKelas');
    Route::get('/kelas/{kelas:slug}',[KelasController::class, 'show'])->name('ShowMahasiswa');
    Route::put('/kelas/{kelas:slug}/update', [KelasController::class,'update'])->name('UpdateKelas');
    Route::get('/kelas/create/checkSlug',[KelasController::class, 'checkslug'])->middleware(['auth']);

    // Rute untuk dosen
    Route::get('/dosen', [DosenController::class,'index'])->name('dosen');
    Route::get('/dosen/create', [DosenController::class,'create'])->name('createdosen');
    Route::post('/dosen/store', [DosenController::class,'store'])->name('storedosen');
    Route::get('/dosen/{dosen:slug}/edit', [DosenController::class, 'edit'])->name('editdosen');
    Route::get('/dosen/{dosen:slug}/delete', [DosenController::class, 'destroy'])->name('destroyDosen');
    Route::put('/dosen/{dosen:slug}', [DosenController::class,'update'])->name('updateDosen');
    Route::get('/dosen/create/checkSlug',[DosenController::class, 'checkslug'])->middleware(['auth']);
    Route::get('/dosen/grupmodul', [DosenController::class, 'grupmodul'])->name('GrupDosen');
    Route::get('/dosen/grupdosen/{modul:slug}', [DosenController::class, 'index_grupdosen'])->name('indexgrupdosen');
    Route::get('/dosen/grupdosen/create/{modul:slug}', [DosenController::class, 'creategrupdos'])->name('creategrupdos');
    Route::post('/dosen/store/grupdosen', [DosenController::class,'storegrup'])->name('storegrupdosen');
    Route::post('/dosen/grupdosen/delete', [DosenController::class, 'deleteanggota'])->name('hapusanggotagrupdosen');
    Route::get('/dosen/create/golongan', [DosenController::class, 'getGolongan']);
    
    //rute menu mahasiswa
    Route::get('/mahasiswa/{user:username}/delete', [MahasiswaController::class, 'destroy'])->name('MahasiswaHapus');
    Route::post('/mahasiswa/store', [MahasiswaController::class,'store'])->name('Mahasiswa-tambah');
    Route::get('/mahasiswa/create/{kelas:slug}', [MahasiswaController::class, 'create'])->name('CreateKetua');
    Route::get('/mahasiswa/import/{kelas:slug}', [MahasiswaController::class, 'createImport'])->name('CreateKetua');
    Route::post('/mahasiswa/import_excel', [MahasiswaController::class, 'ImportExel'])->name('CreateKetua');
    Route::get('/mahasiswa/{user:username}/edit', [MahasiswaController::class, 'edit'])->name('editMahasiswa');
    Route::put('/Mahasiswa/{user:username}/update', [MahasiswaController::class,'update'])->name('UpdateMahasiswa');

    // Rute-rute untuk modul
    Route::get('/kelasmahasiswa', [KelasController::class, 'kelas_mahasiswa'])->name('kelasmahasiswa');
    Route::resource('/modul', ModulController::class);
    Route::get('/modul/{modul:slug}/delete', [ModulController::class, 'destroy'])->name('destroyModul');
    Route::put('/modul/{modul:slug}', [ModulController::class,'update'])->name('updatemodul');
    Route::get('/modul/create/checkSlug',[ModulController::class, 'checkslug']);
});

//rute untuk mahasiswa
Route::middleware(['auth', 'role:Mahasiswa', 'checksession'])->group(function () {
    Route::get('/ujian-mahasiswa', [MahasiswaController::class, 'ujian_index'])->name('mahasiswa-index');
    Route::get('/masuk-ujian/{ujian:slug}', [MahasiswaController::class,'ujian_masuk'])->name('ujian-mahasiswa-index');
    Route::post('/ujian-data', [MahasiswaController::class,'ujian_data'])->name('ujian-data');
    Route::post('/evaluasi/store', [EvaluasiController::class,'store'])->name('ujian-mahasiswa-tambah');
    Route::post('/evaluasi/sebelum', [EvaluasiController::class,'next'])->name('soal-sebelumnya');
    Route::post('/evaluasi/sesudah', [EvaluasiController::class,'sblm'])->name('soal-berikutnya');
    Route::post('/update-ragu-ragu', [EvaluasiController::class, 'updateRaguRaguSession']);
    Route::put('/evaluasi/update/{id}', [EvaluasiController::class,'update'])->name('ujian-mahasiswa-update');
    Route::get('/selesaiujian', [HasilujianController::class,'selesai_ujian'])->name('ujian.berakhir');
    Route::get('/hasil-ujianmhs', [HasilujianController::class,'hasil_ujianmhs'])->name('hasil-ujianmhs');
    Route::post('/selesaiujian', [HasilujianController::class,'selesai_ujian'])->name('ujian.berakhirpost');
});


Route::middleware(['auth', 'role:Admin|Ketua'])->group(function () {
    // Rute-rute untuk grup soal
    Route::get('/grupsoal', [GrupsoalController::class, 'index'])->name('GrupSoalModul');
    Route::get('/grupsoal/{grup_soal:slug}/delete', [GrupsoalController::class, 'destroy'])->name('destroyGrupsoal');
    Route::get('/grupsoal/{modul:slug}', [GrupsoalController::class, 'index_grup'])->name('indexgrupsoal');
    Route::get('/grupsoal/{grup_soal:slug}/edit', [GrupsoalController::class, 'edit'])->name('GrupSoalEdit');
    Route::put('/grupsoal/{grup_soal:slug}/update', [GrupsoalController::class, 'update'])->name('GrupSoalCreate');
    Route::get('/grupsoal/create/{modul:slug}', [GrupsoalController::class, 'create'])->name('GrupSoalcreate');
    Route::post('/grupsoal/store', [GrupsoalController::class, 'store'])->name('GrupSoalstore');
    Route::get('/grupsoal/create/{modul:slug}/checkSlug', [GrupsoalController::class, 'checkslug']);

    //rute soal
    Route::get('/soal/{soal:slug}/edit', [SoalController::class,'edit'])->name('soal_edit');
    Route::put('/soal/{soal:slug}/update', [SoalController::class,'update'])->name('soal_update');
    Route::put('/soal/{soal:slug}/updategambar', [SoalController::class,'updategambar'])->name('soal_updategambar');
    Route::post('/soal/store', [SoalController::class, 'store'])->name('soal_store');
    Route::post('/soal/storegambar', [SoalController::class, 'storegambar'])->name('soal_store');
    Route::get('/soal/{soal:slug}/delete', [SoalController::class, 'destroy'])->name('destroySoal');
    Route::get('/soal/{grup_soal:slug}', [GrupsoalController::class,'show'])->name('soal_show');
    Route::get('/soal/create/{grup_soal:slug}', [SoalController::class,'create'])->name('soal_create');
    Route::get('/soal/tambahgambar/{grup_soal:slug}', [SoalController::class,'create_gambar'])->name('soal_create');
    Route::post('/soal/import_excel', [SoalController::class, 'ImportExel'])->name('CreateKetua');
    Route::get('/soal/import/{grup_soal:slug}', [SoalController::class,'createImport'])->name('soal_create');
    Route::get('/soal/create1/{grup_soal:slug}', [SoalController::class,'create1'])->name('soal_create');
    Route::get('/soal/create2/{grup_soal:slug}', [SoalController::class,'create2'])->name('soal_create');
    
    Route::resource('/ujian', UjianController::class);
    Route::get('/ujian/{ujian:slug}/delete', [UjianController::class, 'destroy'])->name('destroyUjian');
    Route::put('/ujian/{ujian:slug}/update', [UjianController::class, 'update'])->name('UpdateUjian');
    Route::get('/ujian/create/checkSlug',[UjianController::class, 'checkslug']);
    Route::get('/ujian/getGrupSoal/{modul_id}', [UjianController::class, 'getGrupSoalByModul']);
    
    Route::get('/hasilujian', [HasilujianController::class, 'index'])->name('index-hasilujianmodul');
    Route::get('/hasilujian/{modul:slug}', [HasilujianController::class, 'indexhasil'])->name('showhasilUjian');
    Route::post('/hasilujian/hasil_ujian', [HasilujianController::class, 'hasil'])->name('hasilujian.hasil_ujian');
    Route::get('/evaluasi', [EvaluasiController::class, 'indexmodul'])->name('Evaluasis');
    Route::get('/evaluasi/{modul:slug}', [EvaluasiController::class, 'index'])->name('Evaluasis');
    Route::post('/evaluasi/butirsoal', [EvaluasiController::class, 'evaluasibutirsoal'])->name('Evaluasisbutirsoal');
    Route::post('/evaluasi/soal', [EvaluasiController::class, 'soalEvaluasi'])->name('evaluasi_soal');
    Route::post('/evaluasi/show', [EvaluasiController::class, 'show'])->name('showsoal');
    Route::post('/evaluasi/hitung', [EvaluasiController::class, 'hitung'])->name('hitungnilaisoal');
    Route::post('/evaluasi/reset', [EvaluasiController::class, 'resetsemua'])->name('resetdatasemua');
    Route::post('/evaluasi/resetsemua', [EvaluasiController::class, 'reset'])->name('resetdata');
    Route::post('/evaluasi/hitungsemua', [EvaluasiController::class, 'hitungsemua'])->name('hitungsemuasoal');
    Route::post('/evaluasi/analisisnilai', [EvaluasiController::class, 'Analisisnilai'])->name('analisisnilai');
    Route::post('/evaluasi/soal/semua', [EvaluasiController::class, 'Evaluasisemua'])->name('Evaluasisemua');
    Route::post('/cetak', [HasilujianController::class, 'cetak'])->name('cetak');
    Route::post('/cetaksemua', [EvaluasiController::class, 'cetak'])->name('cetaksemua');
});

require __DIR__.'/auth.php';