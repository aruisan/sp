@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Rubros </div>
                <div class="card-body">
                    <a href="{{route('cdp.create')}}" class="btn btn-primary">Cdps</a>
                    <form action="{{route('cdp.autorizar')}}" method="post">
                    @csrf
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <th>
                                    valor
                                </th>
                                <th>
                                    rubro
                                </th>
                                <th>
                                    Aprobar
                                </th>
                                <th>
                                    Rechazar
                                </th>
                            </thead>
                            <tbody>
                                @foreach($cdps as $key => $item)
                                    <tr>
                                        <td>
                                            $ {{$item->valor}}
                                        </td>
                                        <td>
                                            {{$item->rubro->puc->codigo}} - {{$item->rubro->puc->categoria}}
                                        </td>
                                        @if(auth()->user()->cdp2)
                                            @if($item->autoriza1 == 2)
                                             <td>
                                                <div class="form-check">
                                                    <input type="radio" class="form-check-input" name="estado[]" value="{{$item->id}}, aprobar" @if($item->autorizar2 != 1) disabled @endif  @if($item->autorizar2 == 2) checked @endif>
                                                    <label class="form-check-label" >Aprobar</label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check">
                                                    <input type="radio" class="form-check-input" name="estado[]" value="{{$item->id}}, rechazar" @if($item->autorizar2 != 1) disabled @endif  @if($item->autorizar2 == 0) checked @endif>
                                                    <label class="form-check-label">Rechazar</label>
                                                </div>
                                            </td>
                                            @else
                                                <td>
                                                    En espera...
                                                </td>
                                                <td>
                                                    En espera...
                                                </td>
                                            @endif
                                        @else
                                             <td>
                                                <div class="form-check">
                                                    <input type="radio" class="form-check-input" name="estado[]" value="{{$item->id}}, aprobar" @if($item->autorizar1 != 1) disabled @endif @if($item->autorizar1 == 2) checked @endif>
                                                    <label class="form-check-label" >Aprobar</label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check">
                                                    <input type="radio" class="form-check-input" name="estado[]" value="{{$item->id}}, rechazar" @if($item->autorizar1 != 1) disabled @endif @if($item->autorizar1 == 0) checked @endif>
                                                    <label class="form-check-label">Rechazar</label>
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="row mt-3">
                        <button type="submit" class="btn btn-primary">
                            Guardar Cambios
                        </button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script>
    
    </script>
@endsection
