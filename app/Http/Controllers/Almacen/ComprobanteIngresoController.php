<?php

namespace App\Http\Controllers\Almacen;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Persona;
use App\AlmacenComprobanteIngreso;
use App\Model\Administrativo\Contabilidad\PucAlcaldia;

class ComprobanteIngresoController extends Controller
{
    public function create(){
        $proovedores = Persona::all();
        $ingreso = AlmacenComprobanteIngreso::create(['owner_id' => auth()->id()]);
        $pucs = PucAlcaldia::where('hijo', '1')->where('padre_id', '<>', 0)->get();
        return view('almacen.ingresos', compact( 'ingreso', 'proovedores', 'pucs'));
    }


    public function update(Request $request, AlmacenComprobanteIngreso $ingreso){
        $data_factura_update = $request->except(['nombre_articulo', 'codigo', 'referencia', 'cantidad', 'valor_unitario', 'estado','tipo']);
        $ingreso->update($data_factura_update);
        foreach($request->nombre_articulo as $k => $articulo):
            $ingreso->articulos()->create([
                'nombre_articulo' => $request->nombre_articulo[$k],
                'codigo' => $request->codigo[$k],
                'referencia' => $request->referencia[$k],
                'cantidad' => $request->cantidad[$k],
                'valor_unitario' => $request->valor_unitario[$k],
                'estado' => $request->estado[$k],
                'tipo' => $request->tipo[$k]
            ]);
        endforeach;
        return redirect()->route('almacen.inventario');
    }

    public function show(AlmacenComprobanteIngreso $ingreso){
        return view('almacen.ingresos-show', compact('ingreso'));
    }
}
