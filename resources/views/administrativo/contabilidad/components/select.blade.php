<div class="breadcrumb text-center">  
    <div class="btn-group">
        <div class="btn-group">
            <select name="year" class="form-control" id="year" onchange="cambiar_elementos()"></select>
        </div>
        <div class="btn-group">
            <select name="periodo" class="form-control" id="periodo" onchange="cambiar_elementos()"></select>
        </div>
        <div class="btn-group" id="div_select">
            <select name="elemento" class="form-control" id="elementos"></select>
        </div>
        <button class="btn btn-danger" onclick="enviar('vista')">
        <i class="fa fa-search" aria-hidden="true"></i>
        </button>
        <button class="btn btn-danger" onclick="enviar('mostrar-pdf')">
            <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
        </button>
        
    </div>
</div>