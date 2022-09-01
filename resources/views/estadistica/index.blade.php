@extends('layouts.dashboard')
@section('titulo')
    Estadistica
@stop
@section('content')
   <select id="tipo_pasajero" name="tipo_pasajero" class="form_control">
        <option value="Turista">Turista</option>
        <option value="Trabajador">Trabajador</option>
   </select>

    <div class="row field-turista">
        campos del turista
    </div>
    <div class="row field-trabajador">
        campos del trabajador
    </div>
   
@stop

@section('js')
    <script type="text/javascript">

        $(document).ready(function(){
            change_passenger();
        });
           $('#tipo_pasajero').on('change', function(){
                change_passenger();
           }) 

        const change_passenger = () => {
            tipo = $('#tipo_pasajero').val();
            if(tipo == 'Turista'){
                $('.field-turista').show();
                $('.field-trabajador').hide();
            }else{
                 $('.field-turista').hide();
                $('.field-trabajador').show();
            }
        }
    </script>
@stop
