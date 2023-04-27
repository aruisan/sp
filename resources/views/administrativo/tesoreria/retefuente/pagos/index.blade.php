@extends('layouts.dashboard')
@section('titulo') Pagos Retención Fuente @stop
@section('content')
    <div class="breadcrumb text-center">
        <strong>
            <h4><b>Pagos de la Retención en la Fuente {{$vigencia->vigencia}} </b></h4>
        </strong>
    </div>
    <ul class="nav nav-pills">
        <li class="nav-item active">
            <a class="nav-link"><i class="fa fa-home"></i></a>
        </li>
        <li class="nav-item"><a class="nav-link" href="{{ url('/administrativo/tesoreria/retefuente/pago/'.$vigencia_id.'/1') }}">Pago Enero</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ url('/administrativo/tesoreria/retefuente/pago/'.$vigencia_id.'/2') }}">Pago Febrero</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ url('/administrativo/tesoreria/retefuente/pago/'.$vigencia_id.'/3') }}">Pago Marzo</a></li>
    </ul>

    <div class="tab-content" >
        <div id="tabTareas" class="tab-pane fade in active"><br>
            <br>
            <div class="table-responsive">
                @if(count($pagos) > 0)
                    <table class="table table-bordered" id="tabla_CDP">
                        <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Mes</th>
                            <th class="text-center">Valor</th>
                            <th class="text-center">Fecha</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($pagos as $index => $pago)
                            <tr>
                                <td class="text-center">{{ $pago->id }}</td>
                                <td class="text-center">{{ $pago->mes }}</td>
                                <td class="text-center">$ <?php echo number_format($pago->valor,0);?></td>
                                <td class="text-center"> {{ \Carbon\Carbon::parse($pago->created_at)->format('d-m-Y H:i:s') }}</td>
                                <td class="text-center">
                                    <a href="{{ url('administrativo/tesoreria/retefuente/viewpago/'.$pago->id.'/view') }}" title="Ver Pago" class="btn-sm btn-primary"><i class="fa fa-eye"></i></a>
                                    <a href="{{ url('administrativo/tesoreria/retefuente/PDFpago/'.$pago->id.'/PDF') }}" target="_blank" title="Comprobante Contable" class="btn-sm btn-primary"><i class="fa fa-file-pdf-o"></i></a>
                                    <a href="{{ url('administrativo/ordenPagos/pdf/'.$pago->orden_pago_id) }}" title="Orden de Pago" class="btn-sm btn-primary" target="_blank"><i class="fa fa-file-pdf-o"></i></a>
                                    @if($pago->egreso)
                                        @if($pago->egreso['estado'] == '1')
                                            <a href="{{ url('/administrativo/egresos/pdf/'.$pago->egreso['id']) }}" title="Comprobante de Egresos" class="btn-sm btn-primary" target="_blank"><i class="fa fa-file-pdf-o"></i></a>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="alert alert-danger">
                        <center>
                            No hay pagos de retención en la fuente registrados en el sistema.
                        </center>
                    </div>
                @endif
            </div>
        </div>
    </div>
@stop
@section('js')

    <script type="text/javascript" >

        $(document).ready(function(){

            $('.nav-tabs a[href="#tabTareas"]').tab('show')
        });

    </script>

    <script>

        function approve(value, num, cdps){
            if (value == true){
                for (var i = 0; i < num; i++) {
                    var id = "check"+i;
                    var input = "checkInput"+i;
                    document.getElementById(id).checked = value;
                    document.getElementById(input).value = cdps[i]['id'];
                }
            } else{
                for (var i = 0; i < num; i++) {
                    var id = "check"+i;
                    var input = "checkInput"+i;
                    document.getElementById(id).checked = value;
                    document.getElementById(input).value = null;
                }
            }
        }

        function approveUnidad(value, num, cdpId){
            var id = "checkInput"+num;
            if (value == true){
                document.getElementById(id).value = cdpId;
            } else {
                document.getElementById(id).value = null;
            }
        }

        $('#tabla_CDP').DataTable( {
            responsive: true,
            "searching": true,
            dom: 'Bfrtip',
            order: [[0, 'desc']],
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

        $('#tabla_Historico').DataTable( {
            responsive: true,
            "searching": true,
            dom: 'Bfrtip',
            order: [[0, 'desc']],
            buttons: [
                'copy', 'csv', 'excel', 'print'
            ]
        } );

        $('#tabla_Process').DataTable( {
            responsive: true,
            "searching": true,
            dom: 'Bfrtip',
            order: [[0, 'desc']],
            buttons: [
                'copy', 'csv', 'excel', 'print'
            ]
        } );
    </script>
@stop
