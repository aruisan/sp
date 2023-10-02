@extends('impuestos.layout')
@section('container')
    <div class="container" style="background-color: white" >
            <div class="col-md-12 align-self-center">
                <div class="breadcrumb text-center">
                    <strong>
                        <h4><b>Municipio de Providencia y Santa Catalina</b></h4>
                        <h4><b>Secretaria de Hacienda Municipal</b></h4>
                        <h4><b>OFICINA DE RECAUDOS - INDUSTRIA Y COMERCIO</b></h4>
                        <h4><b>REPORTE DE INFORMACION EXOGENA TRIBUTARIA</b></h4>
                        FORMATO SHI-WEB04-2022
                    </strong>
                </div>
                <div class="col-lg-12">
                    <div class="form-validation">
                        <form class="form-valide" action="{{url('/impuestos/ICA/exogena')}}" method="POST" enctype="multipart/form-data" id="formulario">
                            {{ csrf_field() }}
                            {{-- ENCABEZADO--}}
                            <table id="TABLA1" class="table text-center">
                                <tbody>
                                <tr style="background-color: #0e7224; color: white">
                                    <th scope="row" colspan="3">DATOS DEL REPORTANTE</th>
                                </tr>
                                <tr style="background-color: #bfc3bf; color: black">
                                    <th scope="row" >MUNICIPIO O DISTRITO </th>
                                    <th scope="row" colspan="2">PROVIDENCIA Y SANTA CATALINA ISLAS</th>
                                </tr>
                                <tr style="background-color: #bfc3bf; color: black">
                                    <td>DEPARTAMENTO</td>
                                    <td colspan="2">ARCHIPIELAGO DE SAN ANDRES, PROVIDENCIA Y SANTA CATALINA</td>
                                </tr>
                                <tr style="background-color: #bfc3bf; color: black">
                                    <td>AÑO GRAVABLE</td>
                                    <td>
                                        @if($action == "Actualización")
                                            <b>{{ Carbon\Carbon::today()->Format('Y')}}</b>
                                            <input type="hidden" name="año" value="{{ Carbon\Carbon::today()->Format('Y')}}">
                                        @else
                                            <select class="form-control" id="año" name="año">
                                                <option value="2022">2022</option>
                                                <option value="2023">2023</option>
                                            </select>
                                        @endif
                                        </td>
                                    <td>
                                        @if($action == "Creación")
                                            Reporte de Exogena
                                            <input type="hidden" name="opciondeUso" value="Creación">
                                        @else
                                            Actualización de Exogena
                                            <input type="hidden" name="opciondeUso" value="Actualización">
                                        @endif

                                    </td>

                                </tr>
                                </tbody>
                            </table>

                            {{-- TABLA A. INFORMACIÓN DEL CONTRIBUYENTE --}}
                            <table id="TABLA2" class="table text-center">
                                <tbody>
                                <tr style="background-color: #0e7224; color: white">
                                    <th scope="row" colspan="2">A. INFORMACIÓN DEL CONTRIBUYENTE</th>
                                </tr>
                                <tr>
                                    <td>Nombre y apellidos o razón Social</td>
                                    <td>{{ $rit->apeynomContri }}</td>
                                </tr>
                                <tr>
                                    <td>Número de identificación</td>
                                    <td>{{ $rit->tipoDocContri }} No. {{ $rit->numDocContri }}</td>
                                </tr>
                                <tr>
                                    <td>Representante Legal</td>
                                    <td>{{ $rit->nombreRepLegal }}</td>
                                </tr>
                                <tr>
                                    <td>Dirección de Notificación</td>
                                    <td>{{ $rit->dirNotifContri }}</td>
                                </tr>
                                <tr>
                                    <td>Teléfono Móvil</td>
                                    <td>{{ $rit->movilContri }}</td>
                                </tr>
                                <tr>
                                    <td>Correo electrónico</td>
                                    <td>{{ $rit->emailContri }}</td>
                                </tr>
                                <tr>
                                    <td>Departamento</td>
                                    <td>ARCHIPIELAGO DE SAN ANDRES</td>
                                </tr>
                                <tr>
                                    <td>Municipio o Distrito de la Dirección de Notificación</td>
                                    <td>PROVIDENCIA Y SANTA CATALINA ISLAS</td>
                                </tr>
                                </tbody>
                            </table>
                            <br>
                            <div class="table-responsive">
                                <table id="exogena" class="table text-center table-bordered">
                                    <thead>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th style="background-color: #0e7224; color: white; vertical-align: middle" colspan="4">PERSONA NATURAL INFORMADA</th>
                                        <th style="background-color: #0e7224; color: white">PERSONA JURIDICA</th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                    <tr style="background-color: #0e7224; color: white">
                                        <th style="vertical-align: middle"><i class="fa fa-plus"></i></th>
                                        <th style="vertical-align: middle">Número de identificación del informado</th>
                                        <th style="vertical-align: middle">Dv</th>
                                        <th style="vertical-align: middle">Primer apellido</th>
                                        <th style="vertical-align: middle">Segundo Apellido</th>
                                        <th style="vertical-align: middle">Primer Nombre</th>
                                        <th style="vertical-align: middle">Otros Nombres</th>
                                        <th style="vertical-align: middle">Razón Social</th>
                                        <th style="vertical-align: middle">Dirección de Notificación del Informado</th>
                                        <th style="vertical-align: middle">Telefono del Informado</th>
                                        <th style="vertical-align: middle">Dirección de correo electrónico del Informado</th>
                                        <th style="vertical-align: middle">Código Dpto.</th>
                                        <th style="vertical-align: middle">Código Ciudad o Municipio</th>
                                        <th style="vertical-align: middle">Actividad CIUU</th>
                                        <th style="vertical-align: middle">Valor acumulado del pago o abono sujeto a retención</th>
                                        <th style="vertical-align: middle">Tarifa</th>
                                        <th style="vertical-align: middle">Vr de la retención a titulo de Industria y Comercio</th>
                                        <th style="vertical-align: middle">Vr de la retención ASUMIDA a titulo de Industria y Comercio</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if($action == "Actualización")
                                        @foreach($exogena as $exo)
                                            <tr>
                                                <td><button type="button" @click.prevent="eliminar({{ $exo->id }})" class="btn btn-sm btn-primary-impuestos"><i class="fa fa-trash"></i></button></td>
                                                <td> {{ $exo->numIdeInform }}</td>
                                                <td>{{ $exo->dv }}</td>
                                                <td>{{ $exo->primerApe }}</td>
                                                <td>{{ $exo->segApe }}</td>
                                                <td>{{ $exo->primerNom }}</td>
                                                <td>{{ $exo->otrosNombres }}</td>
                                                <td>{{ $exo->razonSocial }}</td>
                                                <td>{{ $exo->dir }}</td>
                                                <td>{{ $exo->tel }}</td>
                                                <td>{{ $exo->email }}</td>
                                                <td>{{ $exo->codeDpto }}</td>
                                                <td>{{ $exo->codeCiudad }}</td>
                                                <td>{{ $exo->ciuu_id }}</td>
                                                <td>{{ $exo->valorAcum }}</td>
                                                <td>{{ $exo->tarifa }}</td>
                                                <td>{{ $exo->valorReten }}</td>
                                                <td>{{ $exo->valorRetenAsum }}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    <tr>
                                        <td><button type="button" @click.prevent="nuevaFilaPrograma" class="btn btn-sm btn-primary-impuestos"><i class="fa fa-plus"></i></button></td>
                                        <td><input class="form-control" type="text" name="numIdeInform[]" required></td>
                                        <td><input class="form-control" style="width: 60px" type="text" name="dv[]" required></td>
                                        <td><input class="form-control" type="text" name="primerApe[]" required></td>
                                        <td><input class="form-control" type="text" name="segApe[]" required></td>
                                        <td><input class="form-control" type="text" name="primerNom[]" required></td>
                                        <td><input class="form-control" type="text" name="otrosNombres[]"></td>
                                        <td><input class="form-control" type="text" name="razonSocial[]" required></td>
                                        <td><input class="form-control" type="text" name="dir[]" required></td>
                                        <td><input class="form-control" type="number" name="tel[]" required></td>
                                        <td><input class="form-control" type="email" name="email[]" required></td>
                                        <td><select class="select2" name="codeDpto[]">@foreach($deptos as $dept)<option value="{{$dept->code_dept}}" >{{ $dept->code_dept }} - {{ $dept->name_dept }}</option>@endforeach</select></td>
                                        <td><select class="select2" name="codeCiudad[]">@foreach($codeMuni as $ciudad)<option value="{{$ciudad->id}}" >{{ $ciudad->code_ciudad }} - {{ $ciudad->name_ciudad }}</option>@endforeach</select></td>
                                        <td><select class="select2" name="ciuu_id[]">@foreach($ciuu as $ciu)<option value="{{$ciu->id}}" >{{ $ciu->code_ciuu }} - {{ $ciu->description }}</option>@endforeach</select></td>
                                        <td><input class="form-control" type="number" name="valorAcum[]" required></td>
                                        <td><input class="form-control" type="number" name="tarifa[]" required></td>
                                        <td><input class="form-control" type="number" name="valorReten[]" required></td>
                                        <td><input class="form-control" type="number" name="valorRetenAsum[]" required></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            {{-- TABLA E. FIRMAS --}}
                            <table id="TABLA7" class="table text-center table-bordered">
                                <tbody>
                                <tr style="background-color: #0e7224; color: white">
                                    <th scope="row" colspan="2">ENVIAR</th>
                                </tr>
                                <tr>
                                    <td>
                                        37. Fecha de envio<br><h3>{{ Carbon\Carbon::today()->Format('d-m-Y')}}</h3>
                                    </td>
                                    <td>
                                        <button type="submit" class="btn btn-impuesto" style="font-size: 25px; color: white">Enviar</button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <table class="table text-center">
                                <tbody>
                                <tr style="background-color: #0e7224; color: white">
                                    <th scope="row">ESTE FORMULARIO Y SU PRESENTACIÓN NO TIENE COSTO ALGUNO</th>
                                </tr>
                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
            </div>
    </div>
@stop
@section('scripts')
    <script>

        let app = new Vue({
            el: '#app',
            methods:{

                eliminar: function(dato){
                    var opcion = confirm("Esta seguro de eliminar la persona del exogena?");
                    if (opcion == true) {
                        var urlexogena = '/impuestos/ICA/exogena/delete/'+dato;
                        axios.delete(urlexogena).then(response => {
                            location.reload();
                        });
                    }
                },

                nuevaFilaPrograma(){
                    $('#exogena tbody tr:first').after('<tr>\n' +
                        '<td><button type="button" class="btn-primary-impuestos btn-sm borrar">&nbsp;-&nbsp; </button></td>\n' +
                        '<td><input class="form-control" type="text" name="numIdeInform[]" required></td>\n' +
                        '<td><input class="form-control" style="width: 60px" type="text" name="dv[]" required></td>\n' +
                        '<td><input class="form-control" type="text" name="primerApe[]" required></td> \n' +
                        '<td><input class="form-control" type="text" name="segApe[]" required></td>\n'+
                        '<td><input class="form-control" type="text" name="primerNom[]" required></td>\n'+
                        '<td><input class="form-control" type="text" name="otrosNombres[]"></td>\n'+
                        '<td><input class="form-control" type="text" name="razonSocial[]" required></td>\n'+
                        '<td><input class="form-control" type="text" name="dir[]" required></td>\n'+
                        '<td><input class="form-control" type="number" name="tel[]" required></td>\n'+
                        '<td><input class="form-control" type="email" name="email[]" required></td>\n' +
                        '<td><select class="select2" name="codeDpto[]">@foreach($deptos as $dept)<option value="{{$dept->code_dept}}" >{{ $dept->code_dept }} - {{ $dept->name_dept }}</option>@endforeach</select></td>\n' +
                        '<td><select class="select2" name="codeCiudad[]">@foreach($codeMuni as $ciudad)<option value="{{$ciudad->id}}" >{{ $ciudad->code_ciudad }} - {{ $ciudad->name_ciudad }}</option>@endforeach</select></td>\n' +
                        '<td><select class="select2" name="ciuu_id[]">@foreach($ciuu as $ciu)<option value="{{$ciu->id}}" >{{ $ciu->code_ciuu }} - {{ $ciu->description }}</option>@endforeach</select></td>\n' +
                        '<td><input class="form-control" type="number" name="valorAcum[]" required></td>\n' +
                        '<td><input class="form-control" type="number" name="tarifa[]" required></td>\n' +
                        '<td><input class="form-control" type="number" name="valorReten[]" required></td>\n' +
                        '<td><input class="form-control" type="number" name="valorRetenAsum[]" required></td>\n'+
                        '</tr>');

                    $('.select2').select2({
                        theme: "classic"
                    });

                }
            }
        });

        $(document).ready(function() {

            $('.select2').select2({
                theme: "classic"
            });
        } );

        $(document).on('click', '.borrar', function (event) {
            event.preventDefault();
            $(this).closest('tr').remove();
        });

        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById("formulario").addEventListener('submit', validarFormulario);
        });

        function validarFormulario(evento) {
            evento.preventDefault();
            /*
            var totIngreOrd = document.getElementById('añoGravable').value;
            if(totIngreOrd.length < 2) {
                alert('Valor menor a dos digitos para el total de ingresos');
                return;
            }
             */
            this.submit();
        }

        $('#exogena').DataTable( {
            language: {
                "lengthMenu": "Mostrar _MENU_ registros",
                "zeroRecords": "No se encontraron resultados",
                "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "infoFiltered": "(filtrado de un total de _MAX_ registros)",
                "sSearch": "Buscar:",
                "oPaginate": {
                    "sFirst": "Primero",
                    "sLast":"Último",
                    "sNext":"Siguiente",
                    "sPrevious": "Anterior"
                },
                "sProcessing":"Procesando...",
            },
            responsive: "true",
            "ordering": false,
            dom: 'lrtip',
            paging: false,
            info: false,
            buttons:[
                {
                    extend:    'copyHtml5',
                    text:      '<i class="fa fa-clone"></i> ',
                    titleAttr: 'Copiar',
                    className: 'btn btn-primary'
                },
                {
                    extend:    'excelHtml5',
                    text:      '<i class="fa fa-file-excel-o"></i> ',
                    titleAttr: 'Exportar a Excel',
                    className: 'btn btn-primary'
                },
                {
                    extend:    'pdfHtml5',
                    text:      '<i class="fa fa-file-pdf-o"></i> ',
                    titleAttr: 'Exportar a PDF',
                    message : 'SIEX-Providencia',
                    header :true,
                    orientation : 'landscape',
                    pageSize: 'LEGAL',
                    className: 'btn btn-primary',
                },
                {
                    extend:    'print',
                    text:      '<i class="fa fa-print"></i> ',
                    titleAttr: 'Imprimir',
                    className: 'btn btn-primary'
                },
            ]
        } );
    </script>
@stop