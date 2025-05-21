<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Illuminate\Support\Facades\Http;

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

    private const API_BASE_URL = 'http://20.20.1.55:8080/api/';
    private const TIMEOUT = 5; // seconds
    private const RETRY_TIMES = 2;
    private const RETRY_DELAY = 100; // milliseconds

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

    public function earns()
    {
        return $this->hasMany(Earn::class, 'idepro', 'idepro');
    }

    public function spends()
    {
        return $this->hasMany(Spend::class, 'idepro', 'idepro');
    }

    public function settlement()
    {
        return $this->hasOne(Settlement::class, 'beneficiary_id', 'id');
    }

    public function hasPlan(): bool
    {
        return ($this->plans()->where('estado', '<>', 'INACTIVO')->exists()
            || $this->readjustments()->where('estado', '<>', 'INACTIVO')->exists());
    }

    public function getCurrentPlan(string $status = 'ACTIVO', string $wildcard = '=')
    {
        $plan = $this->plans()
            ->where('estado', "$wildcard", (strtoupper($wildcard) === 'LIKE' ? "%$status%" : $status))
            ->orderBy('fecha_ppg', 'asc')
            ->get();

        if ($plan->isEmpty()) {
            $plan = $this->readjustments()
                ->where('estado', "$wildcard", (strtoupper($wildcard) === 'LIKE' ? "%$status%" : $status))
                ->orderBy('fecha_ppg', 'asc')
                ->get();
        }

        return $plan;
    }

    public function getFirstQuote()
    {
        $quota = $this->plans()
            ->where('estado', "!=", 'CANCELADO')
            ->orderBy('fecha_ppg', 'asc')
            ->get();

        if ($quota->isEmpty()) {
            $quota = $this->readjustments()
                ->where('estado', "!=", 'CANCELADO')
                ->orderBy('fecha_ppg', 'asc')
                ->get();
        }

        return $quota->first();
    }

    public function hasVouchers(): bool
    {
        return ($this->vouchers()->exists());
    }

    /**
     * Get credit status
     */
    public function statusCredito(): array
    {
        return $this->makeApiRequest('credito/' . $this->idepro);
    }

    /**
     * Get social status
     */
    public function statusSocial(string $codigo): array
    {
        return $this->makeApiRequest('social/' . $codigo);
    }

    /**
     * Get legal status
     */
    public function statusLegal(string $codigo): array
    {
        return $this->makeApiRequest('legal/' . $codigo);
    }

    /**
     * Shared API request logic
     */
    private function makeApiRequest(string $endpoint): array
    {
        try {
            $response = Http::asForm()
                ->acceptJson()
                ->timeout(self::TIMEOUT)
                ->retry(self::RETRY_TIMES, self::RETRY_DELAY)
                ->get(self::API_BASE_URL . $endpoint);

            return $response->throw()->json()['data'] ?? [];
        } catch (\Exception $e) {
            // Log error if needed
            \Log::error("API request failed for {$endpoint}: " . $e->getMessage());
            return [];
        }
    }
}
