<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ExploradorArchivoDirectory extends Model
{
    protected $fillable = [
        'name', 'carpeta_id', 'user_id', 'estado'
    ];


    public function carpetas(){
        // User::blabla();
        return $this->hasMany(ExploradorArchivoDirectory::class, 'carpeta_id');
    }

    public function carpeta(){
        return $this->belongsTo(ExploradorArchivoDirectory::class, 'carpeta_id');
    }

    public function archivos(){
        return $this->hasMany(ExploradorArchivoFile::class, 'carpeta_id');
    }

    public function getNivelJerarquiaAttribute(){
        return is_null($this->carpeta) ? 1 : $this->carpeta->nivel_jerarquia + 1;
    }

    public function migas($user_id){
        $carpeta = $this;
        $array[$carpeta->nivel_jerarquia-1] = [
            'id' => $carpeta->id,
            'carpeta' => $carpeta->name, 
            'show_carpetas' => Route('carpetas-ajax', [$carpeta->id, $user_id]),
            'show_carpeta' => Route('show-carpeta-ajax', [$carpeta->id, $user_id]),
            'tipo' => 'carpeta'
        ];       

        for($i=1;$i<=$this->nivel_jerarquia; $i++){
            if(!is_null($carpeta->carpeta)){
                $carpeta = $carpeta->carpeta;
                $array[$carpeta->nivel_jerarquia-1] = [
                    'carpeta' => $carpeta->name, 
                    //rutas para los permisos
                    'show_carpetas' => Route('carpetas-ajax', [$carpeta->id, $user_id]),
                    'show_carpeta' => Route('show-carpeta-ajax', [$carpeta->id, $user_id]),
                    //rutas para gestor de archivos
                    'tipo' => 'carpeta',
                    'id' => $carpeta->id
                ];
            }
        }
        ksort($array);
        return $array;
    }

    public function getEstructuraCarpetasAttribute(){
        // return 'hjhsjs';
        $etiqueta = 'bdi';
        $limit = Str::limit($this->name, 25, '...');
        return  [
            'id' => "{$this->id}",
            'text' => $this->carpetas->count() == 0 
                        ? "<{$etiqueta} title='{$this->name}'><i class='fa fa-folder-o  icon text-secondary'  aria-hidden='true' ></i> {$limit}</{$etiqueta}>" 
                        : "<{$etiqueta} title='{$this->name}'>{$limit}</{$etiqueta}>",
            'children' => $this->carpetas->map(function($c){return $c->estructura_carpetas;}),
            'nivel' => $this->nivel_jerarquia
        ];
    }
    
    public function getHasFilesAttribute(){
        if ($this->carpetas->sum('has_file') > 0 ) {
            return 1;
        }else{
            return $this->archivos->count() > 0 ?  asset('img/iconos/folderred.svg') :  asset('img/iconos/folderblack.svg');
        }
    }
}
