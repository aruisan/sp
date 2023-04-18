<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class InfPagosExcExport implements FromView
{
    public function __construct($pagos){
        $this->pagos = $pagos;
    }

    public function view(): View
    {
        $pagos = $this->pagos;
        return view('exports.infPagosExc', compact('pagos'));
    }
}

