<div id="informacion_files" class="settings-panel d-flex flex-column pr-2 pl-2">
    <i class="settings-close mdi mdi-close btn-vido "></i>
    <p class="settings-heading">INFORMACIÓN</p>
    <div class="sidebar-bg-options pl-1" id="sidebar-default-theme">
        <strong class="mb-1">Nombre</strong> <a href="" target="_blank" id="url_mod_doc" title="Ir"><i class="mdi mdi-link"></i></a>
        <br>
        <br>
        <span id="dsNom_documento"></span>
        <br>
        <br>
    </div>
    <div class="sidebar-bg-options pl-1 mod_info_cant" id="sidebar-default-theme">
        <strong class="mb-1">Cantidad de documentos</strong>
        <br>
        <br>
        <span id="dsContador"></span>
    </div>

    <hr class="w-50 mod_info_cant">
    <button class="btn btn-primary btn-vido btn_u_nombre_mod">Cambiar nombre</button>
    <br>
    <button class="btn btn-secondary btn-vido btn_u_ubicacion_mod">Mover</button>
    <br>
    <button class="btn btn-danger btn_u_eliminar_mod">Eliminar</button>
    <hr class="w-50 mod_info_prop">

    <div class="sidebar-bg-options pl-1 mod_info_prop" id="sidebar-dark-theme">
        <strong class="mb-1">Propietario</strong>
        <br>
        <br>
        <span id="dsEmail_documento"></span>
    </div>
    <div class="propietario_mod d-none">
        <p class="settings-heading mt-2">Usuarios   <i class="mdi mdi-share-variant shared_i"></i></p>
        <div class="sidebar-bg-options pl-1 pr-0 w-100" id="sidebar-default-theme">
            <ul class="list-group list-group-flush w-100" id="u_permisos_asignados_arch"></ul>
        </div>
    </div>
</div>

<div class="modal fade" id="modCompartirFile" tabindex="-1" role="dialog" aria-labelledby="modCompartirFile" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modCompartirFile">Compartir <strong><span name="mod_compartirfile" id="mod_compartirfile"></span></strong></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="mdi mdi-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="listado_us_compartir_f">Usuario</label>
                    <select class="form-control" name="listado_us_compartir_f" id="listado_us_compartir_f"></select>
                </div>

                <div class="row">
                    <div class="form-group col">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="compfile_perm_ver">
                            <label class="custom-control-label" for="compfile_perm_ver">Ver</label>
                        </div>
                    </div>
                    <div class="form-group col">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="compfile_perm_editar">
                            <label class="custom-control-label" for="compfile_perm_editar">Editar</label>
                        </div>
                    </div>
                    <div class="form-group col">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="compfile_perm_eliminar">
                            <label class="custom-control-label" for="compfile_perm_eliminar">Eliminar</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-vido btn_apl_compartir">Aplicar cambios</button>
              </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modCargar" tabindex="-1" role="dialog" aria-labelledby="modCargar" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modCargar">Cargar archivos</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="mdi mdi-close"></i>
                </button>
            </div>
            <div class="modal-body" id="modal_cargar_files">
                {{-- <input type="file" class="archivosMult" name="files[]" multiple="multiple" accept="application/pdf"> --}}
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modCarpeta" tabindex="-1" role="dialog" aria-labelledby="modCarpeta" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modCarpeta">Crear carpeta</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="mdi mdi-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="mod_ncarpeta">Nombre de carpeta</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="mod_ncarpeta" id="mod_ncarpeta">
                        <span class="input-group-btn">
                            <button class="btn btn-primary btn_n_carpeta_mod" type="button">Crear</button>
                        </span>
                    </div><!-- /input-group -->
                    <span class="invalid-feedback" role="alert" id="modal_cargar_files_Err"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modCambiarNombre" tabindex="-1" role="dialog" aria-labelledby="modCambiarNombre_t" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modCambiarNombre_t">Cambiar nombre</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="mdi mdi-close"></i>
                </button>
            </div>
            <div class="modal-body" id="modal_cargar_files">
                <div class="form-group">
                    <label for="mod_nombreupdate">Nombre</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="mod_nombreupdate" id="mod_nombreupdate">
                        <div class="input-group-append">
                            <button class="btn btn-outline-primary btn_n_nombre_mod" type="button">Actualizar</button>
                        </div>
                    </div>
                    <span class="invalid-feedback" role="alert" id="modal_cambiarnom_Err"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<!--Inicio modal personalizar logo carpeta-->

<div class="modal fade" id="mod_imagenPersonalizada" tabindex="-1" role="dialog" aria-labelledby="imagenPersonalizada_t" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imagenPersonalizada_t">Personalizar Logo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="mdi mdi-close"></i>
                </button>
            </div>
            <div class="modal-body" id="modal_cargar_files">
               <form  id="btn_n_personalizar_mod" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="mod_imagenPersonalizada">Logo</label>
                    <div class="input-group">
                        <input type="file" class="form-control" accept="image/gif,image/jpeg,image/jpg,image/png" name="imagen_personalizada" id="imagenPersonalizada">
                        <div class="input-group-append">
                            <button class="btn btn-outline-primary btn_n_personalizar_mod" id="btn_enviar_img_personalizada"  type="button" >Personalizar</button>

                            <button class="btn btn-danger" id="eliminarImagenPersonalizada"><i class="fas fa-times"></i></button>

                        </div>
                    </div>
                    <span class="invalid-feedback" role="alert" id="modal_personalizar_Err"></span>
                </div>
               </form>
            </div>
        </div>
    </div>
</div>

<!--Fin logo carpeta-->

<div class="modal fade" id="modBuscar" tabindex="-1" role="dialog" aria-labelledby="modBuscar" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modBuscar"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="button_close" style="display:none;">
                    <i class="mdi mdi-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">

                </div>
                <div class="row" id="f_busqueda">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table id="tab_busqueda" class="table table-bordered">
                                <thead class="d-none">
                                    <tr>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="tbod">
                                    <tr>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modEliminarElemento" tabindex="-1" role="dialog" aria-labelledby="modEliminarElemento" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modEliminarElemento">Eliminar</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="mdi mdi-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <div class="input-group">
                        <span type="text" class="form-control" style="border: 0px solid #000000 !important;">Esta seguro que desea eliminar '<span name="mod_eliminarname" id="mod_eliminarname" value="asdsad">asdsad</span>'?</span>
                        <div class="input-group-append">
                            <button class="btn btn-outline-danger btn_n_eliminar_mod" type="button">Eliminar</button>
                        </div>
                    </div>
                    <span class="invalid-feedback" role="alert" id="modal_cargar_files_Err"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modMoverElemento" tabindex="-1" role="dialog" aria-labelledby="modMoverElemento" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modMoverElemento">Mover <strong><span name="mod_movercarpeta" id="mod_movercarpeta"></span></strong> a:</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="mdi mdi-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="row container-items"
                    style="height: min-content !important;" id="div_arbol">
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-danger" data-dismiss="modal" aria-label="Close">
                  Cancelar
              </button>
              <button type="button" class="btn btn-primary" id="submit_btn_mov">
                  Seleccionar
              </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modBuscar" tabindex="-1" role="dialog" aria-labelledby="modBuscar" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modBuscar">Buscar en archivos</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="mdi mdi-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">

                </div>
                <div class="row" id="f_permisos">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table id="tab_busqueda" class="table table-bordered">
                                <thead class="d-none">
                                    <tr>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="tbod">
                                    <tr>
                                        <td><center>Sin registros.</center></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
