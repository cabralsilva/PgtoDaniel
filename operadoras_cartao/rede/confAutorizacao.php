<?php
	@include("TransacaoRede.php");
	$transacao = new TransacaoRedeCar();
	if ($transacao->ConfirmarTransacaoParam("1006993069","0.01","04","20160516","4072","7444","testews","testews","06")){
		//echo "<br /><br />" . $transacao->getConfCodRet();
		echo "<br />" . $transacao->getConfMsgRet();
		
	}else echo "ERRO 123";
?>