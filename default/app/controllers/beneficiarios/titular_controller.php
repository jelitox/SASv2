<?php
/**
 * S.A.S
 *
 * Descripcion: Controlador que se encarga de la gestión de los titulares del sistema
 *
 * @category    
 * @package     Controllers 
 * @author      Javier León (jel1284@gmail.com)
 * @copyright   Copyright (c) 2014 E.M.S. Arroz del Alba S.A. (http://autogestion.arrozdelalba.gob.ve)
 */

Load::models('beneficiarios/titular','personas/persona', 'sistema/usuario');
Load::models('params/pais', 'params/estado', 'params/municipio', 'params/parroquia');

class TitularController extends BackendController {
    
    /**
     * Método que se ejecuta antes de cualquier acción
     */
    protected function before_filter() {
        //Se cambia el nombre del módulo actual
        $this->page_module = 'Gestión de titulares';
    }
    
    /**
     * Método principal
     */
    public function index() {
        DwRedirect::toAction('listar');
    }
    
    /**
     * Método para buscar
     */
    public function buscar($field='nombre1', $value='none', $order='order.id.asc', $page=1) {        
        $page = (Filter::get($page, 'page') > 0) ? Filter::get($page, 'page') : 1;
        $field = (Input::hasPost('field')) ? Input::post('field') : $field;
        $value = (Input::hasPost('field')) ? Input::post('value') : $value;
        $titular = new Titular();
        $titulares = $titular->getAjaxTitular($field, $value, $order, $page);        
        if(empty($titulares->items)) {
            DwMessage::info('No se han encontrado registros');
        }
        $this->titulares = $titulares;
        $this->order = $order;
        $this->field = $field;
        $this->value = $value;
        $this->page_title = 'Búsqueda de titulares del sistema';        
    }
/**
     * Método para obtener titulares
     */
    
        //accion que busca en los titulares y devuelve el json con los datos
    public function autocomplete() {
        View::template(NULL);
        View::select(NULL);
        if (Input::isAjax()) { //solo devolvemos los estados si se accede desde ajax 
            $busqueda = Input::post('busqueda');
            $titulares = Load::model('beneficiarios/titular')->obtener_titulares($busqueda);
            die(json_encode($titulares)); // solo devolvemos los datos, sin template ni vista
            //json_encode nos devolverá el array en formato json ["aragua","carabobo","..."]
        }
    }
/**
     * Método para obtener patologias
     */
    
        //accion que busca en las patologias y devuelve el json con los datos
    public function autocomplete2() {
        View::template(NULL);
        View::select(NULL);
        if (Input::isAjax()) { //solo devolvemos los estados si se accede desde ajax 
            $busqueda = Input::post('busqueda');
            $patologias = Load::model('patologia')->obtener_patologias($busqueda);
            die(json_encode($patologias)); // solo devolvemos los datos, sin template ni vista
            //json_encode nos devolverá el array en formato json ["aragua","carabobo","..."]
        }
    }
    /**
     * Método para listar
     */
    public function listar($order='order.id.asc', $page='pag.1') { 
        $page = (Filter::get($page, 'page') > 0) ? Filter::get($page, 'page') : 1;
        $titular = new Titular();
        $this->titulares = $titular->getListadoTitular('todos', $order, $page);
        $this->order = $order;        
        $this->page_title = 'Listado de titulares del sistema';
    }
    
    /**
     * Método para agregar
     */
    public function agregar() {

        $pais = new Pais(); 
        $estado = new Estado(); 
        $municipio = new Municipio();
        if(Input::hasPost('persona') && Input::hasPost('titular')) {
            ActiveRecord::beginTrans();
            //Guardo la persona
            $persona = Persona::setPersona('create', Input::post('persona'));
            if($persona) {
                if(Titular::setTitular('create', Input::post('titular'), array('persona_id'=>$persona->id))) {
                    ActiveRecord::commitTrans();
                    DwMessage::valid('El titular se ha creado correctamente.');
                    return DwRedirect::toAction('listar');
                }
            } else {
                ActiveRecord::rollbackTrans();
            }
            $this->pais = $pais->getListadoPais();           
            $this->estado = $estado->getListadoEstado(); 
            $this->municipio = $municipio->getListadoMunicipio(); 
        }
        $this->page_title = 'Agregar Titular';
    }
    
    /**
     * Método para editar
     */
    public function editar($key) {        
        if(!$id = DwSecurity::isValidKey($key, 'upd_titular', 'int')) {
            return DwRedirect::toAction('listar');
        }
        
        $titular = new Titular();
        if(!$titular->getInformacionTitular($id)) {
            DwMessage::get('id_no_found');    
            return DwRedirect::toAction('listar');
        }                
        
        if(Input::hasPost('titular')) {
            if(DwSecurity::isValidKey(Input::post('titular_id_key'), 'form_key')) {
                ActiveRecord::beginTrans();
                //Guardo la persona
                $persona = Persona::setPersona('update', Input::post('persona'), array('id'=>$titular->persona_id));
                if($persona) {
                    if(Titular::setTitular('update', Input::post('titular'), array('id'=>$titular->persona_id))) {
                        ActiveRecord::commitTrans();
                        DwMessage::valid('El titular se ha actualizado correctamente.');
                        return DwRedirect::toAction('listar');
                    }
                } else {
                    ActiveRecord::rollbackTrans();
                } 
            }
        }        
        $this->temas = DwUtils::getFolders(dirname(APP_PATH).'/public/css/backend/themes/');
        $this->titular = $titular;
        $this->page_title = 'Actualizar titular';
        
    }
    
    /**
     * Método para inactivar/reactivar
     */
    public function estado($tipo, $key) {
        if(!$id = DwSecurity::isValidKey($key, $tipo.'_usuario', 'int')) {
            return DwRedirect::toAction('listar');
        } 
        
        $usuario = new Usuario();
        if(!$usuario->getInformacionUsuario($id)) {
            DwMessage::get('id_no_found');    
            return DwRedirect::toAction('listar');
        }
        if($tipo == 'reactivar' && $usuario->estado_usuario == EstadoUsuario::ACTIVO) {
            DwMessage::info('El usuario ya se encuentra activo.');
            return DwRedirect::toAction('listar');
        } else if($tipo == 'bloquear' && $usuario->estado_usuario == EstadoUsuario::BLOQUEADO) {
            DwMessage::info('El usuario ya se encuentra bloqueado.');
            return DwRedirect::toAction('listar');
        }  
        
        if(Input::hasPost('estado_usuario')) {            
            if(EstadoUsuario::setEstadoUsuario($tipo, Input::post('estado_usuario'), array('usuario_id'=>$usuario->id))) { 
                ($tipo=='reactivar') ? DwMessage::valid('El usuario se ha reactivado correctamente!') : DwMessage::valid('El usuario se ha bloqueado correctamente!');
                return DwRedirect::toAction('listar');
            }
        }  
        
        $this->page_title = ($tipo=='reactivar') ? 'Reactivación de usuario' : 'Bloqueo de usuario';
        $this->usuario = $usuario;
    }
    
    /**
     * Método para ver
     */
    public function ver($key) {        
        if(!$id = DwSecurity::isValidKey($key, 'shw_titular', 'int')) {
            return DwRedirect::toAction('listar');
        }
        
        $titular = new Titular();
        if(!$titular->getInformacionTitular($id)) {
            DwMessage::get('id_no_found');    
            return DwRedirect::toAction('listar');
        }                
        
        //$estado = new EstadoUsuario();
        //$this->estados = $estado->getListadoEstadoUsuario($usuario->id);
        
        //$acceso = new Acceso();
        //$this->accesos = $acceso->getListadoAcceso($usuario->id, 'todos', 'order.fecha.desc');
        
        $this->titular = $titular;
        $this->page_title = 'Información del titular';
        
    }
    
    /**
     * Método para subir imágenes
     */
    public function upload() {     
        $upload = new DwUpload('fotografia', 'img/upload/personas/');
        $upload->setAllowedTypes('png|jpg|gif|jpeg');
        $upload->setEncryptName(TRUE);
        $upload->setSize(170, 200, TRUE);
        if(!$data = $upload->save()) { //retorna un array('path'=>'ruta', 'name'=>'nombre.ext');
            $data = array('error'=>$upload->getError());
        }
        sleep(1);//Por la velocidad del script no permite que se actualize el archivo
        View::json($data);
    }


    public function getEstadoPais(){
       View::response('view'); 
       $this->pais_id=Input::post('pais_id');
    }


    public function getMunicipioEstado(){
       View::response('view'); 
       $this->estado_id=Input::post('estado_id');
    }

     public function getParroquiaMunicipio(){
       View::response('view'); 
       $this->municipio_id=Input::post('municipio_id');
    }

}

