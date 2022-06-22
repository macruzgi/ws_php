<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/*
Modelo estandarizado y creado por MaCruz-Gi
mail:giancarlos1029@hotmail.com
Modelo que solo inserta registros.
*/
class Transaccionesinsertar extends CI_Model {
	public function InsertarTransaccion($pa_InsertarTransaccion){
		$resultado = $this->db->query("CALL pa_InsertarTransaccion(?,?,?,?)", $pa_InsertarTransaccion);
		return $resultado->row()->MENSAJE;
	}
}
