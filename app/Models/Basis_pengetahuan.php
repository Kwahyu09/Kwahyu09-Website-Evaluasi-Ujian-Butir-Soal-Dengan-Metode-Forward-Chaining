<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Basis_pengetahuan extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function analisis_klasifikasi()
    {
        return $this->hasMany(Analisis_klasifikasi::class);
    }
    public function kesimpulan_analisis()
    {
        return $this->hasMany(Kesimpulan_analisis::class);
    }
}
