<ul class="nav nav-tabs">
    <li class="{{$url == 'ver' ? 'active' : ''}}">
        <a href="{{route('coso.individuo.show', $individuo->id)}}">
            <i class="fa fa-eye" aria-hidden="true"></i>
        </a>
    </li>
    <li class="{{$url == 'galeria' ? 'active' : ''}}">
        <a href="{{route('coso.archivo.create', $individuo->id)}}">
            <i class="fa fa-picture-o" aria-hidden="true"></i>
        </a>
    </li>
      <li class="{{$url == 'comidas' ? 'active' : ''}}">
        <a href="{{route('coso.comida.create', $individuo->id)}}">
            <i class="fa fa-cutlery" aria-hidden="true"></i>
        </a>
    </li>
    <li class="{{$url == 'consultas' ? 'active' : ''}}">
        <a href="{{route('coso.veterinario.create', $individuo->id)}}">
            <i class="fa fa-medkit" aria-hidden="true"></i>
        </a>
    </li>
    @if($url == 'ver')
    <li class="{{$url == 'consultas' ? 'active' : ''}}">
        <a href="{{route('coso.individuo.pdf', $individuo->id)}}" target="_blank">
            <i class="fa fa-file-pdf-o text-danger" aria-hidden="true"></i>
        </a>
    </li>
    @endif
</ul>
{{$url}}

