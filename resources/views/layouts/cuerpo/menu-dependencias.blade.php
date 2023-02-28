@include('modal.updateSoftware')

@can('listar-empleados')
<li class="dropdown ">
   <a class="btn btn-default btn-sm dropdown-toggle item-menu" type="button" data-toggle="dropdown">
   NOMINA
   <span class="caret"></span>
   </a>
   <ul class="dropdown-menu">
      <li><a class="item-menu" tabindex="-1" href="{{route('nomina.empleados.index')}}">Empleados</a></li>
      <li><a class="item-menu" tabindex="-1" href="{{route('nomina.pensionados.index')}}">Pensionados</a></li>
   </ul>

{{--
   <li class="dropdown-submenu">
         <a class="dropdown-item item-menu" >Nomina de Pensionados</a>
         <ul class="dropdown-menu">
            <li><a class="item-menu" tabindex="-1" href="{{route('nomina.empleados.index')}}">Empleados</a></li>
            <li><a class="item-menu" tabindex="-1" href="{{route('nomina.empleados.index')}}">Pensionados</a></li>
         </ul>

      {{--
         <li class="dropdown-submenu">
               <a class="dropdown-item item-menu" >Nomina de Pensionados</a>
               <ul class="dropdown-menu">
                  <li><a class="item-menu" href="{{url('/administrativo/muebles/create')}}">Sueldo</a></li>
                  <li><a class="item-menu" href="{{url('/administrativo/inventario/create')}}">Prima</a></li>
               </ul>
            </li>
            <li class="dropdown-submenu">
               <a class="dropdown-item item-menu">Nomina de Empleados</a>
               <ul class="dropdown-menu">
                  <li><a class="item-menu" href="">Sueldo</a></li>
                  <li><a class="item-menu" href="">Prima Navidad</a></li>
                  <li><a class="item-menu" href="">Vacaciones</a></li>
                  <li><a class="item-menu" href="">Prima de Vacaciones</a></li>
                  <li><a class="item-menu" href="">Prima de Antiguedad</a></li>
               </ul>
            </li>
            <li><a class="item-menu" href="">Nomina de Bonificación</a></li>
      --}}
</li>
@endcan




@if(auth()->user()->roles->first()->id == 1)
   <li >
      <a class="btn btn-default btn-sm item-menu" href="{{ route('coso.individuo.index') }}">
       COSO
      </a>
   </li>
   <li class="dropdown ">
      <a class="btn btn-default btn-sm item-menu" href="{{route('estadistica.index')}}">
      ESTADISTICA
      </a>
   </li>
         <li class="page-scroll ">
            <a class="btn btn-default btn-sm dropdown-toggle item-menu" type="button" data-toggle="dropdown">
             ARCHIVOS
            <span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
               <li><a class="item-menu" tabindex="-1" href="{{route('explorador-archivos.index')}}">Archivos</a></li>
               {{--<li><a class="item-menu" tabindex="-1" href="{{route('carpetas.listar', 'Archivos')}}">Archivos</a></li>
               <li><a class="item-menu" tabindex="-1" href="{{route('carpetas.listar', 'Contratos')}}">Contratos</a></li>
               --}}
               <li class="dropdown-submenu">
                  <a class="dropdown-item item-menu" >Contratos</a>
                  <ul class="dropdown-menu">
                     <li><a class="item-menu" href="{{route('carpetas.listar', 'Contratos')}}">Contratos 2020</a></li>
                     <li><a class="item-menu" href="{{route('carpetas.listar', 'Contratos')}}">Contratos 2021</a></li>
                  </ul>
               </li>
               <li><a class="item-menu" tabindex="-1" href="{{route('carpetas.listar', 'Hojas de Vida')}}">Hojas de Vida</a></li>
               <li><a class="item-menu" tabindex="-1" href="{{route('carpetas.listar', 'Expedientes')}}">Expedientes</a></li>
               <li><a class="item-menu" tabindex="-1" href="{{route('carpetas.listar', 'Manuales')}}">Manuales</a></li>
               <li><a class="item-menu" tabindex="-1" href="{{route('carpetas.listar', 'Planes')}}">Planes</a></li>
               <li><a class="item-menu" tabindex="-1" href="{{route('carpetas.listar', 'Publicaciones')}}">Publicaciones</a></li>
               <li class="dropdown-submenu">
                  <a class="dropdown-item item-menu" >Correspondencia</a>
                  <ul class="dropdown-menu">
                     <li><a class="item-menu" href="{{url('/administrativo/muebles/create')}}">Entrada</a></li>
                     <li><a class="item-menu" href="{{url('/administrativo/inventario/create')}}">Salida</a></li>
                  </ul>
               </li>
                <li class="dropdown-submenu">
                  <a class="dropdown-item item-menu" >Normatividad Interna</a>
                  <ul class="dropdown-menu">
                     <li><a class="item-menu" href="{{route('carpetas.listar', 'Acuerdos')}}">Acuerdos</a></li>
                     <li><a class="item-menu" href="">Resoluciones</a></li>{{--falta url--}}
                     <li><a class="item-menu" href="">Decretos</a></li>{{--falta url--}}
                  </ul>
               </li>
               <li><a class="item-menu" tabindex="-1" href="{{route('carpetas.listar', 'Boletines')}}">Boletines</a></li>

               <li><a class="item-menu" tabindex="-1" href="{{route('carpetas.listar', 'Otros')}}">Otros</a></li>
            </ul>
         </li>
         {{--
         <li class="dropdown">
            <a class="btn btn-default btn-sm dropdown-toggle" type="button" data-toggle="dropdown">
            Cobro Coactivo
            <span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
               <li><a tabindex="-1" href="{{url('/predios')}}">Predial</a></li>
               <li><a tabindex="-1" href="{{url('/personas')}}">Personas</a></li>
            </ul>
         </li>
         <li >
            <a class="btn btn-default btn-sm item-menu" href="{{ url('/contractual') }}">
             CONTRATACIÓN
            </a>
         </li>
         --}}
@endif
@if(auth()->user()->id != 54)
   @if(auth()->user()->roles->first()->id != 7)
      @if(auth()->user()->roles->first()->id != 8 && auth()->user()->roles->first()->id != 9)
         <li >
            <a class="btn btn-default btn-sm item-menu" href="{{ url('/presupuesto') }}">
               PRESUPUESTO
            </a>
         </li>
      @endif
   @endif
@endif

{{-- <li class="dropdown ">
   <a class="btn btn-default btn-sm dropdown-toggle item-menu" type="button" data-toggle="dropdown" data-submenu="">
   PRESUPUESTO
   <span class="caret"></span>
   </a>
   <ul class="dropdown-menu">
       <li><a class="item-menu" tabindex="-1" href="{{ url('/presupuesto') }}">Presupuesto Gastos 2019</a></li>
      
      <li class="dropdown-submenu">
         <a class="dropdown-item item-menu " href="#">Informes</a>
         <ul class="dropdown-menu">
            <li><a class="item-menu" href="#">Contractual</a></li>
            <li><a class="item-menu" href="#">FUT</a></li>
            <li><a class="item-menu" href="#">Comparativo (Ingresos-Gastos)</a></li>
         </ul>
      </li>
   
   </ul>
</li> --}}
  

    
{{--
<li class="dropdown ">
   <a class="btn btn-default btn-sm item-menu" href="{{ url('/admin/ordenDia') }}">
   ORDEN DEL DÍA
   </a>
</li>

<li class="dropdown ">
   <a class="btn btn-default btn-sm item-menu" href="{{ url('/dashboard/concejales') }}">
   CONCEJALES
   </a>
</li>
--}}
   @if(auth()->user()->roles->first()->id == 1)
      <li class="dropdown ">
         <a class="btn btn-default btn-sm dropdown-toggle item-menu" type="button" data-toggle="dropdown">
         ALMACEN
         <span class="caret"></span>
         </a>
         <ul class="dropdown-menu">
            <li><a class="item-menu" tabindex="-1" href="{{route('almacen.inventario')}}">Inventorio</a></li>

            <li><a class="item-menu" tabindex="-1" href="{{route('almacen.comprobante.ingreso')}}">Comprobante de Ingreso</a></li>

            <li><a class="item-menu" tabindex="-1" href="{{route('almacen.comprobante.egreso')}}">Comprobante de egresos</a></li>
         </ul>
      {{--
         <ul class="dropdown-menu">
            <li><a class="item-menu" tabindex="-1" href="{{url('/administrativo/muebles')}}">Bienes, Muebles e Inmuebles</a></li>
            <li class="dropdown-submenu">
               <a class="dropdown-item item-menu" >Comprobantes de Entrada </a>
               <ul class="dropdown-menu">
                  <li><a class="item-menu" href="{{url('/administrativo/muebles/create')}}">Bienes, Muebles e Inmuebles</a></li>
                  <li><a class="item-menu" href="{{url('/administrativo/inventario/create')}}">Inventario</a></li>
               </ul>
            </li>
            <li><a class="item-menu" tabindex="-1" href="{{url('/administrativo/salida/create')}}">Comprobante de Salida</a></li>
            <li class="dropdown-submenu">
               <a class="dropdown-item item-menu" >Comprobantes</a>
               <ul class="dropdown-menu">
                  <li><a class="item-menu" href="#">Baja de Inmueble</a></li>
                  <li><a class="item-menu" href="#">Asignación</a></li>
                  <li><a class="item-menu" href="#">Devolución</a></li>
                  <li><a class="item-menu" href="#">Corrección</a></li>
               </ul>
            </li>
            <li><a class="item-menu" tabindex="-1" href="{{url('/administrativo/productos')}}">Productos</a></li>
            <li><a class="item-menu" tabindex="-1" href="{{url('/administrativo/inventario')}}">Inventario</a></li>
         </ul>
      --}}
      </li>
   @endif

   @if(auth()->user()->roles->first()->id == 1 or auth()->user()->roles->first()->id == 7)
      <li class="dropdown ">
         <a class="btn btn-default btn-sm dropdown-toggle item-menu" type="button" data-toggle="dropdown">
         CONTABILIDAD
         <span class="caret"></span>
         </a>

         <ul class="dropdown-menu">
            {{-- <li class="dropdown-submenu">
               <a class="dropdown-item item-menu" href="#" >PUC</a>
               <ul class="dropdown-menu">
               <li><a class="item-menu" tabindex="-1" href="{{url('/administrativo/contabilidad/puc/pucIndex')}}">PUC Res. 3832 de 2019</a></li>

                  <li><a class="item-menu" tabindex="-1" href="{{url('/administrativo/contabilidad/puc/pucIndexAct')}}">PUC de vigencia</a></li>
               </ul>
            </li> --}}
            <li><a class="item-menu" tabindex="-1" href="#">Comprobantes de Contabilidad</a></li>
            <li><a class="item-menu" tabindex="-1" href="#">Estado de Resultados</a></li>
            <li><a class="item-menu" tabindex="-1" href="#">Notas al Balance</a></li>
            <li><a class="item-menu" tabindex="-1" href="{{url('/administrativo/contabilidad/config')}}">Configuración</a></li>
            <li class="dropdown-submenu">
               <a class="dropdown-item item-menu" >Balances </a>
               <ul class="dropdown-menu">
                  <li><a class="item-menu" href="#">Prueba</a></li>
                  <li><a class="item-menu" href="#">Terceros</a></li>
                  <li><a class="item-menu" href="{{url('/administrativo/contabilidad/informes/lvl/1')}}">General</a></li>
               </ul>
            </li>
            <li><a class="item-menu" tabindex="-1" href="{{url('/administrativo/contabilidad/libros')}}">Libros</a></li>
            <li><a class="item-menu" tabindex="-1" href="#">NICP</a></li>
            <li><a class="item-menu" tabindex="-1" href="{{url('/administrativo/contabilidad/puc')}}">PUC</a></li>
            <li class="dropdown-submenu">
               <a class="dropdown-item item-menu" >Retención en la Fuente</a>
               <ul class="dropdown-menu">
                  <li><a class="item-menu" href="{{ url('/administrativo/tesoreria/retefuente/certificado') }}">Certificado</a></li>
                  <li class="dropdown-submenu">
                     <a class="dropdown-item item-menu" >Pago </a>
                     <ul class="dropdown-menu">
                        <li class="dropdown-submenu">
                           <a class="dropdown-item item-menu" >2023 </a>
                           <ul class="dropdown-menu">
                              <li><a class="item-menu" href="{{ url('/administrativo/tesoreria/retefuente/pago/11/1') }}">Enero</a></li>
                              <li><a class="item-menu" href="{{ url('/administrativo/tesoreria/retefuente/pago/11/2') }}">Febrero</a></li>
                           </ul>
                        </li>
                     </ul>
                  </li>
               </ul>
            </li>
         </ul>
      </li>
   @endif
   @if(auth()->user()->roles->first()->id == 1 or auth()->user()->roles->first()->id == 8 or auth()->user()->roles->first()->id == 4)
      <li class="dropdown ">
         <a class="btn btn-default btn-sm dropdown-toggle item-menu" type="button" data-toggle="dropdown">
         TESORERIA
         <span class="caret"></span>
         </a>
         <ul class="dropdown-menu">
            @if(auth()->user()->id != 54)
               <li class="dropdown-submenu">
                  <a class="dropdown-item item-menu" >Comprobante de Ingresos </a>
                  <ul class="dropdown-menu">
                     <li><a class="item-menu" href="{{ url('/administrativo/CIngresos/4') }}">2020</a></li>
                     <li><a class="item-menu" href="{{ url('/administrativo/CIngresos/8') }}">2022</a></li>
                     <li><a class="item-menu" href="{{ url('/administrativo/CIngresos/12') }}">2023</a></li>
                  </ul>
               </li>
               <li><a class="item-menu" tabindex="-1" href="{{ url('/administrativo/tesoreria/notasCredito') }}">Notas Credito </a></li>
               <li class="dropdown-submenu">
                  <a class="dropdown-item item-menu" >Ordenes de Pagos </a>
                  <ul class="dropdown-menu">
                     <li><a class="item-menu" href="{{ url('/administrativo/ordenPagos/3') }}">2020</a></li>
                     <li><a class="item-menu" href="{{ url('/administrativo/ordenPagos/5') }}">2021</a></li>
                     <li><a class="item-menu" href="{{ url('/administrativo/ordenPagos/7') }}">2022</a></li>
                     <li><a class="item-menu" href="{{ url('/administrativo/ordenPagos/11') }}">2023</a></li>
                  </ul>
               </li>
               <li><a class="item-menu" tabindex="-1" href="{{url('#')}}">Pago a Terceros</a></li>
               <li><a class="item-menu" tabindex="-1" href="{{url('#')}}">Pago Nomina</a></li>
               <!-- <li><a class="item-menu" tabindex="-1" href="{{url('/administrativo/contabilidad/retefuente')}}">Retención en la Fuente</a></li> -->
               <li><a class="item-menu" tabindex="-1" href="#">Informes</a></li>
               <li class="dropdown-submenu">
                  <a class="dropdown-item item-menu" >Bancos</a>
                  <ul class="dropdown-menu">
                     <li><a class="item-menu" href="#">Libro</a></li>
                     <li><a class="item-menu" href="{{ url('/administrativo/tesoreria/bancos/conciliacion') }}">Conciliación bancaria</a></li>
                  </ul>
               </li>
               <!--  <li><a class="item-menu" tabindex="-1" href="{{ url('/administrativo/bancos') }}">Bancos</a></li> -->
               <li><a class="item-menu" tabindex="-1" href="{{ url('/administrativo/pac') }}">PAC</a></li>
               <li class="dropdown-submenu">
                  <a class="dropdown-item item-menu" >Comprobante de Egresos </a>
                  <ul class="dropdown-menu">
                     <li><a class="item-menu" href="{{ url('/administrativo/pagos/3') }}">2020</a></li>
                     <li><a class="item-menu" href="{{ url('/administrativo/pagos/5') }}">2021</a></li>
                     <li><a class="item-menu" href="{{ url('/administrativo/pagos/7') }}">2022</a></li>
                     <li><a class="item-menu" href="{{ url('/administrativo/pagos/11') }}">2023</a></li>
                  </ul>
               </li>
            @else
               <li class="dropdown-submenu">
                  <a class="dropdown-item item-menu" >Impuestos</a>
                  <ul class="dropdown-menu">
                     <li><a class="item-menu" href="{{ url('/administrativo/impuestospredial/liquidador') }}">Liquidador</a></li>
                     <li><a class="item-menu" href="{{ url('/administrativo/impuestos/muellaje') }}">Muellaje</a></li>
                     <li><a class="item-menu" href="{{ url('/administrativo/impuestos/delineacion') }}">Delineación y Urbanismo</a></li>
                     <li><a class="item-menu" href="{{url('/administrativo/impuestos/admin')}}">Administración Impuestos</a></li>
                     <li><a class="item-menu" href="{{url('/administrativo/contabilidad/impumuni')}}">Impuestos Municipales</a></li>
                     <li><a class="item-menu" href="{{url('/administrativo/impuestos/pagos')}}">Pagos</a></li>
                  </ul>
               </li>
            @endif
         </ul>
      </li>
   @endif

   @if(auth()->user()->roles->first()->id == 1)
      <li class="dropdown ">
         <a class="btn btn-default btn-sm dropdown-toggle item-menu" type="button" data-toggle="dropdown" data-submenu="">
         JURIDICA
         <span class="caret"></span>
         </a>
         <ul class="dropdown-menu">
            <li class="dropdown-submenu">
               <a class="dropdown-item item-menu" href="#" >Cobro Coactivo </a>
               <ul class="dropdown-menu">
                  <li><a class="item-menu" href="#">Predial</a></li>
                  <li><a class="item-menu" href="#">Industria y Comercio (ICA)</a></li>
                  <li><a class="item-menu" href="#">Comparendos</a></li>
                  <li><a class="item-menu" href="#">Convivencia</a></li>
                  <li><a class="item-menu" href="#">Otros</a></li>
               </ul>
            </li>
            <li class="dropdown-submenu">
               <a class="dropdown-item item-menu " href="#">Demandas</a>
               <ul class="dropdown-menu">
                  <li><a class="item-menu" href="#">Demandante</a></li>
                  <li><a class="item-menu" href="#">Demandado</a></li>
                  <li><a class="item-menu" href="#">Conciliaciones</a></li>
               </ul>
            </li>
            <li><a class="item-menu" tabindex="-1" href="">Policivos</a></li>
            <li><a class="item-menu" tabindex="-1" href="{{ url('/contractual') }}">Contratos</a></li>
         </ul>
      </li>
   @endif
   @if(auth()->user()->roles->first()->id != 8 && auth()->user()->roles->first()->id != 9)
      <li class="dropdown ">
         <a class="btn btn-default btn-sm dropdown-toggle item-menu" type="button" data-toggle="dropdown" title="Configuración">
         <i class="fa fa-cogs" aria-hidden="true"></i>
         <span class="caret"></span>
         </a>
         <ul class="dropdown-menu">
            @if(auth()->user()->roles->first()->id == 1)
               <li><a class="item-menu" id="google_translate_element"></a></li>

               <li><a class="item-menu" style="cursor: pointer" tabindex="-1" data-toggle="modal" data-target="#updateSoftware">Actualizaciones a la Plataforma</a></li>

               <li><a class="item-menu" tabindex="-1" href="{{ route('configGeneral.index') }}">Configuración General</a></li>

               <li><a class="item-menu" tabindex="-1" href="{{ route('dependencias.index') }}">Gestión de Dependencias</a></li>

               <li><a class="hidden"  tabindex="-1" href="{{ route('rutas.index') }}">Rutas</a></li>

               @can('funcionario-list')
               <li><a class="item-menu" tabindex="-1" href="{{ route('funcionarios.index') }}">Gestión de Funcionarios</a></li>
               @endcan

               @can('role-list')
               <li><a class="item-menu" tabindex="-1" href="{{ route('roles.index') }}">Gestión de Roles</a></li>
               @endcan

                @can('role-list')
               <li><a class="item-menu" tabindex="-1" href="{{ route('modulos.index') }}">Gestión de Modulos</a></li>
               @endcan
            @endif

            <li><a class="item-menu" tabindex="-1" href="{{route('personas.index')}}">Terceros</a></li>
               @if(auth()->user()->roles->first()->id == 1)
                  <li><a class="item-menu" tabindex="-1" href="{{route('audits.index')}}">Logs</a></li>
               @endif
         </ul>
      </li>
   @endif
<li class="dropdown messages-menu">
    @include('layouts.cuerpo.perfil')
</li>

