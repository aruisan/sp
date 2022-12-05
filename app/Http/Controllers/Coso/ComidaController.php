<?php

namespace App\Http\Controllers\Coso;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\CosoIndividuo;

class ComidaController extends Controller
{
    public function store(Request $request, CosoIndividuo $individuo){   
       // dd($request->all());
        $individuo->comidas()->create($request->all());
        return redirect()->route('coso.comida.create', $individuo->id);
    }

    public function create(CosoIndividuo $individuo){
        return view('coso.comidas', compact('individuo'));
    }
}
