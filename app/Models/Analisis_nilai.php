<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Analisis_nilai extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function ujian()
    {
        return $this->belongsTo(Ujian::class);
    }
    public function soal()
    {
        return $this->belongsTo(soal::class);
    }
}
