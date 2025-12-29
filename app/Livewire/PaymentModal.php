<?php

namespace App\Livewire;

use App\Models\Beneficiary;
use App\Models\Payment;
use App\Models\Voucher;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class PaymentModal extends Component
{
    public $paymentModal = false;

    public Beneficiary $beneficiary;

    public $title = '';

    // Cache para totales de pagos
    public $paymentTotals = [];

    public function delete(string $numtramite)
    {
        try {
            DB::transaction(function () use ($numtramite) {
                // Eager load voucher con payments relacionados
                $voucher = Voucher::with(['payments' => function ($query) use ($numtramite) {
                    $query->where('numtramite', $numtramite);
                }])->where('numtramite', $numtramite)->first();

                if (!$voucher) {
                    throw new \Exception('Voucher no encontrado');
                }

                $payments = $voucher->payments->where('prtdtnpag', $voucher->numpago);

                if ($payments->isEmpty()) {
                    throw new \Exception('No hay pagos asociados a este voucher');
                }

                // Calcular montos de forma más eficiente
                $monto = Payment::where('numtramite', $numtramite)
                    ->where('prtdtnpag', $voucher->numpago)
                    ->where(function ($query) {
                        $query->where('prtdtdesc', 'LIKE', 'CAPI%')
                            ->orWhere('prtdtdesc', 'LIKE', '%AMT%')
                            ->orWhere('prtdtdesc', 'LIKE', '%AMR%');
                    })
                    ->sum('montopago');

                // Buscar cuota para reactivar
                $cuotaParaReactivar = $this->beneficiary->plans()
                    ->where('prppgnpag', $voucher->numpago)
                    ->first()
                    ?? $this->beneficiary->readjustments()
                    ->where('prppgnpag', $voucher->numpago)
                    ->first();

                // ELIMINACION
                Payment::where('numtramite', $numtramite)
                    ->where('prtdtnpag', $voucher->numpago)
                    ->delete();

                $voucher->delete();

                // RESTAURACION
                $this->beneficiary->increment('saldo_credito', $monto);

                if ($cuotaParaReactivar && $cuotaParaReactivar->estado == 'CANCELADO') {
                    $cuotaParaReactivar->update([
                        'estado' => ($cuotaParaReactivar->fecha_ppg < now() ? 'VENCIDO' : 'ACTIVO'),
                    ]);
                }

                session()->flash('success', "Voucher $numtramite eliminado con éxito.");
            });

            // Limpiar cache de totales
            $this->paymentTotals = [];
        } catch (\Exception $e) {
            session()->flash('error', "Error al eliminar el voucher $numtramite: " . $e->getMessage());
        }
    }

    public function mount(Beneficiary $beneficiary, string $title = '')
    {
        $this->beneficiary = $beneficiary;
        $this->beneficiary->load(['vouchers', 'payments']);
        $this->title = $title;

        // Cachear totales de pagos para evitar consultas repetitivas
        $this->calculatePaymentTotals();
    }

    private function calculatePaymentTotals()
    {
        $noLegacy = $this->beneficiary->vouchers()
            ->with('payments')
            ->where(function ($query) {
                $query->whereNull('obs_pago')
                    ->orWhere('obs_pago', '')
                    ->orWhere('obs_pago', '!=', 'LEGACY 22/24');
            })->orderBy('numpago', 'ASC')->get();

        // Precalculamos los pagos por tipo para cada voucher
        $paymentsByVoucher = [];
        foreach ($noLegacy as $voucher) {

            // Usamos la relación ya cargada con eager loading
            $payments = $voucher->payments->filter(fn($p) => $p->prtdtnpag === $voucher->numpago);

            // Avoid LIKE in eager-loaded collections; map once and filter in memory
            $paymentsByVoucher[$voucher->numtramite][$voucher->numpago] = [
                'capital' => $payments->filter(fn($p) => str_starts_with($p->prtdtdesc, 'CAPITAL') && ! str_contains($p->prtdtdesc, 'DIF'))
                    ->sum('montopago'),
                'capital_diferido' => $payments->filter(fn($p) => str_contains($p->prtdtdesc, 'CAPITAL DIF'))
                    ->sum('montopago'),
                'interes_diferido' => $payments->filter(fn($p) => str_contains($p->prtdtdesc, 'INTERES DIF'))
                    ->sum('montopago'),
                'amortizacion' => $payments->filter(fn($p) => str_starts_with($p->prtdtdesc, 'AMR'))
                    ->sum('montopago'),
                'intereses' => $payments->filter(fn($p) => str_starts_with($p->prtdtdesc, 'INTERES') && ! str_contains($p->prtdtdesc, 'DIF'))
                    ->sum('montopago'),
                'seguros' => $payments->filter(fn($p) => str_starts_with($p->prtdtdesc, 'SEGU'))
                    ->sum('montopago'),
                'otros' => $payments->filter(fn($p) => str_starts_with($p->prtdtdesc, 'OTR'))
                    ->sum('montopago') + $payments->filter(fn($p) => str_starts_with($p->prtdtdesc, 'GAS'))
                    ->sum('montopago')
            ];

            $payments = null;
        }

        // Convertir array a Collection para usar métodos de Collection
        $paymentsCollection = collect($paymentsByVoucher)->flatten(1);

        $this->paymentTotals = [
            'capital' => $paymentsCollection->sum('capital'),
            'capital_diferido' => $paymentsCollection->sum('capital_diferido'),
            'interes_diferido' => $paymentsCollection->sum('interes_diferido'),
            'amortizacion' => $paymentsCollection->sum('amortizacion'),
            'interes' => $paymentsCollection->sum('intereses'),
            'seguros' => $paymentsCollection->sum('seguros'),
            'total' => $paymentsCollection->sum('capital') + $paymentsCollection->sum('intereses') + $paymentsCollection->sum('seguros'),
        ];
    }

    public function render()
    {
        // Eager load vouchers con payments relacionados
        $vouchers = $this->beneficiary->vouchers()
            ->with(['payments' => function ($query) {
                $query->select('numtramite', 'prtdtnpag', 'prtdtdesc', 'montopago')
                    ->orderBy('prtdtdesc');
            }])
            ->where(function ($query) {
                $query->whereNull('obs_pago')
                    ->orWhere('obs_pago', '')
                    ->orWhere('obs_pago', '!=', 'LEGACY 22/24');
            })
            ->orderBy('numpago', 'DESC')
            ->get();

        return view('livewire.payment-modal', [
            'vouchers' => $vouchers,
            'paymentTotals' => $this->paymentTotals,
        ]);
    }

    public function placeholder()
    {
        return <<<'HTML'
            <div class="flex items-center justify-center p-4">
                <div class="animate-spin h-6 w-6 border-2 border-blue-500 border-t-transparent rounded-full"></div>
                <span class="ml-3 text-gray-600 text-sm font-light">Cargando...</span>
            </div>
        HTML;
    }
}
