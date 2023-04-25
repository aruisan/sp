<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class ChipIngExcExport implements FromView
{
    public function __construct($presupuesto){
        $this->presupuesto = $presupuesto;
    }

    public function view(): View
    {
        $presupuesto = $this->presupuesto;
        $año = Carbon::now()->year;
        return view('exports.chipInExc', compact('presupuesto', 'año'));
    }
}

