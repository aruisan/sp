<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class InfPrepIngExcExport implements FromView
{
    public function __construct($presupuesto){
        $this->presupuesto = $presupuesto;
    }

    public function view(): View
    {

        $prepIng = $this->presupuesto;

        return view('exports.infPrepIngExc', compact('prepIng'));
    }
}

