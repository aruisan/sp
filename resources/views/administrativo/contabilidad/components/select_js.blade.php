<script>
    const estructura_periodos = @json(\App\Helpers\FechaHelper::estructura_periodos());
    const periodos = @json(\App\Helpers\FechaHelper::periodos());
    const age = {{$age}};
    const elemento = parseInt({{$elemento}});
    const tipo = "{{$tipo}}";
    let count = 0;

    $(document).ready(function(){
        iniciar_selects();
        //titulo();
        cambiar_elementos();
    });

    const iniciar_selects = () => {
        let options_periodo = "";
        let options_year = "";
        estructura_periodos['anual'].forEach(y => {
            options_year+=`<option value="${y}">${y}</option>`;
        });

        periodos.forEach(p => {
            options_periodo+=`<option value="${p}">${p}</option>`;
        });

        $('#year').append(options_year).val(age);
        $('#periodo').append(options_periodo).val(tipo);
    }

    const cambiar_elementos = () => {
        let y = $('#year').val();
        let p = $('#periodo').val();
        let options = `<option>Selecciona una opción</option>`;
        $('#elementos').empty();
        if(p != 'anual'){
            estructura_periodos[p][y].forEach((pe, i) => {
                let e = p == 'mensual' ? pe : `${p} ${pe+1}`
                let index = p == 'mensual' ? i+1 : i;
                options +=`<option value="${index}">${e}</option>`;
            })
            $('#elementos').append(options);
            if(!count && elemento >= 0){
                console.log([elemento, count])
                $('#elementos').val(elemento);
            }
            count+=1;
            $('#div_select').show();
        }else{
            $('#div_select').hide();
        }
    }
    
</script>