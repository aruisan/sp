<?php

namespace App\Console\Commands;

use App\Model\Administrativo\Pago\PagoBanks;
use App\Model\Administrativo\Pago\PagoBanksNew;
use App\Model\Administrativo\Pago\Pagos;
use App\Model\Administrativo\Tesoreria\retefuente\TesoreriaRetefuentePago;
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
        $pagos = Pagos::all();
        foreach ($pagos as $pago){
            //SI EL PAGO ES DE ADULTO MAYOR O EMBARGO
            if ($pago->adultoMayor == '1' OR $pago->embargo == '1'){
                foreach ($pago->orden_pago->pucs as $pucOP){
                    if ($pucOP->valor_credito > 0){
                        $newPagoBank = new PagoBanksNew();
                        $newPagoBank->pagos_id = $pago->id;
                        $newPagoBank->rubros_puc_id = $pucOP->rubros_puc_id;
                        $newPagoBank->debito = $pago->valor;
                        $newPagoBank->credito = 0;
                        $newPagoBank->persona_id = $pago->persona_id;
                        $newPagoBank->created_at = $pago->created_at;
                        $newPagoBank->save();
                        echo $newPagoBank;
                    }
                }
            } elseif ($pago->reteFuente == '1'){
                //SI ES UN PAGO DE RETEFUENTE
                $tesoreriaRetefuentePago = TesoreriaRetefuentePago::where('orden_pago_id', $pago->orden_pago_id)->first();
                foreach ($tesoreriaRetefuentePago->contas as $debPay){
                    if ($debPay->debito > 0){
                        $newPagoBank = new PagoBanksNew();
                        $newPagoBank->pagos_id = $pago->id;
                        $newPagoBank->rubros_puc_id = $debPay->cuenta_puc_id;
                        $newPagoBank->debito = $debPay->debito;
                        $newPagoBank->credito = 0;
                        $newPagoBank->persona_id = $debPay->persona_id;
                        $newPagoBank->created_at = $pago->created_at;
                        $newPagoBank->save();
                        echo $newPagoBank;
                    }
                }
            } else{
                foreach ($pago->orden_pago->pucs as $pucOP){
                    if ($pucOP->valor_credito > 0){
                        $newPagoBank = new PagoBanksNew();
                        $newPagoBank->pagos_id = $pago->id;
                        $newPagoBank->rubros_puc_id = $pucOP->rubros_puc_id;
                        $newPagoBank->debito = $pucOP->valor_credito;
                        $newPagoBank->credito = 0;
                        $newPagoBank->persona_id = $pago->persona_id;
                        $newPagoBank->created_at = $pago->created_at;
                        $newPagoBank->save();
                    }
                }
            }
            //SE REGISTRA EL CREDITO
            $pagoBanks = PagoBanks::where('pagos_id', $pago->id)->get();
            foreach ($pagoBanks as $pagoBank){
                $newPagoBank = new PagoBanksNew();
                $newPagoBank->pagos_id = $pagoBank->pagos_id;
                $newPagoBank->rubros_puc_id = $pagoBank->rubros_puc_id;
                $newPagoBank->debito = 0;
                $newPagoBank->credito = $pagoBank->valor;
                $newPagoBank->persona_id = $pago->persona_id;
                $newPagoBank->created_at = $pagoBank->created_at;
                $newPagoBank->updated_at = $pagoBank->updated_at;
                $newPagoBank->save();
            }
        }
        echo "TERMINADO";
    }
}
