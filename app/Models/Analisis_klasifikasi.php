<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Analisis_klasifikasi extends Model
{
    use HasFactory;
    
    protected $guarded = ['id'];
    public function basis_pengetahuan()
    {
        return $this->belongsTo(Basis_pengetahuan::class);
    }
    public function ujian()
    {
        return $this->belongsTo(Ujian::class);
    }
    public function soal()
    {
        return $this->belongsTo(soal::class);
    }
}
