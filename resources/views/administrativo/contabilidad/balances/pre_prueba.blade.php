@extends('layouts.dashboard')
@section('titulo')
    Balance de Prueba
@stop
@section('sidebar')@stop
@section('css')

    <style>
        @import url('//fonts.googleapis.com/css2?family=Montserrat:ital,wght@1,800&display=swap');

        body{
            font-family: 'Montserrat', sans-serif;
        font-weight: 800;
            background-color: #FFF;
            color: #950808;
        }
        /* ======================== */
        .container{   
        display: grid;
            place-content: center;
            height: 100vh;
        }
        .cargando{
            width: 120px;
            height: 30px;
            display: flex;
            flex-wrap: wrap;
            align-items: flex-end;
            justify-content: space-between;
        margin: 0 auto; 
        }
        .texto-cargando{ 
        padding-top:20px
        }
        .cargando span{
            font-size: 20px;
            text-transform: uppercase;
        }
        .pelotas {
            width: 30px;
            height: 30px;
            background-color: #950808;
            animation: salto .5s alternate
            infinite;
        border-radius: 50%  
        }
        .pelotas:nth-child(2) {
            animation-delay: .18s;
        }
        .pelotas:nth-child(3) {
            animation-delay: .37s;
        }
        @keyframes salto {
            from {
                transform: scaleX(1.25);
            }
            to{
                transform: 
                translateY(-50px) scaleX(1);
            }
        }
    </style>
@stop
@section('content')
    <div class="col-md-12 align-self-center">
        <div class="container">
            <div class="cargando">
                <div class="pelotas"></div>
                    <div class="pelotas"></div>
                    <div class="pelotas"></div>
                    <span class="texto-cargando">Cargando...</span>
                </div>
                <div class="progress">
                    <div id="progress-bar" class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 60%;">
                        0%
                    </div>
                </div>
            </div>
            
        </div>
    </div>
@stop

@section('js')
    <script>
        const pucs = @json($pucs);
        const informe = @json($informe);
        const data_count = {{$informe->datos->count()}};
        const porcentaje_pucs = (96/pucs.length)/2;
        let porcentaje_carga = 0;
        let contador = 0;
        let contador_inicial = 0;
        let level = 5;

        $(document).ready(function() {
            generar_pucs();
        })

        const generar_pucs = () => {
            let pucs_level = pucs.filter(p => p.level == level); 
            contador_inicial = 0;
            contador = 0;
            pucs_level.forEach(async p => {
                contador_inicial +=1;
                cargar_porcentaje_actual();
                let resp = await fetch(`/administrativo/contabilidad/blance-prueba/${informe.id}/${p.id}`)
                .then(response => response.json())
                .then(data => {
                    contador +=1;
                    cargar_porcentaje_actual();
                    return data;
                });

                console.log('final', [contador, data_count, contador_inicial]);
                if(contador == contador_inicial){
                    if(level > 1){ 
                        level -=1;
                        generar_pucs();
                    }else{
                        $('#progress-bar').attr("style","width:100%").text(`100%`);

                       // setTimeout(function(){
                            window.location.href = "{{route('balance.prueba-informe', $informe)}}";
                        //}, 2000);
                    }

                }
            })
        }

        const cargar_porcentaje_actual = () => {
            porcentaje_carga += porcentaje_pucs;
            let porcentaje = Math.round(porcentaje_carga)
            $('#progress-bar').attr("style","width:"+porcentaje+'%').text(`${porcentaje}%`);
            console.log('cargando', porcentaje);
        }



    </script>
@stop