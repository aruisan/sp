
<div id="formAsignaRubro" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <form class="form" action="{{ url('/presupuesto/proyectos/asignaRubroActiv') }}" method="POST" enctype="multipart/form-data">
            {!! method_field('POST') !!}
            {{ csrf_field() }}
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3 class="modal-title">Asignación de Rubro a la Actividad</h3>
                </div>
                <div class="modal-body text-center" id="prog">
                    <h4>Seleccione el rubro correspondiente a la actividad <b><div id="codeActividad"></div><div id="nameActividad"></div></b> </h4>
                    <div id="selectedT"></div>
                    <input type="hidden"  name="actividadCode" id="actividadCode"/>
                    <input type="hidden"  name="vigencia_id" id="vigencia_id"/>
                    <select class="form-control" style="width: 100%" name="rubro_id" required>
                        @foreach($rubBPIN as $Rubro)
                            <option value="{{$Rubro['id_rubro']}}">{{ $Rubro['cod'] }} - {{ $Rubro['name'] }}</option>
                        @endforeach
                    </select>
                    <br>
                    <h4>Valor a tomar de la actividad. Dinero disponible:<b><div id="dispActividad"></div></b>  </h4>
                    <input type="number" name="valueAsignarRubro" id="valueAsignarRubro" min="1" class="form-control">
                </div>
                <div class="modal-footer">
                    <center>
                        <button type="submit" class="btn-sm btn-primary">Guardar Rubro</button>
                    </center>
                </div>
            </div>
        </form>
    </div>
</div>

