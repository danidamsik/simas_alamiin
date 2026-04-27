<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiswaKelasHistory extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    protected $table = 'siswa_kelas_history';

    protected $fillable = [
        'siswa_id',
        'kelas_id',
        'periode_id',
        'tanggal_masuk',
        'tanggal_keluar',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'tanggal_keluar' => 'date',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function periode(): BelongsTo
    {
        return $this->belongsTo(Periode::class);
    }
}
