<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $table = 'company'; // Nama table singular

    protected $fillable = [
        'user_id',
        'name',
        'address',
        'phone',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function availablePositions()
    {
        return $this->hasMany(AvailablePosition::class);
    }
}