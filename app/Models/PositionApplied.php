<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PositionApplied extends Model
{
    use HasFactory;

    protected $table = 'position_applied'; // Nama table singular

    protected $fillable = [
        'available_position_id',
        'society_id',
        'apply_date',
        'status',
    ];

    public function availablePosition()
    {
        return $this->belongsTo(AvailablePosition::class);
    }

    public function society()
    {
        return $this->belongsTo(Society::class);
    }
}