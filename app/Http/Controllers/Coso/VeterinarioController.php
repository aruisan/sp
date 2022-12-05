<?php

namespace App\Http\Controllers\Coso;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\CosoIndividuo;
use App\CosoVeterinario;

class VeterinarioController extends Controller
{
    public function create(CosoIndividuo $individuo){
        return view('coso.veterinarios', compact('individuo'));
    }

    public function store(Request $request, $individuo){
        //dd($request->all());
        $new = CosoVeterinario::create($request->all()+['coso_individuo_id' => $individuo]);

        foreach($request->medicamento as $k => $medicamento):
            $new->medicinas()->create([
                'medicamento' => $medicamento,
                'dosis_diaria' => $request->dosis_diaria[$k],
                'hora' => $request->hora[$k].':00',
                'termino' => $request->termino[$k],
                'aplica' => $request->aplica[$k]
            ]);
        endforeach;
        return redirect()->route('coso.veterinario.create', $individuo);
    }
}
