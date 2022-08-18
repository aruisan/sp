<?php

namespace App\Http\Controllers\Administrativo\Almacen;

use App\Model\Administrativo\Almacen\inventario;
use App\Model\Administrativo\Almacen\producto;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use App\Traits\FileTraits;
use Session;


class InventarioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $items = Inventario::all();
        return view('administrativo.almacen.inventario.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $productos = producto::where('tipo','0')->get();
        if (count($productos) == 0){
            Session::flash('error','No hay productos almacenados en la plataforma con el tipo de consumo.');
            return redirect('administrativo/inventario');
        } else {
            return view('administrativo.almacen.inventario.create',compact('productos'));
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
                return redirect('administrativo/inventario/create');
            }
        }

        if($request->hasFile('file'))
        {
            $file = new FileTraits;
            $ruta = $file->File($request->file('file'), 'Inventario');
        }else{
            $ruta = "";
        }

        for($x=0;$x< count($request->producto); $x++){

            $item = new inventario();
            $item->num_factura = $request->num_factura;
            $item->descripcion = $request->descripcion[$x];
            $item->unidad = $request->unidad[$x];
            $item->valor_unidad = $request->unitario[$x];
            $item->valor_final = $request->final[$x];
            $item->cantidad = $request->cantidad[$x];
            $item->fecha_ing = $request->fecha;
            $item->tipo = "0";
            $item->ruta = $ruta;
            $item->producto_id = $request->producto[$x];
            $item->save();

            $prod = producto::findOrFail($request->producto[$x]);
            $prod->cant_actual = $request->cantidad[$x] + $prod->cant_actual;
            $prod->valor_actual = $request->final[$x] + $prod->valor_actual;
            $prod->save();
        }

        Session::flash('success','El comprobante de ingreso se ha creado exitosamente');
        return redirect('/administrativo/inventario');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\inventario  $inventario
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $movimiento = inventario::findOrFail($id);

        return view('administrativo.almacen.inventario.show', compact('movimiento'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\inventario  $inventario
     * @return \Illuminate\Http\Response
     */
    public function edit(inventario $inventario)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\inventario  $inventario
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, inventario $inventario)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\inventario  $inventario
     * @return \Illuminate\Http\Response
     */
    public function destroy(inventario $inventario)
    {
        //
    }
}
