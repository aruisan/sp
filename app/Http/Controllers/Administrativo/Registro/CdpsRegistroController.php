<?php

namespace App\Http\Controllers\Administrativo\Registro;

use App\BPin;
use App\Model\Admin\DependenciaRubroFont;
use App\Model\Administrativo\Cdp\BpinCdpValor;
use App\Model\Administrativo\Cdp\Cdp;
use App\Model\Administrativo\Registro\CdpsRegistro;
use App\Model\Administrativo\Registro\CdpsRegistroValor;
use App\Model\Administrativo\Registro\Registro;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Session;

class CdpsRegistroController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $cdps = $request->cdp_id_s;

        if ($cdps != null) {

            $registro_id = $request->registro_id;
            $registro = Registro::findOrFail($registro_id);
            $registro->iva = $request->iva;
            $registro->save();
            $count = count($cdps);

            for($i = 0; $i < $count; $i++){
                $cdp = Cdp::findOrFail($cdps[$i]);

                if ($cdp->tipo == "Funcionamiento"){
                    $fuenteRubroId = $request->fuente_id;
                    $rubroId = $request->rubro_id;
                    $valorRubro = $request->valorFuenteUsar;
                    $rubrosCdpId = $request->rubros_cdp_id;
                    $rubrosCdpValorId = $request->rubros_cdp_valor_id;

                    if ($valorRubro != null){

                        $countV = count($valorRubro);

                        for($i = 0; $i < $countV; $i++){

                            if ($rubrosCdpValorId[$i]){
                                $this->updateV($rubrosCdpValorId[$i], $valorRubro[$i]);
                            }else{
                                $cdpsRegistroValor = new CdpsRegistroValor();
                                $cdpsRegistroValor->valor = $valorRubro[$i];
                                $cdpsRegistroValor->valor_disp = $valorRubro[$i];
                                $cdpsRegistroValor->fontsRubro_id = $fuenteRubroId[$i];
                                $cdpsRegistroValor->registro_id = $registro_id;
                                $cdpsRegistroValor->cdp_id = $cdps[$i];
                                //$cdpsRegistroValor->rubro_id = $rubroId[$i];
                                $cdpsRegistroValor->cdps_registro_id = $rubrosCdpId[$i];
                                $cdpsRegistroValor->save();
                            }
                        }
                    }
                } else{
                    $rubroId = $request->rubro_id;
                    $valorActividad = $request->valorActividadUsar;
                    $rubrosCdpId = $request->rubros_cdp_id;
                    $bpinCdpValorId = $request->bpin_cdp_valor_id;

                    if ($valorActividad != null){

                        $countV = count($valorActividad);

                        for($i = 0; $i < $countV; $i++){

                            if ($bpinCdpValorId[$i]){
                                $this->updateV($bpinCdpValorId[$i], $valorActividad[$i]);
                            }else{
                                $bpin = BPin::find($request->bpin_id[$i]);
                                if ($bpin){

                                    $bpinCdpValor = BpinCdpValor::where('cdp_id', $cdps[$i])->first();
                                    dd($bpinCdpValor);
                                    $depRubroFont = DependenciaRubroFont::find($bpinCdpValor->dependencia_rubro_font_id);

                                    $cdpsRegistroValor = new CdpsRegistroValor();
                                    $cdpsRegistroValor->valor = $valorActividad[$i];
                                    $cdpsRegistroValor->valor_disp = $valorActividad[$i];
                                    $cdpsRegistroValor->fontsRubro_id = $depRubroFont->rubro_font_id;
                                    $cdpsRegistroValor->registro_id = $registro_id;
                                    $cdpsRegistroValor->cdp_id = $cdps[$i];
                                    //$cdpsRegistroValor->rubro_id = $rubroId[$i];
                                    $cdpsRegistroValor->cdps_registro_id = $rubrosCdpId[$i];
                                    $cdpsRegistroValor->save();

                                } else{
                                    Session::flash('warning','BPIN no detectado');
                                    return back();
                                }

                            }
                        }
                    }
                }
            }
        }
        Session::flash('success','Cdps asignados correctamente');
        return back();
    }

    public function updateV($id,$valor)
    {
        $cambiarValor = CdpsRegistroValor::findOrFail($id);
        $cambiarValor->valor = $valor;
        $cambiarValor->valor_disp = $valor;
        $cambiarValor->save();
    }

    public function destroy($id)
    {
        $cdpsRegistro = CdpsRegistro::findOrFail($id);
        $cdpsRegistro->delete();

        Session::flash('error','CDP eliminado correctamente del registro');
    }
}
