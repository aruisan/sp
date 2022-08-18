@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">BPins </div>
                <div class="card-body">

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <th>
                                    Codigo Proyecto
                                </th>
                                <th>
                                    Nombre Proyecto
                                </th>
                                <th>
                                    Ver
                                </th>
                            </thead>
                            <tbody>
                                @foreach($bpins as $item)
                                    <tr>
                                        <td>
                                            {{$item->cod_proyecto}}
                                        </td>
                                         <td>
                                            {{$item->nombre_proyecto}}
                                        </td>
                                        <td>
                                           <a class="btn btn-success" href="{{route('bpin.show', $item->id)}}">
                                            Ver
                                           </a>                                           
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
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
