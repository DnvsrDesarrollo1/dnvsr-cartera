<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Readjustment extends Model
{

    use HasFactory;

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
        'user_id',
    ];

    protected $guarded = [];

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class, 'idepro', 'idepro');
    }
}
