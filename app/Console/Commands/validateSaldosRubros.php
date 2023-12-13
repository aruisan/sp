<?php

namespace App\Console\Commands;

use App\bpinVigencias;
use App\Model\Admin\DependenciaRubroFont;
use App\Model\Administrativo\Cdp\BpinCdpValor;
use App\Model\Administrativo\Contabilidad\PucAlcaldia;
use App\Model\Administrativo\Registro\Registro;
use App\Model\Hacienda\Presupuesto\Rubro;
use App\Model\Hacienda\Presupuesto\RubrosMov;
use App\Model\Hacienda\Presupuesto\Snap\PresupuestoSnapData;
use App\Model\Hacienda\Presupuesto\Snap\PresupuestoSnap;
use App\Model\Hacienda\Presupuesto\Vigencia;
use App\Traits\LibrosTraits;
use App\Traits\PrepEgresosTraits;
use App\Traits\PrepIngresosTraits;
use Illuminate\Console\Command;
use Carbon\Carbon;

class validateSaldosRubros extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'validate:saldosRubros';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Valida los saldos de los Rubros y Actividades';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $año = Carbon::today()->year;
        $vigens = Vigencia::where('vigencia', $año)->where('tipo', 0)->where('estado', '0')->first();
        $actividades = bpinVigencias::where('vigencia_id', $vigens->id)->get();
        foreach ($actividades as $actividad){
            $bpinCdpValors = BpinCdpValor::where('dependencia_rubro_font_id', $actividad->dep_rubro_id)->get();
            if (count($bpinCdpValors) > 0){
                foreach ($bpinCdpValors as $bpinCdpValor){
                    if ($bpinCdpValor->cdp->jefe_e == '3' and  $bpinCdpValor->cdp->vigencia_id == $vigens->id){
                        if ($bpinCdpValor->dependencia_rubro_font_id == $actividad->dep_rubro_id) {
                            $valueCdps[] = $bpinCdpValor->valor;
                        }
                    }
                }
                if (isset($valueCdps)){
                    //ADICIONES
                    $add = RubrosMov::where('dep_rubro_font_id', $actividad->dep_rubro_id)->where('movimiento','2')->get();
                    //REDUCCIONES
                    $red = RubrosMov::where('dep_rubro_font_id', $actividad->dep_rubro_id)->where('movimiento','3')->get();
                    //CRED
                    $cred = RubrosMov::where('dep_rubro_font_cred_id', $actividad->dep_rubro_id)->get();
                    //CCRED
                    $ccred = RubrosMov::where('dep_rubro_font_cc_id', $actividad->dep_rubro_id)->get();

                    $actividad->propios = $actividad->propios + $add->sum('valor');
                    $actividad->propios = $actividad->propios - $red->sum('valor');
                    $actividad->propios = $actividad->propios + $cred->sum('valor');
                    $actividad->propios = $actividad->propios - $ccred->sum('valor');

                    if ($actividad->propios - array_sum($valueCdps) != $actividad->saldo){
                        $actividad->saldo = $actividad->propios - array_sum($valueCdps);
                        //$actividad->save();

                        $depRubFont = DependenciaRubroFont::find($actividad->dep_rubro_id);
                        if ($depRubFont){
                            if ($depRubFont->value == $actividad->propios){
                                $depRubFont->saldo = $actividad->propios - array_sum($valueCdps);
                                //$depRubFont->save();
                            }
                        }
                        $saldoReal = $actividad->propios - array_sum($valueCdps);

                        echo nl2br($actividad->id.' '.$actividad->dep_rubro_id.' '. array_sum($valueCdps).' '. $actividad->bpin->cod_actividad.' '.$saldoReal." \n ");
                    }
                    unset($valueCdps);
                }
            }
        }
    }
}
