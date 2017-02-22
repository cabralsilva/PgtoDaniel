<?php
	require_once '../util/constantes.php';
	require_once '../models/Transacao.php';
	require_once '../services/TransacaoService.php';
	session_start();
	
	if(isset($_POST["servico"])){
		if($_POST["servico"] == "buscarBoletos") buscarBoletos();
		elseif($_POST["servico"] == "buscarBoletosFiltro") buscarBoletosFiltro();
		elseif($_POST["servico"] == "gerarRemessa") gerarRemessa();
		elseif($_POST["servico"] == "gerarRemessaDia") gerarRemessaDia();
	}else{
		//echo "SERVICO NÃO CATALOGADO";
	}
	
	
	function buscarBoletos(){
		
		$ts = new TransacaoService();
		$listaTransacoesBoletos = $ts->getTransacoesBoletos($_SESSION["dados_acesso"][0]["CODIGO"]);
		$_SESSION["listaTransacoesBoletos"] = array();
		foreach ($listaTransacoesBoletos as $key => $boleto){
			array_push($_SESSION["listaTransacoesBoletos"], $boleto);
		}
		print_r($_SESSION["listaTransacoesBoletos"]);
// 		header("Location: ".BaseProjeto."/views/listaBoletos");

		
	}
	
	function buscarBoletosFiltro(){
		$ts = new TransacaoService();
		$_POST["dataPeriodoI"] = str_replace('/', '-', $_POST["dataPeriodoI"]);
		$_POST["dataPeriodoF"] = str_replace('/', '-', $_POST["dataPeriodoF"]);
		$_POST["dataPeriodoI"] = date("Y-m-d H:i:s", strtotime($_POST["dataPeriodoI"] . " 00:00:00"));
		$_POST["dataPeriodoF"] = date("Y-m-d H:i:s", strtotime($_POST["dataPeriodoF"] . " 23:59:59"));
		$_POST["valorTransacao"] = str_replace(",", ".", $_POST["valorTransacao"]);
		
		$_POST["operadoras"] = substr($_POST["operadoras"], 0, strlen($_POST["operadoras"]) -1 );
		$_POST["status"] = substr($_POST["status"], 0, strlen($_POST["status"]) -1 );
		$listaoperadoras = split(",", $_POST["operadoras"]);
		$listastatus = split(",", $_POST["status"]);
		
		$listaTransacoesBoletos = $ts->getTransacoesBoletosFiltro(
				$_SESSION["dados_acesso"][0]["CODIGO"]
				, $_POST["dataPeriodoI"]
				, $_POST["dataPeriodoF"]
				, $listaoperadoras
				, $listastatus
				, $_POST["codigoPedido"]
				, $_POST["valorTransacao"]
				, $_POST["codTransacao"]);
		$_SESSION["listaTransacoesBoletos"] = array();
		foreach ($listaTransacoesBoletos as $value){
			array_push($_SESSION["listaTransacoesBoletos"], $value);
		}
		echo json_encode($_SESSION["listaTransacoesBoletos"]);
	}
	
	function gerarRemessa(){
		$ts = new TransacaoService();
		$operadoraAnterior = 0;
		$hasMoreOne = false;
		for ($i = 0; $i < count($_SESSION["listaTransacoesBoletos"]); $i++){
			if ($i > 0){
				$operadoraAnterior = $_SESSION["listaTransacoesBoletos"][($i-1)]["fk_operadora"];
				if ($_SESSION["listaTransacoesBoletos"][$i]["fk_operadora"] != $operadoraAnterior) $hasMoreOne = true;
			}
		}
		if ($hasMoreOne) echo "2";
		else echo $ts->gerarRemessaBradescos400($_SESSION["listaTransacoesBoletos"]);
	}
	
	function gerarRemessaDia(){
		$ts = new TransacaoService();
		$_POST["dataRemessa"] = str_replace('/', '-', $_POST["dataRemessa"]);
		$dataI = date("Y-m-d H:i:s", strtotime($_POST["dataRemessa"] . " 00:00:00"));
		$dataF = date("Y-m-d H:i:s", strtotime($_POST["dataRemessa"] . " 23:59:59"));
		
		if($_POST["banco"] == 3) $retorno = $ts->gerarRemessaBradescos400Dia($dataI, $dataF, $_POST["banco"], $_SESSION["dados_acesso"][0]["CODIGO"]);
		elseif($_POST["banco"] == 4) $retorno = $ts->gerarRemessaBancodoBrasil400Dia($dataI, $dataF, $_POST["banco"], $_SESSION["dados_acesso"][0]["CODIGO"]);
		echo json_encode($retorno);
	}
	
	function getOperadorasBoleto(){
		$ts = new TransacaoService();
		$retorno = $ts->getOperadorasBoleto($_SESSION["dados_acesso"][0]["CODIGO"]);
		$_REQUEST["lstOperadoras"] = $retorno;
		//print_r($retorno);
	}