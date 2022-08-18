
<div id="formularios" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title">Formularios Industria y comercio Avisos y tableros (ICA) </h3>
            </div>
            <div class="modal-body text-center" id="prog">
                @if($rit)
                    @if($rit->claseContribuyente == "Contribuyente")
                        <a class="btn btn-sm btn-primary-impuestos" href="{{ route('impuestos.icaContri.create') }}">Formulario Declaracion Contribuyente</a></td>
                    @elseif($rit->claseContribuyente == "Retenedor")
                        <a class="btn btn-sm btn-primary-impuestos" href="">Formulario Declaración Agente Retenedor</a></td>
                    @else
                        <a class="btn btn-sm btn-primary-impuestos" href="{{ route('impuestos.icaContri.create') }}">Formulario Declaracion Contribuyente</a></td>
                        <a class="btn btn-sm btn-primary-impuestos" href="">Formulario Declaración Agente Retenedor</a></td>
                    @endif
                @endif
                <br>&nbsp;<br>
            </div>
        </div>
    </div>
</div>

