<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'session_token',
        'nik',
        'nip',
        'npm',
        'nama',
        'username',
        'role',
        'kelas_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? false, function ($query, $search) {
            return $query->where('username', 'like', '%' . $search . '%')
                ->orWhere('nama', 'like', '%' . $search . '%')
                ->orWhere('nik', 'like', '%' . $search . '%')
                ->orWhere('nip', 'like', '%' . $search . '%')
                ->orWhere('npm', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%');
        });
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($user) {
            $user->modul()->each(function ($modul) {
                $modul->delete();
            });
            $user->grup_soal()->each(function ($grupSoal) {
                $grupSoal->delete();
            });
            $user->ujian()->each(function ($ujian) {
                $ujian->delete();
            });
            $user->hasilujian()->each(function ($hasilujian) {
                $hasilujian->delete();
            });
            $user->kelompok_mahasiswa()->each(function ($kelompokmahasiswa) {
                $kelompokmahasiswa->delete();
            });
            $user->evaluasi()->each(function ($evaluasi) {
                $evaluasi->delete();
            });
        });
    }

    public function modul()
    {
        return $this->hasMany(Modul::class);
    }

    public function grup_soal()
    {
        return $this->hasMany(Grup_soal::class);
    }

    public function ujian()
    {
        return $this->hasMany(Ujian::class);
    }

    public function hasilujian()
    {
        return $this->hasMany(HasilUjian::class);
    }

    public function kelompok_mahasiswa()
    {
        return $this->hasMany(Kelompok_mahasiswa::class);
    }

    public function evaluasi()
    {
        return $this->hasMany(Evaluasi::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }
}