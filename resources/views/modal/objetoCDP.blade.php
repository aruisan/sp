<div id="objetoCDPedit" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
      <form class="form" >
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title">Cambiar Objeto CDP <span id="codeCDP"></span></h4>
              </div>
              <div class="modal-body">
                  <input type="hidden" name="idCdpChange" id="idCdpChange" value="0">
                  <label>OBJETO DEL CDP: </label>
                  <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-file-o" aria-hidden="true"></i></span>
                      <textarea name="objeto" id="objeto" required class="form-control"></textarea>
                  </div>
                  <small class="form-text text-muted">Objeto del CDP</small>
              </div>
              <div class="modal-footer">
                  <a class="btn btn-danger" onclick="editObject()">Cambiar Objeto</a>
              </div>
          </div>
      </form>
  </div>
</div>

