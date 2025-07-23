<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'numtramite',
        'numprestamo',
        'prtdtpref',
        'prtdtccon',
        'fecha_pago',
        'prtdtdesc',
        'montopago',
        'prtdtuser',
        'hora_pago',
        'prtdtfpro',
        'prtdtnpag',
        'depto_pago',
        'observacion',
    ];

    protected $guarded = [];


    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class, 'numprestamo', 'idepro');
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class, 'numtramite', 'numtramite');
    }


}
