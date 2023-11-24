<?php

namespace App\Console\Commands;

use App\Model\Administrativo\Contabilidad\PucAlcaldia;
use App\Model\Administrativo\Registro\Registro;
use App\Model\Hacienda\Presupuesto\Snap\PresupuestoSnapData;
use App\Model\Hacienda\Presupuesto\Snap\PresupuestoSnap;
use App\Model\Hacienda\Presupuesto\Vigencia;
use App\Traits\LibrosTraits;
use App\Traits\PrepEgresosTraits;
use App\Traits\PrepIngresosTraits;
use Illuminate\Console\Command;
use Carbon\Carbon;

class validateSaldosRPs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'validate:saldosRPs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Valida los saldos de los RPs';

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
        $registros = Registro::where('vigencia_id', $vigens->id)->where('jefe_e','3')->get();
        foreach ($registros as $registro){
            dd($registro, $registro->ordenPagos);
        }
    }
}
