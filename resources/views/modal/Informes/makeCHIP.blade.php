
<div id="modalMakeCHIP" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <form class="form">
            <div class="modal-content">
                <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3 class="modal-title text-center">Informes CHIP</h3>
                </div>
                <div class="modal-body text-center" id="prog">
                    <div class="form-validation">
                        <form class="form-valide" action="{{url('#')}}" method="POST" enctype="multipart/form-data" id="formulario">
                        {{ csrf_field() }}
                            <div class="col-md-12 align-self-center">
                                <div class="form-group">
                                    <label class="col-lg-4 col-form-label text-right" for="year">Año: <span class="text-danger">*</span></label>
                                    <div class="col-lg-6">
                                        <input type="number" id="year" disabled class="form-control" value="2023">
                                    </div>
                                    <label class="col-lg-4 col-form-label text-right" for="periodo">Periodo: <span class="text-danger">*</span></label>
                                    <div class="col-lg-6">
                                        <select name="periodo" id="periodo" class="form-control">
                                            <option>Marzo</option>
                                            <option>Junio</option>
                                            <option>Septiembre</option>
                                            <option>Diciembre</option>
                                        </select>
                                    </div>
                                    <label class="col-lg-4 col-form-label text-right" for="dep">Dependencia: <span class="text-danger">*</span></label>
                                    <div class="col-lg-6">
                                        <select name="dep" id="dep" class="form-control">
                                            <option>Administración Central</option>
                                            <option>Concejo</option>
                                            <option>Personeria</option>
                                        </select>
                                    </div>
                                    <label class="col-lg-4 col-form-label text-right" for="categoria">Categoria: <span class="text-danger">*</span></label>
                                    <div class="col-lg-6">
                                        <select name="categoria" id="categoria" class="form-control">
                                            <option>PROGRAMACION DE INGRESOS</option>
                                            <option>EJECUCION DE INGRESOS</option>
                                            <option>PROGRAMACION DE GASTOS</option>
                                            <option>EJECUCION DE GASTOS</option>
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

