<?php

namespace App\Http\Controllers\Administrativo\Almacen;

use App\Model\Administrativo\Almacen\muebles;
use App\Model\Administrativo\Almacen\producto;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use App\Traits\FileTraits;
use App\User;
use Session;

class MueblesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $items = muebles::all();
        return view('administrativo.almacen.muebles.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $productos = producto::where('tipo', '1')->get();
        $users = User::orderBy('id','DESC')->get();
        if (count($productos) == 0) {
            Session::flash('error', 'No hay productos almacenados en la plataforma con el tipo devolutivo.');
            return redirect('administrativo/muebles');
        } else {
            return view('administrativo.almacen.muebles.create', compact('productos','users'));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        for($y=0;$y< count($request->producto); $y++){
            $validate = producto::findOrFail($request->producto[$y]);
            if ($validate->cant_actual + $request->cantidad[$y] > $validate->cant_maxima){
                Session::flash('error','No se pueden superar el almacenamiento maximo permitido para el producto '.$validate->nombre.'.');
                return redirect('administrativo/muebles/create');
            }
        }

        if($request->hasFile('file')) {
            $file = new FileTraits;
            $ruta = $file->File($request->file('file'), 'Muebles');
        }else{
            $ruta = "";
        }

        for($x=0;$x< count($request->producto); $x++){
            $item = new muebles();
            $item->num_factura = $request->num_fact;
            $item->descripcion = $request->descripcion[$x];
            $item->estado = $request->estado[$x];
            $item->avaluo = $request->avaluo[$x];
            $item->depreciacion = $request->avaluo[$x] / $request->vida[$x];
            $item->valor_unidad =  $request->avaluo[$x] / $request->cantidad[$x];
            $item->nuevo_valor = $request->avaluo[$x];
            $item->vida_util = $request->vida[$x];
            $item->cantidad = $request->cantidad[$x];
            $item->fecha_ing = $request->fecha;
            $item->tipo = "0";
            $item->ruta = $ruta;
            $item->persona_id = $request->user[$x];
            $item->producto_id = $request->producto[$x];
            $item->save();

            $prod = producto::findOrFail($request->producto[$x]);
            $prod->cant_actual = $request->cantidad[$x] + $prod->cant_actual;
            $prod->valor_actual = $request->avaluo[$x] + $prod->valor_actual;
            $prod->save();
        }

        Session::flash('success','El comprobante de ingreso se ha creado exitosamente');
        return redirect('/administrativo/muebles');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\muebles  $muebles
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $movimiento = muebles::findOrFail($id);
        $año = $movimiento->created_at->format('Y');
        for ($i=0;$i<$movimiento->vida_util + 1;$i++){
            $años[] = intval($año);
            $año = $año + 1;

        }
        $val = $movimiento->avaluo;
        for ($x=0;$x<$movimiento->vida_util + 1;$x++){
            $values[] = intval($val);
            $val = $val - $movimiento->depreciacion;
        }

        return view('administrativo.almacen.muebles.show',compact('movimiento','años','values'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\muebles  $muebles
     * @return \Illuminate\Http\Response
     */
    public function edit(muebles $muebles)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\muebles  $muebles
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, muebles $muebles)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\muebles  $muebles
     * @return \Illuminate\Http\Response
     */
    public function destroy(muebles $muebles)
    {
        //
    }
}
