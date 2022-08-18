@extends('layouts.dashboard')
@section('titulo')
    Balance de Prueba
@stop
@section('sidebar')@stop
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="col-md-12 align-self-center">
        <div class="breadcrumb text-center">
            <strong>
                <h4><b>Balance de Prueba</b></h4>
            </strong>
        </div>
        <div class="table-responsive">
            <br>
            <table class="table text-center">
                <thead>
                <th colspan="4">FILTRADO DEL BALANCE DE PRUEBA</th>
                </thead>
                <tr>
                    <td>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="lvlCuentas" id="lvlCuentas" checked>
                            <label for="lvlCuentas">Nivel Cuentas</label>
                        </div>
                    </td>
                    <td>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="lvlSubCuentas" id="lvlSubCuentas" checked>
                            <label for="lvlSubCuentas">Nivel SubCuentas</label>
                        </div>
                    </td>
                    <td>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="lvlAuxiliar" id="lvlAuxiliar" checked>
                            <label for="lvlAuxiliar">Nivel Auxiliar</label>
                        </div>
                    </td>
                    <td>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="lvlTercero" id="lvlTercero" checked>
                            <label for="lvlTercero">Nivel Tercero</label>
                        </div>
                    </td>
                </tr>
            </table>
            <table class="table table-bordered table-hover" id="tabla">
                <hr>
                <thead>
                    <th class="text-center">Codigo</th>
                    <th class="text-center">Cuenta</th>
                    <th class="text-center">Debito</th>
                    <th class="text-center">Credito</th>
                    <th class="text-center">Total</th>
                </thead>
                <tbody id="bodyTabla">
                @foreach($result as $data)
                    <tr class="{{$data['lvl']}}">
                        <td>{{$data['code']}}</td>
                        <td>{{$data['name']}}</td>
                        @if($data['lvl'] == "nivel4" or $data['lvl'] == "nivel5" or $data['lvl'] == "nivel6")
                            <td>$<?php echo number_format($data['debito'],0) ?></td>
                            <td>$<?php echo number_format($data['credito'],0) ?></td>
                            <td>$<?php echo number_format($data['total'],0) ?></td>
                        @else
                            <td>{{$data['debito']}}</td>
                            <td>{{$data['credito']}}</td>
                            <td>{{$data['total']}}</td>
                        @endif

                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            toastr.options = {
                "closeButton": true,
                "debug": false,
                "newestOnTop": true,
                "progressBar": true,
                "positionClass": "toast-bottom-right",
                "preventDuplicates": false,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": 3000,
                "extendedTimeOut": 0,
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut",
                "tapToDismiss": true
            }

            var checkboxlvlCuentas = document.getElementById('lvlCuentas');
            var checkboxlvlSubCuentas = document.getElementById('lvlSubCuentas');
            var checkboxlvlAuxiliar = document.getElementById('lvlAuxiliar');
            var checkboxlvlTerceros = document.getElementById('lvlTercero');

            checkboxlvlCuentas.addEventListener("change", validateCuenta, false);
            checkboxlvlSubCuentas.addEventListener("change", validateSubCuenta, false);
            checkboxlvlAuxiliar.addEventListener("change", validateAux, false);
            checkboxlvlTerceros.addEventListener("change", validateTer, false);

            function validateCuenta(){
                if(checkboxlvlCuentas.checked){
                    $(".nivel3").show();
                } else {
                    $(".nivel3").hide();
                }
            }
            function validateSubCuenta(){
                if(checkboxlvlSubCuentas.checked){
                    $(".nivel4").show();
                } else {
                    $(".nivel4").hide();
                }
            }
            function validateAux(){
                if(checkboxlvlAuxiliar.checked){
                    $(".nivel5").show();
                } else {
                    $(".nivel5").hide();
                }
            }
            function validateTer(){
                if(checkboxlvlTerceros.checked){
                    $(".nivel6").show();
                } else {
                    $(".nivel6").hide();
                }
            }
        });

        $('#tabla').DataTable({
            language: {
                "lengthMenu": "Mostrar _MENU_ registros",
                "zeroRecords": "No se encontraron resultados",
                "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "infoFiltered": "(filtrado de un total de _MAX_ registros)",
                "sSearch": "Buscar:",
                "oPaginate": {
                    "sFirst": "Primero",
                    "sLast": "Último",
                    "sNext": "Siguiente",
                    "sPrevious": "Anterior"
                },
                "sProcessing": "Procesando...",
            },
            "pageLength": 100000,
            responsive: true,
            "searching": true,
            ordering: false,
            dom: 'Bfrtip',
            buttons: [
            ]
        })


    </script>
@stop