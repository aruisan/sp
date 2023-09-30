<?php

namespace App\Http\Controllers\Almacen;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\AlmacenArticulo;

class ArticuloController extends Controller
{
    public function index(){
        $articulos = AlmacenArticulo::paginate(10);

        //dd($articulos);
        return view('almacen.inventario', compact('articulos'));
    }

    public function edit(AlmacenArticulo $articulo){
        return view('almacen.articulo-edit', compact('articulo'));
    }

    public function update(AlmacenArticulo $articulo, Request $request){
        $articulo->update($request->all());
        return redirect()->route('almacen.inventario');
    }


    public function articulo_ajax($articulo_code){
        $articulo = AlmacenArticulo::where('codigo', $articulo_code)->first();
        $articulo->puc_credito = $articulo->puc_ccc;
        $articulo->puc_debito = $articulo->puc_ccd;
        return response()->json($articulo);
    }
}
