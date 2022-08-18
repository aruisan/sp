<?php

namespace App\Http\Controllers\Administrativo\Almacen;

use App\Model\Administrativo\Almacen\inventario;
use App\Model\Administrativo\Almacen\muebles;
use App\Model\Administrativo\Almacen\producto;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Session;

class SalidaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $productos = producto::where('cant_actual', '>' ,'cant_minima')->get();
        if (count($productos) == 0){
            Session::flash('error','No hay productos almacenados en la plataforma');
            return redirect('administrativo/productos');
        } else {
            return view('administrativo.almacen.salida',compact('productos'));
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
        if (isset($request->IdProd)){
            $prod = producto::findOrFail($request->IdProd);
            $result = $prod->cant_actual - $request->salida;
            if ($prod->cant_minima <= $result){
                $prod->cant_actual = $result;
                $value = $request->salida * $request->valUni;
                $prod->valor_actual = $prod->valor_actual - $value;
                $prod->save();

                $inventario = new inventario();
                $inventario->descripcion = $request->descripcion;
                $inventario->valor_unidad = $request->valUni;
                $inventario->valor_final = $value;
                $inventario->cantidad = $request->salida;
                $inventario->fecha_salida = Carbon::now()->Format('Y-m-d');
                $inventario->tipo = "1";
                $inventario->producto_id = $request->IdProd;
                $inventario->save();

                Session::flash('success','El comprobante de salida se ha realizado exitosamente');
                return redirect('/administrativo/inventario');
            } else {
                Session::flash('error','No se puede realizar una salida debido a que la cantidad que quedaria tras realizar la salida es menor a la cantidad minima especificada al producto.');
                return redirect('administrativo/salida/create');
            }
        } else{

            $prod = producto::findOrFail($request->IdProdD);
            $result = $prod->cant_actual - $request->salida;
            if ($prod->cant_minima <= $result){
                $prod->cant_actual = $result;
                $value = $request->salida * $request->valUni;
                $prod->valor_actual = $prod->valor_actual - $value;
                $prod->save();

                $mueble = new muebles();
                $mueble->descripcion = $request->descripcion;
                $mueble->fecha_baja = Carbon::now()->Format('Y-m-d');
                $mueble->valor_unidad = $request->valUni;
                $mueble->nuevo_valor = $value;
                $mueble->cantidad = $request->salida;
                $mueble->tipo = "1";
                $mueble->producto_id = $request->IdProdD;
                $mueble->save();

                Session::flash('success','El comprobante de salida se ha realizado exitosamente');
                return redirect('/administrativo/muebles');
            } else {
                Session::flash('error','No se puede realizar una salida debido a que la cantidad que quedaria tras realizar la salida es menor a la cantidad minima especificada al producto.');
                return redirect('administrativo/salida/create');
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
