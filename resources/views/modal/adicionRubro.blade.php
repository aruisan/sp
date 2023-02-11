
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
                        <table id="tabla_rubrosCdp" class="table table-bordered">
                            <thead>
                            <tr>
                                <th class="text-center">FUENTE</th>
                                <th class="text-center">VALOR</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($rubro->fontsRubro as $data)
                                <tr>
                                    <td class="text-center">{{ $data->sourceFunding->code }} - {{ $data->sourceFunding->description }}</td>
                                    <td>
                                        <input type="hidden" name="fontID[]" value="{{$data->id}}">
                                        @if(count($data->rubrosMov) > 0)
                                            @foreach($data->rubrosMov as $mov)
                                                @if($mov->movimiento == 2)
                                                    @php($value = $mov->valor)
                                                    @php($id = $mov->id)
                                                @endif
                                            @endforeach
                                            <input type="text" required  name="valorAdd[]" value="{{ $value }}" style="text-align: center" class="form-control" min="0">
                                            <input type="hidden" id="mov_id[]" name="mov_id[]" value="{{ $id }}">
                                        @else
                                            <input type="text" required  name="valorAdd[]" value="0" style="text-align: center" class="form-control" min="0">
                                            <input type="hidden" id="mov_id[]" name="mov_id[]">
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <center><button type="submit" class="btn-sm btn-primary">Guardar Adición</button></center>
                </div>
            </div>
        </form>
    </div>
</div>

