<?php

namespace App\Http\Controllers\Almacen;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Almacen;
use App\Model\Admin\Dependencia;

class AlmacenController extends Controller
{
    public function index(){
        $articulos = Almacen::get();
        return view('almacen.index', compact('articulos'));
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
}
