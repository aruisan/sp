
<div id="formPagoMuellaje" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <form class="form" action="{{ url('/administrativo/impuestos/muellaje/pay') }}" method="POST" id="add" enctype="multipart/form-data">
            {!! method_field('POST') !!}
            {{ csrf_field() }}
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3 class="modal-title">Cargar Constancia de Pago Muellaje</h3>
                </div>
                <div class="modal-body text-center" id="prog">
                    <h4>Registro de ingreso No.<b><div id="regIngresoText" name="regIngreso"></div></b></h4>
                    <input type="hidden"  name="regIngreso" id="regIngreso"/>
                    <input type="hidden"  name="regId" id="regId"/>
                    <h4>Seleccione el archivo con la constancia de pago</h4>
                    <input type="file" class="form-control" required name="constanciaPago" id="constanciaPago" accept=".pdf">
                </div>
                <div class="modal-footer">
                    <center>
                        <button type="submit" class="btn-sm btn-primary" id="buttonSavePago">Cargar Pago</button>
                    </center>
                </div>
            </div>
        </form>
    </div>
</div>

