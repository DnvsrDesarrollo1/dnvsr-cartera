<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Beneficiary extends Model
{
    use HasFactory;

    protected $fillable = [
        "nombre",
        "ci",
        "complemento",
        "expedido",
        "mail",
        "estado",
        "entidad_financiera",
        "cod_proy",
        "idepro",
        "proyecto",
        "genero",
        "fecha_nacimiento",
        "monto_credito",
        "monto_activado",
        "total_activado",
        "gastos_judiciales",
        "saldo_credito",
        "monto_recuperado",
        "fecha_activacion",
        "plazo_credito",
        "tasa_interes",
        "departamento",
        "user_id",
    ];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    //public $incrementing = false;

    public function payments()
    {
        return $this->hasMany(Payment::class, 'numprestamo', 'idepro');
    }

    public function vouchers()
    {
        return $this->hasMany(Voucher::class, 'numprestamo', 'idepro');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'cod_proy', 'cod_proy_credito');
    }

    public function plans()
    {
        return $this->hasMany(Plan::class, 'idepro', 'idepro');
    }

    public function helpers()
    {
        return $this->hasMany(Helper::class, 'idepro', 'idepro');
    }

    public function readjustments()
    {
        return $this->hasMany(Readjustment::class, 'idepro', 'idepro');
    }

    public function images()
    {
        return $this->hasMany(Image::class, 'idepro', 'idepro');
    }

    public function insurance()
    {
        return $this->hasOne(Insurance::class, 'idepro', 'idepro');
    }
}
