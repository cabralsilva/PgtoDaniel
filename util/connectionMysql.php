<?php
// define("MYSQL_CONN_ERROR", "Unable to connect to database.");
// Ensure reporting is setup correctly
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
require_once '../util/constantes.php';

class BancoMysql{
	private $nome_banco;
	private $nome_usuario;
	private $senha_banco;
	private $host_banco;
	private $conexao_banco;
	private $status_conexao = true;
	private $status_login = false;
	function __construct(){
	}

	public function getStatusConexao(){
		try {
			$this->connect();
			return $this->status_conexao;
		} catch (Exception $e) {
			echo "Falha na Conexão com Base de Dados" .$e->getMessage();
			throw $e;
			return false;
		}
	}

	public function getStatusLogin(){
		return $this->status_login;
	}


	public function connect(){
		$this->nome_banco = NOME_BANCO;
		$this->nome_usuario = NOME_USUARIO;
		$this->senha_banco = SENHA_BANCO;
		$this->host_banco = HOST_BANCO;
			
		try{

			$this->conexao_banco = new mysqli($this->host_banco,$this->nome_usuario,$this->senha_banco);
			$this->conexao_banco->set_charset("utf8");
			$this->conexao_banco->select_db($this->nome_banco);
			$this->status_conexao = true;
		}
		catch(mysqli_sql_exception $e){
			$this->status_conexao = false;
			throw $e;
		}
	}

	public function login($email, $senha){
		try {
			$this->getStatusConexao();
			// Recupera o login
			$login = isset($email) ? addslashes(trim($email)) : FALSE;
			// Recupera a senha, a criptografando em MD5
			//$senha = isset($_POST["senha"]) ? md5(trim($_POST["senha"])) : FALSE;
			$senha = isset($senha) ? $senha : FALSE;

			/**
				* Executa a consulta no banco de dados.
				* Caso o número de linhas retornadas seja 1 o login é válido,
				* caso 0 ou mais de 1, inválido.
				*/
			$sql = "SELECT USUARIOS.id_usuario, USUARIOS.nome_usuario, USUARIOS.email_usuario, USUARIOS.senha_usuario FROM USUARIOS
						WHERE USUARIOS.email_usuario = '" . $login . "'";
			$usuario = $this->conexao_banco->query($sql);
			//$usuario = @mysql_query($SQL) or die("Erro no banco de dados!");
			$total = $usuario->num_rows;
			//$total = @mysql_num_rows($usuario);
			// Caso o usuário tenha digitado um login válido o número de linhas será 1..
			if($total == 1){
				// Obtém os dados do usuário, para poder verificar a senha e passar os demais dados para a sessão
				//$dados = @mysql_fetch_array($usuario);
				$dados = $usuario->fetch_array(MYSQLI_ASSOC);
					
				// Agora verifica a senha
				if(!strcmp($senha, $dados["senha_usuario"])){
					// TUDO OK! Agora, passa os dados para a sessão e redireciona o usuário
					return $dados;
				}else 
					throw new Exception("Senha incorreta.");
			}else 
				throw new Exception("Usuário não localizado no sistema.");
		}
		catch (Exception $e) {
			throw $e;
		}
	}

	public function verificarSistemasHabilitados($idUsuario){
		try {
			$this->getStatusConexao();
			$sql = "SELECT empresa.CODIGO, empresa.NOME, empresa.CNPJ, empresa.SENHA, EMPRESA.HOST_BANCO, EMPRESA.NOME_BANCO, EMPRESA.USER_BANCO, EMPRESA.SENHA_BANCO, sistemas.*, usuarios.nome_usuario  FROM empresa
						INNER JOIN empresa_sistema ON empresa.CODIGO = empresa_sistema.fk_empresa
						INNER JOIN sistemas ON sistemas.id_sistema = empresa_sistema.fk_sistema
						INNER JOIN usuario_empresa_sistema ON usuario_empresa_sistema.fk_empresa_sistema = empresa_sistema.id_empresa_sistema
						INNER JOIN usuarios ON usuarios.id_usuario = usuario_empresa_sistema.fk_usuario
						WHERE USUARIOS.id_usuario = " . $idUsuario;
				
			echo $sql;
			$sistemas = $this->conexao_banco->query($sql);

			// Percorre os registros retornados
			$dadosAcesso = array();
			while($linha = $sistemas->fetch_array(MYSQLI_ASSOC)){
				array_push($dadosAcesso, $linha);
			}
			// Libera o result set
			$sistemas->close();
			return $dadosAcesso;
		}catch (Exception $e) {
			throw $e;
		}
	}
}

?>