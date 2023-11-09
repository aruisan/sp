<?php

namespace App\Console\Commands;

use App\Model\Administrativo\Registro\Registro;
use App\Model\Hacienda\Presupuesto\Snap\PresupuestoSnapData;
use App\Model\Hacienda\Presupuesto\Snap\PresupuestoSnap;
use App\Model\Hacienda\Presupuesto\Vigencia;
use App\Traits\PrepEgresosTraits;
use Illuminate\Console\Command;
use Carbon\Carbon;

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
        //EGRESOS
        $añoActual = Carbon::now()->year;
        $mesActual = Carbon::now()->month;

        $vigens = Vigencia::where('vigencia', $añoActual)->where('tipo', 0)->where('estado', '0')->first();

        $registros = Registro::where('secretaria_e', '3')->where('jefe_e','3')->orderBy('id', 'DESC')->get();
        foreach ($registros as $registro){
            if ($registro->ordenPagos->count() > 0){
                $disp = $registro->valor - $registro->ordenPagos->where('estado','1')->sum('valor');
                if ($disp != $registro->saldo) {
                    //echo($registro->id.'---saldo:'.$disp.'----');
                }
            }
        }

        if ($vigens){
            $findSnap = PresupuestoSnap::where('vigencia_id', $vigens->id)->where('mes', $mesActual)
                ->where('año', $añoActual)->where('tipo','EGRESOS')->first();
            if ($findSnap){
                $delete = true;
                $idSnap = $findSnap->id;
            } else{
                $delete = false;
                $newSnap = new PresupuestoSnap();
                $newSnap->vigencia_id = $vigens->id;
                $newSnap->mes = $mesActual;
                $newSnap->año = $añoActual;
                $newSnap->tipo = 'EGRESOS';
                $newSnap->save();
                $idSnap = $newSnap->id;
            }

            $prepTrait = new PrepEgresosTraits();
            $presupuesto = $prepTrait->prepEgresos($vigens);

            $proy = 0;
            foreach ($presupuesto as $data) {
                if (intval($data['presupuesto_def']) > 0) {
                    if ($data['codBpin'] != "") {
                        $multi = intval($data['cdps']) * 100;
                        $ejec = intval($multi) / intval($data['presupuesto_def']);
                        if ($ejec > 0){
                            $porcenEjec[] = $ejec;
                            $proyectos[] = $data['codBpin'];
                            if ($proy == 0) $proy = $ejec;
                            else {
                                $sum = $proy + $ejec;
                                $proy = $sum/2;
                            }
                        }
                    }
                }
            }

            if ($porcenEjec){
                for ($i = 0; $i < count($porcenEjec); $i++) {
                    if (!isset($newProy)){
                        $newProy[] = $proyectos[$i];
                        $valNewProy[] = $porcenEjec[$i];
                    } else{
                        if (($find = array_search($proyectos[$i], $newProy)) !== FALSE){
                            $valNewProy[$find] = ($valNewProy[$find] + $porcenEjec[$i])/2;
                        } else{
                            $newProy[] = $proyectos[$i];
                            $valNewProy[] = $porcenEjec[$i];
                        }
                    }
                }
            }

            //dd($valNewProy, $newProy);

            if ($delete){
                $findSnapDataOld = PresupuestoSnapData::where('pre_snap_id', $findSnap->id)->get();
                foreach ($findSnapDataOld as $dataOld) $dataOld->delete();
            }

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
                if (intval($data['presupuesto_def']) > 0){
                    $multi = intval($data['cdps']) * 100;
                    $newData->ejec = intval($multi) / intval($data['presupuesto_def']);
                }
                $newData->save();
            }
        }
    }
}
