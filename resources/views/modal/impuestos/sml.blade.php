<div id="formSML" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <form class="form" id="smlform" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3 class="modal-title">Salario Minimo Legal</h3>
                </div>
                <div class="modal-footer">
                    @if(count($smls) > 0)
                        <table class="table table-bordered" id="tabla_UVT">
                            <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-center">Valor</th>
                                <th class="text-center">Fecha</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($smls as $index => $sml)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td class="text-center">$<?php echo number_format($sml->valor,0) ?></td>
                                    <td class="text-center">{{ \Carbon\Carbon::parse($sml->fecha)->format('d-m-Y') }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="alert alert-danger">
                            <center>
                                No hay salarios minimos legales registrados en el sistema.
                            </center>
                        </div>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

