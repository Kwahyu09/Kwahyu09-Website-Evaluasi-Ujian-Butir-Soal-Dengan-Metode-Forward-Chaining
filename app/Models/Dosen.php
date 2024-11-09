<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Dosen extends Model
{
    use HasFactory;
    use Sluggable;
    
    protected $guarded = ['id'];

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ??  false, function($query, $search){
            return $query->where('nip', 'like', '%' . $search . '%')
                  ->orWhere('nama_dos', 'like', '%' . $search . '%')
                  ->orWhere('jenis_kel', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhereHas('prodi', function($query) use ($search) {
                        $query->where('nama_prodi', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('golongan', function($query) use ($search) {
                        $query->where('pangkat', 'like', '%' . $search . '%')
                            ->orWhere('golongan', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('jabatan', function($query) use ($search) {
                        $query->where('keterangan', 'like', '%' . $search . '%');
                    });
        });
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($grupdosen) {
            $grupdosen->grup_dosen()->delete();
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
    
    public function prodi()
    {
        return $this->belongsTo(Prodi::class);
    }
    
    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }
    
    public function golongan()
    {
        return $this->belongsTo(Golongan::class);
    }

    public function grup_dosen()
    {
        return $this->hasMany(Grup_dosen::class);
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'nama_dos'
            ]
        ];
    }
}
