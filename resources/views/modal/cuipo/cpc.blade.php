
<div id="formCPC" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <form class="form" action="{{ url('/presupuesto/rubro/CUIPO/CPC') }}" method="POST" id="add" enctype="multipart/form-data">
            {!! method_field('POST') !!}
            {{ csrf_field() }}
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3 class="modal-title">Asignación CPCs</h3>
                </div>
                <div class="modal-body text-center" id="prog">
                    <h4>Seleccione los CPCs correspondientes al rubro <b><div id="nameRubro"></div></b> </h4>
                    <div id="selectedCPC"></div>
                    <input type="hidden"  name="rubroID" id="rubroID"/>
                    <input type="hidden"  name="vigencia_id" id="vigencia_id"/>
                    <div id="select_CPC">
                        <select class="select-cpc" style="width: 100%" name="code[]" multiple="multiple" required>
                            @foreach($CPCs as $CPC)
                                <option value="{{$CPC->id}}">{{$CPC->code}} - {{$CPC->class}}</option>
                            @endforeach
                        </select>
                    </div>

                </div>
                <div class="modal-footer">
                    <center>
                        <button type="submit" class="btn-sm btn-primary" id="buttonSaveCPCs">Guardar CPCs</button>
                    </center>
                </div>
            </div>
        </form>
    </div>
</div>

