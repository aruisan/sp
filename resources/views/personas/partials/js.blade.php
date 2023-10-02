<script>
    $(document).ready(function(){
        porcentaje_regimen();
    });

    $('#regimen').on('click', function(){
        porcentaje_regimen();
    })

    $('#tipo_persona').on('change', function(){
        porcentaje_regimen();
    })

    const porcentaje_regimen = () => {
        let regimen = $('#regimen').val();
        let tipo_persona = $('#tipo_persona').val();
        let porcentaje = $('#input_porcentaje').val();
        if(regimen == "Responsable Impuesto Renta"){
            $('#regimen_porcentaje').show()
            $('#input_porcentaje').prop('disabled', tipo_persona == 'JURIDICA' ? true : false).val(tipo_persona == 'JURIDICA' ? 11 : porcentaje);
        }else{
            $('#regimen_porcentaje').hide();
        }
    }
</script>
