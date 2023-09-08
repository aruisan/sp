<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Model\Administrativo\Contabilidad\PucAlcaldia;
use Session;

class InformeContableMensualData extends Model
{
    protected $fillable = ['puc_alcaldia_id', 'informe_contable_mensual_id', 'm_credito', 'm_debito', 's_credito', 's_debito', 'i_credito', 'i_debito', 'padre_id'];

    public function puc_alcaldia(){
        return $this->belongsTo(PucAlcaldia::class, 'puc_alcaldia_id');
    }

    public function informe_contable_mensual(){
        return $this->belongsTo(InformeContableMensual::class, 'informe_contable_mensual_id');
    }

    public function hijos(){
        return $this->hasMany(InformeContableMensualData::class, 'padre_id');
    }

    public function padre(){
        return $this->belongsTo(InformeContableMensualData::class, 'padre_id');
    }

    public function getFormatHijosPruebaAttribute(){
        $grupo_puc = "";
        foreach($this->hijos->sortBy('puc_alcaldia.code') as $item):
            if(!is_null($item->puc_alcaldia)){
                if($item->puc_alcaldia->level <= Session::get(auth()->id().'-mes-informe-contable-nivel')){
                    $grupo_puc .= $this->format_puc($item, $item->puc_alcaldia);
               }
           }            
           $grupo_puc .= $item->format_hijos_prueba;
        endforeach;
            
        return $grupo_puc;
    }

    public function getSFinalAttribute($value){
        return $this->puc_alcaldia->naturaleza == "DEBITO" ? $this->valor_inicial + $this->m_debito +$this->a_debito - $this->m_credito + $this->a_credito
                : $this->valor_inicial + $this->m_credito + $this->a_credito - $this->m_debito + $this->a_debito;
    }


    public function almacen_total($year, $mes){
        $credito = $this->puc_alcaldia->almacen_salidas_credito($year,$mes)->sum('total') +$this->puc_alcaldia->almacen_entradas_credito($year,$mes)->sum('total');
        $debito = $this->puc_alcaldia->almacen_salidas_debito($year,$mes)->sum('total') +$this->puc_alcaldia->almacen_entradas_debito($year,$mes)->sum('total');

        foreach($this->puc_alcaldia->hijos as $ph){
            $credito += $ph->almacen_salidas_credito($year,$mes)->sum('total') +$ph->almacen_entradas_credito($year,$mes)->sum('total');
            $debito += $ph->almacen_salidas_debito($year,$mes)->sum('total') +$ph->almacen_entradas_debito($year,$mes)->sum('total');
        }

        return ['credito' => $credito, 'debito' => $debito];
    }

    public function getFormatHijosGeneralPdfAttribute(){
        $grupo_puc = "";
        foreach($this->hijos->sortBy('puc_alcaldia.code') as $item):
            if(!is_null($item->puc_alcaldia)){
                if($item->puc_alcaldia->level <= 2):
                    $grupo_puc .= $this->format_puc_general($item, $item->puc_alcaldia, 'pdf');
                endif;
           }            
           $grupo_puc .= $item->format_hijos_general_pdf;
        endforeach;
            
        return $grupo_puc;
    }

    public function getFormatHijosGeneralVistaAttribute(){
        $grupo_puc = "";
        foreach($this->hijos->sortBy('puc_alcaldia.code') as $item):
            if(!is_null($item->puc_alcaldia)){
                if($item->puc_alcaldia->level <= 2):
                    $grupo_puc .= $this->format_puc_general($item, $item->puc_alcaldia, 'vista');
                endif;
           }            
           $grupo_puc .= $item->format_hijos_general_vista;
        endforeach;
            
        return $grupo_puc;
    }

    public function format_puc_general($data, $puc, $tipo){

        return $tipo == 'vista' ? "<tr><td><b>{$puc['codigo_punto']}<b> - {$puc['concepto']} - {$data['s_final']}</td></tr>"
                                :"<span><b>{$puc['codigo_punto']}<b> - {$puc['concepto']} - {$data['s_final']}</span><br>";
    }



    public function format_puc($data, $puc){
        $codigo = is_null($puc) ? "Se elimino {$puc->id}" : $puc->code;
        $concepto = is_null($puc) ? "Se elimino {$puc->id}" : $puc->concepto;
        $m_debito = $data['m_debito'] + $data['a_debito'];
        $m_credito = $data['m_credito'] + $data['a_credito'];
        $s_debito = $puc['naturaleza'] == "DEBITO" ? $puc['i_debito'] + $m_debito - $m_credito : 0;
        $s_credito = $puc['naturaleza'] == "CREDITO" ?  $puc['i_credito'] + $m_credito - $m_debito : 0;
        $item =  "<tr>
                    <td class='text-left'>{$codigo}</td>
                    <td class='text-rigth'>{$concepto}</td>
                    <td class='text-right'>$".number_format($data['i_debito'])."</td>
                    <td class='text-right'>$".number_format($data['i_credito'])."</td>
                    <td class='text-right'>{$data['i_debito']}</td>
                    <td class='text-right'>{$data['i_credito']}</td>
                    
                    <td class='text-right'>$".number_format($m_debito)."</td>
                    <td class='text-right'>$".number_format($m_credito)."</td>
                    <td class='text-right'>{$m_debito}</td>
                    <td class='text-right'>{$m_credito}</td>
                    <td class='text-right'>$".number_format($data['s_debito'])."</td>
                    <td class='text-right'>$".number_format($data['s_credito'])."</td>
                    <td class='text-right'>{$data['s_debito']}</td>
                    <td class='text-right'>{$data['s_credito']}</td>";

                    if(auth()->id() == 1){

                        $item .=  '   <td class="text-right" style="width=200px;">';
                        if(!is_null($puc) ){

                            if($puc->level == 5){
                                $item .= "<a class='btn btn-primary' href='".route("chip.contable.puc.ver", $puc->id)."' target='_blank'>Movimientos</a>";
                            }
                        }
                        $item .= '</td>';

                              
                            
                    }
        /*
        $item =  "<tr>
                    <td class='text-left'>{$codigo}</td>
                    <td class='text-rigth'>{$concepto}</td>
                    <td class='text-right'>$".number_format($data['i_debito'])."</td>
                    <td class='text-right'>$".number_format($data['i_credito'])."</td>
                    <td class='text-right'>{$data['i_debito']}</td>
                    <td class='text-right'>{$data['i_credito']}</td>
                    
                    <td class='text-right'>$".number_format($data['m_debito'])."</td>
                    <td class='text-right'>$".number_format($data['m_credito'])."</td>
                    <td class='text-right'>{$data['m_debito']}</td>
                    <td class='text-right'>{$data['m_credito']}</td>
                    <td class='text-right'>$".number_format($data['s_debito'])."</td>
                    <td class='text-right'>$".number_format($data['s_credito'])."</td>
                    <td class='text-right'>{$data['s_debito']}</td>
                    <td class='text-right'>{$data['s_credito']}</td>";

                    if(auth()->id() == 1){

                        $item .=  '   <td class="text-right" style="width=200px;">';
                        if(!is_null($puc) ){

                            if($puc->level == 5){
                                $item .= "<a class='btn btn-primary' href='".route("chip.contable.puc.ver", $puc->id)."' target='_blank'>Movimientos</a>";
                            }
                        }
                        $item .= '</td>';

                              
                            
                    }
                    
        */

        $item .= "</tr>";

        return $item;
    }
}
