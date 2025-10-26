<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Society extends Model
{
    use HasFactory;

    protected $table = 'society';

    protected $fillable = [
        'user_id',
        'name',
        'address',
        'phone',
        'date_of_birth',
        'gender',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function portofolios()
    {
        return $this->hasMany(Portofolio::class);
    }

    public function positionApplieds()
    {
        return $this->hasMany(PositionApplied::class);
    }
}