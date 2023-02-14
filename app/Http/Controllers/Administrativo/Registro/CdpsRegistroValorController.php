<?php

namespace App\Http\Controllers\Administrativo\Registro;

use App\Model\Administrativo\Registro\CdpsRegistroValor;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;

class CdpsRegistroValorController extends Controller
{
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CdpsRegistroValor  $cdpsRegistroValor
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $cdpsRegistrosValor = CdpsRegistroValor::where('cdps_registro_id', $id)->get();
        foreach ($cdpsRegistrosValor as $borrar){
            $borrar->delete();
        }
        Session::flash('error','Dinero liberado correctamente del registro');
    }
}
