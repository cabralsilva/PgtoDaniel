<?php
	function zerosEsquerda($number, $sizeField) {
		$number = str_pad ( $number, $sizeField, '0', STR_PAD_LEFT );
		return $number;
	}
	
	function comandoRetorno($valor){
		switch ($valor){
			case "02":
				return "Confirmada entrada de título";
			case "03":
				return "Registro recusado";
			case "05":
				return "Liquidação sem registro";
			case "06":
				return "Liquidação normal";
			case "07":
				return "Liquidação por conta/parcial";
			case "08":
				return "Liquidação por saldo";
			case "09":
				return "Baixa de título";
			case "10":
				return "Baixa solicitada";
			case "11":
				return "Título em ser";
			case "12":
				return "Abatimento concedido";
			case "13":
				return "Abatimento cancelado";
			case "14":
				return "Alteração de vencimento do título";
			case "15":
				return "Liquidação em cartório";
			case "16":
				return "Confirmação alteração juros de mora";
			case "19":
				return "Conf. recebimento de instrução para protesto";
			case "20":
				return "Débito em conta";
			case "21":
				return "Alteração nome sacado";
			case "22":
				return "Alteração endereço sacado";
			case "23":
				return "Indicação de encaminhamento a cartório";
			case "24":
				return "Sustar protesto";
			case "25":
				return "Dispensar juros de mora";
			case "26":
				return "Alteração nº título dado pelo cedente";
			case "28":
				return "Manutenção de titulo vencido";
			case "31":
				return "Conceder desconto";
			case "32":
				return "Não conceder desconto";
			case "33":
				return "Retificar desconto";
			case "34":
				return "Alterar data para desconto";
			case "35":
				return "Cobrar multa";
			case "36":
				return "Dispensar multa";
			case "37":
				return "Dispensar indexador";
			case "38":
				return "Dispensar prazo limite para recebimento";
			case "39":
				return "Alterar prazo limite para recebimento";
			case "41":
				return "Alter. nº controle do participante";
			case "42":
				return "Alter. nº documento do sacado";
			case "44":
				return "Título pago com cheque devolvido";
			case "46":
				return "Titulo pago com cheque, aguardando compensação";
			case "72":
				return "Alteração tipo de cobrança";
			case "73":
				return "Conf. de instr. de paramento de pagamento inicial";
			case "96":
				return "Despesas de protesto";
			case "97":
				return "Despesas de sustação de protesto";
			case "98":
				return "Débito de custas antecipada";
		}
	}
?>