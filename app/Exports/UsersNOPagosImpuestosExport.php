<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class UsersNOPagosImpuestosExport implements FromView
{
    public function __construct($noPagos){
        $this->noPagos = $noPagos;
    }

    public function view(): View
    {
        $noPagos = $this->noPagos;
        return view('exports.UsersNOPagosImpuestosExc', compact('noPagos'));
    }
}

