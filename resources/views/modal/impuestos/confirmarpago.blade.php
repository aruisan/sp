
<div id="formConfirmarPago" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <form class="form" enctype="multipart/form-data" action="{{ url('impuestos/Pagos/confirmPay') }}" method="POST">
            {!! method_field('POST') !!}
            {{ csrf_field() }}
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3 class="modal-title">Confirmar Pago</h3>
                </div>
                <div class="modal-body text-center" id="prog">
                    <h4>Fecha actual en la que se cargo el comprobante de pago, si desea se puede cambiar la fecha de pago o dejar la que ya tiene asignada.
                        <br>
                        El comprobante contable se elaborará con la fecha asignada.
                    </h4>
                    <input type="date" id="fechaComp" name="fechaComp" class="form-control">
                    <input type="hidden" id="pago_id" name="pago_id">
                </div>
                <div class="modal-footer">
                    <center>
                        <button type="submit" class="btn-sm btn-primary">Confirmar Pago</button>
                    </center>
                </div>
            </div>
        </form>
    </div>
</div>

