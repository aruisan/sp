<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class UsersNOPagosImpuestosExport implements FromView
{
    public function __construct($predial, $icaReten, $icaContri){
        $this->predial = $predial;
        $this->icaReten = $icaReten;
        $this->icaContri = $icaContri;
    }

    public function view(): View
    {
        $predial = $this->predial;
        $icaReten = $this->icaReten;
        $icaContri = $this->icaContri;
        return view('exports.UsersNOPagosImpuestosExc', compact('predial','icaReten','icaContri'));
    }
}

