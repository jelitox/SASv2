<?php
	session_start();
	if(!array_key_exists("la_logusr",$_SESSION))
	{
		print "<script language=JavaScript>";
		print "close();";
		print "opener.document.form1.submit();";
		print "</script>";		
	}

   //--------------------------------------------------------------
   function uf_print($as_codconc, $as_nomcon, $as_tipo)
   {
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print
		//		   Access: public
		//	    Arguments: as_codconc  // C�digo del concepto
		//				   as_nomcon  // nombre del concepto
		//				   as_tipo  // Tipo de Llamada del cat�logo
		//	  Description: Funci�n que obtiene e imprime los resultados de la busqueda
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 01/01/2006 								Fecha �ltima Modificaci�n : 
		//////////////////////////////////////////////////////////////////////////////
		global $io_fun_nomina;
		require_once("../shared/class_folder/sigesp_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		require_once("../shared/class_folder/class_sql.php");
		$io_sql=new class_sql($io_conexion);	
		require_once("../shared/class_folder/class_mensajes.php");
		$io_mensajes=new class_mensajes();		
		require_once("../shared/class_folder/class_funciones.php");
		$io_funciones=new class_funciones();		
        $ls_codemp=$_SESSION["la_empresa"]["codemp"];
        $ls_codnom=$_SESSION["la_nomina"]["codnom"];
		print "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
		print "<tr class=titulo-celda>";
		print "<td width=80>C�digo</td>";
		print "<td width=300>Nombre</td>";
		print "<td width=120>Signo</td>";
		print "</tr>";
		$ls_sql="SELECT sno_primaconcepto.codconc, sno_primaconcepto.anopri, sno_primaconcepto.valpri, sno_concepto.nomcon, ".
				"		sno_concepto.sigcon ".
				"  FROM sno_primaconcepto, sno_concepto ".
				" WHERE sno_primaconcepto.codemp='".$ls_codemp."'".
				"   AND sno_primaconcepto.codnom='".$ls_codnom."'".
				"   AND sno_concepto.codconc like '".$as_codconc."' AND sno_concepto.nomcon like '".$as_nomcon."'".
				"   AND sno_primaconcepto.codemp=sno_concepto.codemp ".
				"   AND sno_primaconcepto.codnom=sno_concepto.codnom ".
				"   AND sno_primaconcepto.codconc=sno_concepto.codconc ".
				" ORDER BY sno_primaconcepto.codconc";
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
        	$io_mensajes->message("ERROR->".$io_funciones->uf_convertirmsg($io_sql->message)); 
		}
		else
		{
			while($row=$io_sql->fetch_row($rs_data))
			{
				$ls_codconc=$row["codconc"];
				$ls_nomcon=$row["nomcon"];
				$ls_sigcon=$row["sigcon"];
				$li_anopri=$row["anopri"];
				$li_valpri=$row["valpri"];
				$li_valpri=$io_fun_nomina->uf_formatonumerico($li_valpri);
	
				switch ($ls_sigcon)
				{
					case "A":
						$ls_sigcon="Asignaci�n";
						break;
	
					case "D":
						$ls_sigcon="Deducci�n";
						break;
	
					case "P":
						$ls_sigcon="Aporte Patronal";
						break;
	
					case "R":
						$ls_sigcon="Reporte";
						break;
	
					case "B":
						$ls_sigcon="Reintegro Deducci�n";
						break;
	
					case "E":
						$ls_sigcon="Reintegro Asignaci�n";
						break;
				}			
	
				switch ($as_tipo)
				{
					case "":
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptar('$ls_codconc','$ls_nomcon','$ls_sigcon','$li_anopri','$li_valpri');\">".$ls_codconc."</a></td>";
						print "<td>".$ls_nomcon."</td>";
						print "<td>".$ls_sigcon."</td>";
						print "</tr>";			
						break;						
				}
			}
			$io_sql->free_result($rs_data);
		}
		print "</table>";
		unset($io_include);
		unset($io_conexion);
		unset($io_sql);
		unset($io_mensajes);
		unset($io_funciones);
		unset($ls_codemp);
		unset($ls_codnom);
   }
   //--------------------------------------------------------------
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Cat&aacute;logo de Prima Concepto</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
<!--
a:link {
	color: #006699;
}
a:visited {
	color: #006699;
}
a:active {
	color: #006699;
}
-->
</style>
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
</head>

<body>
<form name="form1" method="post" action="">
  <p align="center">
    <input name="operacion" type="hidden" id="operacion">
</p>
  <table width="500" border="0" align="center" cellpadding="1" cellspacing="1">
    <tr>
      <td width="496" height="20" colspan="2" class="titulo-ventana">Cat&aacute;logo de Prima Concepto </td>
    </tr>
  </table>
<br>
    <table width="500" border="0" cellpadding="1" cellspacing="0" class="formato-blanco" align="center">
      <tr>
        <td width="67" height="22"><div align="right">C&oacute;digo</div></td>
        <td width="431"><div align="left">
          <input name="txtcodconc" type="text" id="txtcodconc" size="30" maxlength="10" onKeyPress="javascript: ue_mostrar(this,event);">        
        </div></td>
      </tr>
      <tr>
        <td height="22"><div align="right">Descripci&oacute;n</div></td>
        <td><div align="left">
          <input name="txtnomcon" type="text" id="txtnomcon" size="30" maxlength="30" onKeyPress="javascript: ue_mostrar(this,event);">
        </div></td>
      </tr>
      <tr>
        <td height="22">&nbsp;</td>
        <td><div align="right"><a href="javascript: ue_search();"><img src="../shared/imagebank/tools20/buscar.gif" title='Buscar' alt="Buscar" width="20" height="20" border="0"> Buscar</a></div></td>
      </tr>
  </table>
  <br>
<?php
	require_once("class_folder/class_funciones_nomina.php");
	$io_fun_nomina=new class_funciones_nomina();
	$ls_operacion =$io_fun_nomina->uf_obteneroperacion();
	$ls_tipo=$io_fun_nomina->uf_obtenertipo();
	if($ls_operacion=="BUSCAR")
	{
		$ls_codconc="%".$_POST["txtcodconc"]."%";
		$ls_nomcon="%".$_POST["txtnomcon"]."%";
		uf_print($ls_codconc, $ls_nomcon, $ls_tipo);
	}
	else
	{
		$ls_codconc="%%";
		$ls_nomcon="%%";
		uf_print($ls_codconc, $ls_nomcon, $ls_tipo);
	}
	unset($io_fun_nomina);
?>
</div>
</form>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script language="JavaScript">
function aceptar(codconc,nomcon,sigcon,anopri,valpri)
{
	opener.document.form1.txtcodconc.value=codconc;
	opener.document.form1.txtcodconc.readOnly=true;
	opener.document.form1.txtnomcon.value=nomcon;
	opener.document.form1.txtnomcon.readOnly=true;
	opener.document.form1.txtsigcon.value=sigcon;
	opener.document.form1.txtsigcon.readOnly=true;
	opener.document.form1.txtanopri.value=anopri;
	opener.document.form1.txtanopri.readOnly=true;
	opener.document.form1.txtvalpri.value=valpri;
	opener.document.images["concepto"].style.visibility="hidden";
	opener.document.form1.existe.value="TRUE";		
	close();
}

function ue_mostrar(myfield,e)
{
	var keycode;
	if (window.event) keycode = window.event.keyCode;
	else if (e) keycode = e.which;
	else return true;
	if (keycode == 13)
	{
		ue_search();
		return false;
	}
	else
		return true
}

function ue_search()
{
	f=document.form1;
  	f.operacion.value="BUSCAR";
  	f.action="sigesp_sno_cat_primaconcepto.php?tipo=<?PHP print $ls_tipo;?>";
  	f.submit();
}
</script>
</html>
