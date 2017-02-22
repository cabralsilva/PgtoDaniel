<?php
	@include("TransacaoRede.php");
	$transacao = new TransacaoRedeCar();
	
	if ($transacao->RequisicaoAutorizacaoDireta()){
		//var_dump($transacao->getRetornoWs());
		if ((($transacao->getConfCodRet() == 0) or ($transacao->getConfCodRet() == 1)) and ($transacao->getConfCodRet() != "")){
			echo "<br /><br />" .$transacao->getConfCodRet() . " - " . $transacao->getConfMsgRet();
			
			if ($transacao->EstornoDireto()){
				echo "<br /><br />";
				//var_dump($transacao->getRetornoWs());

			}
		}
	}
?>