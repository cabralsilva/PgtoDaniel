<?php
	@include("TransacaoCielo.php");
	$transacao = new Transacao();
	if ($transacao->ConsultarTransacaoParametro("1", "100699306900068CF1DA")) var_dump($transacao->getXml_Retorno_Cielo());

?>