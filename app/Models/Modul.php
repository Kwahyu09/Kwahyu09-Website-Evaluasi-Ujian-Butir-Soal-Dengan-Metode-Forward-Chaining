<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Modul extends Model
{
    use HasFactory;
    use Sluggable;

    protected $guarded = ['id'];

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ??  false, function($query, $search){
            return $query->where('kd_modul', 'like', '%' . $search . '%')
                  ->orWhere('nama_modul', 'like', '%' . $search . '%')
                  ->orWhere('semester', 'like', '%' . $search . '%')
                  ->orWhere('sks', 'like', '%' . $search . '%')
                  ->orWhereHas('user', function($query) use ($search) {
                    $query->where('nama', 'like', '%' . $search . '%');
                });
        });
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($modul) {
            // Hapus grup soal yang berelasi dengan modul
            $modul->grup_soal()->each(function ($grupSoal) {
                $grupSoal->delete();
            });

            // Hapus ujian yang berelasi dengan modul
            $modul->ujian()->each(function ($ujian) {
                $ujian->delete();
            });
        });
    }

    public function grup_soal()
    {
        return $this->hasMany(grup_soal::class);
    }

    public function ujian()
    {
        return $this->hasMany(ujian::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
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
}
