  function openModalRelacionarParticipantes(inputId = '', inputIdentidad = '', inputNombre = '', inputTipo = '',){
    inputIdRelacionarParticipante = inputId;
    inputIdentidadRelacionarParticipante = inputIdentidad;
    inputNombreRelacionarParticipante = inputNombre;
    inputTipoRelacionarParticipante = inputTipo;
    $('#modalRelacionarParticipante').modal('show');
  } 

  $('#identificadorRelacionParticipantes').change(function(event){
      $.get("/persona-find/"+event.target.value+"", function(response){
          console.log(response);
          if(response != '')
          {
            $('#Nombre').val(response.nombre);
            $('#Email').val(response.email);
            $('#Tipo').empty()
              if(response.tipo == "NATURAL")
              {
                $('#Tipo').append("<option selected value='"+response.tipo+"'>"+response.tipo+"</option><option value='JURIDICA'>JURIDICA</option>");
              }else if(response.tipo == "JURIDICA")
              {
                $('#Tipo').append("<option selected value='"+response.tipo+"'>"+response.tipo+"</option><option value='NATURAL'>NATURAL</option>");
              }else{
                $('#Tipo').append("</option><option selected value='NATURAL'>NATURAL</option></option><option value='JURIDICA'>JURIDICA</option>");
              }
            $('#Direccion').val(response.direccion);
            $('#Telefono').val(response.telefono);
        }
      });
   });   

  async function submitRelacionarParticipante() {
    let response = await $.ajax({
      type: "POST",
        url: "/persona/find-create",
        data: $("#formRelacionarParticipantes").serialize(), 
        success: function (response) {
            return response;
        }
      });
    
      $('#'+inputIdRelacionarParticipante).val(response.id);
      $('#'+inputIdentidadRelacionarParticipante).val(response.num_dc);
      $('#'+inputNombreRelacionarParticipante).val(response.nombre);
      $('#'+inputTipoRelacionarParticipante).val(response.tipo);

      inputIdRelacionarParticipante = '';
      inputIdentidadRelacionarParticipante = '';
      inputNombreRelacionarParticipante = '';
      inputTipoRelacionarParticipante = '';
      $('#modalRelacionarParticipante').modal('hide');
}


$(document).ready(function(){
   $('#modalRelacionarParticipante').on('hidden.bs.modal', function (e) { 
       $("body").addClass("modal-open") 
    }) 
});
