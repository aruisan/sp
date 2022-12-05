<?php

namespace App\Http\Controllers\Almacen;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Persona;
use App\AlmacenComprobanteEgreso;
use App\Model\Admin\Dependencia;

class ComprobanteEgresoController extends Controller
{
    public function create(){
        $dependencias = Dependencia::all();
        $responsables = Persona::all();
        $egreso = AlmacenComprobanteEgreso::create(['owner_id' => auth()->id()]);
        return view('almacen.egresos', compact('dependencias', 'egreso', 'responsables'));
    }

    public function update(Request $request, AlmacenComprobanteEgreso $egreso){
        //dd($request->all());
        $data_factura_update = $request->except(['id', 'cantidad']);
        $egreso->update($data_factura_update + ['owner_id' => auth()->id()]);
        foreach($request->id as $k => $articulo):
            $egreso->salidas_pivot()->create([
                'almacen_articulo_id' => $articulo,
                'cantidad' => $request->cantidad[$k]
            ]);
        endforeach;
        return redirect()->route('almacen.inventario');
    }

    public function show(AlmacenComprobanteEgreso $egreso){
        return view('almacen.egresos-show', compact('egreso'));
    }
}
