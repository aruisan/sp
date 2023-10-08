<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\Administrativo\Contabilidad\PucAlcaldia;
use App\Model\Data\ExternoBalanceData as BalanceData;
use App\Model\Data\ExternoBalance as Balances;
use Carbon\Carbon;
use Session, Log;
use App\Model\Data\InformeContable as InformeContableMensual;
use App\Model\Data\InformeContableData as InformeContableMensualData;
use App\Traits\BalanceTraits;

class ReporteBalances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reporte_balances:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $mes = date('m');
        $mes = "09";
        $fecha = "2023-{$mes}-01";

        $this->balanceTrim($mes, $mes);
        
        $informe = InformeContableMensual::where('fecha', $fecha)->first();
        $pucs = PucAlcaldia::get();
        if(is_null($informe)):
            $informe = InformeContableMensual::create(['fecha' => $fecha]);
        endif;
        foreach($pucs as $puc):
            $this->generar_informe($informe, $puc);
        endforeach;
        
       // Log::info([$mes, $fecha, $informe, $pucs]);
    }


    public function balanceTrim($mes1, $mes2){
        $año = Carbon::today()->year;
        if($mes1 == $mes2) $balance = Balances::where('año', $año)->where('tipo','MENSUAL')->where('mes',$mes1)->first();
        else $balance = Balances::where('año', $año)->where('tipo','TRIMESTRAL')->where('mes',$mes1.'-'.$mes2)->first();

        Log::info("dddddddddddd". is_null($balance));
        
        if(is_null($balance)){
            Log::info(3);
            $newBal = new Balances();
            $newBal->año = $año;
            if($mes1 == $mes2) {
                $newBal->mes = $mes1;
                $newBal->tipo = 'MENSUAL';
            }else{
                $newBal->mes = $mes1.'-'.$mes2;
                $newBal->tipo = 'TRIMESTRAL';
            }
            $newBal->save();
        }else{
            $newBal = $balance;
        }

            $PUC = PucAlcaldia::where('padre_id',0)->get();
            Log::info(5);
            foreach ($PUC as $first) {
                $cuentas[] = $first;
                $lv2 = PucAlcaldia::where('padre_id', $first->id)->get();
                foreach ($lv2 as $dato) {
                    $cuentas[] = $dato;
                    $lv3 = PucAlcaldia::where('padre_id', $dato->id)->get();
                    foreach ($lv3 as $object) {
                        $cuentas[] = $object;
                        $lv4 = PucAlcaldia::where('padre_id', $object->id)->get();
                        foreach ($lv4 as $lvlLast) {
                            $cuentas[] = $lvlLast;
                            $hijos = PucAlcaldia::where('padre_id', $lvlLast->id)->get();
                            foreach ($hijos as $hijo) $cuentas[] = $hijo;
                        }
                    }
                }
            }

            $balance = new BalanceTraits();
            $data = $balance->balance($mes1, $mes2);

            $deb = 0;
            $cre = 0;
            foreach ($cuentas as $cuenta) {
                foreach ($data as $index => $padre) {
                    if ($index == count($data) - 1) break;
                    if ($cuenta['id'] == $padre['cuenta_id']) {
                        $dataBalance = new BalanceData();
                        $dataBalance->balance_id = $newBal->id;
                        //$dataBalanceHijo->fecha = Carbon::parse($padre['fecha'])->format('Y-m-d');
                        $dataBalance->cuenta_puc_id = $padre['cuenta_id'];
                        $dataBalance->documento = $padre['concepto'];
                        $dataBalance->debito = $padre['debito'];
                        $dataBalance->credito = $padre['credito'];
                        $dataBalance->save();

                        if(strlen($padre['code']) == 1){
                            $deb = $deb + $padre['debito'];
                            $cre = $cre + $padre['credito'];
                        }

                        foreach ($data[count($data) - 1]['hijos'] as $hijo){
                            Log::info(9);
                            if($padre['cuenta_id'] == $hijo['padre_id']){
                                Log::info(10);
                                $dataBalanceHijo = new BalanceData();
                                $dataBalanceHijo->balance_id = $newBal->id;
                                $dataBalanceHijo->fecha = Carbon::parse($hijo['fecha'])->format('Y-m-d');
                                $dataBalanceHijo->cuenta_puc_id = $hijo['cuenta'];
                                $dataBalanceHijo->documento = $hijo['modulo'];
                                $dataBalanceHijo->concepto = $hijo['concepto'];

                                if ($hijo['debito'] > 0) $dataBalanceHijo->debito = $hijo['debito'];
                                else $dataBalanceHijo->debito = 0;
                                if ($hijo['credito'] > 0) $dataBalanceHijo->credito = $hijo['credito'];
                                else $dataBalanceHijo->credito = 0;

                                $dataBalanceHijo->save();
                            }
                        }
                    }
                }
            }
            $dataBalanceTot = new BalanceData();
            $dataBalanceTot->balance_id = $newBal->id;
            $dataBalanceTot->documento = 'TOTALES';
            $dataBalanceTot->debito = $deb;
            $dataBalanceTot->credito = $cre;
            $dataBalanceTot->save();
            Log::info(11);
    }


    public function generar_informe(InformeContableMensual $informe, PucAlcaldia $puc){
        $year = date('Y');
        $mes = intval(Session::get(auth()->id().'-mes-informe-contable-mes'));
        $mes_integer = intval($mes)-1;
        $mes_anterior = $mes_integer > 9 ? $mes_integer : "0{$mes_integer}";
        $fecha_anterior = "2023-{$mes_anterior}-01";
        $pucs_count = PucAlcaldia::count();
        $puc_mensual = InformeContableMensualData::where('informe_contable_mensual_id', $informe->id)->where('puc_alcaldia_id', $puc->id)->first();

        if(is_null($puc_mensual)):
            $puc_mensual = new InformeContableMensualData;
        endif;

        if($mes_integer == 0){
            $i_debito = $puc->naturaleza  == 'DEBITO' ? $puc->v_inicial: 0;
            $i_credito = $puc->naturaleza == 'CREDITO' ? $puc->v_inicial: 0;
        }else{
            $informe_anterior = InformeContableMensual::where('fecha', $fecha_anterior)->first();
            //dd([$informe_anterior, $fecha_anterior]);
            if(is_null($informe_anterior)){
                $informe->delete();
                return "no se ha generado el mes anterior";
            }
            $puc_informe_contable_mensual_anterior = InformeContableMensualData::where('informe_contable_mensual_id', $informe_anterior->id)->where('puc_alcaldia_id', $puc->id)->first();
            
            $i_debito = is_null($puc_informe_contable_mensual_anterior) ? 0 :$puc_informe_contable_mensual_anterior->s_debito;
            $i_credito = is_null($puc_informe_contable_mensual_anterior) ? 0 : $puc_informe_contable_mensual_anterior->s_credito;
        }
        
        $balance = Balances::where('mes', $mes)->where('año', $year)->where('tipo', 'MENSUAL')->first();
        $puc_balances = BalanceData::where('cuenta_puc_id', $puc->id)->where('balance_id', $balance->id)->get();
        //$m_credito = $puc_balances->sum('credito') + array_sum(array_column($puc->almacen_salidas_credito(2023,$mes),'total')) +array_sum(array_column($puc->almacen_entradas_credito(2023,$mes),'total'));
        //$m_debito = $puc_balances->sum('debito') + array_sum(array_column($puc->almacen_salidas_debito(2023,$mes), 'total')) +array_sum(array_column($puc->almacen_entradas_debito(2023,$mes), 'total'));
        $m_credito = $puc_balances->sum('credito');
        $m_debito = $puc_balances->sum('debito');

        $a_credito = $puc->almacen_salidas_credito(2023,$mes)->sum('total') +$puc->almacen_entradas_credito(2023,$mes)->sum('total');
        $a_debito = $puc->almacen_salidas_debito(2023,$mes)->sum('total') +$puc->almacen_entradas_debito(2023,$mes)->sum('total');
        
        $s_debito = $puc->naturaleza == "DEBITO" ? $i_debito + $m_debito - $m_credito : 0;
        $s_credito = $puc->naturaleza == "CREDITO" ?  $i_credito + $m_credito - $m_debito : 0;  
        
        
        $puc_mensual->informe_contable_mensual_id = $informe->id;
        $puc_mensual->puc_alcaldia_id = $puc->id;
        $puc_mensual->m_credito = $m_credito;
        $puc_mensual->m_debito = $m_debito;
        $puc_mensual->i_credito = $i_credito;
        $puc_mensual->i_debito = $i_debito;
        $puc_mensual->s_credito = $s_credito;
        $puc_mensual->s_debito = $s_debito;    
        $puc_mensual->a_credito = $a_credito;
        $puc_mensual->a_debito = $a_debito;  
        $puc_mensual->save();
        

        if($puc_mensual->puc_alcaldia->hijos->count() > 0){
            $pucs_mensuales = InformeContableMensualData::where('informe_contable_mensual_id', $informe->id)->whereIn('puc_alcaldia_id', $puc_mensual->puc_alcaldia->hijos->pluck('id')->toArray())->get();
            foreach($pucs_mensuales as $hijo):
                $hijo->padre_id = $puc_mensual->id;
                $hijo->save();
            endforeach;
        }

        $puc_mensual->a_credito = $puc_mensual->a_credito + $puc_mensual->hijos->sum('a_credito');
        $puc_mensual->a_debito = $puc_mensual->a_debito + $puc_mensual->hijos->sum('a_debito');  
        $puc_mensual->save();


        return response()->json($pucs_count == $informe->datos->count() ? TRUE : FALSE);
    }
}
