
<div id="modalMakeCHIP" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <form class="form">
            <div class="modal-content">
                <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3 class="modal-title text-center">Informes CHIP {{ \Illuminate\Support\Carbon::today()->year }}</h3>
                </div>
                <div class="modal-body text-center" id="prog">
                    <div class="form-validation">
                        <form class="form-valide" action="{{url('#')}}" method="POST" enctype="multipart/form-data" id="formulario">
                        {{ csrf_field() }}
                            <div class="col-md-12 align-self-center">
                                <div class="form-group">
                                    <label class="col-lg-4 col-form-label text-right" for="year">Año: </label>
                                    <div class="col-lg-6">
                                        <input type="number" id="year" disabled class="form-control" value="{{ \Illuminate\Support\Carbon::today()->year }}">
                                    </div>
                                    <label class="col-lg-4 col-form-label text-right" for="periodo">Periodo: <span class="text-danger">*</span></label>
                                    <div class="col-lg-6">
                                        <select name="periodo" id="periodo" class="form-control">
                                            <option value="1">Marzo</option>
                                            <option selected value="2">Junio</option>
                                            <option value="3">Septiembre</option>
                                            <option value="4">Diciembre</option>
                                        </select>
                                    </div>
                                    <label class="col-lg-4 col-form-label text-right" for="dep">Dependencia: <span class="text-danger">*</span></label>
                                    <div class="col-lg-6">
                                        <select name="dep" id="dep" class="form-control">
                                            <option value="AdmC">Administración Central</option>
                                            <option value="Con">Concejo</option>
                                            <option value="Per">Personeria</option>
                                        </select>
                                    </div>
                                    <label class="col-lg-4 col-form-label text-right" for="categoria">Categoria: <span class="text-danger">*</span></label>
                                    <div class="col-lg-6">
                                        <select name="categoria" id="categoria" class="form-control">
                                            <option value="ProgIng">PROGRAMACION DE INGRESOS</option>
                                            <option value="EjecIng">EJECUCION DE INGRESOS</option>
                                            <option value="ProgGas">PROGRAMACION DE GASTOS</option>
                                            <option value="EjecGas">EJECUCION DE GASTOS</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-lg-12 ml-auto text-center">
                                    <br><br><br>
                                    <button type="submit" class="btn btn-primary">Generar CHIP</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">

                </div>
            </div>
        </form>
    </div>
</div>

