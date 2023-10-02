@extends('layouts.dashboard')
@section('titulo')
    Configuración Contable
@stop
@section('content')
    <div class="col-md-12 align-self-center">

        <div class="breadcrumb text-center">
            <strong>
                <h4><b>Configuración Contable</b></h4>
            </strong>
        </div>
		<ul class="nav nav-pills">
			<li class="nav-item active">
				<a class="nav-link" data-toggle="pill" href="#tabCreate">Configuración General</a>
			</li>
		</ul>
     
		<div class="tab-content" style="background-color: white">
			<div id="tabCreate" class="tab-pane active">
				<div class="table-responsive">
					<div class="col-md-12 align-self-center">
						<div class="col-md-1"></div>
						<div class="col-md-5">
							<br>
							<div class="box box-danger">
								<div class="box-header with-border">
									<center><h3 class="box-title">Configuración del Almacen</h3></center>
								</div>
								<form class="form-valide" action="{{url('/administrativo/contabilidad/config')}}" method="POST" enctype="multipart/form-data">
									{{ csrf_field() }}
									<div class="box-body">
										<div class="box-header with-border">
											<h4 class="box-title">Comprobante de Entrada</h4>
										</div>
										<div class="box-body">
											<div class="form-group">
												<label class="col-lg-4 col-form-label text-right">Cuenta<span class="text-danger">*</span></label>
												<div class="col-lg-6">
													<select class="form-control" id="entrada" name="entrada" required>
														<option>Selecciona una cuenta del PUC</option>
														@foreach($pucs as $code)
															<option value="{{$code['id']}}" @if($code['naturaleza'] == null) disabled @endif>{{$code['codigo']}} - {{$code['name']}}</option>
														@endforeach
													</select>
												</div>
											</div>
										</div>
										<hr>
										<div class="box-header with-border">
											<h4 class="box-title">Comprobante de Salida</h4>
										</div>
										<div class="box-body">
											<div class="form-group">
												<label class="col-lg-4 col-form-label text-right">Cuenta<span class="text-danger">*</span></label>
												<div class="col-lg-6">
													<select class="form-control" id="salida" name="salida" required>
														<option>Selecciona una cuenta del PUC</option>
														@foreach($pucs as $code)
															<option value="{{$code['id']}}" @if($code['naturaleza'] == null) disabled @endif>{{$code['codigo']}} - {{$code['name']}}</option>
														@endforeach
													</select>
												</div>
											</div>
										</div>
										<hr>
										<div class="box-header with-border">
											<h4 class="box-title">Comprobante de Baja</h4>
										</div>
										<div class="box-body">
											<div class="form-group">
												<label class="col-lg-4 col-form-label text-right">Cuenta<span class="text-danger">*</span></label>
												<div class="col-lg-6">
													<select class="form-control" id="baja" name="baja" required>
														<option>Selecciona una cuenta del PUC</option>
														@foreach($pucs as $code)
															<option value="{{$code['id']}}" @if($code['naturaleza'] == null) disabled @endif>{{$code['codigo']}} - {{$code['name']}}</option>
														@endforeach
													</select>
												</div>
											</div>
										</div>
									</div>
									<div class="box-footer">
										<div class="col-lg-12 ml-auto">
											<br>
											<center>
												<button type="submit" class="btn btn-primary"><i class="fa fa-cloud-upload"></i>&nbsp;&nbsp;Guardar</button>
											</center>
										</div>
									</div>
								</form>
							</div>
						</div>
						<div class="col-md-5">
							<br>
							<div class="box box-danger">
								<div class="box-header">
									<center><h3 class="box-title">Configuración</h3></center>
								</div>
								<form action="">
									<div class="box-body"></div>
									<div class="box-footer">
										<div class="col-lg-12 ml-auto">
											<br>
											<center>
												<button type="submit" class="btn btn-primary"><i class="fa fa-cloud-upload"></i>&nbsp;&nbsp;Guardar</button>
											</center>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@stop

@section('js')
    <script>
          

 $('#tabla_corrE').DataTable( {
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
	  //para usar los botones   
      "pageLength": 5,
      responsive: "true",
	  dom: 'Bfrtilp',       
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
			  message : 'SIEX',
			  header :true,
              	exportOptions: {
				  columns: [ 0,1,2,3,4]
					},
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

		 });

          $('#tabla_corrS').DataTable( {
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
	  //para usar los botones   
      "pageLength": 5,
      responsive: "true",
	  dom: 'Bfrtilp',       
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
			  message : 'SIEX',
			  header :true,
              	exportOptions: {
				  columns: [ 0,1,2,3,4]
					},
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

		 });
      

               $('#tabla_PA').DataTable( {
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
	  //para usar los botones   
      "pageLength": 5,
      responsive: "true",
	  dom: 'Bfrtilp',       
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
			  message : 'SIEX',
			  header :true,
              	exportOptions: {
				  columns: [ 0,1,2,3,4]
					},
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

		 });

        $(document).ready(function() {
            var table = $('#tabla_corrE').DataTable();
            var table = $('#tabla_corrS').DataTable();
            var table = $('#tabla_PA').DataTable();
        });
        
    </script>
@stop