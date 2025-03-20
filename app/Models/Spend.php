<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Spend extends Model
{

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class, 'idepro', 'idepro');
    }
}
