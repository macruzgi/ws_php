<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tasws extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function __construct(){
        parent::__construct();
        $this->load->library("Nusoap"); //cargando mi biblioteca
        $this->nusoap_server = new soap_server();
        $this->nusoap_server->configureWSDL("Tas WebService","urn:tasWS");
        //cargo db aqui ya que si la auto cargo me da error por la forma de programar el ws
		$this->load->database();
		//cargo el modelo
		$this->load->model("Transaccionesinsertar");
		$this->load->model("Configuracionesbuscar");
		/*
		cosumir cliente
		*/
		global /*$cliente,*/ $CI/*, $err*/;
		
    //registrando funciones
	$this->nusoap_server->register(
			"PETICION", //nombre de la funcion
			array(
				"CLIENTE_TRANS_TARJETAMAN"=>"xsd:string",
				"CLIENTE_TRANS_MONTO"=>"xsd:string",
				"CLIENTE_TRANS_AUDITNO"=>"xsd:string",
				"CLIENTE_TRANS_TARJETAVEN"=>"xsd:string",
				"CLIENTE_TRANS_RECIBOID"=>"xsd:string",
				"CLIENTE_TRANS_TOKENCVV"=>"xsd:string",
				"CLIENTE_TRANS_TRANTIME"=>"xsd:string",
				"CLIENTE_TRANS_TRANDATE"=>"xsd:string",
				"CLIENTE_TRANS_REFERENCIA"=>"xsd:string",
				"CLIENTE_TRANS_AUTORIZA"=>"xsd:string",
				
				"numero_de_meses_plazo"=>"xsd:string",
				"metodo_a_convocar"=>"xsd:string",
				"credenciales"=>"xsd:string"
				), //prametro de etrada y tipo de dato
			array("return"=>"xsd:string")//parametro de salida
	);
		
    }
	public function index(){
		 if($this->uri->rsegment(3) == 'wsdl'){
            $_SERVER['QUERY_STRING'] = 'wsdl';
        } else{
            //$_SERVER['QUERY_STRING'] = '';
			//este redirect evita que se vea la definicion del webservice
			redirect(base_url()."Tasws/wsdl");
        }
		//como estoy fuera del uso de los recursos de CI uso la referencia respectiva con get_instance
		$CI =& get_instance();
		function PETICION($CLIENTE_TRANS_TARJETAMAN, $CLIENTE_TRANS_MONTO, $CLIENTE_TRANS_AUDITNO, $CLIENTE_TRANS_TARJETAVEN, $CLIENTE_TRANS_RECIBOID, $CLIENTE_TRANS_TOKENCVV, $CLIENTE_TRANS_TRANTIME, $CLIENTE_TRANS_TRANDATE, $CLIENTE_TRANS_REFERENCIA, $CLIENTE_TRANS_AUTORIZA, $numero_de_meses_plazo, $metodo_a_convocar, $credenciales){
			global $CI;
			$respuesta = $CI->PETICION($CLIENTE_TRANS_TARJETAMAN, $CLIENTE_TRANS_MONTO, $CLIENTE_TRANS_AUDITNO, $CLIENTE_TRANS_TARJETAVEN, $CLIENTE_TRANS_RECIBOID, $CLIENTE_TRANS_TOKENCVV, $CLIENTE_TRANS_TRANTIME, $CLIENTE_TRANS_TRANDATE, $CLIENTE_TRANS_REFERENCIA, $CLIENTE_TRANS_AUTORIZA, $numero_de_meses_plazo, $metodo_a_convocar, $credenciales);
			return $respuesta;
		}
		
		 $this->nusoap_server->service(file_get_contents("php://input"));
	}
	// SIN USO validacion antigua
	public function Validar($datosRequest, $funcion){
		$datosRequest = json_decode($datosRequest);
		$nombreVariable = "";
		switch($datosRequest){
			case empty($datosRequest->CLIENTE_TRANS_TARJETAMAN):
				$nombreVariable = "CLIENTE_TRANS_TARJETAMAN: no tiene valor"; 
				break;
			case empty($datosRequest->CLIENTE_TRANS_MONTO) || $datosRequest->CLIENTE_TRANS_MONTO <= 0 :
				$nombreVariable = "CLIENTE_TRANS_MONTO: no tiene valor"; 
				break;
			case strlen($datosRequest->CLIENTE_TRANS_MONTO) > 12:
				$nombreVariable = utf8_decode("CLIENTE_TRANS_MONTO: |Sobrepasa longitud establecida 12 dígitos|");
				break;
			case empty($datosRequest->CLIENTE_TRANS_AUDITNO):
				$nombreVariable = "CLIENTE_TRANS_AUDITNO: no tiene valor"; 
				break;
			case strlen($datosRequest->CLIENTE_TRANS_AUDITNO) > 6:
				$nombreVariable = utf8_decode("CLIENTE_TRANS_AUDITNO: |Sobrepasa longitud establecida 6 dígitos|");
				break;
			case empty($datosRequest->CLIENTE_TRANS_TRANTIME) && $funcion =="MANCOMPRANORA":
				$nombreVariable = "CLIENTE_TRANS_TRANTIME: no tiene valor"; 
				break;
			case empty($datosRequest->CLIENTE_TRANS_TRANDATE) && $funcion =="MANCOMPRANORA":
				$nombreVariable = "CLIENTE_TRANS_TRANDATE: no tiene valor"; 
				break;
			case empty($datosRequest->CLIENTE_TRANS_REFERENCIA) && $funcion =="MANCOMPRANORA":
				$nombreVariable = "CLIENTE_TRANS_REFERENCIA: no tiene valor"; 
				break;
			case strlen(@$datosRequest->CLIENTE_TRANS_REFERENCIA) > 12 || strlen(@$datosRequest->CLIENTE_TRANS_REFERENCIA) < 12 && $funcion =="MANCOMPRANORA":
				$nombreVariable = utf8_decode("CLIENTE_TRANS_REFERENCIA: |Longitud establecida 12 dígitos|"); 
				break;
			case empty($datosRequest->CLIENTE_TRANS_AUTORIZA) && $funcion =="MANCOMPRANORA":
				$nombreVariable = "CLIENTE_TRANS_AUTORIZA: no tiene valor"; 
				break;
			case strlen(@$datosRequest->CLIENTE_TRANS_AUTORIZA) > 6 || strlen(@$datosRequest->CLIENTE_TRANS_AUTORIZA) < 6 && $funcion =="MANCOMPRANORA":
				$nombreVariable = utf8_decode("CLIENTE_TRANS_AUTORIZA: |Longitud establecida 6 dígitos|"); 
				break;
			case empty($datosRequest->CLIENTE_TRANS_TARJETAVEN):
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_TARJETAVEN: no tiene valor"; 
				break;
			case strlen($datosRequest->CLIENTE_TRANS_TARJETAVEN) > 4 || strlen($datosRequest->CLIENTE_TRANS_TARJETAVEN) < 4 :
				$nombreVariable = utf8_decode("CLIENTE_TRANS_TARJETAVEN: |Longitud establecida 4 dígitos|"); 
				break;
			case empty($datosRequest->CLIENTE_TRANS_MODOENTRA):
				$nombreVariable = "CLIENTE_TRANS_MODOENTRA: no tiene valor"; 
				break;
			case empty($datosRequest->CLIENTE_TRANS_TERMINALID):
				$nombreVariable = "CLIENTE_TRANS_TERMINALID: no tiene valor"; 
				break;
			case empty($datosRequest->CLIENTE_TRANS_RETAILERID):
				$nombreVariable = "CLIENTE_TRANS_RETAILERID: no tiene valor"; 
				break;
			case empty($datosRequest->CLIENTE_TRANS_RECIBOID):
				$nombreVariable = "CLIENTE_TRANS_RECIBOID: no tiene valor"; 
				break;
			case strlen($datosRequest->CLIENTE_TRANS_RECIBOID) > 6:
				$nombreVariable = utf8_decode("CLIENTE_TRANS_RECIBOID: |Sobrepasa longitud establecida 4 dígitos|"); 
				break;
			case empty($datosRequest->CLIENTE_TRANS_TOKENCVV) && $funcion != "MANCOMPRADEV" && $funcion != "MANCOMPRADEVR" :
				$nombreVariable = "CLIENTE_TRANS_TOKENCVV: no tiene valor"; 
				break;
		}
		return $nombreVariable;
	}
	//valido los datos que se enviaran al servidor remoto
	public function ValidarDatosRequest($datos, $tipoValidacion){
		//$respuesta = json_decode($datos);
		//print_r($datos); exit;
		switch($tipoValidacion){
			case "MANCOMPRANOR":
			case "MANCOMPRANORR":
			case "DFTCAPNO":
			case "MANCOMPRAMIL":
			case "MANCOMPRAMILR":
			case "MANCOMPRANORA":
			case "MANCOMPRADEV":
			case "MANCOMPRADEVR":
			case "MANCONMILLA":
			case "MANCOMPRAMILA":
			case "MANCOMPRAPLAA":
			case "MANCOMPRAPLA":
			case "MANCOMPRAPLAR":
				break;
			default:
				return utf8_decode("No hay ningún Método con el nombre: ".$tipoValidacion); exit;
		}
		$nombreVariable = "";
		$fal = 1;
		if($tipoValidacion== "MANCOMPRANOR" || $tipoValidacion =="MANCOMPRANORR" || $tipoValidacion == "DFTCAPNO" || $tipoValidacion == "MANCOMPRAMIL" || $tipoValidacion == "MANCOMPRAMILR"){
			if(empty($datos["CLIENTE_TRANS_TARJETAMAN"])){
				 $nombreVariable = "CLIENTE_TRANS_TARJETAMAN;";
				 $fal = 0;
				//return $nombreVariable; exit;
			}
			/*if(strlen($datos["CLIENTE_TRANS_TARJETAMAN"]) > 16){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_TARJETAMAN: |Sobrepasa longitud establecida 16 dígitos |;";
				 $fal = 0;
			}*/
			if(empty($datos["CLIENTE_TRANS_MONTO"]) || $datos["CLIENTE_TRANS_MONTO"] <= 0){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_MONTO;";
				 $fal = 0;
				//return $nombreVariable; exit;
			}
			if(strlen($datos["CLIENTE_TRANS_MONTO"]) > 12){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_MONTO: | Sobrepasa longitud establecida 12 dígitos |;";
				 $fal = 0;
			}
			if(empty($datos["CLIENTE_TRANS_AUDITNO"])){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_AUDITNO;";
				 $fal = 0;
			}
			if(strlen($datos["CLIENTE_TRANS_AUDITNO"]) > 6){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_AUDITNO: | Sobrepasa longitud establecida 6 dígitos |;";
				 $fal = 0;
			}
			if(empty($datos["CLIENTE_TRANS_TARJETAVEN"])){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_TARJETAVEN;";
				 $fal = 0;
			}
			if(strlen($datos["CLIENTE_TRANS_TARJETAVEN"]) > 4 || strlen($datos["CLIENTE_TRANS_TARJETAVEN"]) < 4){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_TARJETAVEN: | Longitud establecida 4 dígitos |;";
				 $fal = 0;
			}
			if(empty($datos["CLIENTE_TRANS_MODOENTRA"])){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_MODOENTRA;";
				 $fal = 0;
			}
			if(empty($datos["CLIENTE_TRANS_TERMINALID"])){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_TERMINALID;";
				 $fal = 0;
			}
			if(empty($datos["CLIENTE_TRANS_RETAILERID"])){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_RETAILERID;";
				 $fal = 0;
			}
			if(empty($datos["CLIENTE_TRANS_RECIBOID"])){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_RECIBOID;";
				 $fal = 0;
			}
			if(strlen($datos["CLIENTE_TRANS_RECIBOID"]) > 6){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_RECIBOID: | Sobrepasa longitud establecida 6 dígitos |;";
				 $fal = 0;
			}
			if(empty($datos["CLIENTE_TRANS_TOKENCVV_validar"])){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_TOKENCVV;";
				 $fal = 0;
			}
			
		}
		if($tipoValidacion== "MANCOMPRANORA"){
			if(empty($datos["CLIENTE_TRANS_TARJETAMAN"])){
				 $nombreVariable = "CLIENTE_TRANS_TARJETAMAN;";
				 $fal = 0;
				//return $nombreVariable; exit;
			}
			if(strlen($datos["CLIENTE_TRANS_TARJETAMAN"]) > 16){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_TARJETAMAN: | Sobrepasa longitud establecida 16 dígitos |;";
				 $fal = 0;
			}
			if(empty($datos["CLIENTE_TRANS_MONTO"]) || $datos["CLIENTE_TRANS_MONTO"] <= 0){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_MONTO;";
				 $fal = 0;
				//return $nombreVariable; exit;
			}
			if(strlen($datos["CLIENTE_TRANS_MONTO"]) > 12){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_MONTO: | Sobrepasa longitud establecida 12 dígitos |;";
				 $fal = 0;
			}
			if(empty($datos["CLIENTE_TRANS_AUDITNO"])){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_AUDITNO;";
				 $fal = 0;
			}
			if(strlen($datos["CLIENTE_TRANS_AUDITNO"]) > 6){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_AUDITNO: | Sobrepasa longitud establecida 6 dígitos |;";
				 $fal = 0;
			}
			if(empty($datos["CLIENTE_TRANS_TRANTIME"])){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_TRANTIME;";
				 $fal = 0;
			}
			if(empty($datos["CLIENTE_TRANS_TRANDATE"])){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_TRANDATE;";
				 $fal = 0;
			}
			if(empty($datos["CLIENTE_TRANS_TARJETAVEN"])){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_TARJETAVEN;";
				 $fal = 0;
			}
			if(strlen($datos["CLIENTE_TRANS_TARJETAVEN"]) > 4 || strlen($datos["CLIENTE_TRANS_TARJETAVEN"]) < 4){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_TARJETAVEN: | Longitud establecida 4 dígitos |;";
				 $fal = 0;
			}
			if(empty($datos["CLIENTE_TRANS_MODOENTRA"])){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_MODOENTRA;";
				 $fal = 0;
			}
			if(empty($datos["CLIENTE_TRANS_REFERENCIA"])){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_REFERENCIA;";
				 $fal = 0;
			}
			if(strlen($datos["CLIENTE_TRANS_REFERENCIA"]) > 12){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_REFERENCIA: | Sobrepasa longitud establecida 12 dígitos |;";
				 $fal = 0;
			}
			if(empty($datos["CLIENTE_TRANS_AUTORIZA"])){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_AUTORIZA;";
				 $fal = 0;
			}
			if(strlen($datos["CLIENTE_TRANS_AUTORIZA"]) > 6){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_AUTORIZA: | Sobrepasa longitud establecida 6 dígitos |;";
				 $fal = 0;
			}
			if(empty($datos["CLIENTE_TRANS_TERMINALID"])){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_TERMINALID;";
				 $fal = 0;
			}
			if(empty($datos["CLIENTE_TRANS_RETAILERID"])){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_RETAILERID;";
				 $fal = 0;
			}
			if(empty($datos["CLIENTE_TRANS_RECIBOID"])){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_RECIBOID;";
				 $fal = 0;
			}
			if(strlen($datos["CLIENTE_TRANS_RECIBOID"]) > 6){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_RECIBOID: | Sobrepasa longitud establecida 6 dígitos |;";
				 $fal = 0;
			}
			/*if(empty($respuesta->CLIENTE_TRANS_TOKENCVV)){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_TOKENCVV;";
				 $fal = 0;
			}*/
			/*if($fal ==0){
				return "Los datos requeridos: ".$nombreVariable." NO tienen valor"; exit;
			}*/
		}
		if($tipoValidacion== "MANCOMPRADEV" || $tipoValidacion== "MANCOMPRADEVR"){
			if(empty($datos["CLIENTE_TRANS_TARJETAMAN"])){
				 $nombreVariable = "CLIENTE_TRANS_TARJETAMAN;";
				 $fal = 0;
				//return $nombreVariable; exit;
			}
			if(empty($datos["CLIENTE_TRANS_MONTO"]) || $datos["CLIENTE_TRANS_MONTO"] <= 0){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_MONTO;";
				 $fal = 0;
				//return $nombreVariable; exit;
			}
			if(strlen($datos["CLIENTE_TRANS_MONTO"]) > 12){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_MONTO: | Sobrepasa longitud establecida 12 dígitos |;";
				 $fal = 0;
			}
			if(empty($datos["CLIENTE_TRANS_AUDITNO"])){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_AUDITNO;";
				 $fal = 0;
			}
			if(strlen($datos["CLIENTE_TRANS_AUDITNO"]) > 6){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_AUDITNO: | Sobrepasa longitud establecida 6 dígitos |;";
				 $fal = 0;
			}
			if(empty($datos["CLIENTE_TRANS_TARJETAVEN"])){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_TARJETAVEN;";
				 $fal = 0;
			}
			if(strlen($datos["CLIENTE_TRANS_TARJETAVEN"]) > 4 || strlen($datos["CLIENTE_TRANS_TARJETAVEN"]) < 4){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_TARJETAVEN: | Longitud establecida 4 dígitos |;";
				 $fal = 0;
			}
			if(empty($datos["CLIENTE_TRANS_MODOENTRA"])){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_MODOENTRA;";
				 $fal = 0;
			}
			if(empty($datos["CLIENTE_TRANS_TERMINALID"])){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_TERMINALID;";
				 $fal = 0;
			}
			if(empty($datos["CLIENTE_TRANS_RETAILERID"])){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_RETAILERID;";
				 $fal = 0;
			}
			if(empty($datos["CLIENTE_TRANS_RECIBOID"])){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_RECIBOID;";
				 $fal = 0;
			}
			if(strlen($datos["CLIENTE_TRANS_RECIBOID"]) > 6){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_RECIBOID: | Sobrepasa longitud establecida 6 dígitos |;";
				 $fal = 0;
			}
			/*if($fal ==0){
				return "Los datos requeridos: ".$nombreVariable." NO tienen valor"; exit;
			}*/
		}
		if($tipoValidacion== "MANCONMILLA"){
			if(empty($datos["CLIENTE_TRANS_TARJETAMAN"])){
				 $nombreVariable = "CLIENTE_TRANS_TARJETAMAN;";
				 $fal = 0;
				//return $nombreVariable; exit;
			}
			if(empty($datos["CLIENTE_TRANS_AUDITNO"])){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_AUDITNO;";
				 $fal = 0;
			}
			if(strlen(trim($datos["CLIENTE_TRANS_AUDITNO"])) > 6){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_AUDITNO: | Sobrepasa longitud establecida 6 dígitos |;";
				 $fal = 0;
			}
			if(empty($datos["CLIENTE_TRANS_TARJETAVEN"])){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_TARJETAVEN;";
				 $fal = 0;
			}
			if(strlen(trim($datos["CLIENTE_TRANS_TARJETAVEN"])) > 4 || strlen(trim($datos["CLIENTE_TRANS_TARJETAVEN"])) < 4){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_TARJETAVEN: | Longitud establecida 4 dígitos |;";
				 $fal = 0;
			}
			if(empty($datos["CLIENTE_TRANS_MODOENTRA"])){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_MODOENTRA;";
				 $fal = 0;
			}
			if(empty($datos["CLIENTE_TRANS_TERMINALID"])){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_TERMINALID;";
				 $fal = 0;
			}
			if(empty($datos["CLIENTE_TRANS_RETAILERID"])){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_RETAILERID;";
				 $fal = 0;
			}
			if(empty($datos["CLIENTE_TRANS_RECIBOID"])){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_RECIBOID;";
				 $fal = 0;
			}
			if(strlen(trim($datos["CLIENTE_TRANS_RECIBOID"])) > 6){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_RECIBOID: | Sobrepasa longitud establecida 6 dígitos |;";
				 $fal = 0;
			}
			/*if($fal ==0){
				return "Los datos requeridos: ".utf8_decode($nombreVariable)." NO tienen valor"; exit;
			}*/
		}
		if($tipoValidacion== "MANCOMPRAMILA" || $tipoValidacion =="MANCOMPRAPLAA"){
			if(empty($datos["CLIENTE_TRANS_TARJETAMAN"])){
				 $nombreVariable = "CLIENTE_TRANS_TARJETAMAN;";
				 $fal = 0;
				//return $nombreVariable; exit;
			}
			if(strlen($datos["CLIENTE_TRANS_TARJETAMAN"]) > 16){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_TARJETAMAN: | Sobrepasa longitud establecida 16 dígitos |;";
				 $fal = 0;
			}
			if(empty($datos["CLIENTE_TRANS_MONTO"]) || $datos["CLIENTE_TRANS_MONTO"] <= 0){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_MONTO;";
				 $fal = 0;
				//return $nombreVariable; exit;
			}
			if(strlen($datos["CLIENTE_TRANS_MONTO"]) > 12){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_MONTO: | Sobrepasa longitud establecida 12 dígitos |;";
				 $fal = 0;
			}
			if(empty($datos["CLIENTE_TRANS_AUDITNO"])){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_AUDITNO;";
				 $fal = 0;
			}
			if(strlen($datos["CLIENTE_TRANS_AUDITNO"]) > 6){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_AUDITNO: | Sobrepasa longitud establecida 6 dígitos |;";
				 $fal = 0;
			}
			if(empty($datos["CLIENTE_TRANS_TRANTIME"])){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_TRANTIME;";
				 $fal = 0;
			}
			if(empty($datos["CLIENTE_TRANS_TRANDATE"])){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_TRANDATE;";
				 $fal = 0;
			}
			if(empty($datos["CLIENTE_TRANS_TARJETAVEN"])){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_TARJETAVEN;";
				 $fal = 0;
			}
			if(strlen($datos["CLIENTE_TRANS_TARJETAVEN"]) > 4 || strlen($datos["CLIENTE_TRANS_TARJETAVEN"]) < 4){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_TARJETAVEN: | Longitud establecida 4 dígitos |;";
				 $fal = 0;
			}
			if(empty($datos["CLIENTE_TRANS_MODOENTRA"])){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_MODOENTRA;";
				 $fal = 0;
			}
			if(empty($datos["CLIENTE_TRANS_REFERENCIA"])){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_REFERENCIA;";
				 $fal = 0;
			}
			if(strlen($datos["CLIENTE_TRANS_REFERENCIA"]) > 12){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_REFERENCIA: | Sobrepasa longitud establecida 12 dígitos |;";
				 $fal = 0;
			}
			if(empty($datos["CLIENTE_TRANS_AUTORIZA"])){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_AUTORIZA;";
				 $fal = 0;
			}
			if(strlen($datos["CLIENTE_TRANS_AUTORIZA"]) > 6){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_AUTORIZA: | Sobrepasa longitud establecida 6 dígitos |;";
				 $fal = 0;
			}
			if(empty($datos["CLIENTE_TRANS_TERMINALID"])){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_TERMINALID;";
				 $fal = 0;
			}
			if(empty($datos["CLIENTE_TRANS_RETAILERID"])){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_RETAILERID;";
				 $fal = 0;
			}
			if(empty($datos["CLIENTE_TRANS_RECIBOID"])){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_RECIBOID;";
				 $fal = 0;
			}
			if(strlen($datos["CLIENTE_TRANS_RECIBOID"]) > 6){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_RECIBOID: | Sobrepasa longitud establecida 6 dígitos |;";
				 $fal = 0;
			}
			/*if(empty($respuesta->CLIENTE_TRANS_TOKENCVV)){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_TOKENCVV;";
				 $fal = 0;
			}*/
			/*if($fal ==0){
				return "Los datos requeridos: ".$nombreVariable." NO tienen valor"; exit;
			}*/
		}
		if($tipoValidacion== "MANCOMPRAPLA" || $tipoValidacion =="MANCOMPRAPLAR"){
			if(empty($datos["CLIENTE_TRANS_TARJETAMAN"])){
				 $nombreVariable = "CLIENTE_TRANS_TARJETAMAN;";
				 $fal = 0;
				//return $nombreVariable; exit;
			}
			/*if(strlen($respuesta->CLIENTE_TRANS_TARJETAMAN) > 16){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_TARJETAMAN: |Sobrepasa longitud establecida 16 dígitos |;";
				 $fal = 0;
			}*/
			if(empty($datos["CLIENTE_TRANS_MONTO"]) || $datos["CLIENTE_TRANS_MONTO"] <= 0){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_MONTO;";
				 $fal = 0;
				//return $nombreVariable; exit;
			}
			if(strlen($datos["CLIENTE_TRANS_MONTO"]) > 12){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_MONTO: | Sobrepasa longitud establecida 12 dígitos |;";
				 $fal = 0;
			}
			if(empty($datos["CLIENTE_TRANS_AUDITNO"])){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_AUDITNO;";
				 $fal = 0;
			}
			if(strlen($datos["CLIENTE_TRANS_AUDITNO"]) > 6){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_AUDITNO: | Sobrepasa longitud establecida 6 dígitos |;";
				 $fal = 0;
			}
			if(empty($datos["CLIENTE_TRANS_TARJETAVEN"])){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_TARJETAVEN;";
				 $fal = 0;
			}
			if(strlen($datos["CLIENTE_TRANS_TARJETAVEN"]) > 4 || strlen($datos["CLIENTE_TRANS_TARJETAVEN"]) < 4){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_TARJETAVEN: | Longitud establecida 4 dígitos |;";
				 $fal = 0;
			}
			if(empty($datos["CLIENTE_TRANS_MODOENTRA"])){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_MODOENTRA;";
				 $fal = 0;
			}
			if(empty($datos["CLIENTE_TRANS_TERMINALID"])){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_TERMINALID;";
				 $fal = 0;
			}
			if(empty($datos["CLIENTE_TRANS_RETAILERID"])){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_RETAILERID;";
				 $fal = 0;
			}
			if(empty($datos["CLIENTE_TRANS_RECIBOID"])){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_RECIBOID;";
				 $fal = 0;
			}
			if(strlen($datos["CLIENTE_TRANS_RECIBOID"]) > 6){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_RECIBOID: | Sobrepasa longitud establecida 6 dígitos |;";
				 $fal = 0;
			}
			if(empty($datos["CLIENTE_TRANS_TOKENCVV_validar"])){
				$nombreVariable = $nombreVariable." CLIENTE_TRANS_TOKENCVV;";
				 $fal = 0;
			}
			if(empty($datos["numero_de_meses_plazo"])){
				$nombreVariable = $nombreVariable." numero_de_meses_plazo;";
				 $fal = 0;
			}
			/*if($fal ==0){
				return "Los datos requeridos: ".$nombreVariable." NO tienen valor"; exit;
			}*/
		}
		 
		if($fal ==0){
				return "Los datos requeridos: ".utf8_decode($nombreVariable)." NO tienen valor"; exit;
			}
	}
	//funcion para insertar los datos en la DB y llevar un control interno
	public function InsertarEnDB($datos){
			//mysqli_stmt_bind_param($stmt, "ssss", $datos["p_nombre_request"], $datos["p_request_trasnaccion"], $datos["p_response_trasnaccion"], $datos["p_usuario"]); 
			$respuesta = $this->Transaccionesinsertar->InsertarTransaccion($datos);
			if(!$respuesta){
				return "Hubieron Errores."; exit;
			}
			return $respuesta;
	}
	//valido la respuesta retornada por el servidor remoto y lo muestro al usuario que peticiono la accion
	public function ValidarResponse($respuesta){
		//echo $respuesta->return; exit;
		//return es el indice devuelto por la funcion cardtransaction de serfinsa
		$mensaje 	= $respuesta->return;//el mensaje si es que no existe el indice cliente_trans_respuesta por que no hubo exito 
		
		$respuesta = json_decode($respuesta->return);
		$respuestaRetornar = "Autorizado"; 

		//pongo la @ ya que si no encuentra el indice cliente_trans_respuesta, mostria error que no existe
		switch(@$respuesta->cliente_trans_respuesta){
			case "00":
				$respuestaRetornar = "Autorizado"; 
				break;
			case "01":
				$respuestaRetornar = "Llamar al Emisor"; 
				break;
			case "02":
				$respuestaRetornar = "Llamar al Emisor"; 
				break;
			case "03":
				$respuestaRetornar = "Llamar al Emisor"; 
				break;
			case "04":
				$respuestaRetornar = "Tarjeta Bloqueada"; 
				break;
			case "05":
				$respuestaRetornar = "Llamar al Emisor"; 
				break;
			case "07":
				$respuestaRetornar = "Tarjeta Bloqueada"; 
				break;
			case "12":
				$respuestaRetornar = utf8_decode("Transacción Inválida"); 
				break;
			case "13":
				$respuestaRetornar = utf8_decode("Monto Inválido"); 
				break;
			case "14":
				$respuestaRetornar = "Llamar al Emisor"; 
				break;
			case "15":
				$respuestaRetornar = "Emisor no Disponible"; 
				break;
			case "19":
				$respuestaRetornar = utf8_decode("Reintente Transacción"); 
				break;
			case "25":
				$respuestaRetornar = "Llamar al Emisor"; 
				break;
			case "30":
				$respuestaRetornar = "Error de Formato"; 
				break;
			case "31":
				$respuestaRetornar = "Banco No Soportado"; 
				break;
			case "39":
				$respuestaRetornar = utf8_decode("No es cuenta de Crédito"); 
				break;
			case "41":
				$respuestaRetornar = "Tarjeta Bloqueada"; 
				break;
			case "43":
				$respuestaRetornar = "Tarjeta Bloqueada"; 
				break;
			case "50":
				$respuestaRetornar = "Llamar al Emisor"; 
				break;
			case "51":
				$respuestaRetornar = "Fondos Insuficientes"; 
				break;
			case "52":
				$respuestaRetornar = "No es Cuenta de Cheques"; 
				break;
			case "53":
				$respuestaRetornar = "No es Cuenta de Ahorros"; 
				break;
			case "54":
				$respuestaRetornar = "Tarjeta Expirada"; 
				break;
			case "55":
				$respuestaRetornar = "Pin Incorrecto"; 
				break;
			case "57":
				$respuestaRetornar = utf8_decode("Transacción No Permitida"); 
				break;
			case "58":
				$respuestaRetornar = utf8_decode("Transacción No Permitida"); 
				break;
			case "59":
				$respuestaRetornar = "Sospecha de Fraude"; 
				break;
			case "61":
				$respuestaRetornar = "Actividad de limite Excedido"; 
				break;
			case "62":
				$respuestaRetornar = "Tarjeta Restringida"; 
				break;
			case "83":
				$respuestaRetornar = utf8_decode("Terminal Inválida"); 
				break;
			case "85":
				$respuestaRetornar = utf8_decode("CVV2 Inválido"); 
				break;
			case "87":
				$respuestaRetornar = utf8_decode("CVV2 Inválido"); 
				break;
			case "89":
				$respuestaRetornar = utf8_decode("Comercio Inválido"); 
				break;
			case "91":
				$respuestaRetornar = "Emisor no Disponible"; 
				break;
			case "92":
				$respuestaRetornar = "Emisor no Disponible"; 
				break;
			case "93":
				$respuestaRetornar = utf8_decode("Transacción no ser procesada"); 
				break;
			case "94":
				$respuestaRetornar = utf8_decode("Transacción Duplicada"); 
				break;
			case "96":
				$respuestaRetornar = "Sistema no Disponible"; 
				break;
			default:
				$respuestaRetornar = utf8_decode("NO HAY CÓDIGO PARA TIPIFICAR EL ERROR. MENSAJE:".$mensaje); 
		}
		return $respuestaRetornar;
	}
	//valido si el agente tiene permisos para acceder a las funciones/metodos
	public function ValidarAcceso($adm_usuario){
		//validacio para verificar si el usaurio que consume el ws tiene permisos de hacerlo
		$permisos = $this->Configuracionesbuscar->ValidarAcceso($adm_usuario);
		if(!$permisos){
			return utf8_decode("SIN ACCESO A LOS MÉTODOS");
		}
		else{
			return "OK";
		}
		
	}
	public function PETICION($CLIENTE_TRANS_TARJETAMAN, $CLIENTE_TRANS_MONTO, $CLIENTE_TRANS_AUDITNO, $CLIENTE_TRANS_TARJETAVEN, $CLIENTE_TRANS_RECIBOID, $CLIENTE_TRANS_TOKENCVV, $CLIENTE_TRANS_TRANTIME, $CLIENTE_TRANS_TRANDATE, $CLIENTE_TRANS_REFERENCIA, $CLIENTE_TRANS_AUTORIZA, $numero_de_meses_plazo, $metodo_a_convocar, $credenciales){
		//validar si usuario tiene acceso
		$credenciales = json_decode($credenciales);
		if(count($credenciales)==0){
			return utf8_decode("El formato de las credeciales no es válido"); exit;
		}
		if(!array_key_exists('clave', $credenciales) || !array_key_exists('id_usuario', $credenciales)) {
			return utf8_decode("El formato de las credenciales no es válido"); exit;
		}
		$respuesta = $this->ValidarAcceso(array(md5($credenciales->clave), $credenciales->id_usuario));
		if($respuesta != "OK"){
			return $respuesta; exit;
		}
		$txn = $metodo_a_convocar;
		global $cliente, $err;
		//$CLIENTE_TRANS_MODOENTRA 	= "012";
		//$CLIENTE_TRANS_TERMINALID 	= "00299997";
		//$CLIENTE_TRANS_RETAILERID 	= "000999999999999";
		$conf_configuraciones       = $this->Configuracionesbuscar->TraerDatosConfiguraciones();
		$Json = array(
			"CLIENTE_TRANS_TARJETAMAN"	=> trim($CLIENTE_TRANS_TARJETAMAN),
			"CLIENTE_TRANS_MONTO"		=> str_pad(str_replace(".", "",$CLIENTE_TRANS_MONTO), 12, 0, STR_PAD_LEFT), //RELLENA CON 0 A LA IZQUIERDA
			"CLIENTE_TRANS_AUDITNO" 	=> trim($CLIENTE_TRANS_AUDITNO),
			"CLIENTE_TRANS_TARJETAVEN" 	=> trim($CLIENTE_TRANS_TARJETAVEN),
			"CLIENTE_TRANS_MODOENTRA" 	=> trim($conf_configuraciones->cliente_trans_modoentra),
			"CLIENTE_TRANS_TERMINALID" 	=> trim($conf_configuraciones->cliente_trans_terminalid),
			"CLIENTE_TRANS_RETAILERID"	=> trim($conf_configuraciones->cliente_trans_retailerid),
			"CLIENTE_TRANS_RECIBOID" 	=> trim($CLIENTE_TRANS_RECIBOID),
			"CLIENTE_TRANS_TOKENCVV" 	=> "1611 ".$CLIENTE_TRANS_TOKENCVV,
			"CLIENTE_TRANS_TOKENCVV_validar"=> trim($CLIENTE_TRANS_TOKENCVV),
			"CLIENTE_TRANS_TRANTIME"	=> trim($CLIENTE_TRANS_TRANTIME),
			"CLIENTE_TRANS_TRANDATE"	=> trim($CLIENTE_TRANS_TRANDATE),
			"CLIENTE_TRANS_REFERENCIA"	=> trim($CLIENTE_TRANS_REFERENCIA),
			"CLIENTE_TRANS_AUTORIZA"	=> trim($CLIENTE_TRANS_AUTORIZA),
			"numero_de_meses_plazo"		=> trim($numero_de_meses_plazo),
			
		);
		//los datos de seguridad se envian en json y los traigo de la db
		$security = array(
			"comid"=>$conf_configuraciones->comid,
			"comkey"=>$conf_configuraciones->comkey,
			"comwrkstation"=>$conf_configuraciones->comwrkstation
		);
		$security = json_encode($security);
		//print_r($Json); exit;
		//llamo la funcion para validar datos de entrada	
		$respuesta = $this->ValidarDatosRequest($Json, $metodo_a_convocar);
		if($respuesta !=""){
			return $respuesta; exit;
		}
		//de acuerdo al metodo convocado se evalua y se arma al array para enviarlo al servidor remoto
		switch($metodo_a_convocar){
			case "MANCOMPRANOR"://Compra Manual
			case "MANCOMPRANORR"://Reversa Compra Manual
			case "MANCOMPRAMIL"://Compra con Puntos Manual
			case "MANCOMPRAMILR"://Reversa Compra con Puntos Manual
				$Json = array(
					"CLIENTE_TRANS_TARJETAMAN"	=> $Json["CLIENTE_TRANS_TARJETAMAN"],
					"CLIENTE_TRANS_MONTO"		=> $Json["CLIENTE_TRANS_MONTO"],
					"CLIENTE_TRANS_AUDITNO" 	=> $Json["CLIENTE_TRANS_AUDITNO"],
					"CLIENTE_TRANS_TARJETAVEN" 	=> $Json["CLIENTE_TRANS_TARJETAVEN"],
					"CLIENTE_TRANS_MODOENTRA" 	=> $Json["CLIENTE_TRANS_MODOENTRA"],
					"CLIENTE_TRANS_TERMINALID" 	=> $Json["CLIENTE_TRANS_TERMINALID"],
					"CLIENTE_TRANS_RETAILERID"	=> $Json["CLIENTE_TRANS_RETAILERID"],
					"CLIENTE_TRANS_RECIBOID" 	=> $Json["CLIENTE_TRANS_RECIBOID"],
					"CLIENTE_TRANS_TOKENCVV" 	=> $Json["CLIENTE_TRANS_TOKENCVV"]
				);
				break;
			case "DFTCAPNO"://Transacción para verificación de tarjeta -dftcapno
				$Json = array(
					"CLIENTE_TRANS_TARJETAMAN"	=> $Json["CLIENTE_TRANS_TARJETAMAN"],
					"CLIENTE_TRANS_MONTO"		=> $Json["CLIENTE_TRANS_MONTO"],
					"CLIENTE_TRANS_AUDITNO" 	=> $Json["CLIENTE_TRANS_AUDITNO"],
					"CLIENTE_TRANS_TARJETAVEN" 	=> $Json["CLIENTE_TRANS_TARJETAVEN"],
					"CLIENTE_TRANS_MODOENTRA" 	=> $Json["CLIENTE_TRANS_MODOENTRA"],
					"CLIENTE_TRANS_TERMINALID" 	=> $Json["CLIENTE_TRANS_TERMINALID"],
					"CLIENTE_TRANS_RETAILERID"	=> $Json["CLIENTE_TRANS_RETAILERID"],
					"CLIENTE_TRANS_RECIBOID" 	=> $Json["CLIENTE_TRANS_RECIBOID"],
					"CLIENTE_TRANS_TOKENCVV" 	=> $Json["CLIENTE_TRANS_TOKENCVV"]
				);
				break;
			case "MANCOMPRAPLA"://Compra a Plazos Manual
			case "MANCOMPRAPLAR"://Reversa Compra a Plazos Manual
				$Json = array(
					"CLIENTE_TRANS_TARJETAMAN"	=> $Json["CLIENTE_TRANS_TARJETAMAN"], 
					"CLIENTE_TRANS_MONTO"		=> $Json["CLIENTE_TRANS_MONTO"],
					"CLIENTE_TRANS_AUDITNO" 	=> $Json["CLIENTE_TRANS_AUDITNO"],
					"CLIENTE_TRANS_TARJETAVEN" 	=> $Json["CLIENTE_TRANS_TARJETAVEN"],
					"CLIENTE_TRANS_MODOENTRA"	=> $Json["CLIENTE_TRANS_MODOENTRA"],
					"CLIENTE_TRANS_TERMINALID"	=> $Json["CLIENTE_TRANS_TERMINALID"],
					"CLIENTE_TRANS_RETAILERID"	=> $Json["CLIENTE_TRANS_RETAILERID"],
					"CLIENTE_TRANS_RECIBOID" 	=> $Json["CLIENTE_TRANS_RECIBOID"],
					"CLIENTE_TRANS_TOKENCVV"	=> $Json["CLIENTE_TRANS_TOKENCVV"]
				);
				break;
			case "MANCONMILLA"://Consulta de puntos manual
				$Json = array(
					"CLIENTE_TRANS_TARJETAMAN"	=> $Json["CLIENTE_TRANS_TARJETAMAN"],
					"CLIENTE_TRANS_AUDITNO" 	=> $Json["CLIENTE_TRANS_AUDITNO"],
					"CLIENTE_TRANS_TARJETAVEN" 	=> $Json["CLIENTE_TRANS_TARJETAVEN"],
					"CLIENTE_TRANS_MODOENTRA" 	=> $Json["CLIENTE_TRANS_MODOENTRA"],
					"CLIENTE_TRANS_TERMINALID" 	=> $Json["CLIENTE_TRANS_TERMINALID"],
					"CLIENTE_TRANS_RETAILERID"	=> $Json["CLIENTE_TRANS_RETAILERID"],
					"CLIENTE_TRANS_RECIBOID" 	=> $Json["CLIENTE_TRANS_RECIBOID"]
				);
				break;
			case "MANCOMPRADEV"://Devolución Manual
			case "MANCOMPRADEVR"://Reversa Devolución Manual
				$Json = array(
					"CLIENTE_TRANS_TARJETAMAN"	=> $Json["CLIENTE_TRANS_TARJETAMAN"],
					"CLIENTE_TRANS_MONTO"		=> $Json["CLIENTE_TRANS_MONTO"],
					"CLIENTE_TRANS_AUDITNO" 	=> $Json["CLIENTE_TRANS_AUDITNO"],
					"CLIENTE_TRANS_TARJETAVEN" 	=> $Json["CLIENTE_TRANS_TARJETAVEN"],
					"CLIENTE_TRANS_MODOENTRA" 	=> $Json["CLIENTE_TRANS_MODOENTRA"],
					"CLIENTE_TRANS_TERMINALID" 	=> $Json["CLIENTE_TRANS_TERMINALID"],
					"CLIENTE_TRANS_RETAILERID"	=> $Json["CLIENTE_TRANS_RETAILERID"],
					"CLIENTE_TRANS_RECIBOID" 	=> $Json["CLIENTE_TRANS_RECIBOID"]
				);
				break;
			case "MANCOMPRANORA"://Anulación Compra Manual
			case "MANCOMPRAMILA"://Anulación Compra de Puntos Manual
			case "MANCOMPRAPLAA"://Anulación Compra a Plazos Manual
				$Json = array(
					"CLIENTE_TRANS_TARJETAMAN"	=> $Json["CLIENTE_TRANS_TARJETAMAN"],
					"CLIENTE_TRANS_MONTO"		=> $Json["CLIENTE_TRANS_MONTO"],
					"CLIENTE_TRANS_AUDITNO" 	=> $Json["CLIENTE_TRANS_AUDITNO"],
					"CLIENTE_TRANS_TRANTIME" 	=> $Json["CLIENTE_TRANS_TRANTIME"],
					"CLIENTE_TRANS_TRANDATE" 	=> $Json["CLIENTE_TRANS_TRANDATE"],
					"CLIENTE_TRANS_TARJETAVEN" 	=> $Json["CLIENTE_TRANS_TARJETAVEN"],
					"CLIENTE_TRANS_MODOENTRA"	=> $Json["CLIENTE_TRANS_MODOENTRA"],
					"CLIENTE_TRANS_REFERENCIA"	=> $Json["CLIENTE_TRANS_REFERENCIA"],
					"CLIENTE_TRANS_AUTORIZA" 	=> $Json["CLIENTE_TRANS_AUTORIZA"],
					"CLIENTE_TRANS_TERMINALID"	=> $Json["CLIENTE_TRANS_TERMINALID"],
					"CLIENTE_TRANS_RETAILERID"	=> $Json["CLIENTE_TRANS_RETAILERID"],
					"CLIENTE_TRANS_RECIBOID" 	=> $Json["CLIENTE_TRANS_RECIBOID"]
				);
				break;
		}
		$Json = json_encode($Json);
		//return  $Json; exit;
		//return $security; exit;
		//configuro las opciones para instanciar el cliente SOP nativo de PHP ya qeu nusoap_cliente no me funciono
		$options = array(
			'cache_wsdl' => 0,
			'trace' => 1,
			'stream_context' => stream_context_create(array(
				  'ssl' => array(
					   'verify_peer' => false,
						'verify_peer_name' => false,
						'allow_self_signed' => true
				  )/*,
				  'http' => array(
					'max_redirects' => 101
				)*/
					)),
			'soap_version'=> SOAP_1_1,
			);
		$url = "https://192.168.151.14:2027/WebPubTransactor/TransactorWS?wsdl";
		//instancio el cliente soap y le envio la url del wsdl con las repectivas opciones de configuracion
		$cliente = new SoapClient($url, $options);
		//los paramtros de la funcion deben ser un array, y en la funcion en vierlos en un arrar, se vuelve un array bidimensional, como la funcion espera json, los valores de $parametros de los indices del array ya van paseados en json
		$parametros = array(
			"security"=>$security,
			"txn"=>$txn,
			"message"=>$Json
		);
		//$parametros = array_map("utf8_encode", $parametros);
		//print_r($Json); exit;
		//invoco la funcion del servicio remoto, los parametros se envian en array, partiendo que los parametros es un array en si
		try {
		$respuestaRemota = $cliente->__soapCall("cardtransaction", array($parametros));
		} catch ( SoapFault $e ) {
		 return $e->getMessage(); exit;
		}
		//print_r($respuestaRemota); exit;
		//verificando el valor de la respuesta si se ha autorizado o no, invocando a la funcion local para validar
	
		$respuesta = $this->ValidarResponse($respuestaRemota);
		//return $respuesta;
		if($respuesta != "Autorizado"){
			return $respuesta; exit;
		}
		//quito la clave CLIENTE_TRANS_TARJETAMAN (numero de la tarjeta) del array para evitar usurpacion o robo de datos
		$Json = json_decode($Json);
		unset($Json->CLIENTE_TRANS_TARJETAMAN/*, $Json->CLIENTE_TRANS_TOKENCVV*/);
		$Json = json_encode($Json);
		//inserto peticion y respuesta, control interno
		$trans_transacciones_request = array(
			"p_nombre_request"=>$metodo_a_convocar,
			"p_request_trasnaccion"=>$Json,
			"p_response_trasnaccion"=>$respuestaRemota->return, //return es el indice devuelto por la funcion
			"p_usuario"=>$credenciales->id_usuario
		);
		//inserto la respuesta en la db
		$respuesta = $this->InsertarEnDB($trans_transacciones_request);
		if(!$respuesta){
			return $respuesta; exit;
		}
		$respuesta = $respuestaRemota->return;
		return $respuesta;
	}
	
}