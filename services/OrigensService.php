<?php
require_once '../util/Banco.php';

class OrigensService {
	private $banco;
	function __construct() {
		$this->banco = new BancoDados ();
		try {
			$this->banco->connect ();
		} catch ( Exception $e ) {
			echo "Falha na Conexão com Base de Dados" . $e->getMessage ();
		}
	}
	
	public function getOrigens(){
		$sql = "SELECT * FROM origens WHERE origens.ativo = 1 ORDER BY origens.descricao_origem";
		
		$consulta = $this->banco->getConexaoBanco ()->query ( $sql );
		$lstOrigens = array ();
		while ( $linha = $consulta->fetch_array ( MYSQLI_ASSOC ) ) {
			array_push ( $lstOrigens, $linha );
		}
		$consulta->close ();
		return $lstOrigens;
	}
}
	