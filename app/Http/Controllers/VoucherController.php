<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Voucher;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $vouchers = Voucher::paginate(1000);
        return view('vouchers.index', compact('vouchers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
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
        $detalle->push((object)
        [
            'capital' => $capPagado * -1,
            'interes' => $intPagado * -1,
            'seguro' => $segPagado * -1,
            'total' => ($capPagado + $intPagado + $segPagado) * -1
        ]);


        //return vouchers and detalle to the view

        return view('vouchers.show', compact('vouchers', 'detalle'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Voucher $voucher)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Voucher $voucher)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Voucher $voucher)
    {
        //
    }
}
