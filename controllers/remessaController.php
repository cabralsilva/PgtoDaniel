<?php
	require_once '../services/TransacaoService.php';
	
	class RemessaController{
		function __construct() {
		}
		
		public function buscarOperadorasBoleto(){
			$ts = new TransacaoService();
			$retorno = $ts->getOperadorasBoleto($_SESSION["dados_empresa"]["cod_empresa"]);
			
			$operadoras = array();
			foreach($retorno as $linha){
				$conta = array(
					"idOperadoraEmp" => $linha["id_operadora_empresa"],
					"carteira" => $linha["codigo_carteira"],
					"agencia" => $linha["numero_agencia"],
					"conta" => $linha["numero_conta"]
				);	
				
				$operador = array(
					"idOperadora" => $linha["id_operadora"],
					"nomeOperadora" => $linha["nome_operadora"],
					"contas" => array($conta)
				);
				
				$existe = false;
				foreach ($operadoras as $operadora){
					if ($operadora["idOperadora"] == $operador["idOperadora"]){
						$existe = true;
						break;
					}
				}
				
				if (!$existe){
					array_push($operadoras, $operador);
				}else{
					foreach ($operadoras as $key => $value){
						if ($operadoras[$key]["idOperadora"] == $operador["idOperadora"]){
							array_push($operadoras[$key]["contas"], $conta);
							break;
						}
					}
				}
				
			}
			$_REQUEST["lstOperadoras"] = $operadoras;
		}
	}
	
	
	if(isset($_POST["servico"])){
		if($_POST["servico"] == "gerarRemessaDia") gerarRemessaDia();
	}else{
		//echo "SERVICO NÃO CATALOGADO";
	}
	
	function gerarRemessaDia(){
		$ts = new TransacaoService();
		session_start();
		$_POST["dataRemessa"] = str_replace('/', '-', $_POST["dataRemessa"]);
		$dataI = date("Y-m-d H:i:s", strtotime($_POST["dataRemessa"] . " 00:00:00"));
		$dataF = date("Y-m-d H:i:s", strtotime($_POST["dataRemessa"] . " 23:59:59"));
		
		
		$retorno = $ts->gerarRemessa($dataI, $dataF, $_POST["banco"], $_SESSION["dados_empresa"]["cod_empresa"]);
		
		echo json_encode($retorno);
	}