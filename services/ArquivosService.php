<?php
require_once '../util/Banco.php';

class ArquivosService {
	private $banco;
	function __construct() {
		$this->banco = new BancoDados ();
		try {
			$this->banco->connect ();
		} catch ( Exception $e ) {
			echo "Falha na Conexão com Base de Dados" . $e->getMessage ();
		}
	}
	
	public function getArquivosRetornoBySequencial($sequencial, $operadoraEmpresa){
		$sql = "SELECT * FROM arquivos WHERE arquivos.fk_operadora_empresa = $operadoraEmpresa AND arquivos.tipo_arquivo = 'RETORNO' AND arquivos.sequencial = $sequencial";
		$consulta = $this->banco->getConexaoBanco ()->query ( $sql );
		$lstArquivos = array ();
		while ( $linha = $consulta->fetch_array ( MYSQLI_ASSOC ) ) {
			array_push ( $lstArquivos, $linha );
		}
		$consulta->close ();
		return $lstArquivos;
	}
	
	public function insertArquivoRetorno($sequencial, $dataBanco, $operadoraEmpresa){
		$dataEntrada = new DateTime();
		$dataBanco = new DateTime( date("d/m/Y", strtotime($dataBanco)));
		$sql = "INSERT INTO arquivos (
					sequencial, data_banco, data_entrada, fk_operadora_empresa, tipo_arquivo
				)VALUES (
					$sequencial, 
					DATE '" . $dataBanco->format ( 'Y-m-d' ) . "',
					DATE '" . $dataEntrada->format ( 'Y-m-d' ) . "', 
					" . $operadoraEmpresa . ", 
					'RETORNO')";
		if ($this->banco->getConexaoBanco ()->query ( $sql )) 
			return $this->banco->getConexaoBanco ()->insert_id;
		
		return false;
	}
}
	