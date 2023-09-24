
<div id="revFile" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
      <form class="form" action="{{url('/administrativo/radCuentas/file/rev')}}" method="POST">
          {!! method_field('POST') !!}
          {{ csrf_field() }}
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Enviar Revisión Anexo</h4>
          </div>
          <div class="modal-body">
            <div class="row">
                <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <label>ESTADO: </label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-file-o" aria-hidden="true"></i></span>
                        <select name="estado" id="estado" class="form-control">
                            <option value="1">APROVADO</option>
                            <option value="2">RECHAZADO</option>
                        </select>
                    </div>
                    <small class="form-text text-muted">Estado</small>
                </div>
                <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <label>OBSERVACIÓN: </label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-file-o" aria-hidden="true"></i></span>
                        <textarea name="observacion" class="form-control" id="observacionAprov"></textarea>
                    </div>
                    <small class="form-text text-muted">Observación</small>
                </div>
                <input type="hidden" name="id_anex" id="id_anex">
            </div>
            <div class="modal-footer text-center">
                <button type="submit" class="btn btn-danger">Enviar</button>
            </div>
          </div>
        </div>
      </form>
  </div>
</div>



