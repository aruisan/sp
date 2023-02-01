<?php

namespace App\Http\Controllers\Administrativo\Cdp;

use App\Model\Hacienda\Presupuesto\Rubro;
use App\Model\Administrativo\Cdp\RubrosCdpValor;
use App\Model\Administrativo\Cdp\RubrosCdp;
use App\Model\Administrativo\Cdp\Cdp;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Session;

class RubrosCdpController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $cdp_id = $request->cdp_id;
        $rubros = $request->rubro_id;
        $fuenteRubroId = $request->fuente_id;
        $valorFuente = $request->valorFuenteUsar;
        $rubrosCdpId = $request->rubros_cdp_id;
        $rubrosCdpValorId = $request->rubros_cdp_valor_id;
        $fuentesDep = $request->fuenteDep_id;

        if ($rubros != null){
            $count = count($rubros);
            for($i = 0; $i < $count; $i++){
                $rubrosCdp = new RubrosCdp();
                $rubrosCdp->cdp_id = $cdp_id;
                $rubrosCdp->rubro_id = $rubros[$i];
                $rubrosCdp->save();
            }
        }

        if ($valorFuente != null){
            $countV = count($valorFuente);
            for($i = 0; $i < $countV; $i++){
                if ($rubrosCdpValorId[$i]) $this->updateV($rubrosCdpValorId[$i], $valorFuente[$i]);
                else{
                    $rubrosCdpValor = new RubrosCdpValor();
                    $rubrosCdpValor->valor = $valorFuente[$i];
                    $rubrosCdpValor->valor_disp = $valorFuente[$i];
                    $rubrosCdpValor->fontsRubro_id = $fuenteRubroId[$i];
                    $rubrosCdpValor->cdp_id = $cdp_id;
                    $rubrosCdpValor->rubrosCdp_id = $rubrosCdpId[$i];
                    if ($fuentesDep != null) $rubrosCdpValor->fontsDep_id = $fuentesDep[$i];
                    $rubrosCdpValor->save();
                }
            }
        }

        Session::flash('success','Rubros asignados correctamente');
        return  back();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\RubrosCdp  $rubrosCdp
     * @return \Illuminate\Http\Response
     */
    public function update($id, $rubroId)
    {
        $rubrosCdp = RubrosCdp::findOrFail($id);
        $rubrosCdp->rubro_id = $rubroId;
        $rubrosCdp->save();
    }

    public function updateV($id,$valor)
    {
        $cambiarValor = RubrosCdpValor::findOrFail($id);
        $cambiarValor->valor = $valor;
        $cambiarValor->valor_disp = $valor;
        $cambiarValor->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\RubrosCdp  $rubrosCdp
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $rubroCdp = RubrosCdp::find($id);
        $valorResta = $rubroCdp->rubros->fontsRubro->sum('valor');
        $cdp_id = $rubroCdp->cdp_id;
        //$this->restarDinero($cdp_id, $valorResta);
        $idValores = $rubroCdp->rubrosCdpValor;
        Session::flash('error','Rubro eliminado correctamente del CDP');
        $rubroCdp->delete();

    }

    public function restarDinero($id, $valor)
    {
        $cdp = Cdp::findOrFail($id);
        $cdp->valor = $cdp->valor - $valor;
        $cdp->save();
    }
}
