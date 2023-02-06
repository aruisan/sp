<?php

namespace App\Http\Controllers\Administrativo\Tesoreria\retefuente;

use App\Model\Administrativo\Tesoreria\retefuente\Certificado;
use App\Model\Persona;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Session;

class CertificadoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $personas = Persona::all();
        return view('administrativo.tesoreria.retefuente.certificado', compact('personas'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function getCert(Request $request)
    {
        dd($request);
    }
}
