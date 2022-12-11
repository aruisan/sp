<?php

namespace App\Http\Controllers\Almacen;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Almacen;
use App\AlmacenArticulo;
use App\Model\Admin\Dependencia;
use App\Model\Persona;
use App\User;

class AlmacenController extends Controller
{
    public function index(){
        $articulos = AlmacenArticulo::get();
        return view('almacen.inventario', compact('articulos'));
    }

    /*
    public function ingresos(){
        $dependencias = Dependencia::all();
        $proovedores = Persona::all();
        $factura = AlmacenFactura::create(['owner_id' => auth()->id()]);
        return view('almacen.ingresos', compact('dependencias', 'factura', 'proovedores'));
    }
 

    public function egresos(){
        $dependencias = Dependencia::all();
        return view('almacen.egresos', compact('dependencias'));
    }

    public function create(){
        $dependencias = Dependencia::all();
        return view('almacen.create', compact('dependencias'));
    }

    public function store(Request $request){
        $new = Almacen::create($request->all() + ['owner_id' => auth()->id()]);
        return back();
    }

    public function edit(Almacen $articulo){
        $dependencias = Dependencia::all();
        return view('almacen.edit', compact('articulo', 'dependencias'));
    } 


    public function update(Request $request, Almacen $articulo){
        $articulo->update($request->all());
        return redirect()->route('almacen.index');
    }
       */
}
