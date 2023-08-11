<?php

namespace App\Http\Controllers\Coso;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\CosoIndividuo;
use PDF;

class IndividuoController extends Controller
{
    public function index(){
        $individuos = CosoIndividuo::all();
        return view('coso.index', compact('individuos'));
    }

    public function show(CosoIndividuo $individuo){
        return view('coso.show', compact('individuo'));
    } 

    public function store(Request $request){
        $new = CosoIndividuo::create($request->all());
        return redirect()->route('coso.individuo.index');
    }

    public function pdf(CosoIndividuo $individuo){
        //dd($individuo);
        $pdf = PDF::loadView('coso.pdf', compact('individuo'))->setOptions(['images' => true,'isRemoteEnabled' => true]);
        return $pdf->stream();
        return $pdf->download('invoice.pdf');
        //return view('coso.show', compact('individuo'));
    } 
}
