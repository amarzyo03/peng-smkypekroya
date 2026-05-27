<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class siswa extends Model
{
    use HasFactory;
    protected $table = 'siswas';
    protected $fillable = [
        'nis',
        'nisn',
        'nama',
        'no_ujian',
        'kompetensi_keahlian',
        'status',
    ];
}
