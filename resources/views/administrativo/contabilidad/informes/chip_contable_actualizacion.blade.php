@extends('layouts.dashboard')
@section('titulo')
    Chip Contable
@stop
@section('sidebar')@stop
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="col-md-12 align-self-center">
        <div class="breadcrumb text-center">
            <strong>
                <h4><b id="title"></b></h4>
            </strong>
        </div>
    </div>
@stop

@section('js')
    <script>
        const pucs = @json($pucs);
        $(document).ready(function() {
            let tbl =  $('#tabla').DataTable({
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
                "columnDefs": [
                    {
                        "targets": [3,6,7,9,12,13],
                        "visible": false,
                        "searchable": false
                    }
                ],
                pageLength: 2000,
                responsive: true,
                "searching": true,
                ordering: false,
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend:    'excelHtml5',
                        text:      '<i class="fa fa-file-excel-o"></i> ',
                        titleAttr: 'Chip Contable primer trimestre 2023-01-01 - 2023-03-31',
                        className: 'btn btn-primary',
                        exportOptions: {
                            columns: [0,1,3,6,7,9,12,13]
                        }
                    },
                    {
                            extend:    'pdfHtml5',
                            text:      '<i class="fa fa-file-pdf-o"></i> ',
                            titleAttr: 'Chip Contable primer trimestre 2023-01-01 - 2023-03-31',
                            exportOptions: {
                                columns: [0,1,3,6,7,9,12,13]
                            },
                            className: 'btn btn-primary',
                            customize : function(doc){ 
                                doc.content[1].table.widths = [55,190,75,75,75,75,75,75]; //120 440 7 costringe le colonne ad occupare un dato spazio per gestire il baco del 100% width che non si concretizza mai
                            }
                        },
                ]
            })

            load_data(); 
        })

        const load_data = () => {
            let contador = 0;
            $('#title').text(`El Puc ${pucs[0].code} - ${pucs[0].concepto} se esta actualizando con sus hijos`);
            pucs.forEach( async (p,i) => {
                console.log('ff', [p, i, `/administrativo/contabilidad/chip-contable/${p.id}`]);
                contador +=1;
                await fetch(`/administrativo/contabilidad/chip-contable/${p.id}`)
                .then(response => response.json())
                .then(data => {
                    if(contador == i+1){
                        window.location.href = "{{route('chip.contable')}}";
                    }else{
                        $('#title').text(`El Puc ${pucs[i+1].code} - ${pucs[i+1].concepto} se esta actualizando con sus hijos`);
                    }
                });

               
            });
        }

        const peticion_ajax = () => {
            
        }
    </script>
@stop