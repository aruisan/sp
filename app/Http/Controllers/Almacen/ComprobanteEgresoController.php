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
use Session;

class ComprobanteEgresoController extends Controller
{
    public function create(){
        $dependencias = Dependencia::all();
        $responsables = Persona::all();
        $egreso = AlmacenComprobanteEgreso::create(['owner_id' => auth()->id()]);
        $pucs_credito = PucAlcaldia::where('code', 'Like', '2401%')->get()->filter(function($p){ return $p->level == 5; });
        $pucs_debito = PucAlcaldia::where('code', 'Like', '1635%')->get()->filter(function($p){ return $p->level == 5; });
        $articulos = AlmacenArticulo::get();
        return view('almacen.egresos', compact('dependencias', 'egreso', 'responsables', 'pucs_debito', 'pucs_credito' , 'articulos'));
    }

    public function update(Request $request, AlmacenComprobanteEgreso $egreso){
        //dd($egreso);
        $data_factura_update = $request->except(['id', 'cantidad']);
        $egreso->update($data_factura_update + ['owner_id' => auth()->id()]);
        if(is_null($egreso->dependencia->encargado)){
            $egreso->delete();
            Session::flash('error','la dependencia asiganda no tiene encargado');
        }else{
            foreach($request->id as $k => $articulo):
                $egreso->salidas_pivot()->create([
                    'almacen_articulo_id' => $articulo,
                    'cantidad' => $request->cantidad[$k],
                    'status' => [],
                    'observacion' => []
                ]);
            endforeach;
            $user = $egreso->dependencia->encargado;
            $user->notify(new AutorizacionAlmacen($egreso->id, $egreso->dependencia->name));
            Session::flash('success','se ha generado la salida de almacen con exito.');
        }
        return redirect()->route('almacen.inventario');
    }

    public function show(AlmacenComprobanteEgreso $egreso){
        return view('almacen.egresos-show', compact('egreso'));
    }

    public function dependencia_edit(AlmacenComprobanteEgreso $egreso){
        return view('almacen.egreso-autorizaciones', compact('egreso'));
    }

    public function autorizar(Request $request, AlmacenArticuloSalida $articulo_salida){ 
        $articulo_salida->status->push($request->status);
        $articulo_salida->observacion->push($request->observacion);
        $articulo_saldia->save();

        return back();
    }
}
