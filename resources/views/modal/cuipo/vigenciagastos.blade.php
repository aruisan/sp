
<div id="formVigenciaGastos" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <form class="form" action="{{ url('/presupuesto/rubro/CUIPO/VigenciaGastos') }}" method="POST" enctype="multipart/form-data">
            {!! method_field('POST') !!}
            {{ csrf_field() }}
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3 class="modal-title">Asignación de Vigencia Gastos</h3>
                </div>
                <div class="modal-body text-center" id="prog">
                    <h4>Seleccione la vigencia gasto correspondiente al rubro <b><div id="nameRubroVG"></div></b> </h4>
                    <div id="selectedVG"></div>
                    <input type="hidden"  name="rubroIDVG" id="rubroIDVG"/>
                    <input type="hidden"  name="vigencia_idVG" id="vigencia_idVG"/>
                    <select class="select-budget-section" style="width: 100%" name="codeVG" required>
                        @foreach($vigenciaGastos as $vigenciaG)
                            <option value="{{$vigenciaG->id}}">{{$vigenciaG->code}} - {{$vigenciaG->description}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <center>
                        <button type="submit" class="btn-sm btn-primary">Guardar Vigencia Gasto</button>
                    </center>
                </div>
            </div>
        </form>
    </div>
</div>

