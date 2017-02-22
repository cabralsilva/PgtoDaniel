<?php
require_once '../util/Banco.php';
class EmpresaService {
	private $banco;
	function __construct() {
		$this->banco = new BancoDados ();
		try {
			$this->banco->connect ();
		} catch ( Exception $e ) {
			echo "Falha na Conexão com Base de Dados" . $e->getMessage ();
		}
	}
	
	public function getOperadoraEmpresa($cod_empresa, $agencia, $dg_agencia, $conta_corrente, $dg_conta_corrente){
		$sql = "SELECT operadora_empresa.* FROM operadora_empresa
				WHERE operadora_empresa.fk_empresa = $cod_empresa AND operadora_empresa.numero_agencia = $agencia AND operadora_empresa.digito_agencia = $dg_agencia AND 
				operadora_empresa.numero_conta = $conta_corrente AND operadora_empresa.digito_conta = $dg_conta_corrente";
// 		echo "\n$sql\n";
		$consulta = $this->banco->getConexaoBanco ()->query ( $sql );
		$lstOE = array ();
		while ( $linha = $consulta->fetch_array ( MYSQLI_ASSOC ) ) {
			array_push ( $lstOE, $linha );
		}
// 		$total = $consulta->num_rows;
		$consulta->close ();
		return $lstOE;
// 		return $total;
	}
	
	public function getInfoEmpresaiBoltPag($idUsuario){
		try {
			$sql = "SELECT empresa.CODIGO, empresa.NOME, empresa.CNPJ, empresa.SENHA, EMPRESA.HOST_BANCO, EMPRESA.NOME_BANCO, EMPRESA.USER_BANCO, EMPRESA.SENHA_BANCO, sistemas.*  
						FROM empresa
					INNER JOIN empresa_sistema ON empresa.CODIGO = empresa_sistema.fk_empresa
					INNER JOIN sistemas ON sistemas.id_sistema = empresa_sistema.fk_sistema
					INNER JOIN usuario_empresa_sistema ON usuario_empresa_sistema.fk_empresa_sistema = empresa_sistema.id_empresa_sistema
					INNER JOIN usuarios ON usuarios.id_usuario = usuario_empresa_sistema.fk_usuario
					WHERE USUARIOS.id_usuario = $idUsuario AND sistemas.id_sistema = 1";
// 			echo $sql;
// 			die;
			$empresa = $this->banco->getConexaoBanco ()->query ( $sql );
	
			// Percorre os registros retornados
			$dadosAcesso = array();
			while($linha = $empresa->fetch_array(MYSQLI_ASSOC)){
				array_push($dadosAcesso, $linha);
			}
			// Libera o result set
			$empresa->close();
			return $dadosAcesso;
		}catch (Exception $e) {
			throw $e;
		}
	}
}
	