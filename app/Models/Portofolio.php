<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Portofolio extends Model
{
    use HasFactory;

    protected $table = 'portofolio'; // Nama table singular

    protected $fillable = [
        'society_id',
        'skill',
        'description',
        'file',
    ];

    public function society()
    {
        return $this->belongsTo(Society::class);
    }
}