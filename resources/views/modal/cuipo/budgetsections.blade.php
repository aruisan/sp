
<div id="formBudgetSections" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <form class="form" action="{{ url('/presupuesto/rubro/CUIPO/BudgetSection') }}" method="POST" enctype="multipart/form-data">
            {!! method_field('POST') !!}
            {{ csrf_field() }}
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3 class="modal-title">Asignación de Sección Presupuestal</h3>
                </div>
                <div class="modal-body text-center" id="prog">
                    <h4>Seleccione la sección presupuestal correspondiente al rubro <b><div id="nameRubroBS"></div></b> </h4>
                    <div id="selectedBS"></div>
                    <input type="hidden"  name="rubroIDBS" id="rubroIDBS"/>
                    <input type="hidden"  name="vigencia_idBS" id="vigencia_idBS"/>
                    <select class="select-budget-section" style="width: 100%" name="codeBS" required>
                        @foreach($budgetSections as $budgetSection)
                            <option value="{{$budgetSection->id}}">{{$budgetSection->code}} - {{$budgetSection->description}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <center>
                        <button type="submit" class="btn-sm btn-primary">Guardar Sección Presupuestal</button>
                    </center>
                </div>
            </div>
        </form>
    </div>
</div>

