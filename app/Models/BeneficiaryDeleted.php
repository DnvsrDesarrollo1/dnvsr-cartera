<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeneficiaryDeleted extends Model
{
    protected $table = 'beneficiarios_deleted';

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
        "cod_fondesif",
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
}
