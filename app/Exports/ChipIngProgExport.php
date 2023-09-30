<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class ChipIngProgExport implements FromView
{
    public function __construct($presupuesto){
        $this->presupuesto = $presupuesto;
    }

    public function view(): View
    {
        $presupuesto = $this->presupuesto;
        $año = Carbon::now()->year;
        return view('exports.chipInProg', compact('presupuesto', 'año'));
    }
}

