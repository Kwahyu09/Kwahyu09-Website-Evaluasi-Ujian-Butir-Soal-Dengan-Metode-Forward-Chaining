<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluasi extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function ujian()
    {
        return $this->belongsTo(ujian::class);
    }

    public function mahasiswa()
    {
        return $this->belongsToMany(mahasiswa::class);
    }

    public function soal()
    {
        return $this->belongsTo(soal::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
