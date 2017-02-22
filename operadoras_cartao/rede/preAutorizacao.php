<?php
	@include("TransacaoRede.php");
	$transacao = new TransacaoRedeCar();
	if ($transacao->RequisicaoPreTransacao()){
		//var_dump($transacao->getRetornoWs());
		
	}
?>