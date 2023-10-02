<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class InfRPsExcExport implements FromView
{
    public function __construct($rps){
        $this->rps = $rps;
    }

    public function view(): View
    {
        $rps = $this->rps;
        return view('exports.infRPsExc', compact('rps'));
    }
}

