<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\EstadisticaPresupuestoImport;
use App\ImportEstadisticaPresupuesto;
use Maatwebsite\Excel\Facades\Excel;
use App\Model\Persona;
use App\NominaEmpleado;
use App\Model\Administrativo\Contabilidad\PucAlcaldia;

class ImportEstadisticaPresupuestoController extends Controller
{

    public function create(){
        return view('import.presupuesto');
    }

    public function import(Request $request)
    {
        $new = ImportEstadisticaPresupuesto::create($request->all());
        return response()->json($new);
    }

    public function create_terceros(){
        return view('import.terceros');
    }

    public function import_terceros(Request $request)
    {
        foreach($request->data as $item):
            $new_person = Persona::where('num_dc', $item[1])->first();
            if(is_null($new_person)):
                $new_person = new Persona;
                $new_person->nombre = $item[0];
                $new_person->num_dc = $item[1];
                /*
                $new_person->direccion  = $item[2];
                $new_person->telefono  = $item[3];
                $new_person->email  = $item[4];
                */
                $new_person->tipo_tercero = 'Empleado';
                $new_person->save();
            endif;
        endforeach;

        return response()->json('listo');
    }

    public function create_empleados(){
        return view('import.empleados');
    }

    public function import_empleados(Request $request)
    {
        foreach($request->data as $item):
            
            $new_person = NominaEmpleado::where('num_dc', $item[1])->first();
            if(is_null($new_person)):
                $new_person = new NominaEmpleado;
                $new_person->nombre = $item[0];
                $new_person->num_dc = $item[1];
                $new_person->tipo = 'pensionado';
                //
                /*
                $new_person->cargo = $item[2];
                $new_person->tipo_cargo = $item[3];
                $new_person->codigo_cargo = $item[4];
                $new_person->grado = $item[5];
                */
                $new_person->salario = 0;
                $new_person->save();
            endif;
        endforeach;

        return response()->json('listo');
    }

    public function create_empleados_cuentas(){
        return view('import.empleados_cuentas');
    }

    public function import_empleados_cuentas(Request $request)
    {
        foreach($request->data as $item):
            $new_person = NominaEmpleado::where('num_dc', $item[0])->first();
            $new_person->banco_cuenta_bancaria = $item[1];
            $new_person->tipo_cuenta_bancaria = $item[2];
            $new_person->numero_cuenta_bancaria = $item[3];
            $new_person->save();
        endforeach;

        return response()->json('listo');
    }

    public function create_bancos_saldos_iniciales(){
        return view('import.bancos_saldos_iniciales');
    }

    public function import_bancos_saldos_iniciales(Request $request)
    {
        $encontrados = collect();
        $nuevos = collect();
        foreach($request->data as $item):
            $code = intval($item[0]);
            $concepto = $item[1];
            $hijo = $item[2];
            $padre = intval($item[3]) != 0 ? PucAlcaldia::where('code', intval($item[3]))->first()->id : 0;
            $valor = intval($item[4]);
            $naturaleza = $item[5];
            $categoria = $item[6];

            $new_person = PucAlcaldia::where('code', $code)->get()->first();
            
            if(is_null($new_person)){
                $new_person = new PucAlcaldia;
                $new_person->code = $code;
                $new_person->concepto = $concepto;
                $new_person->hijo = $hijo;
                $new_person->padre_id = $padre;
            }

            $new_person->saldo_inicial = $hijo ? $valor : 0;
            $new_person->naturaleza = $naturaleza;
            $new_person->categoria = $categoria;
            $new_person->save();
        endforeach;

        return response()->json('ok');
    }
}
