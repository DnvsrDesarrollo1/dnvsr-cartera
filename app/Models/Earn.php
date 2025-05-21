<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Earn extends Model
{

    protected $fillable = [
        'idepro',
        'capital',
        'interes',
        'seguro',
        'estado'
    ];

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class, 'idepro', 'idepro');
    }
}
