<?php

namespace App\Helpers;
use Carbon\Carbon;

class InstitutoHelper {
    static function lema(){  
    	return 'Unidos por un Trabajo Social';
    }

    static function secretaria($fecha){  
        if($fecha < '2020-02-01'):
            return 'Virginia Webster Archbold'; 
        elseif($fecha < '2021-01-01'):
            return 'Marhit May Jay';
        else:
            return 'Camila Nicolle Amador Hooker';
        endif;
    }  

    static function cdp1_nomina(){
        return [
            "rubros" => [
                [
                    '2.1.1.01.01.001.01', 'Sueldo Básico'
                ],
                [
                    '2.1.1.01.01.001.01', 'Retroactivo'
                ],
                [
                    '2.1.1.01.01.001.02', 'H. Extras, recargos y festivos'
                ],
    
                [
                    '2.1.1.01.03.003', 'Bonificación Dirección'
                ],
                [
                    '2.1.1.01.01.001.07', 'Bonificación por Servicios prestados'
                ],
                [
                    '2.1.1.01.03.001.03', 'Bonificación recreacion'
                ],
    
                [
                    '2.1.1.01.01.002.12.01', 'Prima Antiguedad'
                ],
                [
                    '2.1.1.01.03.001.01', 'Vacaciones'
                ],
                [
                    '2.1.1.01.01.001.08.02', 'Indemnización por vacaciones'
                ],
    
                [
                    '2.1.1.01.03.001.03', 'Bonificación especial por recreación'
                ],
                [
                    '2.1.1.01.01.001.06', 'Prima de servicios'
                ],
                [
                    '2.1.1.01.01.001.08.01', 'Prima de navidad'
                ],
    
                [
                    '2.1.1.01.01.001.09', 'Prima técnica salarial'
                ],
                [
                    '2.1.1.01.01.001.03', 'Gastos de representación'
                ],
                [
                    '2.1.1.01.01.001.04', 'Auxilio de transporte'
                ]
            ],
            "pagos_debito" => [
                [
                    '5101010001', 'Nomina por pagar'
                ],
                [
                    '5101010001', 'Nomina por pagar (Retroactivo)'
                ],
                [
                    '5101030001', 'Horas extras y festivos'
                ],
                [
                    '5101190001', 'Bonificación de Dirección'
                ],
                [
                    '5101190001', 'Bonificaciones por servicios prestados'
                ],
                [
                    '5101190001', 'Prima de Antiguedad'
                ],
                [
                    '5107010101', 'Vacaciones'
                ],
                [
                    '5107040101', 'Prima de Vacaciones'
                ],
                [
                    '5102030001', 'Indemnizaciones'
                ],
                [
                    '5107060101', 'Prima de Servicios'
                ],
                [
                    '5107050101', 'Prima de Navidad'
                ]
            ],
            "pagos_credito" => [
                [
                    '2511010001', 'Nomina por pagar'
                ],
                [
                    '2511010001', 'Nomina por pagar (Retroactivo)'
                ],
                [
                    '2511010002', 'Horas extras y festivos'
                ],
                [
                    '2511090101', 'Bonificación de Dirección'
                ],
                [
                    '2511090101', 'Bonificaciones por servicios prestados'
                ],
                [
                    '2511100101', 'Otras Primas'
                ],
                [
                    '2511040101', 'Vacaciones'
                ],
                [
                    '2511050101', 'Prima de Vacaciones'
                ],
                [
                    '2513010101', 'Indemnizaciones'
                ],
                [
                    '2511060101', 'Prima de Servicios'
                ],
                [
                    '2511070101', 'Prima de Navidad'
                ],

                [
                    '2424070028', 'Banco Popular'
                ],
                [
                    '2424070029', 'Banco de Bogota'
                ],
                [
                    '2424070051', 'Banco Davivienda'
                ],
                [
                    '2424070053', 'Banco Agrario'
                ],
                [
                    '2424070046', 'cooserpark'
                ],
                [
                    '2424070025', 'Coocasa'
                ],
                [
                    '2424110001', 'Juzgado Promiscuo Municipal'
                ],
                [
                    '2424900029', 'Sindicato Bomberos de Colombia'
                ],
                [
                    '2436150101', 'Dirección de Impuestos y Aduanas DIAN Rentas de trabajo'
                ],
                [
                    '2424020006', 'Nueva EPS'
                ],
                [
                    '2424020004', 'Sanitas'
                ],
                [
                    '2424020003', 'Compensar'
                ],
                [
                    '2424020008', 'Alianza Salud'
                ],
                [
                    '2424010011', 'Colpensiones'
                ],
                [
                    '2424010005', 'Porvenir'
                ],
                [
                    '2424010013', 'Protección'
                ],
            ]
        ];
    }

    static function cdp2_nomina(){
        return [
            [
                '2.1.1.01.02.006', 'Aportes al ICBF'
            ],
            [
                '2.1.1.01.02.007', 'Aportes al SENA'
            ],
            [
                '2.1.1.01.02.008', 'Aportes a la ESAP'
            ],
            [
                '2.1.1.01.02.009', 'Ministerio Educación Nacional MEN'
            ],

            [
                '2.1.1.01.02.001', 'Colpensiones'
            ],
            [
                '2.1.1.01.02.001', ' Porvenir'
            ],

            [
                '2.1.1.01.02.001', 'Protección'
            ],

            [
                '2.1.1.01.02.002', 'Nueva EPS'
            ],
            [
                '2.1.1.01.02.002', 'Sanitas'
            ],

            [
                '2.1.1.01.02.002', 'Aliansalud EPS'
            ],
            [
                '2.1.1.01.02.002', 'Compensar'
            ],
            [
                '2.1.1.01.02.003', 'Aportes de cesantias'
            ],

            [
                '2.1.1.01.02.004', 'Aportes de Cajas de compensación familiar'
            ],
            [
                '2.1.1.01.02.005', 'Aportes generales al sistema de riesgos laborales'
            ]
        ];
    }

    static function orden_pagos1_nomina(){
        return [
            

            [
                '5101010001', 'Nomina por pagar', 'd'
            ],
            [
                '5101010001', 'Nomina por pagar (Retroactivo)', 'd'
            ],
            [
                '5101030001', 'Horas extras y festivos', 'd'
            ],
            [
                '5101190001', 'Bonificación de Dirección', 'd'
            ],
            [
                '5101190001', 'Bonificaciones por servicios prestados', 'd'
            ],
            [
                '5101190001', 'Prima de Antiguedad', 'd'
            ],
            [
                '5107010101', 'Vacaciones', 'd'
            ],
            [
                '5107040101', 'Prima de Vacaciones', 'd'
            ],
            [
                '5102030001', 'Indemnizaciones', 'd'
            ],
            [
                '5107060101', 'Prima de Servicios', 'd'
            ],
            [
                '5107050101', 'Prima de Navidad', 'd'
            ],
            
        ];
    }
}
