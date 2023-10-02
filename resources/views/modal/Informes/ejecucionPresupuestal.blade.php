<div id="ejecucionPresupuestal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <form class="form" action="{{ url('/presupuesto/ejecucion/gastos/'.$V) }}" method="POST" id="add" enctype="multipart/form-data">
            {!! method_field('PUT') !!}
            {{ csrf_field() }}
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3 class="modal-title text-center">Ejecución Presupuestal Egresos</h3>
                </div>
                <div class="modal-body text-center" id="prog">
                    <h4>Seleccione la fecha de inicio y la fecha final de la que desee obtener la ejecución presupuestal de egresos.
                        Recuerde que solo se puede obtener la ejecución de la vigencia actual.</h4>
                    <br>
                    <div class="row ">
                        <div class="col-sm-6">
                            Fecha Inicio
                            <br>
                            <input type="date" class="form-control" name="f_inicio" value="{{ $añoActual }}-01-01" min="{{ $añoActual }}-01-01" max="{{ $lastDay }}">
                            <input type="hidden" class="form-control" name="vigencia" value="{{ $V }}">
                        </div>
                        <div class="col-sm-6">
                            Fecha Final
                            <br>
                            <input type="date" class="form-control" name="f_final" value="{{ $actuallyDay }}" min="{{ $añoActual }}-01-01" max="{{ $actuallyDay }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <center>
                        <button type="submit" class="btn-sm btn-primary">Generar Ejecución Presupuestal</button>
                    </center>
                </div>
            </div>
        </form>
    </div>
</div>