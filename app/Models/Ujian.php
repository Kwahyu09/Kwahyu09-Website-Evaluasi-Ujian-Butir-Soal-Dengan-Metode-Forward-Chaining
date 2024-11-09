<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Ujian extends Model
{
    use HasFactory;
    use Sluggable;

    protected $guarded = ['id'];


    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ??  false, function($query, $search){
            return $query->where('kd_ujian', 'like', '%' . $search . '%')
                  ->orWhere('nama_ujian', 'like', '%' . $search . '%')
                  ->orWhere('tanggal', 'like', '%' . $search . '%')
                  ->orWhere('waktu_mulai', 'like', '%' . $search . '%')
                  ->orWhere('waktu_selesai', 'like', '%' . $search . '%')
                  ->orWhereHas('modul', function($query) use ($search) {
                        $query->where('nama_modul', 'like', '%' . $search . '%');
                    })
                  ->orWhereHas('grup_soal', function($query) use ($search) {
                        $query->where('nama_grup', 'like', '%' . $search . '%');
                    })
                  ->orWhereHas('kelas', function($query) use ($search) {
                        $query->where('nama_kelas', 'like', '%' . $search . '%');
                    });
        });
    }

    public function modul()
    {
        return $this->belongsTo(modul::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function grup_soal()
    {
        return $this->belongsTo(Grup_soal::class);
    }

    public function evaluasi()
    {
        return $this->hasMany(evaluasi::class);
    }

    public function analisis_klasifikasi()
    {
        return $this->hasMany(analisis_klasifikasi::class);
    }

    public function analisis_nilai()
    {
        return $this->hasMany(analisis_nilai::class);
    }
    public function hasil_ujian()
    {
        return $this->hasMany(Hasilujian::class);
    }
    public function kelompok_mahasiswa()
    {
        return $this->hasMany(kelompok_mahasiswa::class);
    }
    public function kesimpulan_analisis()
    {
        return $this->hasMany(kesimpulan_analisis::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'nama_modul'
            ]
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($ujian) {
            // Hapus evaluasi yang berelasi dengan ujian
            $ujian->evaluasi()->each(function ($evaluasi) {
                $evaluasi->delete();
            });

            // Hapus analisis klasifikasi yang berelasi dengan ujian
            $ujian->analisis_klasifikasi()->each(function ($analisis_klasifikasi) {
                $analisis_klasifikasi->delete();
            });
            
            // Hapus analisis nilai yang berelasi dengan ujian
            $ujian->analisis_nilai()->each(function ($analisis_nilai) {
                $analisis_nilai->delete();
            });
            
            // Hapus hasil_ujian yang berelasi dengan ujian
            $ujian->hasil_ujian()->each(function ($hasil_ujian) {
                $hasil_ujian->delete();
            });
            
            // Hapus kelompok_mahasiswa yang berelasi dengan ujian
            $ujian->kelompok_mahasiswa()->each(function ($kelompok_mahasiswa) {
                $kelompok_mahasiswa->delete();
            });
            
            // Hapus kesimpulan_analisis yang berelasi dengan ujian
            $ujian->kesimpulan_analisis()->each(function ($kesimpulan_analisis) {
                $kesimpulan_analisis->delete();
            });
        });
    }
}

