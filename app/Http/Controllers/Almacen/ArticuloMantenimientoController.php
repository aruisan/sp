<?php

namespace App\Http\Controllers\Almacen;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\AlmacenArticulo;
use App\Model\Persona;

class ArticuloMantenimientoController extends Controller
{
    public function listar(AlmacenArticulo $articulo){
        $responsables = Persona::all();
        return view('almacen.articulo-mantenimiento', compact("articulo", "responsables"));
    }

    public function store(Request $request, AlmacenArticulo $articulo){
        $mantenimientos = $articulo->mantenimientos()->create($request->all());
        return back();
    }
}
