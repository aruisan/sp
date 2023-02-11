
<div id="asignarDineroDep" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <form class="form" action="{{url('/presupuesto/rubro/dineroDependencia/'.$rubro->id)}}" method="POST" enctype="multipart/form-data">
            {!! method_field('POST') !!}
            {{ csrf_field() }}
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Asignar Dinero a la Dependencia:  <div class="text-center" id="nameDep"></div></h4>
                    <input type="hidden" id="idDep" name="idDep">
                    <input type="hidden" name="vigenciaid" value="{{ $rubro->vigencia_id }}">
                    <input type="hidden" id="fuenteRid" name="fuenteRid">
                    <input type="hidden" id="depFontID" name="depFontID">
                </div>
                <div class="modal-body" id="prog">
                    <div class="table-responsive" >
                        <table id="tabla_rubrosCdp" class="table table-bordered">
                            <thead>
                            <tr>
                                @foreach($rubro->fontsRubro as $data)
                                    <th class="text-center">Dinero a tomar de la fuente: {{ $data->sourceFunding->description }}</th>
                                @endforeach
                            </tr>
                            </thead>
                            <tbody id="bodyTableFonts">
                            </tbody>
                        </table>
                        <br>
                    </div>
                </div>
                <div class="modal-footer">
                    <center>
                        @if($rol == 3 or $rol == 4)
                            <button type="submit" class="btn-sm btn-primary">Asignar Dinero a Dependencia</button>
                        @endif
                    </center>
                </div>
            </div>
        </form>
    </div>
</div>

