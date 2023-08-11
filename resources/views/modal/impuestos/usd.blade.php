<div id="formUSD" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <form class="form" id="smlform" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3 class="modal-title">Valor del Dolar Registrado en el Sistema</h3>
                </div>
                <div class="modal-footer">
                    @if(count($usds) > 0)
                        <table class="table table-bordered" id="tabla_USD">
                            <thead>
                            <tr>
                                <th class="text-center">Fecha</th>
                                <th class="text-center">Valor</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($usds as $index => $usd)
                                <tr>
                                    <td class="text-center">{{ \Carbon\Carbon::parse($usd->fecha)->format('d-m-Y') }}</td>
                                    <td class="text-center">$<?php echo number_format($usd->valor,0) ?></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="alert alert-danger">
                            <center>
                                No hay valores del dolar registrados en el sistema.
                            </center>
                        </div>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

