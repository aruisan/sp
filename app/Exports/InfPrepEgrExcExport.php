<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class InfPrepEgrExcExport implements FromView
{
    public function __construct(int $año, $plantilla, $mes, $dia){

        $this->año = $año;
        $this->plantilla = $plantilla;
        $this->mesActual = $mes;
        $this->dia = $dia;
    }

    public function view(): View
    {
        $plantilla = $this->plantilla;
        $mesActual = $this->mesActual;
        $año = $this->año;
        $dia = $this->dia;

        return view('exports.infPrepEgExc', compact('plantilla','mesActual','año','dia'));
    }
}

