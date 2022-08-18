<?php
namespace App\Traits;
use App\Carpeta;
use App\Resource;
//App\Traits\ResourceTraits

Class GestionArchivosTraits
{
	public function carpeta($tipo){
        $carpetas = Carpeta::where('tipo', $tipo)->get();
        return view('administrativo.gestiondocumental.carpetas.index', compact('carpetas'));
    }
}