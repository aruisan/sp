
<div id="formTipoNormas" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <form class="form" action="{{ url('/presupuesto/rubro/CUIPO/TipoNormas') }}" method="POST" enctype="multipart/form-data">
            {!! method_field('POST') !!}
            {{ csrf_field() }}
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3 class="modal-title">Asignación de Tipo de Norma</h3>
                </div>
                <div class="modal-body text-center" id="prog">
                    <h4>Seleccione el Tipo de Norma correspondiente al rubro <b><div id="nameRubroTN"></div></b> </h4>
                    <div id="selectedTN"></div>
                    <input type="hidden"  name="rubroIDTN" id="rubroIDTN"/>
                    <input type="hidden"  name="vigencia_idTN" id="vigencia_idTN"/>
                    <select class="select-cpc" style="width: 100%" name="codeTN" required>
                        @foreach($tipoNormas as $tipoNorma)
                            <option value="{{$tipoNorma->id}}">{{$tipoNorma->code}} - {{$tipoNorma->description}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <center>
                        <button type="submit" class="btn-sm btn-primary">Guardar Tipo de Norma</button>
                    </center>
                </div>
            </div>
        </form>
    </div>
</div>

