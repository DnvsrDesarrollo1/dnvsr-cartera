<?php

namespace App\Livewire;

use App\Models\Beneficiary;
use Livewire\Component;

class PaymentModal extends Component
{
    public $paymentModal = false;

    public Beneficiary $beneficiary;

    public $title = '';

    public function delete(string $numtramite)
    {
        try {
            $voucher = \App\Models\Voucher::where('numtramite', $numtramite)->first();
            if ($voucher) {
                $payments = \App\Models\Payment::where('numtramite', $numtramite)->where('prtdtnpag', $voucher->numpago)->get();

                if ($payments->count() > 0) {

                    $regCap = \App\Models\Payment::where('numtramite', $numtramite)
                        ->where('prtdtdesc', 'LIKE', 'CAPI%')
                        ->sum('montopago');

                    $regAmrt = \App\Models\Payment::where('numtramite', $numtramite)
                        ->where('prtdtdesc', 'LIKE', '%AMT%')
                        ->sum('montopago');

                    $regAmtz = \App\Models\Payment::where('numtramite', $numtramite)
                        ->where('prtdtdesc', 'LIKE', '%AMR%')
                        ->sum('montopago');

                    $monto = $regCap + $regAmrt + $regAmtz;

                    $cuotaParaReactivar = $this->beneficiary->plans()->where('prppgnpag', $voucher->numpago)->first()
                        ?? $this->beneficiary->readjustments()->where('prppgnpag', $voucher->numpago)->first();

                    // ELIMINACION

                    $voucher->delete();

                    foreach ($payments as $payment) {
                        $payment->delete();
                    }

                    // RESTAURACION

                    $this->beneficiary->update([
                        'saldo_credito' => $this->beneficiary->saldo_credito + $monto,
                    ]);

                    if ($cuotaParaReactivar->estado == 'CANCELADO') {
                        $cuotaParaReactivar->update([
                            'estado' => ($cuotaParaReactivar->fecha_ppg < now() ? 'VENCIDO' : 'ACTIVO'),
                        ]);
                    }

                    session()->flash('success', "Voucher $numtramite eliminado con éxito.");
                }
            }
        } catch (\Exception $e) {
            session()->flash('error', "Error al eliminar el voucher $numtramite: ".$e->getMessage());
        }
    }

    public function mount(Beneficiary $beneficiary, string $title = '')
    {
        $this->beneficiary = $beneficiary;
        $this->title = $title;
    }

    public function render()
    {
        $vouchers = $this->beneficiary->vouchers()
            ->where(function ($query) {
                $query->whereNull('obs_pago')
                    ->orWhere('obs_pago', '')
                    ->orWhere('obs_pago', '!=', 'LEGACY 22/24');
            })->orderBy('numpago', 'DESC')->get();

        return view('livewire.payment-modal', compact('vouchers'));
    }

    public function placeholder()
    {
        return <<< 'HTML'
            <div class="flex items-center justify-center p-4">
                <div class="animate-pulse h-6 w-6 bg-blue-500 rounded-full"></div>
                <span class="ml-3 text-gray-600 text-sm font-light">Cargando</span>
            </div>
        HTML;
    }
}
