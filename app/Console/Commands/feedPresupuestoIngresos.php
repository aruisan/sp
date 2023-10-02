<?php

namespace App\Console\Commands;

use App\Model\Hacienda\Presupuesto\Snap\PresupuestoSnapData;
use App\Model\Hacienda\Presupuesto\Snap\PresupuestoSnap;
use App\Model\Hacienda\Presupuesto\Vigencia;
use App\Traits\PrepEgresosTraits;
use App\Traits\PrepIngresosTraits;
use Illuminate\Console\Command;
use Carbon\Carbon;

class feedPresupuestoIngresos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feed:presupuestoIngresos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Llena la base de datos del presupuesto de ingresos';

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

        $vigensING = Vigencia::where('vigencia', $añoActual)->where('tipo', 1)->where('estado', '0')->first();
        if ($vigensING){
            $findSnap = PresupuestoSnap::where('vigencia_id', $vigensING->id)->where('mes', $mesActual)
                ->where('año', $añoActual)->where('tipo','INGRESOS')->first();
            if ($findSnap){
                $deleteIng = true;
                $idSnap = $findSnap->id;
            } else{
                $deleteIng = false;
                $newSnap = new PresupuestoSnap();
                $newSnap->vigencia_id = $vigensING->id;
                $newSnap->mes = $mesActual;
                $newSnap->año = $añoActual;
                $newSnap->tipo = 'INGRESOS';
                $newSnap->save();
                $idSnap = $newSnap->id;
            }

            $prepTrait = new PrepIngresosTraits();
            $presupuestoIng = $prepTrait->prepIngresos($vigensING);

            if ($deleteIng){
                $findSnapDataOld = PresupuestoSnapData::where('pre_snap_id', $findSnap->id)->get();
                foreach ($findSnapDataOld as $dataOld) $dataOld->delete();
            }

            foreach ($presupuestoIng as $data){
                $newData = new PresupuestoSnapData();
                $newData->pre_snap_id = $idSnap;
                $newData->rubro = $data['code'];
                $newData->nombre = $data['name'];
                $newData->p_inicial = $data['inicial'];
                $newData->adicion = $data['adicion'];
                $newData->reduccion = $data['reduccion'];
                $newData->credito = $data['anulados'];
                $newData->ccredito = $data['definitivo'];
                $newData->p_def = $data['recaudado'];
                $newData->cdps = $data['porRecaudar'];
                $newData->rps = 0;
                $newData->saldo_disp = 0;
                $newData->saldo_cdps = 0;
                $newData->ops = 0;
                $newData->pagos = 0;
                $newData->cuentas_pagar = 0;
                $newData->reservas = 0;
                if ($data['cod_fuente'] != '') $newData->fuente = $data['cod_fuente'].' - '.$data['name_fuente'];
                if (intval($data['definitivo']) > 0){
                    $multi = intval($data['recaudado']) * 100;
                    $newData->ejec = intval($multi) / intval($data['definitivo']);
                }
                $newData->save();
            }
        }
    }
}
