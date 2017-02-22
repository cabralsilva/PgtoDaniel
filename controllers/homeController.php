<?php
	require_once '../services/TransacaoService.php';
	
	class HomeController{
		private $listPagamentosPendentes = array();
		private $listPagamentosBoletosPendentes = array();
		private $listPagamentosCartoesPendentes = array();
		private $listPagamentosBoletosRemessaPendentes = array();
		private $listPagamentosBoletosRetornoPendentes = array();
		function __construct() {
			session_start();
			define("Page", " Pagamentos pendentes");
		}
		
		public function buscarPagamentosPendentes(){
			$ts = new TransacaoService();
			$this->listPagamentosPendentes = $ts->getPagamentosPendentes($_SESSION["dados_empresa"]["cod_empresa"]);
			foreach($this->listPagamentosPendentes as $pagamentoPendente){
				
				if($pagamentoPendente["id_forma_pagamento"] == 22)
					array_push($this->listPagamentosBoletosPendentes, $pagamentoPendente);
				else 
					array_push($this->listPagamentosCartoesPendentes, $pagamentoPendente);
			}
			
			foreach($this->listPagamentosBoletosPendentes as $pagamentoBoletoPendente){
				
				if(in_array($pagamentoBoletoPendente["fk_status"], [8, 11, 12]))
					array_push($this->listPagamentosBoletosRemessaPendentes, $pagamentoBoletoPendente);
				elseif(in_array($pagamentoBoletoPendente["fk_status"], [1, 3, 5])) //VERIFICIAR CONDIÇÃO PARA RETORNOS JÁ PROCESSADOS
					array_push($this->listPagamentosBoletosRetornoPendentes, $pagamentoBoletoPendente);
			}
		}
		
		public function getListPagamentosPendentes(){
			return $this->listPagamentosPendentes;
		}
		public function getListPagamentosBoletosPendentes(){
			return $this->listPagamentosBoletosPendentes;
		}
		public function getListPagamentosCartoesPendentes(){
			return $this->listPagamentosCartoesPendentes;
		}
		public function getListPagamentosBoletosRemessaPendentes(){
			return $this->listPagamentosBoletosRemessaPendentes;
		}
		public function getListPagamentosBoletosRetornoPendentes(){
			return $this->listPagamentosBoletosRetornoPendentes;
		}
		
	}
	
	if(isset($_POST["servico"])){
		if($_POST["servico"] == "buscarBoletos") buscarBoletos();
		elseif($_POST["servico"] == "buscarBoletosFiltro") buscarBoletosFiltro();
		elseif($_POST["servico"] == "gerarRemessa") gerarRemessa();
		elseif($_POST["servico"] == "prepararBaixa") prepararBaixa();
	}else{
		//echo "SERVICO NÃO CATALOGADO";
	}
	
	
	function prepararBaixa(){
		$ts = new TransacaoService();
		$transacaoPai = $ts->getTransacao($_POST["id_transacao"]);
		if ($transacaoPai){
			print_r($ts->insertTransactionBaixa($transacaoPai));
// 			echo "\n\nEncontrou";
		}
		
// 		print_r($_POST);//"Testes de conexão de arquivos php";
	}