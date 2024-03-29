
<div id="modalMakeCHIP" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <form class="form">
            <div class="modal-content">
                <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3 class="modal-title text-center">Informes CHIP {{ \Illuminate\Support\Carbon::today()->year }}</h3>
                </div>
                <div class="modal-body text-center" id="prog">
                    <div class="form-validation">
                        <form class="form-valide" action="{{url('presupuesto/CHIP')}}" method="POST" enctype="multipart/form-data" id="formCHIP">
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
                                            <option value="2">Junio</option>
                                            <option value="3">Septiembre</option>
                                            <option value="4">Diciembre</option>
                                        </select>
                                    </div>
                                    <label class="col-lg-4 col-form-label text-right" for="categoria">Categoria: <span class="text-danger">*</span></label>
                                    <div class="col-lg-6">
                                        <select name="categoria" id="categoria" class="form-control">
                                            <option value="BolDeuMor">BOLETÍN DEUDORES MOROSOS</option>
                                            <option value="CGRPerCos">CGR PERSONAL Y COSTOS</option>
                                            <option value="ConPriInf">CONPES PRIMERA INFANCIA</option>
                                            <option value="ProgIng">PROGRAMACION DE INGRESOS</option>
                                            <option value="EjecIng">EJECUCION DE INGRESOS</option>
                                            <option value="ProgGasAdm">PROGRAMACION DE GASTOS ADMINISTRACION CENTRAL</option>
                                            <option value="ProgGasCon">PROGRAMACION DE GASTOS CONCEJO</option>
                                            <option value="ProgGasPer">PROGRAMACION DE GASTOS PERSONERIA</option>
                                            <option value="EjecGasAdm">EJECUCION DE GASTOS ADMINISTRACION CENTRAL</option>
                                            <option value="EjecGasCon">EJECUCION DE GASTOS CONCEJO</option>
                                            <option value="EjecGasPer">EJECUCION DE GASTOS PERSONERIA</option>
                                            <option value="FUTCieFis">FUT CIERRE FISCAL</option>
                                            <option value="FUTDeuPub">FUT DEUDA PUBLICA</option>
                                            <option value="FUTRegPre">FUT REGISTRO PRESUPUESTAL</option>
                                            <option value="FUTTesFonSal">FUT TESORERIA FONDO SALUD</option>
                                            <option value="FUTVic">FUT VICTIMAS</option>
                                            <option value="FUTVigFut">FUT VIGENCIAS FUTURAS</option>
                                            <option value="AUPPAE">AUPA PAE</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-lg-12 ml-auto text-center">
                                    <br><br>
                                    <a onclick="formCHIPSubmit()" class="btn btn-primary">Generar CHIP</a>
                                </div>
                            </div>
                            <div class="text-center" id="cargandoCHIP" style="display: none">
                                <h4>Buscando informacion para generar el informe CHIP...</h4>
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

