<?php
require_once '../util/BancoBase.php';
class BaseServices {
	private $banco;
	function __construct() {
		$this->banco = new BancoBase();
		try {
			$this->banco->connect ();
		} catch ( Exception $e ) {
			echo "Falha na Conexão com Base de Dados" . $e->getMessage ();
		}
	}
	
	public function getAdrress($cep){
		try {
			$sql = "SELECT Cep.* FROM Cep WHERE Cep.Cep = '$cep'";
			$consulta = $this->banco->getConexaoBanco ()->query ( $sql );
			$lstAdress = array ();
			while ( $linha = $consulta->fetch_array ( MYSQLI_ASSOC ) ) {
				array_push ( $lstAdress, $linha );
			}
				
			if (count($lstAdress) != 1) return array('CodStatus' => 2, 'Msg' => 'Falha na autenticação da solicitação!');
			$consulta->close ();
			return array('CodStatus' => 1, 'Msg' => 'Sucess', 'Model' => $lstAdress);
				
		} catch (Exception $e) {
			return array('CodStatus' => 3, 'Msg' => $e->getMessage());
		}		
	}
}
	