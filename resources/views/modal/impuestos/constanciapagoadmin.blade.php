
<div id="formConstanciaPago" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <form class="form" action="{{ url('/impuestos/Pagos/constancia/admin') }}" method="POST" id="add" enctype="multipart/form-data">
            {!! method_field('POST') !!}
            {{ csrf_field() }}
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3 class="modal-title">Cargar Constancia de Pago</h3>
                </div>
                <div class="modal-body text-center" id="prog">
                    <table class="table text-center">
                        <thead>
                        <tr>
                            <th class="text-center">Formulario:</th>
                            <th class="text-center">Fecha:</th>
                            <th class="text-center">Valor:</th>
                        </tr>
                        <tbody>
                            <tr>
                                <td><div id="form" name="form"></div></td>
                                <td><div id="fecha" name="fecha"></div></td>
                                <td><div id="value" name="value"></div></td>
                            </tr>
                        </tbody>
                    </table>
                    <br>
                    <h4>Seleccione la cuenta bancaria a la que se le realizo el pago</h4>
                    <select name="cuenta" id="cuenta" class="form-control" required>
                        @foreach($bancos as $cuenta)
                        <option @if($cuenta['hijo'] == 0) disabled @endif value="{{$cuenta['id']}}">{{$cuenta['code']}} -
                            {{$cuenta['concepto']}} - SALDO INICIAL: $<?php echo number_format($cuenta['saldo_inicial'],0) ?></option>
                        @endforeach
                    </select>
                    <br>
                    <input type="hidden"  name="regIngreso" id="regIngreso"/>
                    <input type="hidden"  name="regId" id="regId"/>
                    <h4>Seleccione el archivo con la constancia de pago</h4>
                    <input type="file" class="form-control" required name="constanciaPago" id="constanciaPago" accept=".pdf">
                </div>
                <div class="modal-footer">
                    <center>
                        <button type="submit" class="btn-sm btn-primary" id="buttonSavePago">Cargar Constancia de Pago</button>
                    </center>
                </div>
            </div>
        </form>
    </div>
</div>

