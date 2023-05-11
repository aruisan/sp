<?php

namespace App\Http\Controllers\Almacen;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Persona;
use App\AlmacenComprobanteIngreso;
use App\Model\Administrativo\Contabilidad\PucAlcaldia;
use App\Notifications\AutorizaconAlmacen;
use Session;
use PDF;

class ComprobanteIngresoController extends Controller
{
    public function create(){
        $proovedores = Persona::all();
        $ingreso = AlmacenComprobanteIngreso::create(['owner_id' => auth()->id()]);
        $pucs_credito = PucAlcaldia::where('code', 'Like', '2401%')->get()->filter(function($p){ return $p->level == 5; })->values();
        $pucs_debito = PucAlcaldia::where('code', 'Like', '1635%')->get()->filter(function($p){ return $p->level == 5; })->values();
        
        return view('almacen.ingresos', compact( 'ingreso', 'proovedores', 'pucs_credito', 'pucs_debito'));
    }


    public function update(Request $request, AlmacenComprobanteIngreso $ingreso){
        ///dd($request->ccd[0]);
        
        $data_factura_update = $request->except(['nombre_articulo', 'codigo', 'referencia', 'cantidad', 'valor_unitario', 'estado','tipo','ccd']);
       
        $ingreso->update($data_factura_update);
        foreach($request->nombre_articulo as $k => $articulo):
            $ingreso->articulos()->create([
                'nombre_articulo' => $request->nombre_articulo[$k],
                'marca' => $request->marca[$k],
                'codigo' => $request->codigo[$k],
                'referencia' => $request->referencia[$k],
                'presentacion' => $request->presentacion[$k],
                'cantidad' => $request->cantidad[$k],
                'valor_unitario' => $request->valor_unitario[$k],
                'vida_util' => $request->vida_util[$k],
                'estado' => $request->estado[$k],
                'tipo' => $request->tipo[$k],
                'ccd' => $request->ccd[$k]
            ]);
        endforeach;

        return redirect()->route('almacen.ingreso.show', $ingreso->id);
    }

    public function show(AlmacenComprobanteIngreso $ingreso){
        return view('almacen.ingresos-show', compact('ingreso'));
    }

    public function pdf(AlmacenComprobanteIngreso $ingreso){
        $pucs_id = $ingreso->articulos->pluck('ccd')->unique();
        $pucs = PucAlcaldia::whereIn('id', $pucs_id)->get();
        //dd($pucs);
        $pdf = PDF::loadView('almacen.entrada-pdf', compact('ingreso', 'pucs'))->setOptions(['images' => true,'isRemoteEnabled' => true]);
        return $pdf->stream();
    }

    public function import(Request $request){
        
    }
}
