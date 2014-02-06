<?php
require_once("../shared/class_folder/class_sql.php");
class sigesp_sim_c_unidadmedida
{
	var $obj="";
	var $io_sql;
	var $siginc;
	var $con;

	function sigesp_sim_c_unidadmedida()
	{
		require_once("../shared/class_folder/class_datastore.php");
		require_once("../shared/class_folder/class_mensajes.php");
		require_once("../shared/class_folder/sigesp_include.php");
		require_once("../shared/class_folder/sigesp_c_seguridad.php");
		$in=new sigesp_include();
		$this->con=$in->uf_conectar();
		$this->io_sql=new class_sql($this->con);
		$this->seguridad= new sigesp_c_seguridad();
		$this->io_msg=    new class_mensajes();
		$this->io_funcion=new class_funciones();
	}
	
	function uf_sim_select_unidadmedida($as_codunimed)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_sim_select_unidadmedida
		//         Access: public (sigesp_sim_d_unidadmedida)
		//      Argumento: $as_codunimed    // codigo de unidad de medida
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que realiza una busqueda de una unidad de medida en la tabla de  sim_unidadmedida
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci�n: 10/02/2006							Fecha �ltima Modificaci�n : 10/02/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql = "SELECT * FROM sim_unidadmedida  ".
				  " WHERE codunimed='".$as_codunimed."'" ;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->unidadmedida M�TODO->uf_sim_select_unidadmedida ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
				$this->io_sql->free_result($rs_data);
			}
			else
			{
				$lb_valido=false;
			}
		}
		return $lb_valido;
	} // end function uf_sim_select_unidadmedida

	function  uf_sim_insert_unidadmedida($as_codunimed,$as_denunimed,$as_unidad,$as_obsunimed,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_sim_insert_unidadmedida
		//         Access: public (sigesp_sim_d_unidadmedida)
		//      Argumento: $as_codunimed    // codigo de unidad de medida
		//  			   $as_denunimed    // denominacion de unidad de medida
		//  			   $as_unidad 		// unidad de medida
		//  			   $as_obsunimed    // observacion de unidad de medida
		//  			   $aa_seguridad    // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que inserta una unidad de medida en la tabla de  sim_unidadmedida
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci�n: 10/02/2006							Fecha �ltima Modificaci�n : 10/02/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql = "INSERT INTO sim_unidadmedida (codunimed, denunimed, unidad, obsunimed) ".
					" VALUES('".$as_codunimed."','".$as_denunimed."','".$as_unidad."','".$as_obsunimed."')" ;
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_msg->message("CLASE->unidadmedida M�TODO->uf_sim_insert_unidadmedida ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
			$this->io_sql->rollback();
		}
		else
		{
				$lb_valido=true;
				/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				$ls_evento="INSERT";
				$ls_descripcion ="Insert� la Unidad de Medida ".$as_codunimed;
				$ls_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
												$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
												$aa_seguridad["ventanas"],$ls_descripcion);
				/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				$this->io_sql->commit();
		}
		return $lb_valido;
	} // end  function  uf_sim_insert_unidadmedida

	function uf_sim_update_unidadmedida($as_codunimed,$as_denunimed,$as_unidad,$as_obsunimed,$aa_seguridad) 
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_sim_update_unidadmedida
		//         Access: public (sigesp_sim_d_unidadmedida)
		//      Argumento: $as_codunimed    // codigo de unidad de medida
		//  			   $as_denunimed    // denominacion de unidad de medida
		//  			   $as_unidad 		// unidad de medida
		//  			   $as_obsunimed    // observacion de unidad de medida
		//  			   $aa_seguridad    // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que modifica una unidad de medida en la tabla de  sim_unidadmedida
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci�n: 10/02/2006							Fecha �ltima Modificaci�n : 10/02/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		 $lb_valido=true;
		 $ls_sql = "UPDATE sim_unidadmedida SET   denunimed='". $as_denunimed ."',".
					" unidad='". $as_unidad ."',".
					" obsunimed='". $as_obsunimed ."' ". 
					" WHERE codunimed='" . $as_codunimed ."' ";
        $this->io_sql->begin_transaction();
		$li_row = $this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_msg->message("CLASE->unidadmedida M�TODO->uf_sim_update_unidadmedida ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
			$this->io_sql->rollback();
		}
		else
		{
			$lb_valido=true;
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="UPDATE";
			$ls_descripcion ="Modific� la Unidad de Medida ".$as_codunimed;
			$ls_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$this->io_sql->commit();
		}
	  return $lb_valido;
	} // end function uf_sim_update_unidadmedida

	function uf_sim_delete_unidadmedida($as_codunimed,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_sim_delete_unidadmedida
		//         Access: public (sigesp_sim_d_unidadmedida)
		//      Argumento: $as_codunimed    // codigo de unidad de medida
		//				   $aa_seguridad    // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que elimina una unidad de medida en la tabla de  sim_unidadmedida
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci�n: 10/02/2006							Fecha �ltima Modificaci�n : 10/02/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$lb_existe= $this->uf_sim_select_articulounidad($as_codunimed);
		if($lb_existe)
		{
			$this->io_msg->message("La Unidad de Medida tiene articulos asociados");		
			$lb_valido=false;
		}
		else
		{
			$this->io_sql->begin_transaction();	
			$ls_sql = " DELETE FROM sim_unidadmedida".
						 " WHERE codunimed= '".$as_codunimed. "'"; 
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$this->io_msg->message("CLASE->unidadmedida M�TODO->uf_sim_update_unidadmedida ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
				$lb_valido=false;
				$this->io_sql->rollback();
			}
			else
			{
				$lb_valido=true;
				/////////////////////////////////         SEGURIDAD               /////////////////////////////
				$ls_evento="DELETE";
				$ls_descripcion ="Elimin� la Unidad de Medida ".$as_codunimed;
				$ls_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
												$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
												$aa_seguridad["ventanas"],$ls_descripcion);
				/////////////////////////////////         SEGURIDAD               /////////////////////////////			
				$this->io_sql->commit();
			}
		}			
		return $lb_valido;
	}  // end function uf_sim_delete_unidadmedida

	function uf_sim_select_articulounidad($as_codunimed)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_sim_delete_unidadmedida
		//         Access: private
		//      Argumento: $as_codunimed    // codigo de unidad de medida
		//	      Returns: Retorna un Booleano
		//    Description: Funcion verifica si existen articulos que esten utilizando una determinada unidad de medida
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci�n: 10/02/2006							Fecha �ltima Modificaci�n : 10/02/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql = "SELECT * FROM sim_articulo  ".
				  " WHERE codunimed='".$as_codunimed."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->unidadmedida M�TODO->uf_sim_select_articulounidad ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
				$this->io_sql->free_result($rs_data);
			}
			else
			{
				$lb_valido=false;
			}
		}
		return $lb_valido;
	} // end function uf_sim_select_articulounidad

} // end  class sigesp_sim_c_unidadmedida
?>
