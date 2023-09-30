<?php
namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;


trait StorageTraits
{
    public function uploadFile($file, $newRuta, $oldRuta = ''){
        if($this->existsFile($oldRuta)){
            $this->deleteFile($oldRuta);
        }
        
        return $file->store('public/'.$newRuta);
    }

    public function existsFile($ruta){
        return Storage::exists($ruta);
    }

    public function deleteFile($ruta){
        if($this->existsFile($ruta))
            Storage::delete($ruta);
    }

    public function deleteDir($directory){
        Storage::deleteDirectory($directory);
    }
}