<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    use HasFactory;

    protected $with = 'kelas';

    protected $fillable = [
        'user_id', 'npm'
    ];

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ??  false, function($query, $search){
            return $query->where('npm', 'like', '%' . $search . '%')
                  ->orWhere('nama', 'like', '%' . $search . '%');
        });
    }

    public function kelas()
    {
        return $this->belongsTo(kelas::class);
    }

    public function evaluasi()
    {
        return $this->hasOne(evaluasi::class);
    }
}
