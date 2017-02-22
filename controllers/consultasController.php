<?php
require_once '../services/TransacaoService.php';
require_once '../services/StatusService.php';
require_once '../services/OrigensService.php';
class ConsultasController {
	function __construct() {
		session_start ();
		define ( "Page", " Buscas e totalizadores" );
	}
	public function buscarOperadorasBoleto() {
		$ts = new TransacaoService ();
		$retorno = $ts->getOperadorasBoleto ( $_SESSION["dados_empresa"]["cod_empresa"] );
		
		$operadoras = array ();
		foreach ( $retorno as $linha ) {
			$conta = array (
					"idOperadoraEmp" => $linha ["id_operadora_empresa"],
					"carteira" => $linha ["codigo_carteira"],
					"agencia" => $linha ["numero_agencia"],
					"conta" => $linha ["numero_conta"] 
			);
			
			$operador = array (
					"idOperadora" => $linha ["id_operadora"],
					"nomeOperadora" => $linha ["nome_operadora"],
					"contas" => array (
							$conta 
					) 
			);
			
			$existe = false;
			foreach ( $operadoras as $operadora ) {
				if ($operadora ["idOperadora"] == $operador ["idOperadora"]) {
					$existe = true;
					break;
				}
			}
			
			if (! $existe) {
				array_push ( $operadoras, $operador );
			} else {
				foreach ( $operadoras as $key => $value ) {
					if ($operadoras [$key] ["idOperadora"] == $operador ["idOperadora"]) {
						array_push ( $operadoras [$key] ["contas"], $conta );
						break;
					}
				}
			}
		}
		$_REQUEST ["lstOperadoras"] = $operadoras;
	}
	public function buscarStatus() {
		$ss = new StatusService ();
		$retorno = $ss->getStatusBoleto ();
		$_REQUEST ["lstStatusBoleto"] = $retorno;
		
		$retorno = $ss->getStatusCartao ();
		$_REQUEST ["lstStatusCartao"] = $retorno;
		
		$retorno = $ss->getStatusTodos ();
		$_REQUEST ["lstStatusTodos"] = $retorno;
	}
	
	public function buscarOrigens(){
		$os = new OrigensService();
		$_REQUEST ["lstOrigens"] = $os->getOrigens();
	}
}

if (isset ( $_POST ["servico"] )) {
	if ($_POST ["servico"] == "buscarBoletosFiltro")
		buscarBoletosFiltro ();
	elseif ($_POST ["servico"] == "alterarStatus")
		alterarStatus ();
} else {
	// echo "SERVICO NÃO CATALOGADO";
}
function alterarStatus() {
	$ts = new TransacaoService ();
	$newStatus = $_POST ["id_status"];
	$idTransacao = $_POST ["id_transacao"];
	$idRemessa = $_POST ["id_remessa"];
	try {
		
		$ts->updateStatusTransaction ( $idTransacao, $newStatus, $idRemessa );
		
		$model = $ts->getDescricaoStatusTransaction ( $newStatus );
		$model ["id_transacao"] = $idTransacao;
		$model ["id_status"] = $newStatus;
		echo json_encode ( array (
				'CodStatus' => 1,
				'Msg' => 'Atualizado com sucesso',
				'Model' => $model 
		) );
	} catch ( Exception $e ) {
		
		echo json_encode ( array (
				'CodStatus' => 2,
				'Msg' => $e->getMessage (),
				'Model' => null 
		) );
	}
}
function buscarBoletosFiltro() {
	$ts = new TransacaoService ();
	session_start ();
	
// 	$identificador = $_POST ["identificador"];
	$_POST ["lstOrigem"] = split ( ",", $_POST ["lstOrigem"] );
// 	$codOrigem = $_POST ["codOrigem"];
// 	$codPagamento = $_POST ["codPagamento"];
	$_POST ["lstOperadoras"] = split ( ",", $_POST ["lstOperadoras"] );
	
	if($_POST["dataOrigemI"] != "" && $_POST ["dataOrigemF"] != ""){
		$_POST ["dataOrigemI"] = str_replace ( '/', '-', $_POST ["dataOrigemI"] );
		$_POST ["dataOrigemF"] = str_replace ( '/', '-', $_POST ["dataOrigemF"] );
		$_POST ["dataOrigemI"] = date ( "Y-m-d H:i:s", strtotime ( $_POST ["dataOrigemI"] . " 00:00:00" ) );
		$_POST ["dataOrigemF"] = date ( "Y-m-d H:i:s", strtotime ( $_POST ["dataOrigemF"] . " 23:59:59" ) );
	}else{
		$_POST ["dataOrigemI"] = null;
		$_POST ["dataOrigemF"] = null;
	}
// 	$dateI = $_POST ["dataOrigemI"];
// 	$dateF = $_POST ["dataOrigemF"];
	
	if($_POST["dataEntradaI"] != "" && $_POST ["dataEntradaF"] != ""){
		$_POST ["dataEntradaI"] = str_replace ( '/', '-', $_POST ["dataEntradaI"] );
		$_POST ["dataEntradaF"] = str_replace ( '/', '-', $_POST ["dataEntradaF"] );
		$_POST ["dataEntradaI"] = date ( "Y-m-d H:i:s", strtotime ( $_POST ["dataEntradaI"] . " 00:00:00" ) );
		$_POST ["dataEntradaF"] = date ( "Y-m-d H:i:s", strtotime ( $_POST ["dataEntradaF"] . " 23:59:59" ) );
	}else{
		$_POST ["dataEntradaI"] = null;
		$_POST ["dataEntradaF"] = null;
	}
// 	$dateI = $_POST ["dataEntradaI"];
// 	$dateF = $_POST ["dataEntradaF"];
	
	if($_POST["dataPagamentoI"] != "" && $_POST ["dataPagamentoF"] != ""){
		$_POST ["dataPagamentoI"] = str_replace ( '/', '-', $_POST ["dataPagamentoI"] );
		$_POST ["dataPagamentoF"] = str_replace ( '/', '-', $_POST ["dataPagamentoF"] );
		$_POST ["dataPagamentoI"] = date ( "Y-m-d H:i:s", strtotime ( $_POST ["dataPagamentoI"] . " 00:00:00" ) );
		$_POST ["dataPagamentoF"] = date ( "Y-m-d H:i:s", strtotime ( $_POST ["dataPagamentoF"] . " 23:59:59" ) );
	}else{
		$_POST ["dataPagamentoI"] = null;
		$_POST ["dataPagamentoF"] = null;
	}
// 	$dateI = $_POST ["dataPagamentoI"];
// 	$dateF = $_POST ["dataPagamentoF"];
	
	$_POST ["lstStatus"] = split ( ",", $_POST ["lstStatus"] );
	$_POST ["lstFormaPgto"] = split ( ",", $_POST ["lstFormaPgto"] );
	$_POST ["valorTransacao"] = str_replace ( ",", ".", $_POST ["valorTransacao"] );
	$_POST ["valorPago"] = str_replace ( ",", ".", $_POST ["valorPago"] );
	
	$listaPagamentos = $ts->buscarPersonalizadaPagamentos ( $_POST );
	// $_SESSION["listaTransacoesBoletos"] = array();
	// foreach ($listaPagamentos as $value){
	// array_push($_SESSION["listaTransacoesBoletos"], $value);
	// }
	// echo json_encode($_SESSION["listaTransacoesBoletos"]);
	echo json_encode ( $listaPagamentos );
}
	
	