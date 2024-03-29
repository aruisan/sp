<?php

namespace App\Model\Administrativo\Contabilidad;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use App\Model\Administrativo\Tesoreria\conciliacion\ConciliacionBancaria;
use App\Model\Administrativo\Pago\PagoBanks;
use App\Model\Administrativo\Pago\PagoBanksNew;
use App\Model\Administrativo\Pago\Pagos;
use App\Model\Administrativo\ComprobanteIngresos\ComprobanteIngresosMov;
use App\Model\Administrativo\Contabilidad\CompContMov;
use App\Model\Administrativo\ComprobanteIngresos\ComprobanteIngresos;
use App\Model\Administrativo\Contabilidad\PucAlcaldia;
use App\Model\Administrativo\OrdenPago\DescMunicipales\DescMunicipales;
use App\Model\Administrativo\OrdenPago\RetencionFuente\RetencionFuente;
use App\Model\Administrativo\OrdenPago\OrdenPagos;
use App\AlmacenComprobanteEgreso;
use App\ChipContabilidadData;
use \Carbon\Carbon;
use App\Model\Administrativo\OrdenPago\OrdenPagosPuc;
use App\Model\Administrativo\OrdenPago\OrdenPagosDescuentos;
use App\AlmacenArticulo;
use Session;



class PucAlcaldia extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = "puc_alcaldia";
    protected $appends = ['almacen_puc_credito', 'level'];

    public function contabilidad_data(){
        return $this->hasOne(ChipContabilidadData::class, 'puc_id');
    }

    public function almacen_items(){
        return $this->hasMany(AlmacenArticulo::class, 'ccd');
    }

    public function almacen_salidas(){

    }

    public function almacen_items_mensual(){
        return $this->hasMany(AlmacenArticulo::class, 'ccd')->whereYear('created_at', Carbon::today()->format('Y'))->whereMonth('created_at', Session::get(auth()->id().'-mes-informe-contable-mes'));
    }

    public function almacen_pucs_creditos(){
        return $this->belongsToMany(PucAlcaldia::class, 'almacen_puc_relaciones', 'puc_debito_id', 'puc_credito_id');
    }

    public function almacen_pucs_debitos(){
        return $this->belongsToMany(PucAlcaldia::class, 'almacen_puc_relaciones', 'puc_credito_id', 'puc_debito_id');
    }

    public function getAlmacenPucCreditoAttribute(){
        return $this->almacen_pucs_creditos->first();
    }

    public function getAlmacenItemsCreditosAttribute(){
        $data = collect();
        foreach($this->almacen_pucs_debitos->filter(function($debito){return $debito->almacen_items->count() > 0 ;}) as $puc_debito):
            foreach($puc_debito->almacen_items as $item):
                $data->push($item);
            endforeach;
        endforeach;
        return $data;
    }

    public function conciliaciones() {
        return $this->hasMany(ConciliacionBancaria::class, 'puc_id');
    }

    public function hijos(){
        return $this->hasMany(PucAlcaldia::class, 'padre_id');
    }

    public function padre(){
        return $this->belongsTo(PucAlcaldia::class, 'padre_id');
    }

    //odenes de pago
    public function orden_pagos() {
        return $this->hasMany(OrdenPagosPuc::class, 'rubros_puc_id');
    }

    public function orden_pagos_mensual() {
        return $this->hasMany(OrdenPagosPuc::class, 'rubros_puc_id')->whereYear('created_at', Carbon::today()->format('Y'))->whereMonth('created_at', Session::get(auth()->id().'-mes-informe-contable-mes'));
    }

    //pagos
    public function pagos_bank(){
        return $this->hasMany(PagoBanks::class, 'rubros_puc_id')->whereYear('created_at', Carbon::today()->format('Y'));
    }

    public function pagos_bank_mensual(){
        return $this->hasMany(PagoBanks::class, 'rubros_puc_id')->whereYear('created_at', Carbon::today()->format('Y'))->whereMonth('created_at', Session::get(auth()->id().'-mes-informe-contable-mes'));
    }

    public function getComprobantesAttribute(){
        return ComprobanteIngresosMov::where('cuenta_banco', $this->id)->orwhere('cuenta_puc_id', $this->id)->whereYear('created_at', Carbon::today()->format('Y'))->get();
    }

    public function almacen_entradas_debito($year, $month){
        //return $this->almacen_items;
        $items = collect();
        if($this->almacen_items->count() > 0){
            $items =  $this->almacen_items->filter(
                function($ai)use($year, $month){ 
                    return Carbon::parse($ai->comprobante_ingreso->fecha_factura)->month  == $month && Carbon::parse($ai->comprobante_ingreso->fecha_factura)->year  == $year;
                })->map(function($a){
                    return [
                        'articulo' => $a->nombre_articulo,
                        'cantidad' => $a->cantidad,
                        'valor' => $a->valor_unitario,
                        'total' => $a->total 
                    ];
                });
        }
            
        return $items;
    }


    public function almacen_entradas_credito($year, $month){
        $ccds_id =  $this->almacen_pucs_debitos->pluck('id')->toArray();
        $items = AlmacenArticulo::whereIn('ccd', $ccds_id)->get();
        $articulos = collect();
        //return $items;
        if($items->count() > 0){
            $articulos = $items->filter(
                function($ai)use($year, $month){ 
                    return Carbon::parse($ai->comprobante_ingreso->fecha_factura)->month  == $month && Carbon::parse($ai->comprobante_ingreso->fecha_factura)->year  == $year;
                })->map(function($a){
                    return [
                        'articulo' => $a->nombre_articulo,
                        'cantidad' => $a->cantidad,
                        'valor' => $a->valor_unitario,
                        'total' => $a->total 
                    ];
                });
        }

        return $articulos;
    }

    public function almacen_salidas_credito($year, $month){
        $salidas = AlmacenComprobanteEgreso::where('ccc', $this->id)->get();
        $articulos = collect();
        if($salidas->count() > 0 ){
            $salidas_ = $salidas->filter(function($s) use($year, $month){
                return Carbon::parse($s->fecha)->month  == $month && Carbon::parse($s->fecha)->year  == $year;
            });
            foreach($salidas_ as $salida):
                foreach($salida->salidas_pivot as $articulo_salida): 
                    $articulos->push([
                        'articulo' => $articulo_salida->articulo->nombre_articulo,
                        'cantidad' => $articulo_salida->cantidad,
                        'valor' => $articulo_salida->articulo->valor_unitario,
                        'total' => $articulo_salida->total 
                    ]);
                endforeach;
            endforeach; 

            
        }

        return $articulos;
    }

    public function almacen_salidas_debito($year, $month){
        //return $this->almacen_items;
        $articulos = collect();
        $salidas =  AlmacenComprobanteEgreso::whereMonth('fecha', $month)->whereYear('fecha', $year)->get()->filter(function($e){
            return in_array($this->id, $e->ccd);
        });

        foreach($salidas as $salida): 
            foreach($salida->salidas_pivot as $articulo_salida): 
                if($articulo_salida->articulo->ccd == $this->id):
                    $articulos->push([
                        'articulo' => $articulo_salida->articulo->nombre_articulo,
                        'cantidad' => $articulo_salida->cantidad,
                        'valor' => $articulo_salida->articulo->valor_unitario,
                        'total' => $articulo_salida->total 
                    ]);
                endif;
            endforeach;
        endforeach;
        return $articulos;
    }
    

    //retefuente 1
    public function retefuente_movimientos(){
        return $this->hasMany(CompContMov::class, 'cuenta_puc_id')->where('comp_cont_id', '>', 2);
    }

    ////retencion en la fuente  1
    public function getRetefuenteMensualAttribute(){
        $credito = 0;
        $OPRets = RetencionFuente::where('codigo', $this->code)->get();
        foreach ($OPRets as $retFuen){
            $OPD = OrdenPagosDescuentos::where('retencion_fuente_id', $retFuen->id)->get();
            foreach ($OPD as $descRet){
                $OP = OrdenPagos::find($descRet->orden_pagos_id);
                if ($OP){
                    if ($OP->estado == "1" and Carbon::parse($OP->created_at)->year == 2023){
                        if (Carbon::parse($OP->created_at)->month >=1 and 
                        Carbon::parse($OP->created_at)->month <= 1){
                                $credito += $descRet->valor;
                        }
                    }
                }
            }

        }

        return $credito;
        
    }

    ////descuento municipal 2
    public function getDescuentoMunicipalMensualAttribute(){
        $credito = 0;

        $OPDescMunicipal = DescMunicipales::where('codigo', $this->code)->get();
        foreach ($OPDescMunicipal as $OPDescMuni){
            $OPD = OrdenPagosDescuentos::where('desc_municipal_id', $OPDescMuni->id)->get();
            foreach ($OPD as $descRet){
                $OP = OrdenPagos::find($descRet->orden_pagos_id);
                if ($OP){
                    if ($OP->estado == "1" and Carbon::parse($OP->created_at)->year == 2023){
                        if (Carbon::parse($OP->created_at)->month >= 1  and
                            Carbon::parse($OP->created_at)->month <= 1){
                            $credito += $descRet->valor;
                        }
                    }
                }
            }
        }

        return $credito;
    }

    ///descuentos 3
    public function getDescuentoMensualAttribute(){
        $credito = 0;

        $OPD = OrdenPagosDescuentos::where('cuenta_puc_id', $this->id)->get();
        foreach ($OPD as $descRet){
            $OP = OrdenPagos::find($descRet->orden_pagos_id);
            if ($OP){
                if ($OP->estado == "1" and Carbon::parse($OP->created_at)->year == 2023){
                    if (Carbon::parse($OP->created_at)->month >= 1 and
                        Carbon::parse($OP->created_at)->month <= 1){
                        $credito += $descRet->valor;
                    }
                }
            }
        }

        return $credito;
    }

    //pagos puc 4
    public function getPagosPucMensualAttribute(){
        $credito = 0;
        $debito = 0;

        $OPD = OrdenPagosPuc::where('rubros_puc_id', $this->id)->get();
            foreach ($OPD as $descRet){
                $OP = OrdenPagos::find($descRet->orden_pago_id);
                if ($OP){
                    if ($OP->estado == "1" and Carbon::parse($OP->created_at)->year == 2023){
                        if (Carbon::parse($OP->created_at)->month >= 1 and
                        Carbon::parse($OP->created_at)->month <= 1){
                            $credito += $descRet->valor_credito;
                            $debito += $descRet->valor_debito;
                        }
                    }
                }
            }

        return ['credito' => $credito, 'debito' => $debito];
    }

    ///poagos banck new 5
    public function getPagosBankNewMensualAttribute(){
        $credito = 0;
        $debito = 0;

        $PagosPUCs = PagoBanksNew::where('rubros_puc_id', $this->id)->get();
        foreach ($PagosPUCs as $descRet){
            $pago = Pagos::find($descRet->pagos_id);
            if ($pago){
                if ($pago->estado == "1" and Carbon::parse($pago->created_at)->year == 2023){
                    if (Carbon::parse($pago->created_at)->month >= 1 and
                    Carbon::parse($pago->created_at)->month <= 1){
                        $credito += $descRet->credito;
                        $debito += $descRet->debito;
                    }
                }
            }
        }

        return ['credito' => $credito, 'debito' => $debito];
    }

    //comprobantes de ingreso son los de banco 6
    public function getComprobantesPucMensualAttribute(){
        $credito = 0;
        $debito = 0;

        $compContMovs = ComprobanteIngresosMov::where('cuenta_puc_id', $this->id)->get();
        foreach ($compContMovs as $descRet){
            $compCont = ComprobanteIngresos::find($descRet->comp_id);
            if ($compCont){
                if ($compCont->estado == "1" and Carbon::parse($compCont->created_at)->year == 2023){
                    if (Carbon::parse($compCont->ff)->month >= 1 and
                    Carbon::parse($compCont->ff)->month <= 1){
                        $credito += $descRet->credito;
                        $debito += $descRet->debito;
                    }
                }
            }
        }
        return ['credito' => $credito, 'debito' => $debito];
    }

    public function getComprobantesBancoMensualAttribute(){
        $credito = 0;
        $debito = 0;

        $compContMovs = ComprobanteIngresosMov::where('cuenta_banco', $this->id)->get();
        foreach ($compContMovs as $descRet){
            $compCont = ComprobanteIngresos::find($descRet->comp_id);
            if ($compCont){
                if ($compCont->estado == "1" and Carbon::parse($compCont->created_at)->year == 2023){
                    if (Carbon::parse($compCont->ff)->month >= 1 and
                    Carbon::parse($compCont->ff)->month <= 1){
                        $credito += $descRet->credito;
                        $debito += $descRet->debito;
                    }
                }
            }
        }
        return ['credito' => $credito, 'debito' => $debito];
    }





    //suma de otros pucs 
    public function getOtrosOrdenesPagoPucsAttribute(){
        $data = collect();
        $op_pucs = $this->orden_pagos->count() == 0 ? [] : $this->orden_pagos->filter(function($op_p){ return $op_p->has_pucs;});
        foreach($op_pucs as $op_puc):
            foreach($op_puc->ordenPago->pucs as $orden_pago_puc):
                $data->push($orden_pago_puc);
            endforeach;
        endforeach;
        
        return $data;
    }

    public function getOtrosOrdenesPagoPucsMensualAttribute(){
        $data = collect();
        $op_pucs = $this->orden_pagos_mensual->count() == 0 ? [] : $this->orden_pagos_mensual->filter(function($op_p){ return $op_p->has_pucs;});
        foreach($op_pucs as $op_puc):
            foreach($op_puc->ordenPago->otras_ordenes_con_pucs_mensual as $orden_pago_puc):
                $data->push($orden_pago_puc);
            endforeach;
        endforeach;
        
        return $data;
    }

/*
    public function comprobantes_contables_movimientos(){
        return $this->hasMany(CompContMov::class, 'cuenta_puc_id')->whereYear('created_at', Carbon::today()->format('Y'));
    }
*/

    public function getLevelAttribute(){
        $count_string = strlen($this->code);
        if($count_string == 4){
            return 3;
        }elseif($count_string == 6){
            return 4;
        }elseif($count_string == 10){
            return 5;
        }else{
            return $count_string;
        }

    }

    public function getCodigoPuntoAttribute(){
        $secuencia = [1,2,4,6];
        $numero = '';
        $caracteres_array = str_split($this->code);
        foreach($caracteres_array as $index => $letra):
            if(in_array($index, $secuencia)){
                $numero .= ".";
            }
            $numero .= $letra;
        endforeach;

        return is_null($this->padre) ? $this->code : $numero;
    }

    public function getDebCredTrimestreAttribute(){
        $trimestre = [1,4,7,10];
        //$age = Session::get(auth()->id().'-mes-informe-chip-age');
        //$mes_ = $trimestre[Session::get(auth()->id().'-mes-informe-chip-trimestre')];
        //$mes = $mes_ < 10 ? "0{$mes_}" : $mes_;
        //$mes_final = $mes_ < 10 ? "0".$mes_+2 : $mes_+2;
        //$age =  Carbon::today()->format('Y');
        $totCred = 0;
        $totDeb = 0;
        $dia_final = date("t", strtotime("2023-{$mes_final}-01"));
        $inicio = "{$age}-{$mes}-1";
        $final = "{$age}-{$mes_final}-{$dia_final}";

        if($this->otros_ordenes_pago_pucs->count() > 0):
            $otros_pucs = $this->otros_ordenes_pago_pucs->where('created_at', '>=', $inicio)->where('created_at', '<=', $final);
            $totDeb += $otros_pucs->count() == 0 ? 0 :  $otros_pucs->sum('valor_debito');
            $totCred += $otros_pucs->count() == 0 ? 0 : $otros_pucs->sum('valor_credito');
        endif;

        if($this->almacen_items->count() > 0):
            $totDeb += $this->almacen_items->sum('total');
        endif;

        if($this->almacen_items_creditos->count() > 0):
            $totDeb += $this->almacen_items_creditos->sum('total');
        endif;

        if($this->pagos_bank->count() > 0):
            $totCred += $this->pagos_bank->count() > 0 ? $this->pagos_bank->where('created_at', '>=', $inicio)->where('created_at', '<=', $final)->filter(function($p){ return $p->pago->estado == 1 && $p->pago->adultoMayor == 0;})->sum('valor') : 0;
        endif;

        if($this->comprobantes->count() > 0):
            $totDeb += $this->comprobantes->count() > 0 ? $this->comprobantes->where('created_at', '>=', $inicio)->where('created_at', '<=', $final)->sum('debito') : 0;
            $totCred += $this->comprobantes->count() > 0 ? $this->comprobantes->where('created_at', '>=', $inicio)->where('created_at', '<=', $final)->sum('credito') : 0;
        endif;

        if($this->orden_pagos->count() > 0):
            $totDeb += $this->orden_pagos->count() > 0 ? $this->orden_pagos->where('created_at', '>=', $inicio)->where('created_at', '<=', $final)->sum('valor_debito') : 0;
            $totDeb += $this->orden_pagos->count() > 0 ? $this->orden_pagos->where('created_at', '>=', $inicio)->where('created_at', '<=', $final)->sum('valor_credito') : 0;
            //$totDeb += $this->orden_pagos->count() > 0 ? $this->orden_pagos->where('created_at', '>=', $inicio)->where('created_at', '<=', $final)->sum('suma_pagos') : 0;
            $totCred += $this->orden_pagos->count() > 0 ? $this->orden_pagos->where('created_at', '>=', $inicio)->where('created_at', '<=', $final)->sum('valor_credito') : 0;
        endif;

        if($this->retefuente_movimientos->count() > 0):
            $totDeb += $this->retefuente_movimientos->count() > 0 ? $this->retefuente_movimientos->where('created_at', '>=', $inicio)->where('created_at', '<=', $final)->sum('debito') : 0;
            $totCred += $this->retefuente_movimientos->count() > 0 ? $this->retefuente_movimientos->where('created_at', '>=', $inicio)->where('created_at', '<=', $final)->sum('credito') : 0;
        endif;
        
        return ['debito' => $totDeb, 'credito' => $totCred];
    }

    public function getDebitoTrimestreAttribute(){
        return $this->deb_cred_trimestre['debito'];
    }

    public function getCreditoTrimestreAttribute(){
        return $this->deb_cred_trimestre['credito'];
    }

    public function getMDebitoTrimestreAttribute(){
        $suma = $this->debito_trimestre;
        if($this->hijos->count() > 0):
            $suma = $this->hijos->sum('m_debito_trimestre');
        endif;
            
        return $suma;
    }

    public function getMCreditoTrimestreAttribute(){
        $suma = $this->credito_trimestre;
        if($this->hijos->count() > 0):
            $suma = $this->hijos->sum('m_credito_trimestre');
        endif;
            
        return $suma;
    }

///////////////////////////////////////////////////////////////////////////
    public function getDebCredAttribute(){
        $age =  Carbon::today()->format('Y');
        $totCredAll = 0;
        $totCred = 0;
        $totDeb = 0;
        $mes = Session::get(auth()->id().'-mes-informe-contable-mes',);
        /*
        $inicio = "2023-{$mes}-01";
        $final = \Carbon\Carbon::createFromFormat('Y-m-d', $inicio)->addMonth()->format('Y-m-d');
        */
        /*
        if($this->otros_ordenes_pago_pucs_mensual->count() > 0):
            $otros_pucs = $this->otros_ordenes_pago_pucs_mensual;
            $totDeb += $otros_pucs->sum('valor_debito');
            $totCred += $otros_pucs->sum('valor_credito');
        endif;
        
        
        if($this->almacen_items_creditos->count() > 0):
            $totDeb += $this->almacen_items_creditos->sum('total');
        endif;
        */

        //if($this->level == 5):
            $totCred += $this->retefuente_mensual; 
            $totCred += $this->descuento_municipal_mensual; 
            $totCred += $this->descuento_mensual; 
            $totCred += $this->pagos_puc_mensual['credito']; 
            $totDeb  += $this->pagos_puc_mensual['debito'];
            $totCred += $this->pagos_bank_new_mensual['credito']; 
            $totDeb  += $this->pagos_bank_new_mensual['debito'];
            $totCred += $this->comprobantes_puc_mensual['credito']; 
            $totDeb  += $this->comprobantes_puc_mensual['debito'];
            $totCred += $this->comprobantes_banco_mensual['credito']; 
            $totDeb  += $this->comprobantes_banco_mensual['debito'];
                        /*
            if($this->almacen_items_mensual->count() > 0):
                $totDeb += $this->almacen_items_mensual->sum('total');
            endif;
            */

            /*
            if($this->pagos_bank_mensual->count() > 0):
                $totCredAll = $this->pagos_bank_mensual->sum('valor');
                $totCred += $this->pagos_bank_mensual->filter(function($p){ return $p->pago->estado == 1;})->sum('valor');
            endif;

            if($this->pagos_bank_new_mensual->count() > 0):
                $totDeb += $this->pagos_bank_new_mensual->sum('debito');
                $totCred = $this->pagos_bank_new_mensual->sum('credito');
            endif;

            if($this->comprobantes_mensual->count() > 0):
                $totDeb += $this->comprobantes_mensual->sum('debito');
                $totCred += $this->comprobantes_mensual->sum('credito');
            endif;

            if($this->orden_pagos_mensual->count() > 0):
                $totDeb += $this->orden_pagos_mensual->sum('valor_debito');
                $totCred += $this->orden_pagos_mensual->sum('valor_credito');
            endif;

            if($this->retefuente_mensual->count() > 0):
                $totCred += $this->retefuente_mensual->count() > 0 ? $this->retefuente_mensual->sum('valor') : 0;
            endif;
            */
        //endif;
        
        return ['debito' => $totDeb, 'credito' => $totCred];
    }

    public function getDebitoAttribute(){
        return $this->deb_cred['debito'];
    }

    public function getCreditoAttribute(){
        return $this->deb_cred['credito'];
    }

    public function getVHijosAttribute(){
        return $this->hijos->count() == 0 ? $this->saldo_inicial : $this->hijos->sum('saldo_inicial'); 
    }

    public function getMDebitoAttribute(){
        $suma = $this->debito;
        if($this->hijos->count() > 0):
            $suma = $this->hijos->sum('m_debito');
        endif;
            
        return $suma;
    }

    public function getMCreditoAttribute(){
        $suma = $this->credito;
        if($this->hijos->count() > 0):
            $suma = $this->hijos->sum('m_credito');
        endif;
            
        return $suma;
    }

    /*
    public function getSDebitoAttribute(){
        return $this->debito + $this->m_debito - $this->m_credito;
    }

    public function getSCreditoAttribute(){
        return $this->credito + $this->m_credito - $this->m_debito;
    }
    */

    public function getVInicialAttribute(){
        $suma = $this->saldo_inicial;
        if($this->hijos->count() > 0):
           // $suma += $this->hijos->sum('valor_inicial');
            $suma = $this->hijos->sum('v_inicial');
        endif;
            
        return $suma;
    }

    public function getFormatHijosInicialAttribute(){
        $grupo_puc = "";
        foreach($this->hijos as $item):
            $grupo_puc .= $this->format_puc($item, 'inicial');
            $grupo_puc .= $item->format_hijos_inicial;
        endforeach;
            
        return $grupo_puc;
    }

    public function getFormatHijosPruebaAttribute(){
        $grupo_puc = "";
        foreach($this->hijos as $item):
            $grupo_puc .= $this->format_puc($item, 'prueba');
            $grupo_puc .= $item->format_hijos_prueba;
        endforeach;
            
        return $grupo_puc;
    }

    public function getFormatHijosContabilidadAttribute(){

        $grupo_puc = "";
        foreach($this->hijos as $item):
            if($item->level <= 4):
                $grupo_puc .= $this->format_puc_contabilidad($item);
                $grupo_puc .= $item->format_hijos_contabilidad;
            endif;
        endforeach;
            
        return $grupo_puc;
    }

    public function format_puc_contabilidad($puc){
        $m_debito = $puc['m_debito_trimestre'];
        $m_credito = $puc['m_credito_trimestre'];
        $s_final = $puc['naturaleza'] == "DEBITO" ? $puc['v_inicial'] + $m_debito - $m_credito : $puc['v_inicial'] + $m_credito - $m_debito;
        $corriente = $puc['estado_corriente'] ? $s_final : 0;
        $no_corriente = !$puc['estado_corriente'] ? $s_final : 0;
        return "<tr>
                    <td class='text-left'>D</td>
                    <td class='text-center'>{$puc['codigo_punto']}</td>
                    <td class='text-right' style='width=200px;'>$".number_format($puc['v_inicial'])."</td>
                    <td class='text-right' style='width=200px;'>{$puc['v_inicial']}</td>
                    <td class='text-right' style='width=200px;'>$".number_format($m_debito)."</td>
                    <td class='text-right' style='width=200px;'>$".number_format($m_credito)."</td>
                    <td class='text-right' style='width=200px;'>{$m_debito}</td>
                    <td class='text-right' style='width=200px;'>{$m_credito}</td>
                    <td class='text-right' style='width=200px;'>$".number_format($s_final)."</td>
                    <td class='text-right' style='width=200px;'>{$s_final}</td>
                    <td class='text-right' style='width=200px;'>$".number_format($corriente)."</td>
                    <td class='text-right' style='width=200px;'>$".number_format($no_corriente)."</td>
                    <td class='text-right' style='width=200px;'>{$corriente}</td>
                    <td class='text-right' style='width=200px;'>{$no_corriente}</td>
                    </tr>";
    }


    public function format_puc($puc, $tipo){
        $debito = $puc['naturaleza'] == 'DEBITO' ? $puc['v_inicial']: 0;
        $credito = $puc['naturaleza'] == 'CREDITO' ? $puc['v_inicial']: 0;
       // $padre = is_null($puc['padre']) ? 'no tiene' : $puc['padre']['code'];
        //$hijos = count($puc['hijos']) == 0  ? 'no tiene' : $puc->hijos->pluck('id');
        
        $item =  "<tr>
                    <td class='text-left'>{$puc['code']}</td>
                    <td class='text-rigth'>{$puc['concepto']}</td>
                    <td class='text-right'>$".number_format($debito)."</td>
                    <td class='text-right'>$".number_format($credito)."</td>
                    <td class='text-right'>{$debito}</td>
                    <td class='text-right'>{$credito}</td>";
                    

        if($tipo == 'prueba'){
            $m_debito = $puc['m_debito'];
            $m_credito = $puc['m_credito'];
            $s_debito = $puc-> naturaleza == "DEBITO" ? $debito + $m_debito - $m_credito : 0;
            $s_credito = $puc-> naturaleza == "CREDITO" ?  $credito + $m_credito - $m_debito : 0;

            $item.= "
            <td class='text-right'>$".number_format($m_debito)."</td>
            <td class='text-right'>$".number_format($m_credito)."</td>
            <td class='text-right'>{$m_debito}</td>
            <td class='text-right'>{$m_credito}</td>
            <td class='text-right'>$".number_format($s_debito)."</td>
            <td class='text-right'>$".number_format($s_credito)."</td>
            <td class='text-right'>{$s_debito}</td>
            <td class='text-right'>{$s_credito}</td>
            ";
        }

        $item .= "</tr>";


        return $item;
                    /*
                    <td>{$padre}</td>
                    <td>{$puc['naturaleza']}</td>
                    <td>{$puc['saldo_inicial']}</td>
                    <td>{$hijos}</td>
                    */
    }


    public function validateBeforeMonths($age, $rubroPUC){
        $today = Carbon::today();//2023-04-11
        $lastDate = Carbon::parse($age." 23:59:59");//2023-2-1 23:59:59
        $fechaIni = Carbon::parse($today->year."-01-01");//2023-01-01
        //$fechaFin = Carbon::parse($today->year."-3-31");
        $fechaFin = $lastDate->subDays(1);//2023-31-1 23:59:59
        $total = $rubroPUC->saldo_inicial;
        $totDeb = 0;
        $totCred = 0;
        $mes = $fechaFin->month.'-'.$today->year;//1-2023

        $pagoBanks = PagoBanks::where('rubros_puc_id', $rubroPUC->id)->whereBetween('created_at',array($fechaIni, $fechaFin))->get();
        //select * from pago_banks where rubros_puc_id == 28 and created_at >= 2023-01-01 and created_at <= 2023-31-1
        //trae todos los pagos de bancos hechos entre una fecha inicial que seria el primero de enero del año actual a la fecha final
        // que seria el dia que hagan el descargue de el informe
        if (count($pagoBanks) > 0){
            foreach ($pagoBanks as $pagoBank){
                if ($pagoBank->pago->estado == 1){
                    $total = $total - $pagoBank->valor;
                    $totDeb = $totDeb + 0;
                    $totCred = $totCred + $pagoBank->valor;
                }
            }
        }

        //SE AÑADEN LOS VALORES DE LOS COMPROBANTES CONTABLES AL LIBRO
        $compsCont = ComprobanteIngresosMov::whereBetween('fechaComp',array($fechaIni, $fechaFin))->get();
        //aca coge todos los movimientos de las cuenta de bancos poara generar los libros y al igual que pahgo de bancos tiene una fecha final y una fecha inicial
        if (count($compsCont) > 0){
            foreach ($compsCont as $compCont){
                if ($compCont->cuenta_banco == $rubroPUC->id or $compCont->cuenta_puc_id == $rubroPUC->id){
                    if ($compCont->cuenta_banco == $rubroPUC->id){
                        $total = $total + $compCont->debito;
                        $total = $total - $compCont->credito;
                    } else{
                        $total = $total + $compCont->debito;
                        $total = $total - $compCont->credito;
                    }
                }
            }
        }

        return $total;
    }

}
