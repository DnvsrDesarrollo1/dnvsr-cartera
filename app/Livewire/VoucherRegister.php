<?php

namespace App\Livewire;

use App\Traits\FinanceTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Component;

class VoucherRegister extends Component
{
    use FinanceTrait;

    public $idepro;
    public $beneficiario;
    public $cuota;
    public $numpago;
    public $numtramite;
    public $numprestamo;
    public $fecha_pago;
    public $descripcion;
    public $capital;
    public $interes;
    public $interes_devg;
    public $seguro;
    public $seguro_devg;
    public $otros;
    public $hora_pago;
    public $prtdtfpro;
    public $agencia_pago;
    public $depto_pago;
    public $obs_pago;
    public $capital_diff;
    public $interes_diff;
    public $cuota_diff;
    public $enableDiffFields = false;
    public $totalpagado = 0;
    public $comprobanteDuplicado = false;
    public $voucherModal = false;

    protected $rules = [
        'numpago' => 'required|integer|min:1|max:300',
        'numtramite' => 'required|unique:vouchers,numtramite',
        'numprestamo' => 'required',
        'fecha_pago' => 'required|date',
        'descripcion' => 'required|string|max:255',
        'capital' => 'required|numeric|min:0',
        'interes' => 'required|numeric|min:0',
        'interes_devg' => 'required|numeric|min:0',
        'seguro' => 'required|numeric|min:0',
        'seguro_devg' => 'required|numeric|min:0',
        'hora_pago' => 'required',
        'agencia_pago' => 'required|string',
        'depto_pago' => 'required|string',
        'obs_pago' => 'required|string',
    ];

    protected $messages = [
        'numtramite.unique' => 'Este número de comprobante ya existe en el sistema.',
    ];


    /**
     * Validación en tiempo real del número de comprobante
     */
    public function updatedNumtramite($value)
    {
        $this->comprobanteDuplicado = \App\Models\Voucher::where('numtramite', $value)->exists();
    }

    /**
     * Actualización automática del capital cuando cambia el total pagado
     */
    public function updatedTotalpagado($value)
    {
        if ($value > 0) {
            $this->capital = max(0, $value - $this->interes - $this->interes_devg - $this->seguro - $this->seguro_devg - $this->otros);
        }
    }

    /**
     * Propiedad computada para el monto total a pagar
     */
    #[Computed]
    public function montopago()
    {
        if ($this->enableDiffFields) {
            return round($this->capital_diff + $this->interes_diff, 2);
        }

        return round(
            $this->capital +
                $this->interes +
                $this->interes_devg +
                $this->seguro +
                $this->seguro_devg +
                $this->otros,
            2
        );
    }

    /**
     * Guardar el voucher y los pagos asociados
     */
    public function save()
    {
        try {
            DB::beginTransaction();

            // Generar backup del plan de pagos
            $this->generatePaymentPlanBackup();

            if ($this->enableDiffFields) {
                $this->processDeferredPayment();
            } else {
                $this->validate();
                $this->processStandardPayment();
            }

            DB::commit();

            session()->flash('success', 'Voucher registrado exitosamente.');
            return redirect(request()->header('Referer'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al registrar voucher: ' . $e->getMessage(), [
                'numtramite' => $this->numtramite,
                'numprestamo' => $this->numprestamo,
            ]);

            session()->flash('error', 'Error al registrar el voucher. Por favor, intente nuevamente.');
            return;
        }
    }

    /**
     * Generar backup CSV del plan de pagos actual
     */
    private function generatePaymentPlanBackup()
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $tempDir = storage_path('app/public/temp/');

        if (!File::isDirectory($tempDir)) {
            File::makeDirectory($tempDir, 0755, true, true);
        }

        $filename = "payment_plan_backup_{$this->numprestamo}_{$timestamp}.csv";
        $fullPath = "{$tempDir}/{$filename}";

        $currentPlan = $this->beneficiario->getCurrentPlan('INACTIVO', '!=');

        $file = fopen($fullPath, 'w');
        fputcsv($file, ['idepro', 'prppgnpag', 'fecha_ppg', 'prppgcapi', 'prppginte', 'prppggral', 'prppgsegu', 'prppgcarg', 'prppgotro', 'prppgtota', 'estado']);

        foreach ($currentPlan as $payment) {
            fputcsv($file, [
                $payment->idepro,
                $payment->prppgnpag,
                $payment->fecha_ppg,
                $payment->prppgcapi,
                $payment->prppginte,
                $payment->prppggral,
                $payment->prppgsegu,
                $payment->prppgcarg,
                $payment->prppgotro,
                $payment->prppgtota,
                $payment->estado,
            ]);
        }
        fclose($file);

        // Programar eliminación del archivo después de 30 minutos
        dispatch(function () use ($fullPath) {
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        })->delay(now()->addMinutes(30));
    }

    /**
     * Procesar pago diferido
     */
    private function processDeferredPayment()
    {
        $helpers = $this->beneficiario->helpers()->where('estado', 'ACTIVO')->get();
        $shouldCancelRemaining = $this->capital_diff >= $helpers->sum('capital') &&
            $this->interes_diff >= $helpers->sum('interes');

        // Crear pagos diferidos
        $this->createPayment(1, 'CAPITAL DIFERIDO', $this->capital_diff, $this->cuota_diff);
        $this->createPayment(2, 'INTERES DIFERIDO', $this->interes_diff, $this->cuota_diff);

        // Crear voucher
        $this->createVoucher($this->montopago(), $this->cuota_diff);

        // Actualizar helper de la cuota diferida
        $helper = $this->beneficiario->helpers()
            ->where('estado', 'ACTIVO')
            ->where('indice', $this->cuota_diff)
            ->first();

        if ($helper) {
            $helper->update([
                'capital' => $shouldCancelRemaining ? $this->capital_diff : $helper->capital,
                'interes' => $shouldCancelRemaining ? $this->interes_diff : $helper->interes,
                'estado' => 'CANCELADO',
                'user_id' => Auth::id() ?? 1,
            ]);
        }

        // Cancelar cuotas restantes si corresponde
        if ($shouldCancelRemaining) {
            $this->beneficiario->helpers()
                ->where('estado', 'ACTIVO')
                ->where('indice', '>', $this->cuota_diff)
                ->update([
                    'capital' => 0,
                    'interes' => 0,
                    'estado' => 'CANCELADO',
                    'user_id' => Auth::id() ?? 1,
                ]);
        }

        // Actualizar saldo del beneficiario
        $this->beneficiario->update([
            'saldo_credito' => max(0, $this->beneficiario->saldo_credito - $this->capital_diff),
            'user_id' => Auth::id(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Procesar pago estándar
     */
    private function processStandardPayment()
    {
        // Crear todos los pagos
        $this->createPayment(1, 'CAPITAL', $this->capital, $this->numpago, 1);
        $this->createPayment(2, 'INTERES', $this->interes, $this->numpago, 2);
        $this->createPayment(3, 'INTERES DEVENGADO', $this->interes_devg, $this->numpago, 2);
        $this->createPayment(4, 'SEGURO DESGRAVAMEN', $this->seguro, $this->numpago, 2);
        $this->createPayment(5, 'SEGURO DESGRAVAMEN DEVENGADO', $this->seguro_devg, $this->numpago, 37, 21);
        $this->createPayment(6, 'OTROS', $this->otros, $this->numpago, 37, 21);

        // Crear voucher
        $this->createVoucher($this->montopago(), $this->numpago);

        // Actualizar cuota
        $this->cuota->update([
            'prppgcapi' => $this->capital,
            'prppgtota' => $this->montopago(),
            'estado' => 'CANCELADO',
            'user_id' => Auth::id(),
        ]);

        // Actualizar saldo del beneficiario
        $newSaldo = max(0, $this->beneficiario->saldo_credito - $this->capital);
        $this->beneficiario->update([
            'saldo_credito' => $newSaldo,
        ]);

        // Actualizar plan si es necesario
        if ($this->totalpagado > 0 || $newSaldo <= 0) {
            $this->updatePaymentPlan($newSaldo);
        }

        // Marcar beneficiario como cancelado si el saldo es 0
        if ($newSaldo <= 0) {
            $this->markBeneficiaryAsCancelled();
        }
    }

    /**
     * Crear un registro de pago
     */
    private function createPayment(int $item, string $description, float $amount, int $numpago, int $ccon = 1, int $pref = 20)
    {
        \App\Models\Payment::create([
            'numtramite' => $this->numtramite,
            'prtdtitem' => $item,
            'numprestamo' => $this->numprestamo,
            'prtdtpref' => $pref,
            'prtdtccon' => $ccon,
            'fecha_pago' => $this->fecha_pago,
            'prtdtdesc' => $description,
            'montopago' => $amount,
            'prtdtuser' => 'AEV-PVS VENTANILLA',
            'hora_pago' => $this->hora_pago,
            'prtdtfpro' => null,
            'prtdtnpag' => $numpago,
            'depto_pago' => $this->depto_pago,
            'obs_pago' => $this->obs_pago,
        ]);
    }

    /**
     * Crear un voucher
     */
    private function createVoucher(float $montopago, int $numpago)
    {
        \App\Models\Voucher::create([
            'agencia_pago' => $this->agencia_pago . ' : (VENTANILLA) ' . Auth::user()->name,
            'descripcion' => $this->descripcion,
            'fecha_pago' => $this->fecha_pago,
            'hora_pago' => $this->hora_pago,
            'montopago' => $montopago,
            'numpago' => $numpago,
            'numprestamo' => $this->numprestamo,
            'numtramite' => $this->numtramite,
            'depto_pago' => $this->depto_pago,
            'obs_pago' => $this->obs_pago,
        ]);
    }

    /**
     * Actualizar el plan de pagos
     */
    private function updatePaymentPlan(float $newSaldo)
    {
        $planVigente = $this->beneficiario->getCurrentPlan('CANCELADO', '!=');
        $this->beneficiario->refresh();
        $this->beneficiario->load('plans');
        $totalPlan = $this->beneficiario->plans->sum('prppgcapi');

        $this->actualizarPlanActual($this->numprestamo, $newSaldo, $planVigente, $totalPlan);
    }

    /**
     * Marcar beneficiario como cancelado
     */
    private function markBeneficiaryAsCancelled()
    {
        $planVigente = $this->beneficiario->getCurrentPlan('ACTIVO');
        $totalPlan = $this->beneficiario->getCurrentPlan('INACTIVO', '!=')->sum('prppgcapi');

        $this->beneficiario->refresh();
        $this->actualizarPlanActual($this->numprestamo, 0, $planVigente, $totalPlan);

        $this->beneficiario->update([
            'saldo_credito' => 0,
            'estado' => 'CANCELADO',
            'user_id' => Auth::id(),
            'updated_at' => now(),
        ]);
    }


    public function mount($idepro)
    {
        $p = \App\Models\Plan::where('idepro', $idepro)
            ->whereIn('estado', ['VENCIDO', 'ACTIVO'])
            ->orderBy('fecha_ppg', 'asc')
            ->first();

        if (! $p) {
            $p = \App\Models\Readjustment::where('idepro', $idepro)
                ->whereIn('estado', ['VENCIDO', 'ACTIVO'])
                ->orderBy('fecha_ppg', 'asc')
                ->first();
        }

        $this->beneficiario = \App\Models\Beneficiary::where('idepro', $idepro)
            ->first();

        $this->cuota = $p;

        if ($p != null) {
            $this->numpago = $p->prppgnpag;
            $this->numtramite = null;
            $this->numprestamo = $p->idepro;
            $this->fecha_pago = $p->fecha_ppg;
            $this->descripcion = null;
            $this->montopago = round($p->prppgtota, 4);
            $this->capital = round($p->prppgcapi, 4);
            $this->interes = round($p->prppginte, 4);
            $this->interes_devg = round($p->prppggral, 4);
            $this->seguro = round($p->prppgsegu, 4);
            $this->seguro_devg = round($p->prppgcarg, 4);
            $this->otros = round($p->prppgotro, 4);
            $this->hora_pago = now();
            $this->prtdtfpro = null;
            $this->agencia_pago = null;
            $this->depto_pago = null;
            $this->obs_pago = null;
        }
    }


    public function render()
    {
        return view('livewire.voucher-register');
    }
}
