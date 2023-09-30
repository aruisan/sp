<?php

namespace App\Http\Controllers\Administrativo\GestionDocumental;

use App\Carpeta;
use App\Http\Controllers\Controller;
use App\Model\Administrativo\GestionDocumental\Documents;
use App\Model\Persona;
use App\Traits\ResourceTraits;
use Illuminate\Http\Request;
use Session;

class CarpetaController extends Controller
{
    private $ruta_view = 'administrativo.gestiondocumental.carpetas';

    public function listar($tipo){
        $carpetas = Carpeta::where('tipo', $tipo)->get();
        return view("{$this->ruta_view}.listar", compact('carpetas', 'tipo'));
    }

    public function create(){
        return view("{$this->ruta_view}.create");
    }

    public function store(Request $request, Carpeta $carpeta){
        $carpeta->create($request->all() + ['owner_id' => auth()->id()]);
        return redirect($request->rutaIndex);
    }

    public function show(Carpeta $carpeta){
        $terceros = Persona::all();
       return view("{$this->ruta_view}.show", compact('carpeta', 'terceros'));
    }

    public function edit(Carpeta $carpeta){
        return view("{$this->ruta_view}.edit", compact('carpeta'));
    }

    public function update(Request $request, Carpeta $carpeta){
        $carpeta->update($request->all());
        return redirect($request->rutaIndex);
    }

    public function destroy(Carpeta $carpeta){
        $carpeta->documentos()->delete();
        $carpeta->delete();
        return back();
    }


    public function storeArchivo(Request $request, Carpeta $carpeta){
        $resourceTraits  = new ResourceTraits();
        $carpeta->documentos()->create([
            'ff_document' => $request->ff_document,
            'ff_salida' => $request->ff_document,
            'ff_primerdbte' => $request->ff_document,
            'ff_segundodbte' => $request->ff_document,
            'ff_aprobacion' => $request->ff_document,
            'ff_sancion' => $request->ff_document,
            'ff_vence' => $request->ff_vence,
            'cc_id' => $request->cc_id,
            'name' => $request->name,
            'respuesta' => $request->name,
            'number_doc' => $request->number_doc,
            'estado' => $request->estado,
            'resource_id' => $resourceTraits->resource($request->archivo, "public/{$carpeta->tipo}"),
            'user_id' => auth()->id(),
            'tercero_id' => $request->tercero_id,
        ]);
        Session::flash('success','El archivo se ha almacenado exitosamente');
        return back();
    }

    public function deleteArchivo(Documents $archivo){
        $archivo->delete();
        Session::flash('danger','El archivo se ha eliminado exitosamente');
        return back();
    }
}
