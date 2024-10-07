<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    public function beneficiaries(): HasMany
    {
        return $this->hasMany(Beneficiary::class, 'cod_proy', 'cod_proy_credito');
    }
}
