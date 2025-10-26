<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AvailablePosition extends Model
{
    use HasFactory;

    protected $table = 'available_position'; // Nama table singular

    protected $fillable = [
        'company_id',
        'position_name',
        'capacity',
        'description',
        'submission_start_date',
        'submission_end_date',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function positionApplieds()
    {
        return $this->hasMany(PositionApplied::class);
    }
}