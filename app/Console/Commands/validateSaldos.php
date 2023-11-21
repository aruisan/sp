<?php

namespace App\Console\Commands;

use App\Model\Administrativo\Contabilidad\PucAlcaldia;
use App\Model\Hacienda\Presupuesto\Snap\PresupuestoSnapData;
use App\Model\Hacienda\Presupuesto\Snap\PresupuestoSnap;
use App\Model\Hacienda\Presupuesto\Vigencia;
use App\Traits\LibrosTraits;
use App\Traits\PrepEgresosTraits;
use App\Traits\PrepIngresosTraits;
use Illuminate\Console\Command;
use Carbon\Carbon;

class validateSaldos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'validate:saldos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Valida los saldos de las cuentas contables y los actualiza';

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
        $lv1 = PucAlcaldia::where('padre_id', 7 )->get();
        foreach ($lv1 as $dato){
            $cuentasBanc[] = $dato;
            $lv2 = PucAlcaldia::where('padre_id', $dato->id )->get();
            foreach ($lv2 as $cuenta) $cuentasBanc[] = $cuenta;
        }

        foreach ($cuentasBanc as $cuenta){
            if ($cuenta->hijo == 1){
                $librosTraits = new LibrosTraits();
                $resultFind = $librosTraits->movAccountLibros($cuenta->id, $añoActual.'-01-01', $añoActual.'-12-31');
                dd($resultFind->last(), $cuenta);
                break;
            }
        }
    }
}
