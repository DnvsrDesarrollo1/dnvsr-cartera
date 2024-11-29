<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'proy_cod',
        'cod_proy_credito',
        'proy_nombre',
        'proy_subprograma',
        'proy_numActa',
        'proy_fechaAprobacion',
        'proy_numViviendas',
        'proy_estado',
        'proy_modalidad',
        'entidad_inter_finan',
        'proy_programa',
        'fecha_ini_obra',
        'fecha_fin_obra',
        'proy_viv_concluidas',
        'proy_viv_cartera',
        'proy_componente',
        'proy_depto',
        'proy_provincia',
        'proy_municipio',
        'proy_ubicacion',
        'proy_avance_finan',
        'proy_avance_fis',
    ];

    public function beneficiaries(): HasMany
    {
        return $this->hasMany(Beneficiary::class, 'cod_proy', 'cod_proy_credito');
    }
}
