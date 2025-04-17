<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Voucher;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function index()
    {
        $vouchers = Voucher::paginate(1000);
        return view('vouchers.index', compact('vouchers'));
    }

    public function show($codigo)
    {
        $vouchers = Voucher::where('numprestamo', $codigo)->get();

        $capPagado = Payment::where('numprestamo', $codigo)
                            ->where('montopago', '<', 0)
                            ->where('prtdtdesc', 'LIKE', '%CAPITAL%')
                            ->sum('montopago');

        $intPagado = Payment::where('numprestamo', $codigo)
                            ->where('montopago', '<', 0)
                            ->where('prtdtdesc', 'LIKE', '%INTE%')
                            ->sum('montopago');

        $segPagado = Payment::where('numprestamo', $codigo)
                            ->where('montopago', '<', 0)
                            ->where('prtdtdesc', 'LIKE', '%SEG%')
                            ->sum('montopago');

        $detalle = new Collection();
        $detalle->push((object) [
            'capital' => $capPagado * -1,
            'interes' => $intPagado * -1,
            'seguro' => $segPagado * -1,
            'total' => ($capPagado + $intPagado + $segPagado) * -1
        ]);

        return view('vouchers.show', compact('vouchers', 'detalle'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'numprestamo' => 'required|string',
            // Add other validation rules as needed
        ]);

        Voucher::create($validated);

        return redirect()->route('vouchers.index')->with('success', 'Voucher created successfully.');
    }

    public function update(Request $request, Voucher $voucher)
    {
        $validated = $request->validate([
            'numprestamo' => 'required|string',
            // Add other validation rules as needed
        ]);

        $voucher->update($validated);

        return redirect()->route('vouchers.index')->with('success', 'Voucher updated successfully.');
    }

    public function destroy(Voucher $voucher)
    {
        //$voucher->delete();

        return redirect()->route('vouchers.index')->with('success', 'Voucher deleted successfully.');
    }
}
