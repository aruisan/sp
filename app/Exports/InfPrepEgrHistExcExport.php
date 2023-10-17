<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class InfPrepEgrExcHistExport implements FromView
{
    public function __construct($presupuesto){
        $this->presupuesto = $presupuesto;
    }

    public function view(): View
    {
        $presupuesto = $this->presupuesto;
        return view('exports.infPrepEgHistExc', compact('presupuesto'));
    }
}

