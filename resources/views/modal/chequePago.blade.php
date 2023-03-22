<div id="chequePagoedit" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
      <form class="form" >
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title">Cambiar Número de Cheque del Pago # <span id="codePago"></span></h4>
              </div>
              <div class="modal-body">
                  <input type="hidden" name="idPagoChange" id="idPagoChange" value="0">
                  <label>NÚMERO DE CHEQUE: </label>
                  <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-file-o" aria-hidden="true"></i></span>
                      <textarea name="cheque" id="cheque" required class="form-control"></textarea>
                  </div>
                  <small class="form-text text-muted">Número de Cheque del Pago</small>
              </div>
              <div class="modal-footer">
                  <a class="btn btn-danger" onclick="editCheque()">Cambiar Número de Cheque</a>
              </div>
          </div>
      </form>
  </div>
</div>

