<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'numpago',
        'numtramite',
        'numprestamo',
        'fecha_pago',
        'descripcion',
        'montopago',
        'hora_pago',
        'siguiente_pago',
        'agencia_pago',
        'departamento_pago',
        'observaciones',
    ];

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class, 'numprestamo', 'idepro');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'numtramite', 'numtramite');
    }

}
