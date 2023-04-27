<table>
    <thead>
    <tr>
        <th class="text-center">Fecha</th>
        <th class="text-center">Documento</th>
        <th class="text-center">Concepto</th>
        <th class="text-center">Tercero</th>
        <th class="text-center">Cuenta</th>
        <th class="text-center">Debito</th>
        <th class="text-center">Credito</th>
    </tr>
    </thead>
    <tbody>
        @foreach($ordenPagos as $codigo)
            @foreach($codigo['descuentos'] as $descuentos)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($codigo['info']->created_at)->format('d-m-Y') }}</td>
                    <td>Orden de pago #{{ $codigo['info']->code }}</td>
                    <td>{{ $codigo['info']->nombre }}</td>
                    <td>{{ $codigo['ccH']}} - {{ $codigo['tercero'] }}</td>
                    @if($descuentos->desc_municipal_id != null)
                        <td>{{ $descuentos->descuento_mun['codigo'] }} - {{ $descuentos->descuento_mun['concepto'] }}</td>
                    @elseif($descuentos->retencion_fuente_id != null)
                        <td>{{ $descuentos->descuento_retencion->codigo}} - {{ $descuentos->descuento_retencion->concepto }}</td>
                    @else
                        <td>{{ $descuentos->puc->code}} - {{ $descuentos->puc->concepto}}</td>
                    @endif
                    <td>0</td>
                    <td>{{ $descuentos['valor'] }}</td>
                </tr>
            @endforeach
            @for($z = 0; $z < count($codigo['pucs']); $z++)
                @if(isset($codigo['pucs'][$z]->data_puc))
                    <tr class="text-center">
                        <td>{{ \Carbon\Carbon::parse($codigo['info']->created_at)->format('d-m-Y') }}</td>
                        <td>Orden de pago #{{ $codigo['info']->code }}</td>
                        <td>{{ $codigo['info']->nombre }}</td>
                        <td>{{ $codigo['ccH']}} - {{ $codigo['tercero'] }}</td>
                        <td>{{$codigo['pucs'][$z]->data_puc->code}} - {{$codigo['pucs'][$z]->data_puc->concepto}}</td>
                        <td>$<?php echo number_format($codigo['pucs'][$z]->valor_debito,0);?></td>
                        <td>$<?php echo number_format($codigo['pucs'][$z]->valor_credito,0);?></td>
                    </tr>
                @else
                    <tr class="text-center">
                        <td>{{ \Carbon\Carbon::parse($codigo['info']->created_at)->format('d-m-Y') }}</td>
                        <td>Orden de pago #{{ $codigo['info']->code }}</td>
                        <td>{{ $codigo['info']->nombre }}</td>
                        <td>{{ $codigo['pucs'][$z]->persona->num_dc }} - {{ $codigo['pucs'][$z]->persona->nombre }}</td>
                        <td>{{ $codigo['pucs'][$z]->puc->code}} - {{ $codigo['pucs'][$z]->puc->concepto}}</td>
                        <td>$ <?php echo number_format($codigo['pucs'][$z]->debito,0);?></td>
                        <td>0</td>
                    </tr>
                @endif
            @endfor
        @endforeach
    </tbody>
</table>