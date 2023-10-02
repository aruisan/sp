<?php

namespace App\Http\Controllers\Coso;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\StorageTraits;
use App\CosoIndividuo;

class ArchivoController extends Controller
{
    use StorageTraits;

    public function store(Request $request, CosoIndividuo $individuo){   
        
        $ruta = $this->uploadFile($request->archivo, 'coso/'.$individuo->id);
        $individuo->archivos()->create(['ruta' => $ruta]);

        return redirect()->route('coso.archivo.create', $individuo->id);
    }

    public function create(CosoIndividuo $individuo){
        return view('coso.archivos', compact('individuo'));
    }
}
