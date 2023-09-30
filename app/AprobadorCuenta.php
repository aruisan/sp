<?php
    namespace App;

    use Illuminate\Database\Eloquent\Model;
    use Auth;
    use Carbon\Carbon;
    
    class AprobadorCuenta extends Model
    {
        public function tramiteCuenta(){
            return $this->belongsTo(TramiteCuenta::class, 'tramite_cuenta_id');
        }
    
        public function aprobadorUser(){
            return $this->belongsTo(AprobadorUser::class, 'aprobado_user_id');
        }
    
        public function getRowNextAttribute(){
            return AprobadorCuenta::where('id', '>', $this->id)->orderBy('id', 'asc')->first();
        }
    
        public function getVsDateAttribute(){
            $next =  AprobadorCuenta::where('id', '>', $this->id)->orderBy('id', 'asc')->first();
            if($next){
                return $next->recibido == null ? 0 : Carbon::parse($next->recibido)->diffForHumans(Carbon::parse($this->recibido));
            }else{
                return 0;
            }
        }
    
        public function getEstadoRecibidoAttribute(){
            $validar_usuario = AprobadorUser::where('id', $this->aprobado_user_id)->first();
            $array_anterior = AprobadorCuenta::where('tramite_cuenta_id', $this->tramite_cuenta_id)->where('id', '<', $this->id)->orderBy('id', 'asc')->get();
            $last = $array_anterior->last();
            $anterior_estado = $last ?  $last->estado : "Aprobado";
            if($anterior_estado == 'Aprobado' && $validar_usuario->user_id == Auth::user()->id){
                if($this->recibido != null){
                    if($this->estado == 'Aprobado'){
                        return $this->estado;
                    }else{
                        return '<div class="input-group">
                                      <div class="input-group-btn">
                                        <button type="button" class="btn btn-default dropdown-toggle"
                                                data-toggle="dropdown">
                                          Selecciona una Acción <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu pull-right" role="menu">
                                          <li><a href="javascript:devolver('.$this->id.')">Devolver</a></li>
                                          <li><a href="'.route('aprobador_cuentas.aprobar', [$this->id]).'">Aprobar</a></li>
                                          <li><a href="javascript:aplazar('.$this->id.')">Aplazar</a></li>
                                        </ul>
                                      </div>
                                    </div>';
                    }
                }else{
                    return '<div class="form-check">
                                <input type="checkbox" name="recibido" class="form-check-input" onchange="enviarRecibido('.$this->id.')">
                                <label class="form-check-label" for="exampleCheck1">Recibido</label>
                            </div>';
                }
            }else{
                if($this->recibido != null){
                    if($this->estado != null){
                        return $this->estado;
                    }else{
                        return '<i class="fa fa-check-square-o" aria-hidden="true"></i>';
                    }
                }else{
                    return '<i class="fa fa-square-o" aria-hidden="true"></i>';
                }
            }
    
            if($this->estado != null){
                return $this->estado;
            }else if($this->recibido != null){
                return 'Recibido';
            }else{
                return 'sin Accion';
            }
        }
    
    /*
        public function getValidarRecibidoAttribute(){
            $validar_usuario = AprobadorUser::where('id', $this->aprobado_user_id)->first();
            $anterior = AprobadorUser::where('id', '<', $this->id)->orderBy('id', 'asc')->first();
    
            if($anterior->estado != null){
                if($this->recibido != null){
                    return '<i class="fa fa-check-square-o" aria-hidden="true"></i>';
                }else{
                    if($validar_usuario->user_id == Auth::user()->id){
                        return false;                                                   ;
                    }else{
                        return '<i class="fa fa-square-o" aria-hidden="true"></i>';
                    }
                }
            }
            
        }
    
        public function getValidarEstadoAttribute(){
            $validar_usuario = AprobadorUser::where('id', $this->aprobado_user_id)->first();
            $anterior = AprobadorUser::where('id', '<', $this->id)->orderBy('id', 'asc')->first();
    
            if($anterior->estado != null){
                if($this->estado != null){
                    return $this->estado;
                }else{
                    if($validar_usuario->user_id == Auth::user()->id){
                        return false;                                                   ;
                    }else{
                        return '<i class="fa fa-square-o" aria-hidden="true"></i>';
                    }
                }
            }
        }
        */
    
    

    public function remitente(){
        return $this->belongsTo('App\User', 'remitente_id');
    }

    public function chequeosCuenta(){
        return $this->hasMany(ChequeoCuenta::class, 'tramite_cuenta_id');
    }

    public function AprobadoresCuenta(){
        return $this->hasMany(AprobadorCuenta::class, 'tramite_cuenta_id');
    }

    public function getFechaRecibidoAttribute(){
        return $this->created_at->format('Y-m-d');
    }
}
