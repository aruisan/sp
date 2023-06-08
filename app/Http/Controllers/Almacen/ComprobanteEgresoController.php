<?php

namespace App\Http\Controllers\Almacen;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Persona;
use App\AlmacenComprobanteEgreso;
use App\Model\Admin\Dependencia;
use App\Model\Administrativo\Contabilidad\PucAlcaldia;
use App\Notifications\AutorizacionAlmacen;
use App\AlmacenArticulo;
use App\AlmacenArticuloSalida;
use App\User;
use Session;
use PDF;


class ComprobanteEgresoController extends Controller
{

    public function index(){
        $salidas = AlmacenComprobanteEgreso::get()->filter(function($g){ return $g->salidas_pivot->count() > 0; });
        $recientes = $salidas->filter(function($salida){ return $salida->historico ==  FALSE; });
        $historicos = $salidas->filter(function($salida){ return $salida->historico; });
        return view('almacen.egreso-index', compact('recientes', 'historicos', 'salidas'));
    } 

    public function create(){
        $dependencias = Dependencia::all();
        $responsables = Persona::all();
        $egreso = AlmacenComprobanteEgreso::create([
            'owner_id' => auth()->id(),
            'status' => [],
            'observacion' => []
        ]);
        $pucs_credito = PucAlcaldia::where('code', 'Like', '2401%')->get()->filter(function($p){ return $p->level == 5; });
        $pucs_debito = PucAlcaldia::where('code', 'Like', '1635%')->get()->filter(function($p){ return $p->level == 5; });
        $articulos = AlmacenArticulo::get();
        return view('almacen.egresos', compact('dependencias', 'egreso', 'responsables', 'pucs_debito', 'pucs_credito' , 'articulos'));
    }

    public function update(Request $request, AlmacenComprobanteEgreso $egreso){
        //dd($request->all());
        $data_factura_update = $request->except(['id', 'cantidad']);
        $egreso->update($data_factura_update + ['owner_id' => auth()->id()]);
        $egreso->responsable_id = $request->responsable_id;
        $egreso->save();

        if(is_null($egreso->dependencia->encargado)){
            $egreso->delete();
            Session::flash('error','la dependencia asiganda no tiene encargado');
        }else{
            foreach($request->id as $k => $articulo):
                $egreso->salidas_pivot()->create([
                    'almacen_articulo_id' => $articulo,
                    'cantidad' => $request->cantidad[$k]
                ]);
            endforeach;
            Session::flash('success','se ha generado la salida de almacen con exito.');
        }
        return redirect()->route('almacen.egreso.show', $egreso->id);
    }

    public function show(AlmacenComprobanteEgreso $egreso){
        return view('almacen.egresos-show', compact('egreso'));
    }

    public function autorizar(AlmacenComprobanteEgreso $egreso){
        $pucs_credito = PucAlcaldia::get()->filter(function($p){ return $p->level == 5; });
        return view('almacen.egreso-autorizaciones', compact('egreso', 'pucs_credito'));
    }

    public function autorizar_store(Request $request, AlmacenComprobanteEgreso $egreso){ 
        if(auth()->user()->validar_cargo('Secretaria')):  
            foreach($request->id as $id):   
                //dd($id);
                $articulo_salida = AlmacenArticuloSalida::find($id);
                $articulo_salida->cantidad = $request->cantidad[$id];
                $articulo_salida->save();
            endforeach;
        endif;

        $status = $egreso->status;
        $observacion = $egreso->observacion;
        array_push($status, $request->status);
        array_push($observacion, $request->observacion);
        $egreso->ccc = auth()->user()->validar_cargo('almacenista') ? intval($request->ccc) : $egreso->ccc;
        $egreso->status = $status;
        $egreso->observacion = $observacion;
        $egreso->save();
        /*
                if(count($egreso->status) < 2):
                    $users = User::get()->filter(function($u){ return $u->validar_cargo('almacenista');});
                    foreach($users as $user):
                        $user->notify(new AutorizacionAlmacen($egreso->id, $egreso->dependencia->name));
                    endforeach;
                endif;
        */
        return redirect()->route('almacen.comprobante.egreso.index');
    }

    public function pdf(AlmacenComprobanteEgreso $egreso){
        //dd($egreso->salidas->pluck('ccd'));
        $pucs_id = $egreso->salidas->pluck('ccd')->unique();
        $pucs = PucAlcaldia::whereIn('id', $pucs_id)->get();
        $pdf = PDF::loadView('almacen.salida-pdf', compact('egreso', 'pucs'))->setOptions(['images' => true,'isRemoteEnabled' => true]);
        return $pdf->stream();
    }
}
