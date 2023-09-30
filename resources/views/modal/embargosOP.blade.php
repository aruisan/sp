
<div id="formEmbargo" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <form class="form-valide" action="{{url('/administrativo/tesoreria/ordenPagos/embargos/make')}}" method="POST" enctype="multipart/form-data">
            <meta name="csrf-token" content="{{ csrf_token() }}">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3 class="modal-title">Embargos a Orden de Pago</h3>
                </div>
                <div class="modal-body text-center" id="prog">
                    <center><h2>Descuentos Municipales</h2></center>
                    <hr><br>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="tabla_desc_muni">
                            <thead>
                            <th class="text-center">Concepto</th>
                            <th class="text-center">Valor</th>
                            </thead>
                            <tbody id="cuerpoDesc">
                            </tbody>
                        </table>
                    </div>

                    <div class="text-center" id="buttonAddActividad">
                        <button type="button" @click.prevent="nuevaFilaDescMuni" class="btn btn-sm btn-primary">AGREGAR DESCUENTO MUNICIPAL</button>
                    </div>
                </div>
                <div class="modal-footer">
                    <center>
                        <button type="submit" class="btn-sm btn-primary" id="buttonDownloadPazySalvo">REALIZAR EMBARGO</button>
                    </center>
                </div>
            </div>
        </form>
    </div>
</div>

