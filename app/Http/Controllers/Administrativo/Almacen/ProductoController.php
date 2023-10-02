<?php

namespace App\Http\Controllers\Administrativo\Almacen;

use App\Model\Administrativo\Contabilidad\RegistersPuc;
use App\Model\Administrativo\Almacen\producto;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use App\Traits\FileTraits;
use Session;
use Intervention\Image\ImageManagerStatic as Image;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $items = producto::all();
        return view('administrativo.almacen.producto.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $R1 = RegistersPuc::where('register_puc_id', NULL)->get();

        foreach ($R1 as $r1) {
            $codigoEnd = $r1->code;
            $codigos[] = collect(['id' => $r1->id, 'codigo' => $codigoEnd, 'name' => $r1->name, 'register_id' => $r1->register_puc_id, 'code_N' =>  '', 'name_N' => '', 'naturaleza' => '','per_id' => '']);
            foreach ($r1->codes as $data1){
                $reg0 = RegistersPuc::findOrFail($data1->registers_puc_id);
                $codigo = $reg0->code;
                $codigoEnd = "$r1->code$codigo";
                $codigos[] = collect(['id' => $reg0->id, 'codigo' => $codigoEnd, 'name' => $reg0->name, 'register_id' => $reg0->register_puc_id, 'code_N' =>  '', 'name_N' => '', 'naturaleza' => '','per_id' => '']);
                if ($reg0->codes){
                    foreach ($reg0->codes as $data3){
                        $reg = RegistersPuc::findOrFail($data3->registers_puc_id);
                        $codigo = $reg->code;
                        $codigoF = "$codigoEnd$codigo";
                        $codigos[] = collect(['id' => $reg->id, 'codigo' => $codigoF, 'name' => $reg->name, 'register_id' => $reg->register_puc_id, 'code_N' =>  '', 'name_N' => '', 'naturaleza' => '','per_id' => '']);
                        foreach ($reg->codes as $data4){
                            $reg1 = RegistersPuc::findOrFail($data4->registers_puc_id);
                            $codigo = $reg1->code;
                            $code = "$codigoF$codigo";
                            $codigos[] = collect(['id' => $reg1->id, 'codigo' => $code, 'name' => $reg1->name, 'register_id' => $reg1->register_puc_id, 'code_N' =>  '', 'name_N' => '', 'naturaleza' => '','per_id' => '']);
                            foreach ($reg1->rubro as $rubro){
                                $codigo = $rubro->codigo;
                                $code1 = "$code$codigo";
                                $codigos[] = collect(['id' => $rubro->id, 'codigo' => $code1, 'name' => $rubro->nombre_cuenta, 'code' => $rubro->codigo, 'code_N' =>  $rubro->codigo_NIPS, 'name_N' => $rubro->nombre_NIPS, 'naturaleza' => $rubro->naturaleza,'per_id' => $rubro->persona_id, 'register_id' => $rubro->registers_puc_id]);
                            }
                        }
                    }
                }
            }
        }
        return view('administrativo.almacen.producto.create', compact('codigos'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $prod = new producto();
        $prod->nombre = $request->name;
        $prod->cant_inicial = $request->cant_inicial;
        $prod->cant_actual = $request->cant_inicial;
        $prod->cant_minima = $request->cant_min;
        $prod->cant_maxima = $request->cant_max;
        $prod->metodo = $request->metodo;
        $prod->tipo = $request->tipo;
        $prod->rubros_puc_id = $request->PUC;
        $prod->valor_inicial = $request->valor;
        $prod->valor_actual = $request->valor;
        $prod->save();

        if ($request->file){
            $fileName = $prod->id.'.jpg';
            $path = public_path('/img/productos/'.$fileName);
            Image::make($request->file('file'))->resize(300, 300)->save($path);
        }

        Session::flash('success','El producto se ha creado exitosamente');
        return redirect('administrativo/productos');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Producto  $producto
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $item = producto::findOrFail($id);
        if ($item->tipo == 0){
            $data = $item->inventario;
        } else {
            $data = $item->mueble;
        }
        $cant = $item->cant_inicial;
        $total = $item->valor_inicial;
        foreach ($data as $prod){
            if ($prod->tipo == 0){
                if ($item->tipo == 0){
                    $saldos[] = collect(['cantidad' => $cant + $prod->cantidad, 'total' => $total + $prod->valor_final]);
                    $cant = $cant +  $prod->cantidad;
                    $total = $total + $prod->valor_final;
                    $valEntrada[] = $prod->valor_final;
                }elseif ($item->tipo == 1){
                    $saldos[] = collect(['cantidad' => $cant + $prod->cantidad, 'total' => $total + $prod->nuevo_valor]);
                    $cant = $cant +  $prod->cantidad;
                    $total = $total + $prod->nuevo_valor;
                    $valEntrada[] = $prod->nuevo_valor;
                }
            } else{
                if ($item->tipo == 0){
                    $saldos[] = collect(['cantidad' => $cant - $prod->cantidad, 'total' => $total - $prod->valor_final]);
                    $cant = $cant -  $prod->cantidad;
                    $total = $total - $prod->valor_final;
                    $valSalida[] = $prod->valor_final;
                }elseif ($item->tipo == 1){
                    $saldos[] = collect(['cantidad' => $cant - $prod->cantidad, 'total' => $total - $prod->nuevo_valor]);
                    $cant = $cant -  $prod->cantidad;
                    $total = $total - $prod->nuevo_valor;
                    $valSalida[] = $prod->nuevo_valor;
                }
            }
        }
        if (isset($saldos)){
            if (isset($valSalida)){
                $Saldo = array_last($saldos);
                $finSaldo = $Saldo['total'];
                $finEntrada = array_sum($valEntrada) + $item->valor_inicial;
                $finSalida = array_sum($valSalida);
            } else {
                $Saldo = array_last($saldos);
                $finSaldo = $Saldo['total'];
                $finEntrada = array_sum($valEntrada) + $item->valor_inicial;
                $finSalida = 0;
            }
        } else {
            $saldos = 0;
            $finSaldo = $item->valor_inicial;
            $finSalida = 0;
            $finEntrada = $item->valor_inicial;
        }

        return view('administrativo.almacen.producto.show', compact('item', 'data', 'saldos','finEntrada','finSalida','finSaldo'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Producto  $producto
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $item = producto::findOrFail($id);

        $R1 = RegistersPuc::where('register_puc_id', NULL)->get();

        foreach ($R1 as $r1) {
            $codigoEnd = $r1->code;
            $codigos[] = collect(['id' => $r1->id, 'codigo' => $codigoEnd, 'name' => $r1->name, 'register_id' => $r1->register_puc_id, 'code_N' =>  '', 'name_N' => '', 'naturaleza' => '','per_id' => '']);
            foreach ($r1->codes as $data1){
                $reg0 = RegistersPuc::findOrFail($data1->registers_puc_id);
                $codigo = $reg0->code;
                $codigoEnd = "$r1->code$codigo";
                $codigos[] = collect(['id' => $reg0->id, 'codigo' => $codigoEnd, 'name' => $reg0->name, 'register_id' => $reg0->register_puc_id, 'code_N' =>  '', 'name_N' => '', 'naturaleza' => '','per_id' => '']);
                if ($reg0->codes){
                    foreach ($reg0->codes as $data3){
                        $reg = RegistersPuc::findOrFail($data3->registers_puc_id);
                        $codigo = $reg->code;
                        $codigoF = "$codigoEnd$codigo";
                        $codigos[] = collect(['id' => $reg->id, 'codigo' => $codigoF, 'name' => $reg->name, 'register_id' => $reg->register_puc_id, 'code_N' =>  '', 'name_N' => '', 'naturaleza' => '','per_id' => '']);
                        foreach ($reg->codes as $data4){
                            $reg1 = RegistersPuc::findOrFail($data4->registers_puc_id);
                            $codigo = $reg1->code;
                            $code = "$codigoF$codigo";
                            $codigos[] = collect(['id' => $reg1->id, 'codigo' => $code, 'name' => $reg1->name, 'register_id' => $reg1->register_puc_id, 'code_N' =>  '', 'name_N' => '', 'naturaleza' => '','per_id' => '']);
                            foreach ($reg1->rubro as $rubro){
                                $codigo = $rubro->codigo;
                                $code1 = "$code$codigo";
                                $codigos[] = collect(['id' => $rubro->id, 'codigo' => $code1, 'name' => $rubro->nombre_cuenta, 'code' => $rubro->codigo, 'code_N' =>  $rubro->codigo_NIPS, 'name_N' => $rubro->nombre_NIPS, 'naturaleza' => $rubro->naturaleza,'per_id' => $rubro->persona_id, 'register_id' => $rubro->registers_puc_id]);
                            }
                        }
                    }
                }
            }
        }

        return view('administrativo.almacen.producto.edit', compact('item', 'codigos'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Producto  $producto
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $item = producto::findOrFail($id);
        $item->nombre = $request->name;
        $item->cant_minima = $request->cant_min;
        $item->cant_maxima = $request->cant_max;
        $item->metodo = $request->metodo;
        $item->tipo = $request->tipo;
        $item->rubros_puc_id = $request->PUC;
        $item->save();

        if ($request->file){
            $file = new FileTraits;
            $ruta = $file->Img($request->file('file'), 'productos', $item->id);
        }

        Session::flash('success','El producto se ha actualizado correctamente');
        return redirect('administrativo/productos');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Producto  $producto
     * @return \Illuminate\Http\Response
     */
    public function destroy(Producto $producto)
    {
        //
    }
}
