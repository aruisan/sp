@extends('layouts.dashboard')
@section('titulo')
    Listar Roles
@stop
@section('sidebar')
  {{-- @include('admin.roles.cuerpo.aside') --}}
@stop
@section('content')
  <div class="breadcrumb text-center">
            <strong>
                <h4><b>Administración Roles</b></h4>
            </strong>
        </div>
            <ul class="nav nav-pills">
                <li class="nav-item active">
                    <a class="nav-link " data-toggle="pill" href="#lista"> Roles</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{route('roles.create')}}">Crear Rol</a>
                </li>
             
            </ul>
     
            <div class="tab-content" style="background-color: white">
                <div id="lista" class="tab-pane active"><br>

<table class="table table-bordered">
  <tr>
     <th>No</th>
     <th>Name</th>
     <th class="text-center"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span></th>
     <th width="280px"><span class="glyphicon glyphicon-remove " aria-hidden="true"></span></th>
  </tr>
    @foreach ($roles as $key => $role)
    <tr>
        <td class="text-center">{{ ++$i }}</td>
        <td class="text-center">{{ $role->name }}</td>
        <td class="text-center">
          <a href="{{ route('roles.edit',$role->id) }}" class="btn btn-sm btn-danger">
            <span class="glyphicon glyphicon-edit" aria-hidden="true"></span>
          </a>
        </td>
        <td class="text-center">
            {!! Form::open(['method' => 'DELETE','route' => ['roles.destroy', $role->id],'style'=>'display:inline']) !!}
                <button type="submit" name="delete_modal" class="btn btn-sm btn-danger" >
                    <span class="glyphicon glyphicon-remove " aria-hidden="true"></span>
                </button>
            {!! Form::close() !!}
        </td>
    </tr>
    @endforeach
</table>
</div></div>

{!! $roles->render() !!}


@endsection