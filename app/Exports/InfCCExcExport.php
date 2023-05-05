<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class InfCCExcExport implements FromView
{
    public function __construct($compContables){
        $this->compContables = $compContables;
    }

    public function view(): View
    {
        $compContables = $this->compContables;
        return view('exports.infCCExc', compact('compContables'));
    }
}

