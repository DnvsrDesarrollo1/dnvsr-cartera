<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Helper extends Model
{
    use HasFactory;

    protected $fillable = [
        'idepro',
        'indice',
        'capital',
        'interes',
        'vencimiento',
        'estado',
        'user_id'
    ];

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class, 'idepro', 'idepro');
    }
}
