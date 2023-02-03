
<div id="formPazySalvo" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <form class="form" id="pazysalvoform" enctype="multipart/form-data">
            <meta name="csrf-token" content="{{ csrf_token() }}">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3 class="modal-title">Generar Paz y Salvo</h3>
                </div>
                <div class="modal-body text-center" id="prog">
                    <h4>Seleccione el usuario del que desea descargar el paz y salvo</h4>
                    <select name="paySelected" id="paySelected" class="form-control" required>
                        @foreach($pagosFinalizados as $pago)
                            <option value="{{ $pago->id }}">{{ $pago->user->name }} - {{ $pago->user->email }}
                            - {{ $pago->modulo }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <center>
                        <button type="submit" class="btn-sm btn-primary" id="buttonDownloadPazySalvo">Descargar Paz y Salvo</button>
                    </center>
                </div>
            </div>
        </form>
    </div>
</div>

