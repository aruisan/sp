
<div id="formPoliticaPublica" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <form class="form" action="{{ url('/presupuesto/rubro/CUIPO/PoliticaPublica') }}" method="POST" enctype="multipart/form-data">
            {!! method_field('POST') !!}
            {{ csrf_field() }}
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3 class="modal-title">Asignación de Politica Publica</h3>
                </div>
                <div class="modal-body text-center" id="prog">
                    <h4>Seleccione la politica pública correspondiente al rubro <b><div id="nameRubroPP"></div></b> </h4>
                    <div id="selectedPP"></div>
                    <input type="hidden"  name="rubroIDPP" id="rubroIDPP"/>
                    <input type="hidden"  name="vigencia_idPP" id="vigencia_idPP"/>
                    <select class="select-politica-publica" style="width: 100%" name="codePP" required>
                        @foreach($publicPolitics as $publicPolitic)
                            <option value="{{$publicPolitic->id}}">{{$publicPolitic->code}} - {{$publicPolitic->description}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <center>
                        <button type="submit" class="btn-sm btn-primary">Guardar Politica Pública</button>
                    </center>
                </div>
            </div>
        </form>
    </div>
</div>

