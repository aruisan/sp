<button class="btn btn-raised  dropdown-toggle item-perfil" type="button" data-toggle="dropdown">
    @if(Auth::user()->avatar == "public/avatar/default.png")
        <img class="img-circle-mn" src="{{ asset('/img/user.png') }}">
    @else
        <img class="img-circle-mn" src="{{Storage::url(Auth::user()->avatar)}}">
    @endif
    {!! str_limit(Auth::user()->name, 10) !!}
    <span class="caret"></span>
</button>
<ul class="dropdown-menu pull-left">
    <form action="{{ route('user-avatar')}}" id="avatarForm" enctype="multipart/form-data" method="POST">
        {{ csrf_field() }}
        <input type="file" style="display: none" name="avatar" id="avatarInput" accept="image/*">
    </form>
    <li >
        <div class="avatarThumbnail">
            @if(Auth::user()->avatar == "public/avatar/default.png")
                <img class="img-circle img-responsive text-center " src="{{ asset('/img/user.png') }}">
            @else
                <img class="img-circle img-responsive text-center " src="{{Storage::url(Auth::user()->avatar)}}">
            @endif
            <div class="avatarCaption"><br>
                <button class="btn btn-primary btn-sm" id="avatarImagenDefault"><i class="fa fa-cloud-upload"></i></button>
            </div>
        </div>
    </li>
   <li class="item-perfil">
       <a class="item-perfil">
            <i class="fa fa-suitcase" aria-hidden="true"></i>
           <label> Tipo:</label>
            @foreach(Auth::user()->roles as $rol)
                {{ $rol->name }}
            @endforeach
       </a>
    </li>
    <li class="item-perfil">
        <a class="item-perfil" href="" data-toggle="modal" data-target="#cambiarPasword">
            <i class="fa fa-unlock-alt" aria-hidden="true"></i>
            Cambiar clave 
        </a>
    </li>
    <li class="item-perfil">
        <a class="item-perfil" href="{{ route('logout') }}"
            onclick="event.preventDefault();
            document.getElementById('logout-form').submit();">
            <i class="fa fa-sign-out" aria-hidden="true"></i>
            Salir
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        {{ csrf_field() }}
        </form>
    </li>
</ul>
