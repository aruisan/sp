<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class InfPrepEgrExcExport implements FromView
{
    public function __construct(int $año, $plantillas, $mes, $dia){

        $this->año = $año;
        $this->plantillas = $plantillas;
        $this->mesActual = $mes;
        $this->dia = $dia;
    }

    public function view(): View
    {
        $plantillas = $this->plantillas;
        $mesActual = $this->mesActual;
        $año = $this->año;
        $dia = $this->dia;

        return view('exports.infPrepEgExc', compact('plantillas','mesActual','año','dia'));
    }
}

