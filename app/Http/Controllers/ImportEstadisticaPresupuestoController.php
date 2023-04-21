<?php

namespace App\Http\Controllers;

use App\ComprobanteIngresoTemporal;
use Illuminate\Http\Request;
use App\Imports\EstadisticaPresupuestoImport;
use App\ImportEstadisticaPresupuesto;
use Maatwebsite\Excel\Facades\Excel;
use App\Model\Persona;
use App\NominaEmpleado;
use App\NominaEmpleadoNomina;
use App\Model\Administrativo\Contabilidad\PucAlcaldia;
use App\PacInformeIngresoEgreso;

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

    public function create_comprobantes_old(){
        return view('import.comprobantes_old');
    }

    public function import_comprobantes_old(Request $request)
    {
        foreach($request->data as $item):
            $new_person = new ComprobanteIngresoTemporal;
            $new_person->code = $item[0];
            $new_person->fecha = $item[1];
            $new_person->referencia = $item[2];
            $new_person->cc = $item[3];
            $new_person->tercero = $item[4];
            $new_person->valor = $item[5];
            $new_person->concepto = $item[6];
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

    public function create_pac(){
        return view('import.pac');
    }

    public function import_pac(Request $request)
    {
        $encontrados = collect();
        $nuevos = collect();
        foreach($request->data as $item):
            $codigo = $item[0];
            $nombre = $item[1];
            $inicial = $item[2];
            $tipo = $item[3];
            
            $new = new PacInformeIngresoEgreso;
            $new->codigo = $codigo;
            $new->nombre = $nombre;
            $new->inicial = $inicial;
            $new->tipo = $tipo;
            $new->save();
        endforeach;

        return response()->json('ok');
    }

    public function create_reintegro(){
        return view('import.descuento_reintegro');
    }

    public function import_reintegro(Request $request)
    {
        $encontrados = collect();
        $nuevos = collect();
        foreach($request->data as $item):
            $empleado = NominaEmpleado::where('num_dc', intval($item[0]))->first();
            if(is_null($empleado)){
                $nuevos->push($item[0]);
            }else{
               $n_empleado =  NominaEmpleadoNomina::where('nomina_empleado_id', $empleado->id)->where('nomina_id', 58)->first();
               $n_empleado->descuento_reintegro = intval($item[1]);
               $n_empleado->save();
               $encontrados->push($item[0]);
            }
        endforeach;
        return response()->json(['encontrados'=> $encontrados, 'nuevos' => $nuevos]);
    }
}
