<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $fillable = [
        'image_b64',
        'image_json',
        'ci',
        'idepro',
        'request_status',
        'image_xml'
    ];

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class, 'idepro', 'idepro');
    }
}
