<?php

namespace App\Http\Controllers\Almacen;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\AlmacenFactura;

class FacturaController extends Controller
{
    public function update(Request $request, AlmacenFactura $factura){
        $data_factura_update = $request->except(['nombre_articulo', 'codigo', 'referencia', 'cantidad', 'valor_unitario', 'ccd', 'ccc', 'tipo']);
        $factura->update($data_factura_update);
        foreach($request->nombre_articulo as $k => $articulo):
            $factura->articulos()->create([
                'nombre_articulo' => $request->nombre_articulo[$k],
                'codigo' => $request->codigo[$k],
                'referencia' => $request->referencia[$k],
                'cantidad' => $request->cantidad[$k],
                'valor_unitario' => $request->valor_unitario[$k],
                'ccd' => $request->ccd[$k],
                'ccc' => $request->ccc[$k],
                'tipo' => $request->tipo[$k],
                'dependencia_id' => 1
            ]);
        endforeach;
        return redirect()->route('almacen.index');
    }


    public function store(Request $request){

        $nuevo_empleado = Empleado::create($request->all());//crea el usuario
        $nuevo_empleado->sueldos()->create(['sueldo' => $request->sueldo]);//crea el sueldo al usuario

        $ruta = "empleados/{$nuevo_empleado->id}/";  // genera la ruta
        $certificado_bancario = $this->File($request->file_certificado_bancario, $carpeta);//guardan archivos
        $apto_administrativo_archivo = $this->File($request->apto_administrativo_archivo, $carpeta);//guardan archivos

        $nuevo_empleado->certificado_bancario = $certificado_bancario;//editan empleado
        $nuevo_empleado->apto_administrativo_archivo = $apto_administrativo_archivo;//editan empleado
        $nuevo_empleado->save();//guarda base de datos

        return back();
    }


}
