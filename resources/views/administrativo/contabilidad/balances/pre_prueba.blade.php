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
        const porcentaje_pucs = (85/pucs.length)/2;
        let porcentaje_carga = 0;

        $(document).ready(function() {
            pucs.forEach(async p => {
                cargar_porcentaje_actual();
                let resp = await fetch(`/administrativo/contabilidad/blance-prueba/${informe.id}/${p.id}`)
                .then(response => response.json())
                .then(data => {
                    cargar_porcentaje_actual();
                    return data;
                });

                if(resp){
                    cargar_relaciones_data();
                }
            })
        })

        const cargar_porcentaje_actual = () => {
            porcentaje_carga += porcentaje_pucs;
            let porcentaje = Math.round(porcentaje_carga)
            $('#progress-bar').attr("style","width:"+porcentaje+'%').text(`${porcentaje}%`);
            console.log('cargando', porcentaje);
        }

        const cargar_relaciones_data = async () => {
            let resp = await fetch(`/administrativo/contabilidad/blance-prueba-relaciones/${informe.id}`)
            .then(response => response.json())
            .then(data => data);

            if(resp){
                $('#progress-bar').attr("style","width:100%").text(`100%`);

                setTimeout(function(){
                    window.location.href = "{{route('balance.prueba-informe', $informe)}}";
                }, 2000);
            }
        }


    </script>
@stop