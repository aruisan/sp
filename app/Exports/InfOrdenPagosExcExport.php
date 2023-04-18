<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class InfOrdenPagosExcExport implements FromView
{
    public function __construct($ordenPagos){
        $this->ordenPagos = $ordenPagos;
    }

    public function view(): View
    {
        $ordenPagos = $this->ordenPagos;
        return view('exports.infOrdenPagosExc', compact('ordenPagos'));
    }
}

