<?php
/**
 * S.A.S
 *
 * Descripcion: Modelo para el manejo de beneficiarioes
 *
 * @category
 * @package     Models
 * @subpackage
 * @author      Javier León (jel1284@gmail.com)
 * @copyright   Copyright (c) 2014 UPTP / E.M.S. Arroz del Alba S.A. (http://autogestion.arrozdelalba.gob.ve) 
 */

//Load::models('sistema/usuario', 'personas/persona');

class beneficiario extends ActiveRecord {
    
    /**
     * Método para definir las relaciones y validaciones
     */
    protected function initialize() {
	$this->has_one('titular');	
	$this->belongs_to('persona');
      //  $this->has_one('usuario');
      //  $this->has_one('persona');

    }
   /**
     * Método que devuelve el inner join con el estado_usuario
     * @return string
     */
//    public static function getInnerEstado() {
//        return "INNER JOIN (SELECT usuario_id, CASE estado_usuario WHEN ".EstadoUsuario::COD_ACTIVO." THEN '".EstadoUsuario::ACTIVO."' WHEN ".EstadoUsuario::COD_BLOQUEADO." THEN '".EstadoUsuario::BLOQUEADO."' ELSE 'INDEFINIDO' END AS estado_usuario, descripcion FROM (SELECT * FROM estado_usuario ORDER BY estado_usuario.id DESC ) AS estado_usuario GROUP BY estado_usuario.usuario_id,estado_usuario.estado_usuario, descripcion) AS estado_usuario ON estado_usuario.usuario_id = usuario.id ";        
//    }
    /**
     * Método para obtener titulares
     * @return obj
     */
   public function obtener_beneficiarios($beneficiario) {
        if ($beneficiario != '') {
            $beneficiario = stripcslashes($beneficiario);
            $res = $this->find_all_by_sql("
select beneficiario.id,beneficiario.titular_id,beneficiario.persona_id,persona.nombre1,persona.apellido1,cast(persona.cedula as integer) 
from titular,beneficiario,persona where beneficiario.titular_id in (select id from titular) and persona.cedula like '%{$beneficiario}%' 
and beneficiario.persona_id = persona.id ");
            
            if ($res) {
                foreach ($res as $beneficiario) {
                    $beneficiarios[] = array('id'=>$beneficiario->id,'value'=>$beneficiario->cedula,'idnombre'=>$beneficiario->nombre1.' '.$beneficiario->nombre2.' '.$beneficiario->apellido1.' '.$beneficiario->apellido2);
                }
                return $beneficiarios;
            }
        }
        return array('no hubo coincidencias');
    }
        
    /**
     * Método para setear un Objeto
     * @param string    $method     Método a ejecutar (create, update)
     * @param array     $data       Array para autocargar el objeto
     * @param array     $optData    Array con con datos adicionales para autocargar
     */
    public static function setbeneficiario($method, $data=array(), $optData=array()) {
        $obj = new beneficiario($data);
        if(!empty($optData)) {
            $obj->dump_result_self($optData);
        }
        //Creo otro objeto para comparar si existe
        $old = new beneficiario($data);
        //$check = $old->_getbeneficiarioRegistrado('find_first');
        $check = false;
        if($check) { //Si existe
            if(empty($obj->id)) {
                $obj->id = $old->id; //Asigno el id del encontrado al nuevo
            } else { //Si se actualiza y existe otro con la misma información
                if($obj->id != $old->id) {
                    DwMessage::info('Lo sentimos, pero ya existe una persona registrada con el mismo número de identificación');
                    return FALSE;
                }
            }
            if($method=='create') { //Si se crea la persona, pero ya está registrada la actualizo
                $method = 'update';
            }
        }
        $rs = $obj->$method();
        return ($rs) ? $obj : FALSE;
    }

    /**
     * Método para listar beneficiarioes
     * @return obj
     */

    public function getListadobeneficiario($estado, $order='', $page=0) {
        $columns = 'beneficiario.*, persona.*';
        $join= 'INNER JOIN persona ON persona.id = beneficiario.persona_id ';        
//        $join.= 'INNER JOIN tipoempleado  ON  beneficiario.tipoempleado_id = tipoempleado.id ';   
//        $join.= 'INNER JOIN departamento  ON  beneficiario.departamento_id = departamento.id ';   

        $conditions = "";//Por el super usuario
                       
           
        
        if($page) {
            return $this->paginated("columns: $columns", "join: $join", "page: $page");
        } else {
            return $this->find("columns: $columns", "join: $join");
        }  
    }

    /**
     * Método para verificar si una persona ya se encuentra registrada
     * @return obj
     */
    protected function _getbeneficiarioRegistrado($method='count') {
        $conditions = "cedula = '$this->cedula'";
        $conditions.= (isset($this->id)) ? " AND id != $this->id" : '';
        if($method != 'count' && $method !='find_first') {
            $method = 'count';
        }
        return $this->$method("conditions: $conditions");
    }

    /**
     * Callback que se ejecuta antes de guardar/modificar
     */
    public function before_save() {
//        $this->tipoempleado_id = Filter::get($this->tipoempleado_id, 'numeric');
//        $this->fecha_ingreso = Filter::get($this->fecha_ingreso, 'string'); 
//        $this->profesion_id = Filter::get($this->profesion_id, 'numeric');
//        $this->departamento_id = Filter::get($this->departamento_id, 'numeric');
//        $this->cargo_id = Filter::get($this->cargo_id, 'numeric'); 
//        $this->observacion = Filter::get($this->observacion, 'string');
    }    

    
    /**
     * Método para obtener la información de un usuario
     * @return type
     */
    public function getInformacionbeneficiario($beneficiario) {
        $beneficiario = Filter::get($beneficiario, 'int');
        if(!$beneficiario) {
            return NULL;
        }
        $columns = 'beneficiario.*, persona.*, tipoempleado.id, tipoempleado.nombre as tipoe, departamento.id, departamento.nombre as departamento';
        $join= 'INNER JOIN persona ON persona.id = beneficiario.persona_id ';        
        $join.= 'INNER JOIN tipoempleado  ON  beneficiario.tipoempleado_id = tipoempleado.id ';   
        $join.= 'INNER JOIN departamento  ON  beneficiario.departamento_id = departamento.id ';   
//        $columnas = 'beneficiario.*, persona.cedula, persona.nombre1, persona.nombre2, persona.apellido1, persona.apellido2, persona.nacionalidad, persona.sexo, persona.fecha_nacimiento, persona.pais_id, persona.estado_id, persona.municipio_id, persona.parroquia_id, persona.direccion_habitacion, persona.estado_civil, persona.celular, persona.telefono, persona.correo_electronico, persona.grupo_sanguineo, persona.fotografia, estado_usuario.estado_usuario, estado_usuario.descripcion, sucursal.sucursal';
  //      $join = self::getInnerEstado();
//        $join.= 'INNER JOIN perfil ON perfil.id = usuario.perfil_id ';
//        $join.= 'INNER JOIN persona ON persona.id = usuario.persona_id ';               
//        $join.= 'LEFT JOIN sucursal ON sucursal.id = usuario.sucursal_id ';
        $condicion = "beneficiario.id = $beneficiario";        
        return $this->find_first("columns: $columns", "join: $join", "conditions: $condicion");
    } 
}
?>
