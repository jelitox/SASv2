<?php
/**
 * infoalex
 * @category
 * @package     Models
 * @subpackage
 * @author      alexis borges
 * @copyright    
 */
class Medicina extends ActiveRecord {
    
    /**
     * Constante para definir el id de la oficina principal
     */
    const OFICINA_PRINCIPAL = 1;

    /**
     * Método para definir las relaciones y validaciones
     */
    protected function initialize() {
  /*      $this->belongs_to('empresa');
        $this->belongs_to('ciudad');
        $this->has_many('usuario');

        $this->validates_presence_of('sucursal', 'message: Ingresa el nombre de la sucursal');        
        $this->validates_presence_of('direccion', 'message: Ingresa la dirección de la sucursal.');
        $this->validates_presence_of('ciudad_id', 'message: Indica la ciudad de ubicación de la sucursal.');
    */            
    }  
    /**
     * Método para ver la información de una sucursal
     * @param int|string $id
     * @return Sucursal
     */
    public function getInformacionMedicina($id, $isSlug=false) {
        $id = ($isSlug) ? Filter::get($id, 'string') : Filter::get($id, 'numeric');
        $columnas = 'medicina.*';
        $join = '';
        $condicion ="medicina.id = '$id'";
        return $this->find_first("columns: $columnas", "join: $join", "conditions: $condicion");
    } 
    /**
     * Método para obtener medicinas
     * @return obj
     */
   public function obtener_medicinas($medicina) {
        if ($medicina != '') {
            $medicina = stripcslashes($medicina);
            $res = $this->find('columns: descripcion', "descripcion like '%{$medicina}%'");
            if ($res) {
                foreach ($res as $medicina) {
                    $medicinas[] = $medicina->descripcion;
                }
                return $medicinas;
            }
        }
        return array('no hubo coincidencias');
    }    
    /**
     * Método que devuelve las sucursales
     * @param string $order
     * @param int $page 
     * @return ActiveRecord
     */
    public function getListadoMedicina($order='order.descripcion.asc', $page='', $empresa=null) {
        $columns = 'medicina.*';
        $join = '';        
        //$conditions 
        $order = $this->get_order($order, 'medicina', array('medicina'=>array('ASC'=>'medicina.descripcion ASC, medicina.observacion ASC',
                                                                        'DESC'=>'medicina.descripcion DESC, medicina.observacion ASC')));
        if($page) {                
            return $this->paginated("columns: $columns", "join: $join", "order: $order", "page: $page");
        } else {
            return $this->find("columns: $columns", "join: $join", "order: $order", "page: $page");            
        }
    }
    
    /**
     * Método para setear
     * @param string $method Método a ejecutar (create, update, save)
     * @param array $data Array con la data => Input::post('model')
     * @param array $otherData Array con datos adicionales
     * @return Obj
     */
    public static function setMedicina($method, $data, $optData=null) {
        //Se aplica la autocarga
        $obj = new Medicina($data);
        //Se verifica si contiene una data adicional para autocargar
        if ($optData) {
            $obj->dump_result_self($optData);
        }   
        if($method!='delete') {
            //$obj->ciudad_id = Ciudad::setCiudad($obj->ciudad)->id;        
        }
        $rs = $obj->$method();
        
        return ($rs) ? $obj : FALSE;
    }

    /**
     * Método que se ejecuta antes de guardar y/o modificar     
     */
    public function before_save() {        
        $this->descripcion = Filter::get($this->descripcion, 'string');
        $conditions = "descripcion = '$this->descripcion'";
        $conditions.= (isset($this->id)) ? " AND id != $this->id" : '';
        if($this->count("conditions: $conditions")) {
            DwMessage::error('Lo sentimos, pero ya existe una Medicina registrada con el mismo nombre.');
            return 'cancel';
        }
        
    }
    
    /**
     * Callback que se ejecuta antes de eliminar
     */
    public function before_delete() {
        if($this->id == 1) { //Para no eliminar la información de sucursal
            DwMessage::warning('Lo sentimos, pero esta sucursal no se puede eliminar.');
            return 'cancel';
        }
    }
    
}
