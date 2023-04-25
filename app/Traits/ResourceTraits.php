<?php
namespace App\Traits;
use App\Model\Hacienda\Presupuesto\ResourcesMov;
use App\Resource;
//App\Traits\ResourceTraits

Class ResourceTraits
{
	public function resource($documents, $carpeta){

		 $ruta = $documents->store($carpeta);
        dd($ruta, $carpeta, $documents);

     	 $file = new Resource; 
     	 $file->ruta = $ruta;
	     $file->save();
	     return $file->id;
	}

    public function resourceMov($documents, $carpeta, $idMov){
        $ruta = $documents->store($carpeta);
        $file = new ResourcesMov();
        $file->mov_id = $idMov;
        $file->ruta = $ruta;
        $file->save();
        return $file->id;
    }

}