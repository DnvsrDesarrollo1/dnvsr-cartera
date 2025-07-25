<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Plan extends Model
{
    use HasFactory;

    protected $casts = [
        'fecha_ppg' => 'date',
    ];

    protected $fillable = [
        'idepro',
        'fecha_ppg',
        'prppgnpag',
        'prppgcapi',
        'prppginte',
        'prppggral',
        'prppgsegu',
        'prppgotro',
        'prppgcarg',
        'prppgtota',
        'prppgahor',
        'prppgmpag',
        'estado',
        'user_id'
    ];

    public function beneficiary(): BelongsTo
    {
        return $this->belongsTo(Beneficiary::class, 'idepro', 'idepro');
    }
}
