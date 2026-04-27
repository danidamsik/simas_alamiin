<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbsensiDetail extends Model
{
    use HasFactory;

    protected $table = 'absensi_detail';

    protected $fillable = [
        'absensi_id',
        'siswa_id',
        'status',
    ];

    public function absensi(): BelongsTo
    {
        return $this->belongsTo(Absensi::class);
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }
}
