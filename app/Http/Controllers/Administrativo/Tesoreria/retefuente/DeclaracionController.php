<?php

namespace App\Http\Controllers\Administrativo\Tesoreria\retefuente;

use App\Model\Administrativo\Tesoreria\retefuente\Declaracion;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Session;

class DeclaracionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('administrativo.tesoreria.retefuente.declaracion.index');
    }

}
