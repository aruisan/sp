
<div id="anularOP" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
      <form class="form" action="{{url('/administrativo/ordenPagos/'.$OrdenPago->id.'/anular')}}" method="POST">
          {!! method_field('POST') !!}
          {{ csrf_field() }}
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Anular la Orden de Pago</h4>
          </div>
          <div class="modal-body">
            <div class="row">
                <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <label>OBSERVACIÓN DEL MOTIVO DE ANULACIÓN: </label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-file-o" aria-hidden="true"></i></span>
                        <textarea name="observacion" required class="form-control"></textarea>
                    </div>
                    <small class="form-text text-muted">Observación de la anulación</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-danger">Anular</button>
            </div>
          </div>
        </div>
      </form>
  </div>
</div>



