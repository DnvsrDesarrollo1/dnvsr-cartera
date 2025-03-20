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
        //'prtdtfpro',
        'agencia_pago',
        'depto_pago',
        'obs_pago',
    ];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class, 'numprestamo', 'idepro');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'numtramite', 'numtramite');
    }

}
