<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class soal extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ??  false, function($query, $search){
            return $query->where('kode_soal', 'like', '%' . $search . '%')
                  ->orWhere('pertanyaan', 'like', '%' . $search . '%')
                  ->orWhere('opsi_a', 'like', '%' . $search . '%')
                  ->orWhere('opsi_b', 'like', '%' . $search . '%')
                  ->orWhere('opsi_c', 'like', '%' . $search . '%')
                  ->orWhere('opsi_d', 'like', '%' . $search . '%')
                  ->orWhere('jawaban', 'like', '%' . $search . '%')
                  ->orWhere('bobot', 'like', '%' . $search . '%');
        });
    }

    public function grup_soal()
    {
        return $this->belongsTo(grup_soal::class);
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
    
    public function kesimpulan_analisis()
    {
        return $this->hasMany(kesimpulan_analisis::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($soal) {
            // Hapus evaluasi yang berelasi dengan soal
            $soal->evaluasi()->each(function ($evaluasi) {
                $evaluasi->delete();
            });

            // Hapus analisis klasifikasi yang berelasi dengan soal
            $soal->analisis_klasifikasi()->each(function ($analisis_klasifikasi) {
                $analisis_klasifikasi->delete();
            });
            
            // Hapus analisis nilai yang berelasi dengan soal
            $soal->analisis_nilai()->each(function ($analisis_nilai) {
                $analisis_nilai->delete();
            });
            
            // Hapus kesimpulan_analisis yang berelasi dengan soal
            $soal->kesimpulan_analisis()->each(function ($kesimpulan_analisis) {
                $kesimpulan_analisis->delete();
            });
        });
    }
}
