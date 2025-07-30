<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre_proyecto',
        'subprograma',
        'user_id',
        'observaciones',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function beneficiaries()
    {
        return $this->hasMany(Beneficiary::class,'proyecto', 'nombre_proyecto');
    }
}
