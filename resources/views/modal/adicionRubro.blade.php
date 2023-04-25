
<div id="adicion" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <form class="form" action="{{url('/presupuesto/rubro/m/2/'.$rubro->id)}}" method="POST" id="add" enctype="multipart/form-data">
            {!! method_field('PUT') !!}
            {{ csrf_field() }}
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Adición al Rubro:  {{ $rubro->name }}</h4>
                </div>
                <div class="modal-body" id="prog">
                    <div class="table-responsive" >
                        <div class="col-md-12 align-self-center">
                            <div class="alert alert-danger text-center">
                                Recuerde añadir el archivo en el que esta la resolución de la adición. &nbsp;
                            </div>
                        </div>
                        <div class="form-group-sm">
                            <input type="file" required name="fileAdicion" accept="application/pdf" class="form-control">
                            <input type="hidden" name="vigencia_id" id="vigencia_id" value="{{ $rubro->vigencia_id }}">
                        </div>
                        <br>
                        <h4 class="text-center"> SELECCIONE LA FUENTE A LA QUE SE DESTINARÁ LA ADICIÓN</h4>
                        <select class="form-control" id="fuenteDep" name="fuenteDep" onchange="findFontAdd(this.value, 2)">
                            <option value="0">Seleccione la fuente a realizar adición</option>
                            @foreach($rubro->fontsRubro as $data)
                                @if($vigens->tipo == 0)
                                    @foreach($data->dependenciaFont as $depFont)
                                        <option value="{{ $depFont->id }}"> {{ $data->sourceFunding->code }} - {{ $data->sourceFunding->description }} -
                                        Dep: {{$depFont->dependencias->name}}</option>
                                    @endforeach
                                @else
                                    <option value="{{ $data->id }}"> {{ $data->sourceFunding->code }} - {{ $data->sourceFunding->description }}</option>
                                @endif
                            @endforeach
                        </select>
                        <input type="hidden" name="DepFontID" id="DepFontID" value="0">
                        <input type="hidden" name="movRubroID" id="movRubroID" value="0">
                        <input type="hidden" name="tipoVigencia" id="tipoVigencia" value="{{ $vigens->tipo }}">
                        <div class="text-center" style="display: none" id="divValues" name="divValues">
                            <br>
                            <h5 class="text-center"> VALOR ACTUAL DE ADICIÓN DE LA FUENTE: <span id="valueFont"></span> </h5>
                            <h5 class="text-center"> NUEVO VALOR A ADICIONAR: </h5>
                            <input type="number" class="form-control" name="valorAdd" id="valorAdd" value="0" min="1">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <center><button type="submit" style="display: none" id="buttonEnviarAdd" class="btn-sm btn-primary">Guardar Adición</button></center>
                </div>
            </div>
        </form>
    </div>
</div>

