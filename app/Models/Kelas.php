<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Kelas extends Model
{
    use HasFactory;
    use Sluggable;

    protected $guarded = ['id'];

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ??  false, function($query, $search){
            return $query->where('nama_kelas', 'like', '%' . $search . '%')
                  ->orWhere('tahun_ajaran', 'like', '%' . $search . '%')
                  ->orWhereHas('prodi', function($query) use ($search) {
                    $query->where('nama_prodi', 'like', '%' . $search . '%');
                });
        });
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($kelas) {
            $kelas->user()->each(function ($user) {
                $user->delete();
            });
            $kelas->ujian()->each(function ($ujian) {
                $ujian->delete();
            });
        });
    }

    public function user()
    {
        return $this->hasMany(User::class);
    }

    public function ujian()
    {
        return $this->hasMany(Ujian::class);
    }

    public function prodi()
    {
        return $this->belongsTo(Prodi::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'nama_kelas'
            ]
        ];
    }
}
