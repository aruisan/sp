@extends('layouts.frontend')

@section('css')
  <style type="text/css">
      .imagen-principal{
        background-image: url('{{asset('img/principal/Banner_974.jpg')}}');
         -webkit-background-size: cover;
         -moz-background-size: cover;
         -o-background-size: cover;
         background-size: cover;
         height: 100%;
         width: 90% ;
         margin-left: 5%;
         text-align: center;
         z-index: -3;
      }
  </style>
@stop
@section('contenido')
      <div class="imagen-principal container">
      </div>
@stop