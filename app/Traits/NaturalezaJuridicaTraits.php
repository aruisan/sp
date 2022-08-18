<?php
namespace App\Traits;

trait NaturalezaJuridicaTraits
{
	public function nameNaturalezaJuridica($code){
        if ($code == "PJ") return "Jurídica";
        elseif ($code == "PN") return "Natural";
        elseif ($code == "SH") return "Sociedad de Hecho";
        elseif ($code == "PA") return "Patrimonio Autónomo";
        elseif ($code == "CO") return "Consorcios";
        elseif ($code == "UT") return "Unidad Temporal";
        elseif ($code == "CR") return "Comunidad organizada";
        elseif ($code == "SI") return "Sucesión Iliquida";
        elseif ($code == "SM") return "Sociedad de Economía mixta de todo orden";
        elseif ($code == "UA") return "Unidad Administrativa con régimen especial";
        elseif ($code == "DC") return "Departamento de Cundinamarca";
        elseif ($code == "LN") return "La Nación";
        elseif ($code == "EE") return "Entidad del Estado";
        elseif ($code == "EM") return "Establecimiento Público y Empresa Industrial, Comercial de Orden Municipal";
        elseif ($code == "EC") return "Entidad del Estado de cualquier naturaleza";
        elseif ($code == "EN") return "Establecimiento Público y Empresa Industrial, Comercial de Orden Nacional";
        elseif ($code == "ED") return "Establecimiento Público y Empresa Industrial, Comercial de Orden Departamental";
        else return "CODIGO NO RECONOCIDO";
	}

    public function nameTipoSociedad($code){
        if ($code == "1") return "1 - Colectiva";
        elseif ($code == "2") return "2 - Limitada";
        elseif ($code == "3") return "3 - Anónima";
        elseif ($code == "4") return "4 - En comandita por acciones";
        elseif ($code == "5") return "5 - En comandita simple";
        elseif ($code == "6") return "6 - Unipersonal";
        elseif ($code == "7") return "7 - Anónima Simplificada";
        elseif ($code == "8") return "8 - De Economía mixta";
        elseif ($code == "9") return "9 - Extranjera";
        elseif ($code == "10") return "10 - Civil";
        elseif ($code == "11") return "11 - Asociativa de Trabajo";
        elseif ($code == "12") return "12 - Otras";
        else return "CODIGO NO RECONOCIDO";
    }

    public function nameTipoEntidad($code){
        if ($code == "20") return "20 - Financiera";
        elseif ($code == "21") return "21 - Oficial";
        elseif ($code == "22") return "22 - Privada";
        elseif ($code == "23") return "23 - Patrimonios Autónomos";
        else return "CODIGO NO RECONOCIDO";
    }

    public function nameClaseEntidad($code){
        if ($code == "30") return "30 - BANCOS";
        elseif ($code == "31") return "31 - CORPORCION FINANCIERA";
        elseif ($code == "32") return "32 - COMPAÑÍA DE SEGUROS";
        elseif ($code == "33") return "33 - CIAS DE FINANCIAMIENTO COMERCIAL";
        elseif ($code == "34") return "34 - ALMACEN GENERAL DE DEPOSITO";
        elseif ($code == "35") return "35 - SOCIEDAD DE CAPITALIZACIÓN";
        elseif ($code == "36") return "36 - LEASING";
        elseif ($code == "37") return "37 - FIDUCIARIAS";
        elseif ($code == "38") return "38 - DEMÁS ENTE DE CRÉDITO Y FINANCIACIÓN";
        elseif ($code == "39") return "39 - BANCO DE LA REPÚBLICA";
        elseif ($code == "40") return "40 - DEL ORDEN NACIONAL";
        elseif ($code == "41") return "41 - DEL ORDEN DEPARTAMENTAL";
        elseif ($code == "42") return "42 - DEL ORDEN MUNICIPAL";
        elseif ($code == "43") return "43 - COOPERATIVA";
        elseif ($code == "44") return "44 - PRECOOPERATIVA";
        elseif ($code == "45") return "45 - ASOCIACIÓN MUTUAL";
        elseif ($code == "46") return "46 - FONDO DE EMPLEADOS";
        elseif ($code == "47") return "47 - MICROEMPRESAS Y FAMIEMPRESAS";
        elseif ($code == "48") return "48 - EDUCACIÓN PRIVADA";
        elseif ($code == "49") return "49 - RECICLAJE";
        elseif ($code == "50") return "50 - SERVICIOS DE SALUD";
        elseif ($code == "51") return "51 - ASISTENCIA SOCIAL";
        elseif ($code == "52") return "52 - ECOLOGIA Y ROTECCION DEL AMBIENTE";
        elseif ($code == "53") return "53 - ATENCIÓN A LOS DAMNIFICADOS";
        elseif ($code == "54") return "54 - VOLUNTARIADO SOCIAL DESARROLLO COMUNITARIO";
        elseif ($code == "55") return "55 - INVESTIGACIÓN DIVULGACIÓN CIENCIA TECNOLOGÍA";
        elseif ($code == "56") return "56 - PROMOCIÓN DEPORTE Y RECREACIÓN POPULAR";
        elseif ($code == "57") return "57 - PROMOCIÓN VALORES PARTICIPACIÓN CIUDADANA";
        elseif ($code == "58") return "58 - PROMOCIÓN DE MICRO Y FAMIEMPRESAS";
        elseif ($code == "59") return "59 - PROMOCIÓN DE ANTIVIDADES CULTURALES";
        elseif ($code == "60") return "60 - PROMOCIÓN ENTIDADES SIN ÁNIMO DE LUCRO";
        elseif ($code == "61") return "61 - ORGANISMOS DE SOCORRO";
        elseif ($code == "62") return "62 - PRIVADA";
        else return "CODIGO NO RECONOCIDO";
    }
}