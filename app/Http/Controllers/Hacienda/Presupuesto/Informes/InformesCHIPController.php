<?php

namespace App\Http\Controllers\Hacienda\Presupuesto\Informes;

use App\Model\Hacienda\Presupuesto\Vigencia;
use App\Exports\InformePresupuestosExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ChipEgrProgExport;
use App\Traits\PrepIngresosTraits;
use App\Exports\ChipEgrExcExport;
use App\Exports\ChipIngExcExport;
use App\Traits\PrepEgresosTraits;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Session;
use PDF;

class InformesCHIPController extends Controller
{

    public function makeEgresosExec(Request $request, $inicio, $final)
    {
        $vigencia = Vigencia::where('vigencia', Carbon::now()->year)->where('tipo', 0)->where('estado', '0')->first();
        $prepTrait = new PrepEgresosTraits();
        $presupuesto = $prepTrait->prepEgresos($vigencia, $inicio, $final);

        return Excel::download(new ChipEgrExcExport($presupuesto),
            'CHIP Egresos Ejecucion '.$inicio.'-'.$final.'.xlsx');
    }

    public function makeIngresosExec(Request $request, $inicio, $final)
    {
        $vigencia = Vigencia::where('vigencia', Carbon::now()->year)->where('tipo', 1)->where('estado', '0')->first();
        $prepTrait = new PrepIngresosTraits();
        $presupuesto = $prepTrait->prepIngresos($vigencia, $inicio, $final);

        return Excel::download(new ChipIngExcExport($presupuesto),
            'Ejecucion Presupuesto de Ingresos '.$inicio.'-'.$final.'.xlsx');
    }

    public function makeEgresosProg(Request $request, $inicio, $final)
    {
        $vigencia = Vigencia::where('vigencia', Carbon::now()->year)->where('tipo', 0)->where('estado', '0')->first();
        $prepTrait = new PrepEgresosTraits();
        $presupuesto = $prepTrait->prepEgresos($vigencia, $inicio, $final);

        return Excel::download(new ChipEgrProgExport($presupuesto),
            'Programacion Presupuesto de Egresos '.$inicio.'-'.$final.'.xlsx');
    }

    public function makeIngresosProg(Request $request, $inicio, $final)
    {
        $vigencia = Vigencia::where('vigencia', Carbon::now()->year)->where('tipo', 1)->where('estado', '0')->first();
        $prepTrait = new PrepIngresosTraits();
        $presupuesto = $prepTrait->prepIngresos($vigencia, $inicio, $final);

        return Excel::download(new ChipIngExcExport($presupuesto),
            'Programacion Presupuesto de Ingresos '.$inicio.'-'.$final.'.xlsx');
    }

    public function make(Request $request){
        $año = Carbon::today()->year;
        $inicio = $año.'-01-01';
        switch ($request->periodo){
            case 1:
                $final = $año.'-03-31';
                break;
            case 2:
                $final = $año.'-06-30';
                break;
            case 3:
                $final = $año.'-09-30';
                break;
            case 4:
                $final = $año.'-12-31';
                break;
        }

        if ($request->categoria == "ProgIng") {
            $vigencia = Vigencia::where('vigencia', Carbon::now()->year)->where('tipo', 1)->where('estado', '0')->first();
            $prepTrait = new PrepIngresosTraits();
            $presupuesto = $prepTrait->prepIngresos($vigencia, $inicio, $final);
            $prep[] = collect(['1' => 'S', '2' => 216488564, '3' => 11206, '4' => $año, '5' => 'A_PROGRAMACION_DE_INGRESOS']);
            $prep[] = collect(['1' => 'Detalle', '2' => 'Rubro', '3' => 'Ppto Inicial', '4' => 'Ppto Final']);
            foreach ($presupuesto as $data) {
                $prep[] = collect(['1' => 'D', '2' => $data['code'], '3' => $data['inicial'], '4' => $data['definitivo']]);
            }

            return $prep;
        } elseif ($request->categoria == "EjecIng") {
            $vigencia = Vigencia::where('vigencia', Carbon::now()->year)->where('tipo', 1)->where('estado', '0')->first();
            $prepTrait = new PrepIngresosTraits();
            $presupuesto = $prepTrait->prepIngresos($vigencia, $inicio, $final);
            $prep[] = collect(['1' => 'S', '2' => 216488564, '3' => 11212, '4' => $año, '5' => 'B_EJECUCION_DE_INGRESOS']);
            $prep[] = collect(['1' => 'Detalle', '2' => 'Rubro', '3' => 'CPC', '4' => 'Detalle Sectorial', '5' => 'Codigo Fuente',
                '6' => 'Tercero', '7' => 'Politica Publica', '8' => 'Numero y Fecha Norma', '9' => 'Tipo Norma',
                '10' => 'Recaudo vigencia actual sin situación de fondos', '11' => 'Recaudo vigencia actual con fondos',
                '12' => 'Recaudo vigencia anterior sin situación de fondos', '13' => 'Recaudo Anterior  con fondos']);
            foreach ($presupuesto as $data) {
                if ($data['cod_fuente']) $prep[] = collect(['1' => 'D', '2' => $data['code'], '3' => 0, '4' => 0, '5' => $data['cod_fuente'],
                    '6' => 1, '7' => 0, 8 => 'ley 99 de 1993', '9' => 5, '10' => 0, '11' => $data['recaudado'],
                    '12' => 0, '13' => 0]);
            }

            return $prep;

        }elseif ($request->categoria == "ProgGasAdm"){

            $vigencia = Vigencia::where('vigencia', Carbon::now()->year)->where('tipo', 0)->where('estado', '0')->first();
            $presupuesto = new PrepEgresosTraits();
            $result = $presupuesto->prepEgresos($vigencia, $inicio, $final);
            $prep[] = collect(['1' => 'S', '2' => 216488564, '3' => 11212, '4' => $año, '5' => 'C_PROGRAMACION_DE_GASTOS_ADMINISTRACION_CENTRAL']);
            $prep[] = collect(['1' => 'Detalle', '2' => 'Rubro', '3' => 'Vigencia', '4' => 'Administracion Central',
                '5' => 'Programa MGA', '6' => 'BPIN', '7' => 'Apropiacion Inicial', '8' => 'Apropiacion Definitiva']);
            foreach ($result as $data){
                if ( $data['dep'] != "" and $data['dep'] != "Concejo" and $data['dep'] != "Personería"){
                    $prep[] = collect(['1' => 'D', '2' => $data['cod'], '3' => 1, '4' => $data['codDep'].' - '.$data['dep'],
                        '5' => $data['codProgMGA'], '6' => $data['codBpin'], '7' => $data['presupuesto_inicial'],
                        '8' => $data['presupuesto_def']]);
                }
            }

            return $prep;

        }elseif ($request->categoria == "ProgGasCon"){

            $vigencia = Vigencia::where('vigencia', Carbon::now()->year)->where('tipo', 0)->where('estado', '0')->first();
            $presupuesto = new PrepEgresosTraits();
            $result = $presupuesto->prepEgresos($vigencia, $inicio, $final);
            $prep[] = collect(['1' => 'S', '2' => 216488564, '3' => 11212, '4' => $año, '5' => 'C_PROGRAMACION_DE_GASTOS_CONCEJO']);
            $prep[] = collect(['1' => 'Detalle', '2' => 'Rubro', '3' => 'Vigencia', '4' => 'Administracion Central',
                '5' => 'Programa MGA', '6' => 'BPIN', '7' => 'Apropiacion Inicial', '8' => 'Apropiacion Definitiva']);
            foreach ($result as $data){
                if ( $data['dep'] != "" and $data['dep'] == "Concejo"){
                    $prep[] = collect(['1' => 'D', '2' => $data['cod'], '3' => 1, '4' => $data['codDep'].' - '.$data['dep'],
                        '5' => $data['codProgMGA'], '6' => $data['codBpin'], '7' => $data['presupuesto_inicial'],
                        '8' => $data['presupuesto_def']]);
                }
            }

            return $prep;

        }elseif ($request->categoria == "ProgGasPer"){

            $vigencia = Vigencia::where('vigencia', Carbon::now()->year)->where('tipo', 0)->where('estado', '0')->first();
            $presupuesto = new PrepEgresosTraits();
            $result = $presupuesto->prepEgresos($vigencia, $inicio, $final);
            $prep[] = collect(['1' => 'S', '2' => 216488564, '3' => 11212, '4' => $año, '5' => 'C_PROGRAMACION_DE_GASTOS_PERSONERIA']);
            $prep[] = collect(['1' => 'Detalle', '2' => 'Rubro', '3' => 'Vigencia', '4' => 'Administracion Central',
                '5' => 'Programa MGA', '6' => 'BPIN', '7' => 'Apropiacion Inicial', '8' => 'Apropiacion Definitiva']);
            foreach ($result as $data){
                if ( $data['dep'] != "" and $data['dep'] == "Personería"){
                    $prep[] = collect(['1' => 'D', '2' => $data['cod'], '3' => 1, '4' => $data['codDep'].' - '.$data['dep'],
                        '5' => $data['codProgMGA'], '6' => $data['codBpin'], '7' => $data['presupuesto_inicial'],
                        '8' => $data['presupuesto_def']]);
                }
            }

            return $prep;

        }elseif ($request->categoria == "EjecGasAdm"){

            $vigencia = Vigencia::where('vigencia', Carbon::now()->year)->where('tipo', 0)->where('estado', '0')->first();
            $presupuesto = new PrepEgresosTraits();
            $result = $presupuesto->prepEgresos($vigencia, $inicio, $final);
            $prep[] = collect(['1' => 'S', '2' => 216488564, '3' => 11212, '4' => $año, '5' => 'C_JECUCION_DE_GASTOS_ADMINISTRACION_CENTRAL']);
            $prep[] = collect(['1' => 'Detalle', '2' => 'Rubro', '3' => 'Vigencia', '4' => 'Administracion Central',
                '5' => 'Producto MGA', '6' => 'CPC', '7' => 'Detalle sectorial', '8' => 'Fuente financiacion',
                '9' => 'BPIN', '10' => 'Seleccione C /Sin fondos', '11' => 'polítca publica', '12' => 'tercero',
                '13' => 'compromisos', '14' => 'obligaciones', '15' => 'pagos']);
            foreach ($result as $data){
                if ( $data['dep'] != "" and $data['dep'] != "Concejo" and $data['dep'] != "Personería"){
                    $prep[] = collect(['1' => 'D', '2' => $data['cod'], '3' => 1, '4' => $data['codDep'].' - '.$data['dep'],
                        '5' => $data['cod_producto'], '6' => 0, '7' => $data['codProgMGA'], '8' => $data['cod_fuente'],
                        '9' => $data['codBpin'], '10' => 'C', '11' => 0, '12' => 0, '13' => 0, '14' => 0,
                        '15' => $data['pagos']]);
                }
            }

            return $prep;

        }elseif ($request->categoria == "EjecGasCon"){

            $vigencia = Vigencia::where('vigencia', Carbon::now()->year)->where('tipo', 0)->where('estado', '0')->first();
            $presupuesto = new PrepEgresosTraits();
            $result = $presupuesto->prepEgresos($vigencia, $inicio, $final);
            $prep[] = collect(['1' => 'S', '2' => 216488564, '3' => 11212, '4' => $año, '5' => 'C_JECUCION_DE_GASTOS_ADMINISTRACION_CONCEJO']);
            $prep[] = collect(['1' => 'Detalle', '2' => 'Rubro', '3' => 'Vigencia', '4' => 'Administracion Central',
                '5' => 'Producto MGA', '6' => 'CPC', '7' => 'Detalle sectorial', '8' => 'Fuente financiacion',
                '9' => 'BPIN', '10' => 'Seleccione C /Sin fondos', '11' => 'polítca publica', '12' => 'tercero',
                '13' => 'compromisos', '14' => 'obligaciones', '15' => 'pagos']);
            foreach ($result as $data){
                if ( $data['dep'] != "" and $data['dep'] == "Concejo"){
                    $prep[] = collect(['1' => 'D', '2' => $data['cod'], '3' => 1, '4' => $data['codDep'].' - '.$data['dep'],
                        '5' => $data['cod_producto'], '6' => 0, '7' => $data['codProgMGA'], '8' => $data['cod_fuente'],
                        '9' => $data['codBpin'], '10' => 'C', '11' => 0, '12' => 0, '13' => 0, '14' => 0,
                        '15' => $data['pagos']]);
                }
            }

            return $prep;

        }elseif ($request->categoria == "EjecGasPer"){

            $vigencia = Vigencia::where('vigencia', Carbon::now()->year)->where('tipo', 0)->where('estado', '0')->first();
            $presupuesto = new PrepEgresosTraits();
            $result = $presupuesto->prepEgresos($vigencia, $inicio, $final);
            $prep[] = collect(['1' => 'S', '2' => 216488564, '3' => 11212, '4' => $año, '5' => 'C_JECUCION_DE_GASTOS_ADMINISTRACION_PERSONERIA']);
            $prep[] = collect(['1' => 'Detalle', '2' => 'Rubro', '3' => 'Vigencia', '4' => 'Administracion Central',
                '5' => 'Producto MGA', '6' => 'CPC', '7' => 'Detalle sectorial', '8' => 'Fuente financiacion',
                '9' => 'BPIN', '10' => 'Seleccione C /Sin fondos', '11' => 'polítca publica', '12' => 'tercero',
                '13' => 'compromisos', '14' => 'obligaciones', '15' => 'pagos']);
            foreach ($result as $data){
                if ( $data['dep'] != "" and $data['dep'] == "Personería"){
                    $prep[] = collect(['1' => 'D', '2' => $data['cod'], '3' => 1, '4' => $data['codDep'].' - '.$data['dep'],
                        '5' => $data['cod_producto'], '6' => 0, '7' => $data['codProgMGA'], '8' => $data['cod_fuente'],
                        '9' => $data['codBpin'], '10' => 'C', '11' => 0, '12' => 0, '13' => 0, '14' => 0,
                        '15' => $data['pagos']]);
                }
            }

            return $prep;
        } else {
            dd("other");
        }
    }
}