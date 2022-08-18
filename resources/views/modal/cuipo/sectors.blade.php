
<div id="formSectors" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <form class="form" action="{{ url('/presupuesto/rubro/CUIPO/Sectors') }}" method="POST" enctype="multipart/form-data">
            {!! method_field('POST') !!}
            {{ csrf_field() }}
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3 class="modal-title">Asignación del Sector</h3>
                </div>
                <div class="modal-body text-center" id="prog">
                    <h4>Seleccione el sector correspondiente al rubro <b><div id="nameRubroSec"></div></b> </h4>
                    <div id="selectedSec"></div>
                    <input type="hidden"  name="rubroIDSec" id="rubroIDSec"/>
                    <input type="hidden"  name="vigencia_idSec" id="vigencia_idSec"/>
                    <select class="select-sectors" style="width: 100%" name="codeSec" required>
                        @foreach($sectors as $sector)
                            <option value="{{$sector->id}}">{{$sector->code}} - {{$sector->description}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <center>
                        <button type="submit" class="btn-sm btn-primary">Guardar Sector</button>
                    </center>
                </div>
            </div>
        </form>
    </div>
</div>

