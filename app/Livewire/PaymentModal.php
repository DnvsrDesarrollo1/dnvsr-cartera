<?php

namespace App\Livewire;

use App\Models\Beneficiary;
use Livewire\Component;

class PaymentModal extends Component
{
    public $isOpen = false;

    public $confirmingSave = false;

    public Beneficiary $beneficiary;
    public $title = '';

    public function delete(string $numtramite)
    {
        $voucher = \App\Models\Voucher::where('numtramite', $numtramite)->first();
        if ($voucher) {
            $payments = \App\Models\Payment::where('numtramite', $numtramite)->get();
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
                        'estado' => ($cuotaParaReactivar->fecha_ppg < now() ? 'VENCIDO' : 'ACTIVO')
                    ]);
                }

                session()->flash('success', "Voucher $numtramite eliminado con éxito.");
            }
        }
    }

    public function mount(Beneficiary $beneficiary, string $title = '')
    {
        $this->beneficiary = $beneficiary;
        $this->title = $title;
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    public function render()
    {
        return view('livewire.payment-modal');
    }

    public function placeholder()
    {
        return <<< 'HTML'
                        <div class="flex justify-center items-center h-32">
                            <svg class="animate-spin h-10 w-10 text-gray-800" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="ml-3 text-gray-700">Cargando Vouchers y Glosas...</span>
                        </div>
                    HTML;
    }
}
