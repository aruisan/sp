
<div id="formSF" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <form class="form" action="{{ url('/presupuesto/rubro/CUIPO/SourceFundings') }}" method="POST" id="add" enctype="multipart/form-data">
            {!! method_field('POST') !!}
            {{ csrf_field() }}
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3 class="modal-title">Asignación de Fuentes de Financiación</h3>
                </div>
                <div class="modal-body text-center" id="prog">
                    <h4>Seleccione las fuentes de financiación correspondientes al rubro <b><div id="nameRubro"></div></b> </h4>
                    <div id="selectedSF"></div>
                    <input type="hidden"  name="rubroID" id="rubroID"/>
                    <input type="hidden"  name="vigencia_id" id="vigencia_id"/>
                    <div id="select_SF">
                        <select class="select-sf" style="width: 100%" name="code[]" multiple="multiple">
                            @foreach($fuentes as $fuente)
                                <option value="{{$fuente->id}}">{{$fuente->code}} - {{$fuente->description}}</option>
                            @endforeach
                        </select>
                    </div>

                </div>
                <div class="modal-footer">
                    <center>
                        <button type="submit" class="btn-sm btn-primary" id="buttonSaveSF">Guardar Fuentes de Financiación</button>
                    </center>
                </div>
            </div>
        </form>
    </div>
</div>

