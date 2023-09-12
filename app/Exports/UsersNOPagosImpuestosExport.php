<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class UsersNOPagosImpuestosExport implements FromView
{
    public function __construct($noPagos, $predial){
        $this->noPagos = $noPagos;
        $this->predial = $predial;
    }

    public function view(): View
    {
        $noPagos = $this->noPagos;
        $predial = $this->predial;
        return view('exports.UsersNOPagosImpuestosExc', compact('noPagos','predial'));
    }
}

