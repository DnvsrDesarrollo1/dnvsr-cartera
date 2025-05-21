<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settlement extends Model
{
    /**
     * Los atributos que NO son asignables en masa.
     *
     * @var array
     */
    protected $guarded = [
        'id',
        'created_at',
        'updated_at'
    ];

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array
     */
    protected $fillable = [
        'capital_inicial',
        'capital_final',
        'capital_diferido',
        'interes',
        'interes_devengado',
        'interes_diferido',
        'seguro',
        'seguro_devengado',
        'gastos_judiciales',
        'gastos_administrativos',
        'otros',
        'plan_de_pagos',
        'estado',
        'comentarios',
        'observaciones',
        'anexos',
        'beneficiary_id',
        'user_id',
        'descuento'
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }

    public function beneficiary()
    {
        return $this->belongsTo(\App\Models\Beneficiary::class, 'beneficiary_id','id');
    }
}
