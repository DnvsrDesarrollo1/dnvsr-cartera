<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Beneficiary extends Model
{
    use HasFactory;

/*     public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'numprestamo', 'idepro');
    }

    public function plans(): HasMany
    {
        return $this->hasMany(Plan::class, 'idepro', 'idepro');
    } */

    public function vouchers()
    {
        return $this->hasMany(Voucher::class, 'numprestamo', 'idepro');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'cod_proy', 'cod_proy_credito');
    }
}
