<?php

namespace App\Imports;

use App\ImportEstadisticaPresupuesto;
use Maatwebsite\Excel\Concerns\ToModel;
//use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EstadisticaPresupuestoImport implements ToModel //, WithHeadingRow
{
    //private $contador = 0;
    //private $num_index;

/*
    public function  __construct( $num_index)
    {
        $this->num_index = $num_index;

    }
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        //dd($row);
        //ini_set('memory_limit', '1024M');
        //$this->contador = $this->contador+1;
        return new ImportEstadisticaPresupuesto([
            'campo' => $row[0]
            //'index' => $this->num_index + 1,
            //'type' => ($this->contador > 1) ? 'row' : 'header'
        ]);
    }
}
