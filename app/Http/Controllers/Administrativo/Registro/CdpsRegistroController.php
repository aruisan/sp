<?php

namespace App\Http\Controllers\Administrativo\Registro;

use App\BPin;
use App\bpinVigencias;
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
            if ($request->value_water){
                $registro->value_water = $request->value_water;
                $registro->act_water = $request->actividad;
                $registro->font_water = $request->font_water;
                $registro->loc_water = $request->loc_water;
            }
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
                    $valorActividad = $request->valorActividadUsar;
                    $rubrosCdpId = $request->rubros_cdp_id;

                    if ($valorActividad != null){

                        $countV = count($valorActividad);

                        for($i = 0; $i < $countV; $i++){

                            if (isset($request->cdp_registro_valor_id[$i])){
                                $bpinCdpValor = BpinCdpValor::find($request->bpin_cdp_valor_id[$i]);
                                if ($bpinCdpValor->valor_disp >= intval($valorActividad[$i])){
                                    $depRubroFont = DependenciaRubroFont::find($bpinCdpValor->dependencia_rubro_font_id);

                                    $cdpsRegistroValor = CdpsRegistroValor::find($request->cdp_registro_valor_id[$i]);
                                    $cdpsRegistroValor->valor = $valorActividad[$i];
                                    $cdpsRegistroValor->valor_disp = $valorActividad[$i];
                                    $cdpsRegistroValor->fontsRubro_id = $depRubroFont->rubro_font_id;
                                    $cdpsRegistroValor->registro_id = $registro_id;
                                    $cdpsRegistroValor->cdp_id = $cdps[$i];
                                    $cdpsRegistroValor->cdps_registro_id = $rubrosCdpId[$i];
                                    $cdpsRegistroValor->bpin_cdp_valor_id = $request->bpin_cdp_valor_id[$i];
                                    $cdpsRegistroValor->save();
                                } else{
                                    Session::flash('warning','Dinero disponible de la fuente del CDP es menos al solicitado. Revisar valores');
                                    return back();
                                }
                            }else{
                                $bpinCdpValor = BpinCdpValor::find($request->bpin_cdp_valor_id[$i]);
                                if ($bpinCdpValor->valor_disp >= intval($valorActividad[$i])){
                                    $depRubroFont = DependenciaRubroFont::find($bpinCdpValor->dependencia_rubro_font_id);

                                    $cdpsRegistroValor = new CdpsRegistroValor();
                                    $cdpsRegistroValor->valor = $valorActividad[$i];
                                    $cdpsRegistroValor->valor_disp = $valorActividad[$i];
                                    $cdpsRegistroValor->fontsRubro_id = $depRubroFont->rubro_font_id;
                                    $cdpsRegistroValor->registro_id = $registro_id;
                                    $cdpsRegistroValor->cdp_id = $cdps[$i];
                                    $cdpsRegistroValor->cdps_registro_id = $rubrosCdpId[$i];
                                    $cdpsRegistroValor->bpin_cdp_valor_id = $request->bpin_cdp_valor_id[$i];
                                    $cdpsRegistroValor->save();
                                } else{
                                    Session::flash('warning','Dinero disponible de la fuente del CDP es menos al solicitado. Revisar valores');
                                    return back();
                                }
                            }
                        }
                    }
                }
            }
        }
        Session::flash('success','Dinero tomado de las fuentes del CDP correctamente');
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
