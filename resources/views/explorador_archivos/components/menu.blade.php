{{--

<nav aria-label="breadcrumb" style="background-color: #fdfefe !important" >
    <ol class="breadcrumb p-2" id="migas_carpetas">
        <li class="breadcrumb-item"><a href=""><i class="mdi mdi-home" ></i></a></li>
    </ol>
</nav>

--}}

<div class="btn-group" role="group" aria-label="...">
    <button class="btn btn-primary" onclick="modal_crear_carpeta()">
        <i class="fa fa-folder-o" aria-hidden="true"></i>
    </button>
    <button class=" btn btn-primary" onclick="btnCargarFile()">
        <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
    </button>
    <button type="button" class="btn btn-primary" onclick="cambiar_presentacion_items('{{route('presentacion.carpeta.item', 'fila')}}')">
        <i class="fa fa-list-ul" aria-hidden="true"></i>
    </button>
    <button type="button" class="btn btn-primary"  onclick="cambiar_presentacion_items('{{route('presentacion.carpeta.item', 'columna')}}')">
        <i class="fa fa-th-large" aria-hidden="true"></i>
    </button>
</div>
