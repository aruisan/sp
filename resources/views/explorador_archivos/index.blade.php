@extends('layouts.dashboard')

@section('titulo')
Archivos
@endsection

@section('css')
{{--
<link rel="stylesheet" href="{{ asset('fupload/font/font-fileuploader.css') }}">
<link rel="stylesheet" href="{{ asset('fupload/jquery.fileuploader.min.css') }}"> --}}
<!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.19.1/css/mdb.min.css" rel="stylesheet"> -->
<link rel="stylesheet" href="{{ asset('fupload/font/font-fileuploader.css') }}">
<link rel="stylesheet" href="{{ asset('fupload/jquery.fileuploader.min.css') }}">
<link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css">
<style>
    
.list-group-item.active {
        /* padding: 0.75rem 0.25rem !important; */
        /* color: white */
        /* background: #2ba0bd */
        background: #2ba0bd !important;

    }

    .list-group-item.active .icon {
        /* padding: 0.75rem 0.25rem !important; */
        color: white
            /* background: #2ba0bd */
            /* background:#2ba0bd !important; */

    }

    .list-group-item:active {
        /* padding: 0.75rem 0.25rem !important; */
        color: #2ba0bd
            /* background: #2ba0bd */
            /* background:#2ba0bd !important; */

    }

    .icon:active {
        color: white
    }

    .icon {
        color: #2ba0bd;
        font-size: 25px;

    }

    .arbol_seleccionado>div:first-child {
        -webkit-box-shadow: -1px -1px 13px 0px rgba(45, 156, 240, 1);
        -moz-box-shadow: -1px -1px 13px 0px rgba(45, 156, 240, 1);
        box-shadow: -1px -1px 13px 0px rgba(45, 156, 240, 1);
    }

    .active_f {
        border: 1px solid #006d8d;
        border-radius: 6px;
    }

    .shared_i {
        color: white !important;
        background-color: black !important;
        border-radius: 10px !important;
        padding: 4px !important;
        cursor: pointer !important;
    }

    .settings-panel.open {
        right: 0;
        -webkit-box-shadow: 7px 0px 80px -9px rgba(0, 0, 0, 0.15);
        box-shadow: 7px 0px 80px -9px rgba(0, 0, 0, 0.15);
    }

    .settings-panel {
        display: block;
        position: fixed;
        top: 0;
        right: -300px;
        bottom: 0;
        width: 300px;
        height: 100vh;
        min-height: 100%;
        background: #ffffff;
        -webkit-transition-duration: 0.25s;
        transition-duration: 0.25s;
        -webkit-transition-timing-function: ease;
        transition-timing-function: ease;
        -webkit-transition-property: right, box-shadow;
        -webkit-transition-property: right, -webkit-box-shadow;
        transition-property: right, -webkit-box-shadow;
        transition-property: right, box-shadow;
        transition-property: right, box-shadow, -webkit-box-shadow;
        z-index: 9999;
    }

    #theme-settings .settings-close {
        top: 12px;
    }

    .settings-panel .settings-close {
        position: absolute;
        top: 8px;
        right: 10px;
        color: #ffffff;
        background: transparent;
        border-radius: 4px;
        padding: 0 3px;
        cursor: pointer;
        -webkit-transition-duration: 0.2s;
        transition-duration: 0.2s;
        z-index: 999;
    }

    .settings-panel .settings-heading {
        padding: 16px 0 13px 35px;
        font-size: 0.875rem;
        font-family: "Open Sans", sans-serif;
        font-weight: 700;
        line-height: 1;
        color: rgba(0, 0, 0, 0.9);
        opacity: 0.9;
        margin-bottom: 0;
        border-top: 1px solid #e4e9f0;
        border-bottom: 1px solid #e4e9f0;
    }

    .settings-panel .sidebar-bg-options {
        padding: 13px 35px;
        display: -webkit-box;
        display: -ms-flexbox;
        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;
        font-size: 0.875rem;
        line-height: 1;
        color: #595959;
        background: #ffffff;
        -webkit-transition-duration: 0.25s;
        transition-duration: 0.25s;
        -webkit-transition-property: background;
        transition-property: background;
    }
    .ocultar, .d-none{
        display: none;
    }


    .container-items {
        display: flex;
        width: 100%;

        flex-wrap: wrap;
        align-content: flex-end;
    }

    .item {
        width: 20%;
    }
</style>
@endsection
@section('migas')
@endsection

@section('content')
 @include('explorador_archivos.components.menu')

<div class="container-fluid m-0 p-0">

    @if (auth()->user()->presentacion_archivos == 'fila')
    <div class="row ">
        <div class="col-sm-10 text-right ">
            <button type="button" class="btn btn-outline-primary mt-2 mb-2 d-none" id="botonMoverMasivo">Mover</button>
        </div>
    </div>
    @endif


    <div class="row " style="align:right;">
        <div class="col-md-8" style="text-align: end">
            {{-- <button></button> --}}
            <a class="carpeta_expediente btn btn-add-form mt-2 mb-2" id="carpeta_padre"></a>
        </div>
        <div class="col-md-2"></div>
    </div>
    <div class="row m-0 p-0">
        <!--columnas-->

    @if(auth()->user()->presentacion_archivos == 'columna')
        <div class=" col-md-8 col-xs-10 col-sm-7 arch_secundario resfiles container-items" style="height: min-content !important;" id="divAgregarItemsColumna">
        </div>
    @else
        <div class=" col-md-8 col-xs-10 col-sm-7 arch_secundario resfiles" style="height: min-content !important;">
            <div class="table-responsive " style="overflow:hidden !important; cursor: pointer !important;">
                <table class="tableArchivosFila table table-bordered table-hover">
                    <tbody id="divAgregarItemsFila">
                    </tbody>
                </table>
            </div>
        </div>
    @endif

        <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="d-flex justify-content-center">
                        <div class="treeviewBulk w-80 border">
                            <ul class="mb-1 pl-3 pb-2">
                                {{-- $arbol --}}
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">

            <div id="tree">

            </div>
        </div>
    </div>
</div>

@include('explorador_archivos.components.modales')


@endsection

@section('js')
<script src="{{ asset('fupload/jquery.fileuploader.min.js') }}" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-contextmenu/2.7.1/jquery.contextMenu.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-contextmenu/2.7.1/jquery.ui.position.js"></script>
{{--<script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>--}}

{{-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.19.1/js/mdb.min.js">
</script> --}}
{{-- <script src="{{ asset('fupload/jquery.fileuploader.min.js') }}" type="text/javascript"></script> --}}
<script>
        let arbol = @json($arbol);
        let carpetas = @json($carpetas);
        let carpeta = @json($carpeta);
        let carpetas_arbol = [];
        let carpeta_arbol = null;
        let tipo_estructura = "{!!auth()->user()->presentacion_archivos!!}";
        var iid_carpeta = null;
        var url_subida = "{!! route('enviararchivo') !!}";
        var clicks = 0,
            timer = null,
            click_actual = "",
            tipo_click_actual = "",
            p_ver = 1,
            p_eliminar = 1,
            p_agregar = 1,
            p_editar = 1;
            tree = {};


        $(document).ready(function(){
            console.log('carpetas', carpetas);
            console.log('arbol', arbol);
          listarCarpetas();
          checkedFC();
          cargar_arbol();
          validar_subir_archivos();
          cargar_carpetas_arbol()
          console.log('lis-carpetas', arbol);
          $('#carpeta_padre').hide();
        })
        $('#botonMoverMasivo').on('click', function(){
            $('#modMoverElemento').modal();
        })

        const modal_crear_carpeta = () => {
            $('#mod_ncarpeta').removeClass('is-invalid').val('');
            $('#modal_cargar_files_Err').hide();
            $("#modCarpeta").modal('show');
        } 


        const mostrarBotonMoverMasivo = nodo =>{
            let inputs = document.querySelectorAll('input:checked')
            console.log('checked', inputs.length);

            if(inputs.length >= 1)
            {
                let botonMoverMasivo = document.getElementById('botonMoverMasivo')
                botonMoverMasivo.classList.remove('d-none')
            }else{
                botonMoverMasivo.classList.add('d-none')
            }
        }
        $(".btn_n_carpeta_mod").on("click", function() {
                var nom_camp = $('#mod_ncarpeta');
                var nom_camp_err = $('#modal_cargar_files_Err');
                var modal_cargar_files_Err = $('#modal_cargar_files_Err');
                modal_cargar_files_Err.hide();
                nom_camp.removeClass('is-invalid');

                if (nom_camp.val() == '') {
                    nom_camp.addClass('is-invalid');
                    nom_camp_err.html('El campo nombre es obligatorio.');
                    modal_cargar_files_Err.show();
                    return false;
                }

                $.ajax({
                    type: "post",
                    url: "{!! route('nuevacarpeta') !!}",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        nombre: nom_camp.val(),
                        carpeta: carpeta != null ? carpeta.id : 'null'
                    },
                    success: function(response) {
                        if (response == "duplicado") {
                            nom_camp.addClass('is-invalid');
                            nom_camp_err.html('Esta carpeta ya se encuentra creada.');
                            modal_cargar_files_Err.show();
                            return false;
                        }else{
                          carpetas.push(response);
                          listarCarpetas();
                        }
                        $("#modCarpeta").modal('hide');
                    },
                    error: function(response) {
                        nom_camp.addClass('is-invalid');
                        nom_camp_err.html('Ocurrio un error creando la carpeta.');
                        modal_cargar_files_Err.show();
                        return false;
                    }
                });

            });
        
        const cambiar_presentacion_items = url =>{
            window.location.href = url;    
        }


      
        const btnCargarFile = () => {
            $("#modCargar").modal();
            // console.log('ndndcncn');
            generarRegenerarInput();
        }
        $(document).on('click','.arbol_seleccionar',function(){
            $('.arbol_seleccionado').removeClass('arbol_seleccionado');
            $(this).addClass('arbol_seleccionado');
            let index = carpetas_arbol.findIndex( e => e.id == $(this).attr('data-id'));
            carpeta_arbol = carpetas_arbol[index];

            $(".arbol_seleccionado").each(function(index) {
                $('#submit_btn_mov').removeClass('disabled');
            });
        });

        // $(".cargar_files").on("click", function() {
        //     $("#modCargar").modal('show');
        //     // console.log('ndndcncn');
        //     generarRegenerarInput();
        // });

        

        const cargar_carpetas_arbol = () => {
            tree = $('#tree').tree({
                uiLibrary: 'bootstrap',
                dataSource: arbol,
                icons: {
                    expand: '<i class="fa fa-folder  icon"  aria-hidden="true"></i>',
                    collapse: '<i class="fas fa-folder-open icon"></i>',
                    empty:'<i class="fas fa-house icon"></i>'
                },
                
                primaryKey: 'id',
                imageUrlField: 'flagUrl',
                silent : false,
                select: function (e, node, str_id) {
                    array_id = str_id.split('--');
                    cargar_subcarpetas(array_id[0], array_id[1]);
                    console.log('nivel', arbol);
                    
                }
            });
            console.log('var_tree', tree)
            
        }
       
        const migas = () =>{
          $('#migas_carpetas').html(`
              <li class="breadcrumb-item"><a href=""><i class="mdi mdi-home"></i></a></li>
              <li class="breadcrumb-item"><a href="">Archivos</a></li>
              <li class="breadcrumb-item active">{{auth()->user()->dependencia->first()->nombre}}</li>
            `);
        if(carpeta != null){
          carpeta.migas.forEach((e, i) => {
              let li = carpeta.migas.length != i+1
                  ?`<li class="breadcrumb-item"><a href="#" onclick="cargar_subcarpetas(${e.id})">${e.carpeta}</a></li>`
                  :`<li class="breadcrumb-item active">${e.carpeta}</li>`;
              $('#migas_carpetas').append(li);
            });
          }
        }

        const cargar_arbol = async () => {
          $('#div_arbol').empty()
          carpetas_arbol = await fetch("{{route('arbol')}}", {
              method:'POST',
              body: JSON.stringify({
                "_token": "{{ csrf_token() }}"
              }),
              headers:{
                'Content-Type': 'application/json'
              }
            }).then(res => res.json())
            .then(res => res)
            carpetas_arbol.forEach(c => {
              if(c.id != click_actual){
                let element =   `<div class="item carpeta arbol_seleccionar  ${c.permisos.ver ? "" : "d-none"}" data-id="${c.id}" title="${c.nombre}" ondblclick="cargar_arbol(${c.id})">
                            <div class="mb-0 mx-auto"  style="width:100px; height:100px;">
                              <img class="img-responsive imgCarpeta carpeta_in" id="imagen_${c.id}"  padre="${c.padre}" ruta_personalizar_imagen_carpeta="${c.ruta_imagen_personalizada}" dp-ver="${c.permisos.ver}" 
                              dp-agregar="${c.permisos.guardar}" dp-editar="${c.permisos.editar}" dp-eliminar="${c.permisos.eliminar}" data-id="${c.id}" data-nombre="${c.nombre}" data-contador="" 
                              src="{{asset('img/iconos/folderyellow.svg')}}" width="100"  style="cursor: pointer !important;">
                            </div>
                            <div class="" style="width: 100% !important;">
                              <small class="text-movil"style="text-align: justify !important;font-size:12px !important;">
                                <b>${c.nombre.substr(0,22)} ${c.nombre.length > 22 ? "..." : ""}</b>
                              </small>
                            </div>
                         </div>`;
                $('#div_arbol').append(element);

                `<div class="item carpeta ${c.permisos.ver ? '' : 'd-none'}" title="${c.nombre}" ondblclick="cargar_subcarpetas(${c.id})">
                            <div  style="width:100px; height:100px; overflow:hidden; ">
                              <img class="img-responsive imgCarpeta carpeta_in" id="imagen_${c.id}"  padre="${c.padre}" ruta_personalizar_imagen_carpeta="${c.ruta_imagen_personalizada}" 
                              dp-ver="${c.permisos.ver}" dp-agregar="${c.permisos.guardar}" dp-editar="${c.permisos.editar}" dp-eliminar="${c.permisos.eliminar}" data-id="${c.id}" 
                              data-nombre="${c.nombre}" data-contador="" src="${c.imagen_personalizada}" width="100" style="cursor: pointer !important;">
                            </div>
                            <div class="" style="width: 100% !important;">
                              <small class="text-movil text-center"style="text-align: justify !important;font-size:12px !important;">
                                <b>${c.nombre.substr(0,22)} ${c.nombre.length > 22 ? "..." : ""}</b>
                              </small>
                            </div>
                         </div>`;
              }
            })
        }

        const checkedFC = () => {
          let estado = tipo_estructura == 'fila' ? true :false;
          $('#changeFilaColumna').attr('checked', estado);
        }

        const listarCarpetas = () =>{
          console.log(carpetas);
          let carpetas_filas = "";
          $('#divAgregarItemsFila, #divAgregarItemsColumna').empty();
          if(carpetas.length > 0){
            if(tipo_estructura == 'fila'){
              carpetas.map(c => {
                let element = `
                                <tr class="${c.permisos.ver ? "" : "d-none"}" title="${c.nombre}" ondblclick="cargar_subcarpetas(${c.id})">
                                  <td style="padding-top:15px !important;" width="20px">
                                    <input type="checkbox" name="mover_carpetas[]" value="${c.id}" onclick="mostrarBotonMoverMasivo(this)" >
                                  </td>
                                  <td width="50px">
                                    <img class="img-responsive carpeta_in" id="imagen_${c.id}" padre="${c.padre}" ruta_personalizar_imagen_carpeta="${c.ruta_imagen_personalizada}" dp-ver="${c.permisos.ver}" 
                                            dp-agregar="${c.permisos.guardar}" dp-editar="${c.permisos.editar}" dp-eliminar="${c.permisos.eliminar}" data-id="${c.id}" data-nombre="${c.nombre}" data-contador="" 
                                            src="${c.imagen_personalizada}" style="cursor: pointer !important;">
                                    </td>
                                  <td style="padding-top:15px !important; " ><b>${c.nombre.substr(0,22)} ${c.nombre.length > 22 ? "..." : ""}</b></td>
                                </tr>`;
                $('#divAgregarItemsFila').append(element);
              })
            }else{
              carpetas.forEach(c => {
                let element =   `<div class="item carpeta ${c.permisos.ver ? '' : 'd-none'}" title="${c.nombre}" ondblclick="cargar_subcarpetas(${c.id})">
                            <div  style="width:100px; height:100px; overflow:hidden; ">
                              <img class="img-responsive imgCarpeta carpeta_in" id="imagen_${c.id}"  padre="${c.padre}" ruta_personalizar_imagen_carpeta="${c.ruta_imagen_personalizada}" 
                              dp-ver="${c.permisos.ver}" dp-agregar="${c.permisos.guardar}" dp-editar="${c.permisos.editar}" dp-eliminar="${c.permisos.eliminar}" data-id="${c.id}" 
                              data-nombre="${c.nombre}" data-contador="" src="${c.imagen_personalizada}" width="100" style="cursor: pointer !important;">
                            </div>
                            <div class="" style="width: 100% !important;">
                              <small class="text-movil text-center"style="text-align: justify !important;font-size:12px !important;">
                                <b>${c.nombre.substr(0,22)} ${c.nombre.length > 22 ? "..." : ""}</b>
                              </small>
                            </div>
                         </div>`;
                $('#divAgregarItemsColumna').append(element);
              })
            }
          }

          //archivos
          //
          sub_archivos();
          migas();
        }

        const sub_archivos = async () => {
            let archivos = await fetch("{{route('cargar.subarchivos')}}", {
                method:'POST',
                body: JSON.stringify({
                  id: carpeta != null ? carpeta.id : 'null',
                  "_token": "{{ csrf_token() }}"
                }),
                headers:{
                  'Content-Type': 'application/json'
                }
              }).then(res => res.json())
              .then(res => res)

            if(tipo_estructura == 'fila'){
              console.log('archivos', archivos)
              archivos.map(a => {
                let element_archivo = `
                                <tr class="${a.permisos.ver ? "" : "d-none"}" ondblclick="cargar_url_archivo('${a.ruta}')">
                                  <td  style="padding-top:15px !important;" width="20px"><input type="checkbox" name="mover_archivos[]" value="${a.id}" onclick="mostrarBotonMoverMasivo(this)"></td>
                                  <td width="50px">
                                    <img class="img-responsive archivo_in" dp-ver="${a.permisos.ver}" dp-agregar="${a.permisos.guardar}" dp-editar="${a.permisos.editar}" dp-eliminar="${a.permisos.eliminar}" data-href="${a.ruta}" data-id="${a.id}" data-nombre="${a.nombre}" data-contador="" src="${a.ruta_imagen_personalizada}" style="width: 100% !important height="100%";cursor: pointer !important;"></td>
                                  <td style="padding-top:15px !important;"><b>${a.nom_real.substr(0,22)} ${a.nom_real.length > 22 ? "..." : ""}</b></td>
                                </tr>
                              `;
                $('#divAgregarItemsFila').append(element_archivo);
              })
            }else{
              archivos.forEach(a => {
                let element_archivo =   `<div class="item archivo ${a.permisos.ver ? "" : "d-none"}" title="${a.titulo} | ${a.nom_real}" ondblclick="cargar_url_archivo('${a.ruta}')">
                            <div style="width:100px; height:100px; overflow:hidden; ">
                              <img class="img-responsive archivo_in" dp-ver="${a.permisos.ver}" dp-editar="${a.permisos.editar}" dp-eliminar="${a.permisos.eliminar}"
                                data-href="${a.ruta}" data-id="${a.id}" data-nombre="${a.nombre}" src="${a.ruta_imagen_personalizada}" style="width: 90px !important; height="90px" !important; cursor: pointer !important;">
                            </div>
                            <div class="mt-0" style="width: 100% !important;word-break: break-all !important;">
                              <small style="text-align: justify !important;font-size:11px !important;">'
                                ${a.nom_real.substr(0,22)} ${a.nom_real.length > 22 ? "..." : ""}
                              </small>
                            </div>
                         </div>`;
                $('#divAgregarItemsColumna').append(element_archivo);
              })
            }
           
        }

        const changeFilaColumna = async () => {
          tipo_estructura = await fetch('{{route('configurar.columna.fila')}}')
          .then(response => response.json())
          .then(data => data);
          listarCarpetas();
        }

        const cargar_url_archivo = ruta => {
          console.log('ruta', ruta)
          window.open(ruta, '_blank');
        }

        const cargar_subcarpetas = async (id, tipo) => {
            //console.log(id)
            //let index_carpeta = carpetas.findIndex( e => e.id == id && e.tipo == tipo);
            let data = {id:id, tipo:tipo, "_token": "{{ csrf_token() }}",}
            
            //console.log('carpeta_select', carpeta);
            // $('#carpeta_padre').text(`carpeta padre: ${carpeta.nombre}`);
            let resp = await fetch(`/explorador-archivos/subcarpetas`, {
                method: 'POST',
                body: JSON.stringify(data),
                headers:{
                'Content-Type': 'application/json'
                }
            }).then(res => res.json())
            .then(res => res)
            console.log('resp', resp)
            carpeta = resp.carpeta;
            carpetas = resp.carpetas;
            
            console.log('carpeta_select', carpeta);
            if (carpeta.level == 3) {
                $('#carpeta_padre').show();
            }else{
                $('#carpeta_padre').hide();

            }
          
            validar_subir_archivos();
            listarCarpetas();
        }

        

        const validar_subir_archivos = () => {
            if(carpeta == null){
                $('.cargar_files').addClass('ocultar');
            }else{
                $('.cargar_files').removeClass('ocultar');
            }
        }

        const propiedades = id => {
          var prop_info = $(".mod_info_prop").hide();
          var prop_cont = $(".mod_info_cant").hide();
          let index_carpeta = carpetas.findIndex( e => e.id == id);
          let car = carpetas[index_carpeta];
          console.log(id)

          if (car.permisos.editar) {
              $(".btn_u_nombre_mod").show();
              $(".btn_u_ubicacion_mod").show();
          }

          if (car.permisos.eliminar) {
              $(".btn_u_eliminar_mod").show();
          }

          $('#dsNom_documento').html(car.nombre);
          $('#url_mod_doc').attr('onclick', 'cargar_subcarpetas()');

          var mrh = $('#informacion_files');
          if (!mrh.hasClass('open')) {
              mrh.addClass('open');
          }

          p_ver = car.permisos.ver;
          p_agregar = car.permisos.guardar;
          p_editar = car.permisos.editar;
          p_eliminar = car.permisos.eliminar;

          click_actual = car.id;
          tipo_click_actual = 'carpeta';
          self.parents("div.col-1.mr-0").addClass("active_f");
        }

        const propiedades_archivos = id => {
          var prop_info = $(".mod_info_prop").hide();
          var prop_cont = $(".mod_info_cant").hide();
          let index_archivo = archivos.findIndex( e => e.id == id);
          let car = archivos[index_archivo];
          console.log(id)

          if (car.permisos.editar) {
              $(".btn_u_nombre_mod").show();
              $(".btn_u_ubicacion_mod").show();
          }

          if (car.permisos.eliminar) {
              $(".btn_u_eliminar_mod").show();
          }

          $('#dsNom_documento').html(car.nombre);
          $('#url_mod_doc').attr('onclick', `cargar_url_archivo(${car.ruta})`);

            /*
                    if (self.attr('data-creador-email')) {
                        prop_info.show();
                        $('#dsEmail_documento').html(self.attr('data-creador-email'));
                    }
                    */
            /*
                    if (self.attr('data-contador')) {
                        prop_cont.show();
                        $('#dsContador').html(self.attr('data-contador'));
                    }
            */
          var mrh = $('#informacion_files');
          if (!mrh.hasClass('open')) {
              mrh.addClass('open');
          }

          p_ver = car.permisos.ver;
          p_agregar = car.permisos.guardar;
          p_editar = car.permisos.editar;
          p_eliminar = car.permisos.eliminar;

          click_actual = car.id;
          tipo_click_actual = 'archivo';
          self.parents("div.col-1.mr-0").addClass("active_f");
        }

        $(document).ready(function() {
            if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                var fagregads = $('.div_b_f_agregadas');
                fagregads.parent('div.row').prepend(fagregads.detach());
            }

            //generarListado();

            $('.shared_i').on('click', function() {
                alert("Compartir carpeta: " + $(this).attr('data-proyecto'));
            });

            $('.settings-close').on('click', function() {
                var mrh = $('#informacion_files');
                mrh.removeClass('open');
            });

        

            $(".carpeta_expediente").on("click", function() {
                $('#mod_ncarpeta').removeClass('is-invalid').val('');
                $('#modal_cargar_files_Err').hide();
                $("#modalExpediente").modal('show');
            });
        });

        function generarListado() {
            $.ajax({
                type: "post",
                url: "{!! route('getArchivos') !!}",
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                dataType: "html",
                success: function(response) {

                    $('.resfiles').html(response);
                }
            });
        }
        
        function generarRegenerarInput() {
            console.log('iid_carpeta', iid_carpeta);
            var modcarfi = $('#modal_cargar_files');
            modcarfi.html('');
            var dfecha = new Date();
            var nominp = dfecha.getFullYear() + '' + dfecha.getMonth() + '' + dfecha.getDate() + '' + dfecha.getHours() +
                '' + dfecha.getMinutes() + '' + dfecha.getSeconds() + 'inp_files';
            modcarfi.html('<input type="file" id="' + nominp +
                '" class="archivosMult" name="files[]" multiple="multiple" accept="application/pdf">');

            $('#' + nominp).fileuploader({
                limit: 1000,
                maxSize: 10000,
                theme: 'default',
                upload: {
                    url: url_subida,
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "carpeta": carpeta == null ? 'null' : carpeta.id
                    },
                    type: 'POST',
                    enctype: 'multipart/form-data',
                    start: true,
                    synchron: true,
                    onSuccess: function(result, item) {
                        var data = {};

                        item.name = item.name;
                        item.html.find('.column-title > div:first-child').text(item.name).attr('title', item
                            .name);

                        item.html.find('.fileuploader-action-remove').addClass('fileuploader-action-success');
                        setTimeout(function() {
                            item.html.find('.progress-bar2').fadeOut(400);
                        }, 400);
                    },
                    onProgress: function(data, item) {
                        var progressBar = item.html.find('.progress-bar2');

                        if (progressBar.length > 0) {
                            progressBar.show();
                            progressBar.find('span').html(data.percentage + "%");
                            progressBar.find('.fileuploader-progressbar .bar').width(data.percentage + "%");
                        }
                    },
                    onComplete: function(data, item) {
                        listarCarpetas();
                        return true;
                    },
                },
                // onRemove: function(item) {
                //     $.post('./php/ajax_remove_file.php', {
                //         file: item.name
                //     });
                // },
            });

            setTimeout(function() {
                $('.fileuploader-input-caption').children().html('Seleccione los archivos a procesar');
                $('.fileuploader-input-button').children().html('Seleccione los archivos');

                $('#' + nominp).on('change', function() {
                    var camp_noimp = $('.fileuploader-input-caption').children();
                    if (camp_noimp.html().indexOf("file was chosen") != -1) {
                        camp_noimp.html(camp_noimp.html().replace('file was chosen',
                            'archivo seleccionado'));

                    } else if (camp_noimp.html().indexOf("files were chosen") != -1) {
                        camp_noimp.html(camp_noimp.html().replace('files were chosen',
                            'archivos seleccionados'));

                    } else {
                        camp_noimp.html('Seleccione los archivos a procesar');
                    }
                });
            }, 200);
        }

        function getCountFolders() {

            function getCountFolders() {
                console.log('aja')
            }

            getCountFolders()
        }
</script>

<script>
    let  ruta_personalizar_imagen_carpeta = '';


    // Cambiar nombre
    $(".btn_u_nombre_mod").on("click", function(){
    btn_u_nombre_mod()
    });

    const btn_u_nombre_mod = () => {
    let item = data_item();
    if(!item.permisos.editar){
        alert("No cuentas con permisos para modificar este elemento");
        return false;
    }

    $('.settings-close').click();
    $('#mod_nombreupdate').val(item.nombre);
    $('#modCambiarNombre').modal('show');
    }

    const data_item = () =>{
    let items = tipo_click_actual == 'archivo' ? archivos : carpetas;
    let index = items.findIndex( e => e.id == click_actual);
    return  items[index];
    }

    $(".btn_n_nombre_mod").on("click", function(){
    var nom_camp = $('#mod_nombreupdate');

    var nom_camp_err = $('#modal_cambiarnom_Err');
        nom_camp_err.hide();
        nom_camp.removeClass('is-invalid');

    if (nom_camp.val() == '') {
        nom_camp.addClass('is-invalid');
        nom_camp_err.html('El campo nombre es obligatorio.').show();
        return false;
    }

    if (nom_camp.val() == $('#dsNom_documento').html()) {
        nom_camp.addClass('is-invalid');
        nom_camp_err.html('No se detectaron cambios en el nombre.').show();
        return false;
    }
    //console.log('tipo_click_actual', tipo_click_actual)
    $.ajax({
        type: "post",
        url: "{!! route('update_nombre') !!}",
        data: {
            "_token": "{{ csrf_token() }}",
            id: click_actual,
            tipo: tipo_click_actual,
            nombre: nom_camp.val(),
            ruta: iid_carpeta
        },
        success: function (response) {
            if (response == "duplicado") {
                nom_camp.addClass('is-invalid');
                nom_camp_err.html('Esta nombre ya se encuentra en uso.');
                nom_camp_err.show();
                return false;
                }

                if(tipo_click_actual == 'archivo'){
                let index = archivos.findIndex( e => e.id == click_actual);
                archivos[index].nombre = nom_camp.val();
                }else{
                let index = carpetas.findIndex( e => e.id == click_actual);
                carpetas[index].nombre = nom_camp.val();
                }
            listarCarpetas();
            tipo_click_actual = "";
            click_actual = "";
            clicks = 0;
            $("#modCambiarNombre").modal('hide');
        },
        error: function (response) {
            nom_camp.addClass('is-invalid');
            nom_camp_err.html('Ocurrio un error creando la carpeta.');
            nom_camp_err.show();
            return false;
        }
    });
    });
    // Cambiar nombre

    // Mover carpeta o archivo
    $(".btn_u_ubicacion_mod").on("click", function(){

    if(p_editar == 0){
        alert("No cuentas con permisos para modificar este elemento");
        return false;
    }
    //cargar_arbol(cliente, 'proyecto')
    $('.settings-close').click();
    $('#mod_movercarpeta').html($('#dsNom_documento').html());
    $('#modMoverElemento').modal('show');
    $('#submit_btn_mov').addClass('disabled');
    //  $('.treeview').mdbTreeview();
    });

    $('#submit_btn_mov').on('click',function(){
    $.ajax({
        type: "post",
        url: "{!! route('mover_elemento') !!}",
        data: {
            "_token": "{{ csrf_token() }}",
            id: click_actual,
            tipo: tipo_click_actual,
            destino: carpeta_arbol.id
        },
        success: function (response) {
            if(tipo_click_actual != 'carpeta'){
            let index = archivos.findIndex( e => e.id == click_actual);
            archivos.splice(index, 1)
            }else{
            let index = carpetas.findIndex( e => e.id == click_actual);
            carpetas.splice(index, 1)
            }

            listarCarpetas();


            $('#modMoverElemento').modal('hide');
            carpeta_arbol = null;
        },
        error: function (response) {
            nom_camp.addClass('is-invalid');
            nom_camp_err.html('Ocurrio un error moviendo el elemento.');
            nom_camp_err.show();
            return false;
        }
    });
    });

    // Mover carpeta o archivo


    /**
     *
     * mover carpetas o archivos de manera masiva
     *
    */

    $('.treeviewBulk').delegate('.btnn_mov','click',function(){
    var btn_act = $(this);
    var id_mov = btn_act.attr('data-movid');
    let inputCarpetas = document.getElementsByName("mover_carpetas[]");
    let inputArchivos = document.getElementsByName("mover_archivos[]");
    let carpetas = []
    let archivos = []

    for (const iterator of inputCarpetas) {
        if(iterator.checked)
        {
            carpetas.push(iterator.value)
        }
    }

    for (const iterator of inputArchivos) {
        if(iterator.checked)
        {
            archivos.push(iterator.value)
        }
    }

    let validarExisteDestinoEnCarpeta = carpetas.includes(id_mov);

    if(validarExisteDestinoEnCarpeta)
    {
        alert('No puede enviar carpetas y/o archivos a una carpeta seleccionada')
    }else
    {
        $.ajax({
            type: "post",
            url: "{!! route('mover_elementos_bulk') !!}",
            data: {
                "_token": "{{ csrf_token() }}",
                id: click_actual,
                tipo: tipo_click_actual,
                destino: id_mov,
                carpetas:carpetas,
                archivos:archivos,
            },
            success: function (response) {
                location.href = response;

            },
            error: function (response) {
                nom_camp.addClass('is-invalid');
                nom_camp_err.html('Ocurrio un error moviendo el elemento.');
                nom_camp_err.show();
                return false;
            }
        });
    }


    });

    /**
     *
     * fin de mover carpetas y archivos de manera masiva
     *
     *
    */





    // Eliminar archivo o carpeta
    $(".btn_u_eliminar_mod").on("click", function(){
    if(p_eliminar == 0){
        alert("No cuentas con permisos para eliminar este elemento");
        return false;
    }

    $('.settings-close').click();
    $('#mod_eliminarname').html($('#dsNom_documento').html());
    $('#modEliminarElemento').modal('show');
    });

    $(".btn_n_eliminar_mod").on("click", function(){
    btn_n_eliminar_mod();
    });

    const btn_n_eliminar_mod = () => {
    var nom_camp = $('#mod_nombreupdate');
    $.ajax({
        type: "post",
        url: "{!! route('delete_elemento') !!}",
        data: {
            "_token": "{{ csrf_token() }}",
            id: click_actual,
            tipo: tipo_click_actual
        },
        success: function (response) {
            if(tipo_click_actual == 'archivo'){
            let index = archivos.findIndex( e => e.id == click_actual);
            archivos.splice(index, 1)
            }else{
            let index = carpetas.findIndex( e => e.id == click_actual);
            carpetas.splice(index, 1)
            }
            listarCarpetas();
            tipo_click_actual = "";
            click_actual = "";
            clicks = 0;
            $("#modEliminarElemento").modal('hide');
        },
        error: function (response) {
            nom_camp.addClass('is-invalid');
            nom_camp_err.html('Ocurrio un error eliminando el elemento.');
            nom_camp_err.show();
            return false;
        }
    });
    }
    // Eliminar archivo o carpeta

    // Compartir
    $('.shared_i').on('click',function(){
    $('.settings-close').click();
    $('#listado_us_compartir_f').html('');

    $('#compfile_perm_eliminar').removeAttr('checked');
    $('#compfile_perm_ver').removeAttr('checked');
    $('#compfile_perm_editar').removeAttr('checked');

    $('#mod_compartirfile').html($('#dsNom_documento').html());


    $('#modCompartirFile').modal('show');
    });

    $('#listado_us_compartir_f').on('change',function(){
    var seleccionado = $(this).find(":selected");

    console.log(seleccionado.attr('dp-ver'));
    if (seleccionado.attr('dp-ver') == 1) {
        $('#compfile_perm_ver').attr('checked',true);
    } else {
        $('#compfile_perm_ver').removeAttr('checked');
    }

    if (seleccionado.attr('dp-editar') == 1) {
        $('#compfile_perm_editar').attr('checked',true);
    } else {
        $('#compfile_perm_editar').removeAttr('checked');
    }

    if (seleccionado.attr('dp-eliminar') == 1) {
        $('#compfile_perm_eliminar').attr('checked',true);
    } else {
        $('#compfile_perm_eliminar').removeAttr('checked');
    }
    });

    $('.btn_apl_compartir').on('click',function(){

    var usuario_comp_f = $('#listado_us_compartir_f');
    if (usuario_comp_f.val() == null || usuario_comp_f.val() == "") {
        usuario_comp_f.addClass('is-invalid');
        return false;
    }


    $('#modCompartirFile').modal('hide');
    });

    // Compartir

    // Buscador
    $('#fa_barchivos').keyup(function(e){
    if(e.keyCode == 13) {
        $('.btn_search').click();
    }
    });
    // Buscador

    // Click derecho
    $.contextMenu({
    selector: '.archivo_in',
    build: function($trigger, e) {
        var ver = $trigger.attr('dp-ver');
        var editar = $trigger.attr('dp-editar');
        var eliminar = $trigger.attr('dp-eliminar');

        var items_d = {};
        if (ver == 1) {
            Object.assign(items_d, { "ver": {name: "Ver archivo", icon: "far fa-eye"} });
        }

        if (editar == 1) {
            Object.assign(items_d, { "mover": {name: "Mover archivo", icon: "fas fa-exchange-alt"} });
            //Object.assign(items_d, { "compartir": {name: "Compartir archivo", icon: "fas fa-share-alt"} });
            Object.assign(items_d, { "renombrar": {name: "Cambiar nombre", icon: "fas fa-ticket-alt"} });
            Object.assign(items_d, { "propiedades": {name: "Propiedades", icon: "fas fa-bars"} });
        }

        if(eliminar == 1) {
            Object.assign(items_d, { "eliminar": {name: "Eliminar archivo", icon: "delete"} });
        }

        return {
            callback: function(key, opt) {
                var ver = opt.$trigger.attr('dp-ver');
                var editar = opt.$trigger.attr('dp-editar');
                var eliminar = opt.$trigger.attr('dp-eliminar');
                let id = opt.$trigger.attr('data-id')

                if ((key == "ver" && ver == 1 ) || (key == "mover" && editar == 1) || (key == "renombrar" && editar == 1) || (key == "eliminar" && eliminar == 1) || (key == "propiedades" && editar == 1)) {
                    clicks = 0;
                    click_actual = id;
                    tipo_click_actual = "archivo";
                    $(opt.$trigger[0]).click();

                    setTimeout(function(){
                        if(key == "eliminar" && eliminar == 1) {
                            btn_n_eliminar_mod();
                        } else if (key == "ir" && ver == 1) {
                            location.href = $('#url_mod_doc').attr('href');
                        } else if (key == "mover" && editar == 1) {
                            $('.btn_u_ubicacion_mod ').click();
                        } else if (key == "renombrar" && editar == 1) {
                            btn_u_nombre_mod(id, 'archivo')
                        } else if (key == "compartir" && editar == 1) {
                            $('.shared_i ').click();
                        }else if(key == "propiedades" && editar == 1){
                            propiedades_archivos(id);
                        }
                    },700);
                } else {
                    alert("No cuentas con permisos para la acción: "+key);
                }
            },
            items: items_d
        };
    }
    });

    $.contextMenu({
    selector: '.carpeta_in',
    build: function($trigger, e) {
        var ver = $trigger.attr('dp-ver');
        var editar = $trigger.attr('dp-editar');
        var eliminar = $trigger.attr('dp-eliminar');

        let padreCarpeta = document.querySelector('.carpeta_in').attributes[2].value

        console.log(typeof(padreCarpeta))

        var items_d = {};
        if (ver == 1) {
            Object.assign(items_d, { "ver": {name: "Ir a carpeta", icon: "far fa-eye"} });
        }

        if (editar == 1) {
            Object.assign(items_d, { "mover": {name: "Mover carpeta", icon: "fas fa-exchange-alt"} });
            Object.assign(items_d, { "renombrar": {name: "Cambiar nombre", icon: "fas fa-ticket-alt"} });
            Object.assign(items_d, { "propiedades": {name: "Propiedades", icon: "fas fa-bars"} });
            //  if(padreCarpeta === null || padreCarpeta === "")
            @if(auth()->user()->admin)
                Object.assign(items_d, { "personalizar": {name: "Personalizar", icon: "far fa-image"} });
            @endif
        }

        if(eliminar == 1) {
            Object.assign(items_d, { "eliminar": {name: "Eliminar carpeta", icon: "delete"} });
        }

        return {
            callback: function(key, opt) {
                var ver = opt.$trigger.attr('dp-ver');
                var editar = opt.$trigger.attr('dp-editar');
                var eliminar = opt.$trigger.attr('dp-eliminar');
                var ruta_pca =opt.$trigger.attr('ruta_personalizar_imagen_carpeta')
                let id = opt.$trigger.attr('data-id')

                if ((key == "ver" && ver == 1 ) || (key == "mover" && editar == 1) || (key == "renombrar" && editar == 1) || (key == "personalizar" && editar == 1) || (key == "propiedades" && editar == 1) || (key == "eliminar" && eliminar == 1)) {
                    clicks = 0;
                    click_actual = id;
                    tipo_click_actual = "carpeta";
                    $(opt.$trigger[0]).click();

                    setTimeout(function(){
                        if(key == "eliminar" && eliminar == 1) {
                            btn_n_eliminar_mod();
                        } else if (key == "ver" && ver == 1) {
                            cargar_subcarpetas(id);
                        } else if (key == "mover" && editar == 1) {
                            $('.btn_u_ubicacion_mod ').click();
                        } else if (key == "renombrar" && editar == 1) {
                            btn_u_nombre_mod(id, 'carpeta')
                        }else if (key == "personalizar" && editar == 1) {
                            ruta_personalizar_imagen_carpeta = ruta_pca;
                            personalizar(this);
                        }else if(key == "propiedades" && editar == 1){
                            propiedades(id);
                        }
                    },700);
                } else {
                    alert("No cuentas con permisos para la acción: "+key);
                }
            },
            items: items_d
        };
    }
    });


    // Click derecho

    const personalizar  = (input) =>{
    $('.settings-close').click();
    $('#mod_imagenPersonalizada').modal('show');
    }


    $('#btn_enviar_img_personalizada').on('click', function(){

    var nom_personalizar= $('#imagenPersonalizada');
    nom_personalizar.removeClass('is-invalid');

    var nom_pers_err = $('#modal_personalizar_Err');
    nom_pers_err.hide();
    nom_pers_err.removeClass('is-invalid');

    if (nom_personalizar.val() == '') {
        nom_personalizar.addClass('is-invalid');
        nom_pers_err.html('Debe Cargar una imagen.').show();
        return false;
    }

    let data = new FormData();
    data.append('_token', $('input[name="_token"]').val());
    data.append('foto', $('#imagenPersonalizada')[0].files[0]);



    $.ajax({
        type: "post",
        url: ruta_personalizar_imagen_carpeta,
        cache:false,
        contentType: false,
        processData: false,
        data: data,
        success: function (response) {
            $(`#imagen_${response.id}`).attr('src', response.ruta);
        },
        error: function (response) {

        }
    });

    });


    //metodo para eliminar la imagen personalizada del usuario autenticado

    const eliminarImagenPersonalizadas = document.getElementById('eliminarImagenPersonalizada');

    if(eliminarImagenPersonalizadas)
    {
    eliminarImagenPersonalizadas.addEventListener('click',(e) =>{
        eliminarImagenPersonalizada()
    })
    }

    const eliminarImagenPersonalizada = () =>
    {

        ruta_personalizar_imagen_carpeta = ruta_personalizar_imagen_carpeta.replace('personalizar','eliminar-imagen-personalizada')

        $.ajax({
            type: "post",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: ruta_personalizar_imagen_carpeta,
            cache:false,
            contentType: false,
            processData: false,

            success: function (response) {
                $(`#imagen_${response.id}`).attr('src', response.ruta);


            },
            error: function (response) {

            }
        });
    }

    

</script>

@endsection