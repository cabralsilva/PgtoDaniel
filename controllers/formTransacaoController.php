<?php
	require_once '../util/constantes.php';
	require_once '../models/Transacao.php';
	require_once '../services/TransacaoService.php';
	require_once '../services/BaseServices.php';
	session_start();
	
	if(isset($_POST["servico"])){
		if($_POST["servico"] == "createTransacao") criarTransacao();
		elseif ($_POST["servico"] == "getOperadoras") getOperadoras();
		elseif ($_POST["servico"] == "getAddress") getAddress();
	}else{
		//echo "SERVICO NÃO CATALOGADO";
	}
	
	
	function criarTransacao(){
		
		$ts = new TransacaoService();
		$_POST["usr"] = $_SESSION["dados_empresa"]["cnpj"];
		$_POST["pwd"] = $_SESSION["dados_empresa"]["senha"];
		$_POST["dataDocumento"] = strtotime(str_replace('/', '-', $_POST["dataDocumento"]));
		$_POST["dataDocumento"] = date("Y-m-d", $_POST["dataDocumento"]);
		$_POST["dataVencimento"] = strtotime(str_replace('/', '-', $_POST["dataVencimento"]));
		$_POST["dataVencimento"] = date("Y-m-d", $_POST["dataVencimento"]);
		$_POST["inscricaoPagador"] = str_replace(".", "", $_POST["inscricaoPagador"]);
		$_POST["inscricaoPagador"] = str_replace("-", "", $_POST["inscricaoPagador"]);
		$_POST["inscricaoPagador"] = str_replace("/", "", $_POST["inscricaoPagador"]);
		$_POST["cep"] = str_replace(".", "", $_POST["cep"]);
		
		$retornoAutenticacao = $ts->autenticarInsert($_POST["usr"], $_POST["pwd"]);
		if ($retornoAutenticacao != null){
			$retornoFP = $ts->getFormaPagamentoOperadoraEmpresa($retornoAutenticacao[0]["CODIGO"], $_POST["formaPagamento"], $_POST["operadoraEmpresa"]);
			if ($retornoFP["CodStatus"] == 1){
				echo json_encode($ts->insertTransaction($_POST, $retornoFP["Model"]));
			}else 
				echo "Inconsistencia no cadastro referente a formas de pagamento e operadoras";
		}else{
			echo "fail";
		}
	}
	
	
	function getOperadorasBoleto(){
		$ts = new TransacaoService();
		$retorno = $ts->getOperadorasBoleto($_SESSION["dados_empresa"]["cod_empresa"]);
		$_REQUEST["lstOperadoras"] = $retorno;
		//print_r($retorno);
	}
	
	function getFormasPagamento(){
		$ts = new TransacaoService();
		$retorno = $ts->getFormasPagamento();
		$_REQUEST["lstFormasPagamento"] = $retorno;
	}
	
	function getOperadoras(){
		$ts = new TransacaoService();
		$usr = $_SESSION["dados_empresa"]["cnpj"];
		$pwd = $_SESSION["dados_empresa"]["senha"];
		
		$retorno = $ts->getOperadorasPorEmpresa($_POST["codFP"], $usr, $pwd);
		echo json_encode($retorno);
// 		echo $retorno;
	}
	
	function getAddress(){
		$bs = new BaseServices();
		$retorno = $bs->getAdrress($_POST["cep"]);
		echo json_encode($retorno);
	}