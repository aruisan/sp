<?php

namespace App\Console\Commands;

use App\BPin;
use App\bpinVigencias;
use App\Model\Admin\DependenciaRubroFont;
use App\Model\Administrativo\Cdp\BpinCdpValor;
use App\Model\Administrativo\Cdp\Cdp;
use App\Model\Administrativo\Cdp\RubrosCdpValor;
use App\Model\Administrativo\OrdenPago\OrdenPagos;
use App\Model\Administrativo\OrdenPago\OrdenPagosRubros;
use App\Model\Administrativo\Pago\Pagos;
use App\Model\Administrativo\Registro\CdpsRegistroValor;
use App\Model\Administrativo\Registro\Registro;
use App\Model\Hacienda\Presupuesto\FontsRubro;
use App\Model\Hacienda\Presupuesto\PlantillaCuipoEgresos;
use App\Model\Hacienda\Presupuesto\Rubro;
use App\Model\Hacienda\Presupuesto\RubrosMov;
use App\Model\Hacienda\Presupuesto\Snap\PresupuestoSnap;
use App\Model\Hacienda\Presupuesto\Snap\PresupuestoSnapData;
use App\Model\Hacienda\Presupuesto\Vigencia;
use App\Traits\PrepEgresosTraits;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class feedPresupuesto extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feed:presupuesto';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Llena la base de datos del presupuesto cada dia';

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
        $añoActual = Carbon::now()->year;
        $mesActual = Carbon::now()->month;
        $diaActual = Carbon::now()->day;

        $vigens = Vigencia::where('vigencia', $añoActual)->where('tipo', 0)->where('estado', '0')->first();
        if ($vigens){
            $findSnap = PresupuestoSnap::where('vigencia_id', $vigens->id)->where('mes', $mesActual)
                ->where('año', $añoActual)->first();
            if ($findSnap){
                $findSnapDataOld = PresupuestoSnapData::where('pre_snap_id', $findSnap->id)->get();
                foreach ($findSnapDataOld as $dataOld){
                    $dataOld->delete();
                }
                $idSnap = $findSnap->id;
            } else{
                $newSnap = new PresupuestoSnap();
                $newSnap->vigencia_id = $vigens->id;
                $newSnap->mes = $mesActual;
                $newSnap->año = $añoActual;
                $newSnap->save();
                $idSnap = $newSnap->id;
            }

            $prepTrait = new PrepEgresosTraits();
            $presupuesto = $prepTrait->prepEgresos($vigens);
            foreach ($presupuesto as $data){
                $newData = new PresupuestoSnapData();
                $newData->pre_snap_id = $idSnap;
                $newData->code_bpin = $data['codBpin'];
                $newData->code_act = $data['codActiv'];
                $newData->name_act = $data['nameActiv'];
                $newData->rubro = $data['cod'];
                $newData->nombre = $data['name'];
                $newData->p_inicial = $data['presupuesto_inicial'];
                $newData->adicion = $data['adicion'];
                $newData->reduccion = $data['reduccion'];
                $newData->credito = $data['credito'];
                $newData->ccredito = $data['ccredito'];
                $newData->p_def = $data['presupuesto_def'];
                $newData->cdps = $data['cdps'];
                $newData->rps = $data['registros'];
                $newData->saldo_disp = $data['saldo_disp'];
                $newData->saldo_cdps = $data['saldo_cdp'];
                $newData->ops = $data['ordenes_pago'];
                $newData->pagos = $data['pagos'];
                $newData->cuentas_pagar = $data['cuentas_pagar'];
                $newData->reservas = $data['reservas'];
                $newData->cod_dep = $data['codDep'];
                $newData->name_dep = $data['dep'];
                $newData->fuente = $data['fuente'];
                $newData->cod_producto = $data['codProd'];
                $newData->cod_indicador = $data['codIndProd'];
                $newData->save();
            }
        }
    }
}
