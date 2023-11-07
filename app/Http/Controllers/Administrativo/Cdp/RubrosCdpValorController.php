<?php

namespace App\Http\Controllers\Administrativo\Cdp;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Administrativo\Cdp\RubrosCdpValor;

use Session;

class RubrosCdpValorController extends Controller
{
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $rubrosCdpValor = RubrosCdpValor::where('rubrosCdp_id', $id)->get();
        foreach ($rubrosCdpValor as $borrar){
            $borrar->delete();
        }
        Session::flash('error','Dinero liberado correctamente del rubro');
    }
}
