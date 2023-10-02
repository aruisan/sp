
<div id="formTerceros" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <form class="form" action="{{ url('/presupuesto/rubro/CUIPO/Terceros') }}" method="POST" enctype="multipart/form-data">
            {!! method_field('POST') !!}
            {{ csrf_field() }}
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3 class="modal-title">Asignación de Tercero</h3>
                </div>
                <div class="modal-body text-center" id="prog">
                    <h4>Seleccione el Tercero correspondiente al rubro <b><div id="nameRubroT"></div></b> </h4>
                    <div id="selectedT"></div>
                    <input type="hidden"  name="rubroIDT" id="rubroIDT"/>
                    <input type="hidden"  name="vigencia_idT" id="vigencia_idT"/>
                    <select class="select-tercero" style="width: 100%" name="codeT" required>
                        @foreach($terceros as $tercero)
                            <option value="{{$tercero->id}}">{{$tercero->code}} - {{$tercero->entity}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <center>
                        <button type="submit" class="btn-sm btn-primary">Guardar Tercero</button>
                    </center>
                </div>
            </div>
        </form>
    </div>
</div>

