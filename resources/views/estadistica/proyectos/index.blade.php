@extends('layouts.dashboard')
@section('titulo')
    EStadisticas de Proyectos
@stop
@section('content')
    <div class="container-fluid">
        <div class="row">
            <h3>Estadistica de Proyectos</h3>
            <div>
                <div class="dropdown col-lg-12 marginbottom10" style="">
                    <button class="btn dropdown-toggle" type="button" data-toggle="dropdown">Columnas
                        <span class="caret"></span></button>
                    <ul class="dropdown-menu" style="margin-left: 10px; padding:20px;" id="ul-li">
                    </ul>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table" id="tabla">
                </table>   
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        const rows = @json($data->data);
        const columns = rows.shift();
        const columns_data = [];
        

        $(document).ready(function(){
            columns.forEach((e,i) =>{
                $('#ul-li').append(
                    `
                    <li><input type="checkbox" data-column="${i}" checked>${e}</li>
                    `
                );
            });
            
            columns.forEach( e => {
                columns_data.push({'title':e});
            });
            
            let table =  $('#tabla').DataTable( {
                data:rows,
                columns:columns_data,
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json"
                },
                filters: false,
                dom: 'Bfrtilp',       
                buttons:[ 
                    {
                        extend:    'copyHtml5',
                        text:      '<i class="fa fa-clone"></i> ',
                        titleAttr: 'Copiar',
                        className: 'btn btn-primary',
                        exportOptions: {
                                columns: ':not(.no-print):visible'
                                , format: {
                                    body: function(data, row, column, node) {
                                        data_string = data.replace(/<[^>]+>/g, '');
                                        return data_string;
                                    }
                                    , header: function(data, column, row) {
                                        return data;
                                    }
                                    , footer: function(data, column) {
                                        return data;
                                    }
                                }
                            }
                        //   exportOptions: {
                        //	  columns: [ 0,1,2,3,4,5,6]
                        //		}
                    },
                    {
                        extend:    'excelHtml5',
                        text:      '<i class="fa fa-file-excel-o"></i> ',
                        titleAttr: 'Exportar a Excel',
                        className: 'btn btn-primary',
                        exportOptions: {
                                        columns: ':not(.no-print):visible'
                                        , format: {
                                            body: function(data, row, column, node) {
                                                data_string = data.replace(/<[^>]+>/g, '');
                                                return data_string;
                                            }
                                            , header: function(data, column, row) {
                                                return data;
                                            }
                                            , footer: function(data, column) {
                                                return data;
                                            }
                                        }
                                    }
                        
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
                        exportOptions: {
                                        columns: ':not(.no-print):visible'
                                        , format: {
                                            body: function(data, row, column, node) {
                                                data_string = data.replace(/<[^>]+>/g, '');
                                                return data_string;
                                            }
                                            , header: function(data, column, row) {
                                                return data;
                                            }
                                            , footer: function(data, column) {
                                                return data;
                                            }
                                        }
                                    }
                    },
                        
                    {
                        extend:    'print',
                        text:      '<i class="fa fa-print"></i> ',
                        titleAttr: 'Imprimir',
                        className: 'btn btn-primary',
                        exportOptions: {
                                        columns: ':not(.no-print):visible'
                                        , format: {
                                            body: function(data, row, column, node) {
                                                data_string = data.replace(/<[^>]+>/g, '');
                                                return data_string;
                                            }
                                            , header: function(data, column, row) {
                                                return data;
                                            }
                                            , footer: function(data, column) {
                                                return data;
                                            }
                                        }
                                    }
                        
                    },
                ]	             
            });

            console.log('columns', columns_data);
            console.log('rows', rows);

/*
            $('#tabla thead tr').clone(true).appendTo('#tabla thead');
            $('#tabla thead tr:eq(0) th').each(function(i) {
                var title = $(this).text();
                $(this).html('<input type="text" placeholder="Buscar ' + title + '" />');
                $('input', this).on('keyup change', function() {
                    if (table.column(i).search() !== this.value) {
                        table
                            .column(i)
                            .search(this.value)
                            .draw();
                    }
                });
            });
*/
            $('[data-column]').on('click', function(e) {

                console.log($(this).data('column'))
                var column = table.column($(this).data('column'));
                console.log(column)

                // Toggle the visibility
                column.visible(!column.visible());
                console.log(table.column());

                return true
            });

        });

        
    </script>
@stop