
<div id="formUVT" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <form class="form" id="pazysalvoform" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3 class="modal-title">UVT</h3>
                </div>
                <div class="modal-footer">
                    @if(count($uvts) > 0)
                        <table class="table table-bordered" id="tabla_UVT">
                            <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-center">Valor</th>
                                <th class="text-center">Año</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($uvts as $index => $uvt)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td class="text-center">$<?php echo number_format($uvt->valor,0) ?></td>
                                    <td class="text-center">{{ $uvt->año }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="alert alert-danger">
                            <center>
                                No hay UVTs registrados en el sistema.
                            </center>
                        </div>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

