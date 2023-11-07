@extends('layouts.frontend')

@section('css')
<style type="text/css">
    html, body, div, iframe { margin:0; padding:0; height:100%; }
    iframe { display:block; width:100%; border:none; }
</style>
@stop
@section('contenido')
<iframe src="https://gapfergon.com" width="900" height="700" allow="fullscreen"></iframe>

   
@stop

@section('js')
      <script>
        $(document).ready(function(){
          {{--
            $('#modal_celebracion').modal();
            --}}
          setTimeout(function(){
            $('#modal_celebracion').modal('hide');
          }, 5000);
        });
      </script>
@endsection