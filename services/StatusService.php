<?php
require_once '../util/Banco.php';

class StatusService {
	private $banco;
	function __construct() {
		$this->banco = new BancoDados ();
		try {
			$this->banco->connect ();
		} catch ( Exception $e ) {
			echo "Falha na Conexão com Base de Dados" . $e->getMessage ();
		}
	}
	
	public function getStatusBoleto(){
		$sql = "SELECT * FROM status WHERE status.tipo_status = 'Boleto' GROUP BY status.estado ORDER BY status.id_status";
		// echo $sql;
		$consulta = $this->banco->getConexaoBanco ()->query ( $sql );
		$lstStatus = array ();
		while ( $linha = $consulta->fetch_array ( MYSQLI_ASSOC ) ) {
			array_push ( $lstStatus, $linha );
		}
		$consulta->close ();
		return $lstStatus;
	}
	
	public function getStatusCartao(){
		$sql = "SELECT * FROM status WHERE status.tipo_status = 'Cartao' GROUP BY status.estado ORDER BY status.id_status";
		// echo $sql;
		$consulta = $this->banco->getConexaoBanco ()->query ( $sql );
		$lstStatus = array ();
		while ( $linha = $consulta->fetch_array ( MYSQLI_ASSOC ) ) {
			array_push ( $lstStatus, $linha );
		}
		$consulta->close ();
		return $lstStatus;
	}
	
	public function getStatusTodos(){
		$sql = "SELECT * FROM status WHERE status.tipo_status = 'Todos' GROUP BY status.estado ORDER BY status.id_status";
		// echo $sql;
		$consulta = $this->banco->getConexaoBanco ()->query ( $sql );
		$lstStatus = array ();
		while ( $linha = $consulta->fetch_array ( MYSQLI_ASSOC ) ) {
			array_push ( $lstStatus, $linha );
		}
		$consulta->close ();
		return $lstStatus;
	}

	
}
	