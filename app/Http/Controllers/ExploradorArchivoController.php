<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ExploradorArchivoDirectory;
use App\ExploradorArchivoFile;

class ExploradorArchivoController extends Controller
{
    private $carpeta_vista = "explorador_archivos";

    public function index(){
        $cliente = 1;
        $carpeta = null;
        $arbol = [];
        //dd(ExploradorArchivoDirectory::whereNull('carpeta_id')->get());
        $carpetas_list = ExploradorArchivoDirectory::whereNull('carpeta_id')->get();
        $carpetas = $carpetas_list->map(function($e){
            return $this->carpeta_format($e->id);
        });

        //dd($carpetas[0]->estructura_carpetas);
        $arbol = $carpetas_list->count() == 0 ? [] : $carpetas_list->map(function($a){
            return $a->estructura_carpetas;
        });

        //dd($arbol);
        return view("{$this->carpeta_vista}.index", compact('carpetas', 'carpeta', 'arbol', 'cliente'));
    }

    public function carpeta_format($id){
        $carpeta = ExploradorArchivoDirectory::find($id);
        return [
            'id' => $carpeta->id,
            'nombre' =>$carpeta->name,
            'padre' => $carpeta->carpeta,
            'migas' => $carpeta->migas(auth()->id()),
            'ruta_imagen_personalizada' => '',
            'imagen_personalizada' => $carpeta->has_files,
            'permisos' => [
                'ver' => 1,
                'editar' => 1,
                'guardar' => 1,
                'eliminar' => 1
            ],
            'n_archivos' => ExploradorArchivoFile::select('id')->where('carpeta_id', $carpeta->id)->count(),
        ];
    }

    public function subArchivos(Request $request){
            $carpetas = $request->id == 'null' ? ExploradorArchivoFile::all() : ExploradorArchivoFile::where('carpeta_id', $request->id)->get();
            return $carpetas->map(function($e)use($request){
               return [
                'id' => $e->id,
                'permisos' => [
                    'ver' => 1,
                    'editar' => 1,
                    'guardar' => 1,
                    'eliminar' => 1
                ],
                'titulo' => $e->name,
                'nom_real' => $e->name,
                'nombre' => $e->url,
                'ruta_imagen_personalizada' => asset('img/iconos/archivo_OCRNO.png'),
                'ruta' => route('archivo.mostrar', [$e->id])
               ];
              });
    }


    public function subcarpetas(Request $request){
        $carpeta = ExploradorArchivoDirectory::find($request->id);
        
        return [
            'carpeta' => $this->carpeta_format($carpeta->id),
            'carpetas' => $carpeta->carpetas->count() == 0 ? collect() : $carpeta->carpetas->map(function($e)use($request){
                return $this->carpeta_format($e->id);
            })
        ];
        
    }

    public function archivo_mostrar(ExploradorArchivoFile $archivo){
        return view('archivos.mostrar', compact('archivo'));
    }


    public function getArchivos(){

    }

    public function almacenarArchivos(Request $request)
    {
        //return $request->carpeta;
        foreach ($request->file('files') as $key => $archivo) {

            $nom_real = str_replace('innostudio.de_', '', $archivo->getClientOriginalName());
            $ruta = $archivo->store('public/explorador-archivos/'.auth()->user()->id);
            //$archivo->storeAs("public/proyectos/$proyecto->id/", $nombre);

            $arch = new ExploradorArchivoFile();
            $arch->user_id = auth()->id();
            $arch->carpeta_id = $request->carpeta != 'null' ? $request->carpeta : NULL;
            $arch->ruta = $ruta;
            $arch->name = $nom_real;
            $arch->save();
        }

        return response(200);
    }

    public function change_columna_fila(){
        $user = auth()->user();
        $user->presentacion_archivos = $user->presentacion_archivos == 'fila' ? 'columna' : 'fila';
        $user->save();
        
        return response()->json($user->presentacion_archivos);
    }


    public function nuevacarpeta(Request $request)
    {
        /*
        $nombre = $request->nombre;
        $padre = $request->padre;
        if (Carpetas::where('nombre', $nombre)->where('proyecto_id', $proyecto_id)->first()) {
            $nombre = $this->generarNombreNorep($nombre, 'carpeta', $proyecto_id, $padre);
        }
*/
        $carpeta = new ExploradorArchivoDirectory;
        $carpeta->user_id = auth()->id();
        $carpeta->name = $request->nombre;
        $carpeta->carpeta_id = $request->carpeta != 'null' ? $request->carpeta : NULL;
        $carpeta->save();

        return $this->carpeta_format($carpeta->id);
    }

    //actualiza el nombre de la carpeta
    public function update_nombre(Request $request, Carpetas $carpetas)
    {

        $entidad = null;
        if ($tipo == 'archivo') {
            $entidad = ExploradorArchivoFile::find($request->id);
            $entidad->name = $nombre;
        } else {
            $entidad = ExploradorArchivoDirectory::find($request->id);
            $entidad->name = $nombre;
        }
        $entidad->save();

        return "ok";
    }

    //elimina el archivo o carpeta
    public function delete_elemento(Request $request)
    {
        $entidad_id = $request->id;
        $tipo = $request->tipo;
        $entidad = null;
        if ($tipo == 'archivo') {
            $entidad = ExploradorArchivoFile::find($entidad_id);
            if($this->existsFile($entidad->ruta))
                    Storage::delete($entidad->ruta);
        } else {
            $entidad = Carpetas::find($entidad_id);
        }
        $entidad->delete();
        return "ok";
    }

    //mueve el elemento
    public function mover_elemento(Request $request)
    {

        $entidad = null;
        if ($request->tipo == 'archivo') {
            $entidad = ExploradorArchivoFile::find($request->id);
            $entidad->carpeta_id = $request->destino;
        } else {
            $entidad = Carpetas::find($request->id);
            $entidad->carpeta_id = $request->destino;
        }
        $entidad->save();

        $carpeta = Carpetas::find($request->destino);
        return route('archivos_cliente');
    }

    public function moverCarpetasArchivos(Request $request)
    {

        if ($request->carpetas || $request->archivos) {
            $carpetas = $request->carpetas;

            $archivos = $request->archivos;



            if ($carpetas) {
                foreach ($carpetas as $carpeta) {
                    $entidad = ExploradorArchivoDirectory::find($carpeta);
                    $entidad->carpeta_id = $request->destino;
                    $entidad->save();
                }
            }


            if ($archivos) {
                foreach ($archivos as $archivo) {
                    $doc = ExploradorArchivoFile::find($archivo);
                    $doc->carpeta_id = $request->destino;
                    $doc->save();
                }
            }
                return route('archivos_cliente');
        }
    }

    public function carpetasAjax(Carpetas $carpeta, User $user){
        return $carpeta->carpetas->map(function($e)use($user){
            return $this->carpeta_format($e->id);
            /*
            return [
                'id' => $e->id,
                'nombre' => $e->name,
                'permisos' => $e->user_permisos($user->id),
                'url_show' => [
                    Route('carpetas-ajax', [$e->id, $user->id]),
                    Route('show-carpeta-ajax', [$e->id, $user->id])
                ],
                'imagen' => $e->imagen_personalizada,
                'migas' => $e->migas($user->id),
                'carpetas_count' => $e->carpetas->count()
            ];
            */
        });
    }

    public function showCarpetaAjax(Carpetas $carpeta, User $user){
        return $this->carpeta_format($carpeta->id);
        /*return [
            'id' => $carpeta->id,
            'nombre' => $carpeta->nombre,
            'permisos' => $carpeta->user_permisos($user->id),
            'url_show' => [
                Route('carpetas-ajax', [$carpeta->id, $user->id]),
                Route('show-carpeta-ajax', [$carpeta->id, $user->id])
            ],
            'imagen' => $carpeta->imagen_personalizada,
            'migas' => $carpeta->migas($user->id),
            'carpetas_count' => $carpeta->carpetas->count()
        ];*/
    }

    public function presentacionItem($tipo)
    {
        $user = auth()->user();
        $user->presentacion_archivos =  $tipo;
        $user->save();

        return back();
    }


    public function arbol(){
            $carpetas = ExploradorArchivoDirectory::All();
        return $carpetas->map(function($e){
            return [
                'id' => $e->id,
                'nombre' =>$e->name,
                'padre' => $e->carpeta,
                'ruta_imagen_personalizada' => '',
                'imagen_personalizada' => $e->has_files,
                'permisos' => [
                    'ver' => 1,
                    'editar' => 1,
                    'guardar' => 1,
                    'eliminar' => 1
                ]
            ];
        });
    }
    
}
