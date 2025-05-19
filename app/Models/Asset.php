<?php

namespace App\Models;


    
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Asset extends Model
{

    use HasFactory;

    protected $fillable = [
        'name',
        'serial_number',
        'kategori', // Tambahkan semua field yang akan di isi
        'image', // Jika kamu pakai relasi kategori
    ];

    
}
