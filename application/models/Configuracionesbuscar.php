<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/*
Modelo estandarizado y creado por MaCruz-Gi
mail:giancarlos1029@hotmail.com
Modelo que solo inserta registros.
*/
class Configuracionesbuscar extends CI_Model {
	public function TraerDatosConfiguraciones(){
		$resultado = $this->db->query("SELECT cliente_trans_modoentra, cliente_trans_terminalid, cliente_trans_retailerid, comid,
		comkey, comwrkstation
FROM conf_configuraciones limit 1");
		return $resultado->row();
	}
	public function ValidarAcceso($adm_usuario){
		$resultado = $this->db->query("SELECT usuario_nombre FROM adm_usuario WHERE usuario_clave =? AND id_usaurio=? AND usuario_estado = 1 limit 1", $adm_usuario);
		return $resultado->row();
	}
}