<?php
	session_start();
	include("../util/connectionMysql.php");
	include("../services/EmpresaService.php");
	
	
	$bancoMysql = new BancoMysql();
	
	try {
		$bancoMysql->connect();
	} catch (Exception $e) {
		echo "Falha na Conexão com Base de Dados" .$e->getMessage();
	}
	
	if ($bancoMysql->getStatusConexao()){
		try {
			$dados = $bancoMysql->login($_POST["email"], $_POST["senha"]);
			$_SESSION["usuario_logado"]["id_usuario"] = $dados["id_usuario"];
			$_SESSION["usuario_logado"]["nome_usuario"] = stripslashes($dados["nome_usuario"]);
			$_SESSION["usuario_logado"]["email_usuario"] = stripslashes($dados["email_usuario"]);
			
// 			header("location: ".BaseProjeto."/pagamentos-pendentes");
		} catch (Exception $e) {
			$_SESSION["falha_login"] = $e->getMessage(); 
			unset($_SESSION["usuario_logado"]);
			header("location: " . $_SERVER["HTTP_REFERER"] );
			exit();
		}
		
		try {
			$es = new EmpresaService();
			$array = $es->getInfoEmpresaiBoltPag($_SESSION["usuario_logado"]["id_usuario"]);
			
			$_SESSION["dados_empresa"]["cod_empresa"] = $array[0]["CODIGO"];
			$_SESSION["dados_empresa"]["nome_empresa"] = $array[0]["NOME"];
			$_SESSION["dados_empresa"]["host_banco_empresa"] = $array[0]["HOST_BANCO"];
			$_SESSION["dados_empresa"]["nome_banco_empresa"] = $array[0]["NOME_BANCO"];
			$_SESSION["dados_empresa"]["user_banco_empresa"] = $array[0]["USER_BANCO"];
			$_SESSION["dados_empresa"]["senha_banco_empresa"] = $array[0]["SENHA_BANCO"];
			$_SESSION["dados_empresa"]["cnpj"] = $array[0]["CNPJ"];
			$_SESSION["dados_empresa"]["senha"] = $array[0]["SENHA"];
			$_SESSION["sistema"]["cod_sistema"] = $array[0]["id_sistema"];
			$_SESSION["sistema"]["nome_sistema"] = $array[0]["descricao_sistema"];
			$_SESSION["sistema"]["diretorio_sistema"] = $array[0]["diretorio_sistema"];
			$_SESSION["sistema"]["diretorio_logo"] = $array[0]["diretorio_logo"];
			
			header("location: ".BaseProjeto."/pagamentos-pendentes");
// 			header("location: ../views/");
		} catch (Exception $e) {
			$_SESSION["falha_login"] = $e->getMessage(); 
			header("location: " . $_SERVER["HTTP_REFERER"] );
			exit();
		}
	}
	
	