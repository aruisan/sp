
<div id="reduccion" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <form class="form" action="{{url('/presupuesto/rubro/m/3/'.$rubro->id)}}" method="POST" id="red" enctype="multipart/form-data">
            {!! method_field('PUT') !!}
            {{ csrf_field() }}
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Reducción al Rubro:  {{ $rubro->name }}</h4>
                </div>
                <div class="modal-body" id="prog">
                    <div class="table-responsive" >
                        <table id="tabla_rubrosCdp" class="table table-bordered">
                            <thead>
                            <tr>
                                @foreach($rubro->fontsRubro as $data)
                                    <th class="text-center">Dinero a retirar de  la fuente: {{ $data->fontRubro }}</th>
                                @endforeach
                                <th scope="col" class="text-center">Archivo</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                @foreach($rubro->fontsRubro as $fuentesRubro)
                                    <input type="hidden" name="fuenteR_id[]" value="{{ $fuentesRubro->fontRubro }}">
                                    <td>
                                        <div class="col-lg-12">
                                            <!--

                                           
                                            -->

                                        </div>
                                    </td>
                                @endforeach
                                <td>
                                    <div class="form-group-sm">
                                        <input type="file" required name="fileReduccion" accept="application/pdf" class="form-control">
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <br>
                    </div>
                </div>
                <div class="modal-footer">
                    <center>
                        @if($rol == 2)
                            <button type="submit" class="btn-sm btn-primary">Guardar Reducción</button>
                        @endif
                    </center>
                </div>
            </div>
        </form>
    </div>
</div>

