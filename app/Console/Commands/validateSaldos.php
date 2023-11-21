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
        $allAccounts = PucAlcaldia::where('hijo', '1')->get();
        foreach ($allAccounts as $cuenta){
            $librosTraits = new LibrosTraits();
            $cuenta->saldo_actual = $librosTraits->saldoActual($cuenta);
            $cuenta->save();
            echo nl2br($cuenta->code.' '.$cuenta->concepto.' SALDO ACTUAL: '.$cuenta->saldo_actual." \n ");
            echo nl2br("asd \n");
            echo nl2br("aasdsd \n");
            break;
        }
    }
}
