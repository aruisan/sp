<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class InfPrepIngExcExport implements FromView
{
    public function __construct(int $año, $presupuesto, $mes, $dia){

        $this->año = $año;
        $this->presupuesto = $presupuesto;
        $this->mesActual = $mes;
        $this->dia = $dia;
    }

    public function view(): View
    {

        $prepIng = $this->presupuesto;
        $mesActual = $this->mesActual;
        $año = $this->año;
        $dia = $this->dia;

        return view('exports.infPrepIngExc', compact('prepIng','mesActual','año','dia'));
    }
}

