<?php

namespace App\Console\Commands;

use App\bpinVigencias;
use App\Model\Administrativo\Cdp\BpinCdpValor;
use App\Model\Administrativo\Contabilidad\PucAlcaldia;
use App\Model\Administrativo\Registro\Registro;
use App\Model\Hacienda\Presupuesto\Rubro;
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
                    if ($bpinCdpValor->cdp->jefe_e == '3'){
                        dd($bpinCdpValor, $actividad, $bpinCdpValor->cdp);
                    }
                }
            }
        }
    }
}
