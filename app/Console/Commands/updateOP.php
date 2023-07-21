<?php

namespace App\Console\Commands;

use App\Model\Administrativo\Pago\PagoBanks;
use App\Model\Administrativo\Pago\PagoBanksNew;
use Illuminate\Console\Command;

class updateOP extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:OP';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update OPs';

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
        $pagoBanksOld = PagoBanks::all();
        foreach ($pagoBanksOld as $pagoB){
            //SE ALMACENAN LOS PAGOS DE ADULTO MAYOR
            if ($pagoB->pago->adultoMayor){
                foreach ($pagoB->pago->orden_pago->pucs as $pucOP){
                    if ($pucOP->valor_credito > 0){
                        $newPagoBank = new PagoBanksNew();
                        $newPagoBank->pagos_id = $pagoB->pago->id;
                        $newPagoBank->rubros_puc_id = $pucOP->rubros_puc_id;
                        $newPagoBank->debito = $pagoB->pago->valor;
                        $newPagoBank->credito = 0;
                        $newPagoBank->save();
                    }
                }
                $newPagoBank = new PagoBanksNew();
                $newPagoBank->pagos_id = $pagoB->pagos_id;
                $newPagoBank->rubros_puc_id = $pagoB->rubros_puc_id;
                $newPagoBank->debito = 0;
                $newPagoBank->credito = $pagoB->valor;
                $newPagoBank->save();
            }
        }
    }
}
