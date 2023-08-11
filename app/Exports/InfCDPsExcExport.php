<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class InfCDPsExcExport implements FromView
{
    public function __construct($cdps){
        $this->cdps = $cdps;
    }

    public function view(): View
    {
        $cdps = $this->cdps;
        return view('exports.infCDPsExc', compact('cdps'));
    }
}

