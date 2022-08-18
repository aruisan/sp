@extends('layouts.dashboard')
@section('titulo') CUIPO @stop
@section('content')
	@if($paso == "1")
		@include('modal.cuipo.cpc')
	@elseif($paso == "2")
		@include('modal.cuipo.terceros')
		@include('modal.cuipo.sourcefundings')
		@include('modal.cuipo.tiponormas')
		@include('modal.cuipo.politicapublica')
	@elseif($paso == "3")
		@include('modal.cuipo.budgetsections')
		@include('modal.cuipo.vigenciagastos')
		@include('modal.cuipo.sectors')
	@endif
    <div class="col-md-12 align-self-center" id="crud">
		<div class="row justify-content-center">
			<div class="breadcrumb text-center">
				<strong>
					<h2>  Asignación del CUIPO a los Rubros para la Vigencia {{ $vigencia->vigencia }}</h2>
				</strong>
    </div>

 	<ul class="nav nav-pills">
		@if($paso == "1")
			<li class="nav-item regresar"> <a href="{{ url('/presupuesto/rubro/create',$vigencia->id) }}" class="nav-link"><i class="fa fa-arrow-left"></i><span class="hide-menu">&nbsp; Rubros</span></a></li>
		@elseif($paso == "2")
			<li class="nav-item regresar"> <a href="{{ url('/presupuesto/rubro/CUIPO/1',$vigencia->id) }}" class="nav-link"><i class="fa fa-arrow-left"></i>&nbsp; Anterior</a></li>
		@elseif($paso == "3")
			<li class="nav-item regresar"> <a href="{{ url('/presupuesto/rubro/CUIPO/2',$vigencia->id) }}" class="nav-link"><i class="fa fa-arrow-left"></i>&nbsp; Anterior</a></li>
		@endif
		<li class="nav-item active"> <a href="#crear" class="nav-link">CUIPO</a></li>
		@if($paso == "1")
			<li class="nav-item regresar"> <a href="{{ url('/presupuesto/rubro/CUIPO/2',$vigencia->id) }}" class="nav-link"><i class="fa fa-arrow-right"></i>&nbsp; Siguiente</a></li>
		@elseif($paso == "2")
			<li class="nav-item regresar"> <a href="{{ url('/presupuesto/rubro/CUIPO/3',$vigencia->id) }}" class="nav-link"><i class="fa fa-arrow-right"></i>&nbsp; Siguiente</a></li>
		@elseif($paso == "3")
			{{-- <li class="nav-item regresar"> <a href="{{ url('/presupuesto/rubro/CUIPO/4',$vigencia->id) }}" class="nav-link"><i class="fa fa-arrow-right"></i>&nbsp; Siguiente</a></li> --}}
		@endif
	</ul>
		<input type="hidden" id="vigencia_id" name="vigencia_id" value="{{  $vigencia->id }}">
		<div class="table-responsive" id="crud">   <br>
			<table class="table table-bordered" id="tabla">
				<thead>
				<th class="text-center">Codigo</th>
				<th class="text-center">Nombre</th>
				@if($paso == "1")
					<th class="text-center">CPC</th>
				@elseif($paso == "2")
					<th class="text-center">Fuentes de Financiación</th>
					<th class="text-center">Tercero</th>
					<th class="text-center">Politica Pública</th>
				@elseif($paso == "3")
					<th class="text-center">Secciones Presupuestales</th>
					<th class="text-center">Vigencia Gasto</th>
					<th class="text-center">Sector</th>
					<th class="text-center">Situación de Fondos</th>
					<th class="text-center">Secciones Presupuestales Adición</th>
					<th class="text-center">Detalle Sectorial</th>
					<th class="text-center">Tipo de Norma</th>
				@endif
				</thead>
				<tbody>
				@foreach($rubros as $data)
					<tr>
						<td>{{$data['cod']}}</td>
						<td>{{$data['name']}}</td>
						@if($paso == "1")
							<td class="text-center">
								@if(count($data['cpcs']) > 0)
									<button onclick="getModalCPC({{$data}}, {{$CPCs}})" class="btn btn-success">OK!</button>
								@else
									<button onclick="getModalCPC({{$data}}, {{$CPCs}})" class="btn btn-danger">Pendiente!</button>
								@endif
							</td>
						@elseif($paso == "2")
							<td class="text-center">
								@if(count($data['fontsRubro']) > 0)
									<button onclick="getModalSourceFundings({{$data}}, {{$fuentes}})" class="btn btn-success">OK!</button>
								@else
									<button onclick="getModalSourceFundings({{$data}}, {{$fuentes}})" class="btn btn-danger">Pendiente!</button>
								@endif
							</td>
							<td class="text-center">
								@if($data['terceros_id'] != null)
									<button onclick="getModalTercero({{$data}}, {{$terceros}})" class="btn btn-success">OK!</button>
								@else
									<button onclick="getModalTercero({{$data}}, {{$terceros}})" class="btn btn-danger">Pendiente!</button>
								@endif
							</td>
							<td class="text-center">
								@if($data['public_politics_id'] != null)
									<button onclick="getModalPoliticaPublica({{$data}}, {{$publicPolitics}})" class="btn btn-success">OK!</button>
								@else
									<button onclick="getModalPoliticaPublica({{$data}}, {{$publicPolitics}})" class="btn btn-danger">Pendiente!</button>
								@endif
							</td>
						@elseif($paso == "3")
							<td class="text-center">
								@if($data['budget_sections_id'] != null)
									<button onclick="getModalBudgetSection({{$data}}, {{$budgetSections}})" class="btn btn-success">OK!</button>
								@else
									<button onclick="getModalBudgetSection({{$data}}, {{$budgetSections}})" class="btn btn-danger">Pendiente!</button>
								@endif
							</td>
							<td class="text-center">
								@if($data['vigencia_gastos_id'] != null)
									<button onclick="getModalVigenciaGastos({{$data}}, {{$vigenciaGastos}})" class="btn btn-success">OK!</button>
								@else
									<button onclick="getModalVigenciaGastos({{$data}}, {{$vigenciaGastos}})" class="btn btn-danger">Pendiente!</button>
								@endif
							</td>
							<td class="text-center">
								@if($data['sectors_id'] != null)
									<button onclick="getModalSectors({{$data}}, {{$sectors}})" class="btn btn-success">OK!</button>
								@else
									<button onclick="getModalSectors({{$data}}, {{$sectors}})" class="btn btn-danger">Pendiente!</button>
								@endif
							</td>
							<td class="text-center">
								@if($data['fund_situations_id'] != null)
									<button class="btn btn-success">OK!</button>
								@else
									<button class="btn btn-danger">Pendiente!</button>
								@endif
							</td>
							<td class="text-center">
								@if($data['additional_budget_sections_id'] != null)
									<button class="btn btn-success">OK!</button>
								@else
									<button class="btn btn-danger">Pendiente!</button>
								@endif
							</td>
							<td class="text-center">
								@if($data['sector_details_id '] != null)
									<button class="btn btn-success">OK!</button>
								@else
									<button class="btn btn-danger">Pendiente!</button>
								@endif
							</td>
							<td class="text-center">
								@if($data['source_fundings_id'] != null)
									<button class="btn btn-success">OK!</button>
								@else
									<button class="btn btn-danger">Pendiente!</button>
								@endif
							</td>
						@endif
					</tr>
				@endforeach
				</tbody>
			</table>
		</div>
			@if($paso == "1")
				<center><a href="{{ url('/presupuesto/rubro/CUIPO/2',$vigencia->id) }}" class="btn btn-primary"><i class="fa fa-arrow-right"></i>&nbsp; Siguiente</a></center>
				<br>
			@elseif($paso == "2")
				<center><a href="{{ url('/presupuesto/rubro/CUIPO/3',$vigencia->id) }}" class="btn btn-primary"><i class="fa fa-arrow-right"></i>&nbsp; Siguiente</a></center>
				<br>
			@elseif($paso == "3")
				<center><a href="{{ url('/presupuesto/rubro/CUIPO/4',$vigencia->id) }}" class="btn btn-primary"><i class="fa fa-arrow-right"></i>&nbsp; Siguiente</a></center>
				<br>
			@endif
		</div>
 </div>
@stop

@section('js')
<script>

	function getModalCPC(rubro, CPCs){
		$('#rubroID').val(rubro['id']);
		$('#vigencia_id').val(rubro['vigencia_id']);
		document.getElementById("nameRubro").innerHTML = rubro['name'];

		if(rubro['cpcs'].length > 0){

			document.getElementById("select_CPC").style.display = "none";
			document.getElementById("buttonSaveCPCs").style.display = "none";
			document.getElementById("selectedCPC").innerHTML = "" +
					"<p>CPCs seleccionados actualmente</p>"+
					"<table class='table table-bordered'><thead><th class='text-center'>Código</th><th class='text-center'>Clase o Subclase</th><th class='text-center'>Eliminar CPC</th> " +
					"</thead><tbody id='bodyCPC'></tbody></table>";

			$("#bodyCPC").html("");
			for(var i=0; i<rubro['cpcs'].length; i++){
				var tr = `<tr>
          <td>`+ CPCs[rubro['cpcs'][i]['cpc_id']]['code']+`</td>
          <td>`+ CPCs[rubro['cpcs'][i]['cpc_id']]['class']+`</td>
          <td><a href="/presupuesto/rubro/CUIPO/CPC/` +rubro['cpcs'][i]['id'] +`/`+ rubro['vigencia_id']+`/DELETE" type="button" class="btn-sm btn-danger" ><i class="fa fa-trash"></i></a></td>
        </tr>`;
				$("#bodyCPC").append(tr)
			}

			if(rubro['cpcs'].length > 1){
				var deleteallCPCs = `<tr><td colspan="3"><a href="/presupuesto/rubro/CUIPO/CPC/` +rubro['id'] +`/`+ rubro['vigencia_id']+`/DELETEALL" type="button" class="btn-sm btn-danger" ><i class="fa fa-trash"></i> BORRAR TODOS LOS CPCS ASIGNADOS AL RUBRO</a> </td></tr>`;
				$("#bodyCPC").append(deleteallCPCs)
			}

		} else {
			document.getElementById("selectedCPC").innerHTML = "";
			document.getElementById("select_CPC").style.display = "";
			document.getElementById("buttonSaveCPCs").style.display = "";
		}

		$('#formCPC').modal('show');
	}

	function getModalSourceFundings(rubro, Fuentes){
		$('#rubroID').val(rubro['id']);
		$('#vigencia_id').val(rubro['vigencia_id']);
		document.getElementById("nameRubro").innerHTML = rubro['name'];

		if(rubro['fonts_rubro'].length > 0){
			document.getElementById("select_SF").style.display = "none";
			document.getElementById("buttonSaveSF").style.display = "none";
			document.getElementById("selectedSF").innerHTML = "" +
					"<p>Fuentes de financiación seleccionadas actualmente</p>"+
					"<table class='table table-bordered'><thead><th class='text-center'>Código</th><th class='text-center'>Descripción</th><th class='text-center'>Eliminar Fuente</th> " +
					"</thead><tbody id='bodySF'></tbody></table>";

			$("#bodySF").html("");
			for(var i=0; i<rubro['fonts_rubro'].length; i++){
				var tr = `<tr>
          <td>`+ Fuentes[rubro['fonts_rubro'][i]['source_fundings_id']]['code']+`</td>
          <td>`+ Fuentes[rubro['fonts_rubro'][i]['source_fundings_id']]['description']+`</td>
          <td><a href="/presupuesto/rubro/CUIPO/SourceFundings/` +rubro['fonts_rubro'][i]['id'] +`/`+ rubro['vigencia_id']+`/DELETE" type="button" class="btn-sm btn-danger" ><i class="fa fa-trash"></i></a></td>
        </tr>`;
				$("#bodySF").append(tr)
			}

			if(rubro['fonts_rubro'].length > 1){
				var deleteallSF = `<tr><td colspan="3"><a href="/presupuesto/rubro/CUIPO/SourceFundings/` +rubro['id'] +`/`+ rubro['vigencia_id']+`/DELETEALL" type="button" class="btn-sm btn-danger" ><i class="fa fa-trash"></i> BORRAR TODAS LAS FUENTES DE FINANCIACIÓN ASIGNADAS AL RUBRO</a> </td></tr>`;
				$("#bodySF").append(deleteallSF)
			}

		} else {
			document.getElementById("selectedSF").innerHTML = "";
			document.getElementById("select_SF").style.display = "";
			document.getElementById("buttonSaveSF").style.display = "";
		}

		$('#formSF').modal('show');
	}

	function getModalTipoNorma(rubro, tipoNormas){
		$('#rubroIDTN').val(rubro['id']);
		$('#vigencia_idTN').val(rubro['vigencia_id']);
		document.getElementById("nameRubroTN").innerHTML = rubro['name'];

		if(rubro['tipo_normas_id'] != null){
			var idFind = rubro['tipo_normas_id'] - 1;
			document.getElementById("selectedTN").innerHTML = "" +
					"<p>Tipo de Norma seleccionada actualmente</p>"+
					"<table class='table table-bordered'><thead><th class='text-center'>Código</th><th class='text-center'>Descripción</th> " +
					"</thead><tbody><tr><td>"+ tipoNormas[idFind]['code'] +"</td><td>"+ tipoNormas[idFind]['description'] +"</td></tr></tbody></table>"+
					"<p>Cambiar Tipo de Norma:</p>";
		} else document.getElementById("selectedTN").innerHTML = "";

		$('#formTipoNormas').modal('show');
	}

	function getModalTercero(rubro, terceros){
		$('#rubroIDT').val(rubro['id']);
		$('#vigencia_idT').val(rubro['vigencia_id']);
		document.getElementById("nameRubroT").innerHTML = rubro['name'];

		if(rubro['terceros_id'] != null){
			var idFind = rubro['terceros_id'] - 1;
			document.getElementById("selectedT").innerHTML = "" +
					"<p>Tercero seleccionado actualmente</p>"+
					"<table class='table table-bordered'><thead><th class='text-center'>Código</th><th class='text-center'>Entidad</th> " +
					"</thead><tbody><tr><td>"+ terceros[idFind]['code'] +"</td><td>"+ terceros[idFind]['entity'] +"</td></tr></tbody></table>"+
					"<p>Cambiar Tercero:</p>";
		} else document.getElementById("selectedT").innerHTML = "";

		$('#formTerceros').modal('show');
	}

	function getModalPoliticaPublica(rubro, publicPolitics){
		$('#rubroIDPP').val(rubro['id']);
		$('#vigencia_idPP').val(rubro['vigencia_id']);
		document.getElementById("nameRubroPP").innerHTML = rubro['name'];

		if(rubro['public_politics_id'] != null){
			var idFind = rubro['public_politics_id'] - 1;
			document.getElementById("selectedPP").innerHTML = "" +
					"<p>Politica pública seleccionada actualmente</p>"+
					"<table class='table table-bordered'><thead><th class='text-center'>Código</th><th class='text-center'>Descripción</th> " +
					"</thead><tbody><tr><td>"+ publicPolitics[idFind]['code'] +"</td><td>"+ publicPolitics[idFind]['description'] +"</td></tr></tbody></table>"+
					"<p>Cambiar Politica Pública:</p>";
		} else document.getElementById("selectedPP").innerHTML = "";

		$('#formPoliticaPublica').modal('show');
	}

	function getModalBudgetSection(rubro, budgetSections){
		$('#rubroIDBS').val(rubro['id']);
		$('#vigencia_idBS').val(rubro['vigencia_id']);
		document.getElementById("nameRubroBS").innerHTML = rubro['name'];

		if(rubro['budget_sections_id'] != null){
			var idFind = rubro['budget_sections_id'] - 1;
			document.getElementById("selectedBS").innerHTML = "" +
					"<p>Sección presupuestal seleccionada actualmente</p>"+
					"<table class='table table-bordered'><thead><th class='text-center'>Código</th><th class='text-center'>Descripción</th> " +
					"</thead><tbody><tr><td>"+ budgetSections[idFind]['code'] +"</td><td>"+ budgetSections[idFind]['description'] +"</td></tr></tbody></table>"+
					"<p>Cambiar Sección presupuestal:</p>";
		} else document.getElementById("selectedBS").innerHTML = "";

		$('#formBudgetSections').modal('show');
	}

	function getModalVigenciaGastos(rubro, vigenciaGastos){
		$('#rubroIDVG').val(rubro['id']);
		$('#vigencia_idVG').val(rubro['vigencia_id']);
		document.getElementById("nameRubroVG").innerHTML = rubro['name'];

		if(rubro['vigencia_gastos_id'] != null){
			var idFind = rubro['vigencia_gastos_id'] - 1;
			document.getElementById("selectedVG").innerHTML = "" +
					"<p>Vigencia Gasto seleccionada actualmente</p>"+
					"<table class='table table-bordered'><thead><th class='text-center'>Código</th><th class='text-center'>Descripción</th> " +
					"</thead><tbody><tr><td>"+ vigenciaGastos[idFind]['code'] +"</td><td>"+ vigenciaGastos[idFind]['description'] +"</td></tr></tbody></table>"+
					"<p>Cambiar Vigencia Gasto:</p>";
		} else document.getElementById("selectedVG").innerHTML = "";

		$('#formVigenciaGastos').modal('show');
	}

	function getModalSectors(rubro, sectors){
		$('#rubroIDSec').val(rubro['id']);
		$('#vigencia_idSec').val(rubro['vigencia_id']);
		document.getElementById("nameRubroSec").innerHTML = rubro['name'];

		if(rubro['sectors_id'] != null){
			var idFind = rubro['sectors_id'] - 1;
			document.getElementById("selectedSec").innerHTML = "" +
					"<p>Sector seleccionado actualmente</p>"+
					"<table class='table table-bordered'><thead><th class='text-center'>Código</th><th class='text-center'>Descripción</th> " +
					"</thead><tbody><tr><td>"+ sectors[idFind]['code'] +"</td><td>"+ sectors[idFind]['description'] +"</td></tr></tbody></table>"+
					"<p>Cambiar Sector:</p>";
		} else document.getElementById("selectedSec").innerHTML = "";

		$('#formSectors').modal('show');
	}

$(document).ready(function() {
	$('.select-cpc').select2({
		theme: "classic"
	});
	$('.select-sf').select2({
		theme: "classic"
	});
	$('.select-tercero').select2();
	$('.select-politica-publica').select2();
	$('.select-budget-section').select2();
	$('.select-sectors').select2();

		$('.select-cpc').on('select2:opening select2:closing', function( event ) {
			var $searchfield = $(this).parent().find('.select2-search__field');
			$searchfield.prop('enabled', true);
		});
$('#tabla').DataTable( {
	responsive: true,
	"searching": false,
	paging: false,
	"oLanguage": {"sZeroRecords": "", "sEmptyTable": ""
	}
} );
} );

//funcion para borrar una celda
$(document).on('click', '.borrar', function (event) {
event.preventDefault();
$(this).closest('tr').remove();
});

new Vue({
el: '#crud',

methods: {

	eliminarV: function (dato, vigencia) {
		var urlVigencia = '/presupuesto/rubro/CUIPO' + dato + '/' + vigencia;
		axios.delete(urlVigencia).then(response => {
			toastr.error('CPC eliminado del rubro correctamente');
			document.location.reload(true);
		});
	}
}});
</script>
@stop