<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Model\Admin\ConfigGeneral;
use App\Http\Controllers\Controller;
use App\Traits\FileTraits;

use Session;

class ConfigGeneralController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $configGeneral = ConfigGeneral::all();

        return view('admin.configgeneral.index', compact('configGeneral'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $firma = new ConfigGeneral();

        return view('admin.configgeneral.create', compact('firma'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $f_i = $request->fecha_inicio;
        $f_f = $request->fecha_fin;

        if ($f_i >= $f_f){
            Session::flash('error','La fecha inicial no puede ser despues de la fecha final, ejemplo: Inicio 01/01/2021 - Final 01/01/2022');
            return redirect('/admin/configGeneral/create')->withInput();
        } else {
            $newFecha = new ConfigGeneral();
            $newFecha->nombres = $request->nombres;
            $newFecha->tipo = $request->tipo;
            $newFecha->fecha_inicio = $f_i;
            $newFecha->fecha_fin = $f_f;

            $newFecha->save();
        }

        Session::flash('success','La firma se ha almacenado exitosamente');
        return redirect('admin/configGeneral');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ConfigGeneral  $configGeneral
     * @return \Illuminate\Http\Response
     */
    public function show(ConfigGeneral $configGeneral)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ConfigGeneral  $configGeneral
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $firma = ConfigGeneral::findOrFail($id);

        return view('admin.configgeneral.edit', ['firma' => $firma]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ConfigGeneral  $configGeneral
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $firma = ConfigGeneral::findOrFail($id);
        $f_i = $request->fecha_inicio;
        $f_f = $request->fecha_fin;

        if ($f_i >= $f_f){
            Session::flash('error','La fecha inicial no puede ser despues de la fecha final, ejemplo: Inicio 01/01/2021 - Final 01/01/2022');
            return redirect('/admin/configGeneral/create')->withInput();
        } else {
            $firma->nombres = $request->nombres;
            $firma->tipo = $request->tipo;
            $firma->fecha_inicio = $f_i;
            $firma->fecha_fin = $f_f;

            $firma->save();
        }

        Session::flash('success','La firma se ha actualizado exitosamente');
        return redirect('admin/configGeneral');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ConfigGeneral  $configGeneral
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $firma = ConfigGeneral::findOrFail($id);
        $firma->delete();

        Session::flash('error','Firma borrada correctamente');
        return redirect('/admin/configGeneral');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ConfigGeneral  $configGeneral
     * @return \Illuminate\Http\Response
     */
    public function newImgProy(Request $request)
    {
        if ($request->file()){
            $file = new FileTraits;
            $ruta = $file->Img($request->file('logo'), '', 'masporlasislas');

            Session::flash('success','Imagen de logo actualizado');
            return redirect('admin/configGeneral');
        } else{
            Session::flash('error','No se detecta imagen');
            return redirect('/admin/configGeneral');
        }
    }
}
