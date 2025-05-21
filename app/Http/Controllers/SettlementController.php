<?php

namespace App\Http\Controllers;

use App\Models\Settlement;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class SettlementController extends Controller
{
    public function pdf(Settlement $settlement)
    {
        $pdf = Pdf::loadView('beneficiaries.pdf-settlement', compact('settlement'));
        return $pdf->stream("settlement_{$settlement->id}_" . uniqid() . '.pdf');
    }
}
