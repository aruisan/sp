<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExploradorArchivoFile extends Model
{
    public function getUrlAttribute(){
        return \Storage::url($this->ruta);
    }
}
