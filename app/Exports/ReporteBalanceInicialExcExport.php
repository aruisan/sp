<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class ReporteBalanceInicialExcExport implements FromView
{
    public function __construct(int $año, $pucs, $mes, $dia){

        $this->año = $año;
        $this->pucs = $pucs;
        $this->mesActual = $mes;
        $this->dia = $dia;
    }

    public function view(): View
    {
        $pucs = $this->pucs;
        $mesActual = $this->mesActual;
        $año = $this->año;
        $dia = $this->dia;

        return view('exports.ReporteBalanceInicialEgExc', compact('pucs','mesActual','año','dia'));
    }
}

