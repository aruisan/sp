<?php

namespace App\Http\Controllers\Administrativo\Tesoreria;

use App\Model\Administrativo\Contabilidad\RegistersPuc;
use App\Model\Administrativo\Tesoreria\bancos;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Session;

class BancosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $items = bancos::all();
        return view('administrativo.bancos.index',compact('items'));
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

        return view('administrativo.bancos.create', compact('codigos'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $bank = new bancos();
        $bank->numero_cuenta = $request->num;
        $bank->descripcion = $request->descripcion;
        $bank->valor_inicial = $request->value;
        $bank->valor_actual = $request->value;
        $bank->estado = "0";
        $bank->rubros_puc_id = $request->PUC;
        $bank->save();

        Session::flash('success','El banco se ha almacenado exitosamente');
        return redirect('administrativo/bancos');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\bancos  $bancos
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $item = bancos::findOrFail($id);

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

        return view('administrativo.bancos.show',compact('item','codigos'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\bancos  $bancos
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $item = bancos::findOrFail($id);
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

        return view('administrativo.bancos.edit',compact('item','codigos'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\bancos  $bancos
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $bank = bancos::findOrFail($id);
        $bank->numero_cuenta = $request->num;
        $bank->descripcion = $request->descripcion;
        $bank->rubros_puc_id = $request->PUC;
        $bank->save();

        Session::flash('success','El banco se ha actualizado exitosamente');
        return redirect('administrativo/bancos/'.$bank->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\bancos  $bancos
     * @return \Illuminate\Http\Response
     */
    public function destroy(bancos $bancos)
    {
        //
    }
}
