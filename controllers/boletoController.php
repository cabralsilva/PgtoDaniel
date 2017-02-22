<?php
require_once '../util/constantes.php';
require_once '../util/funcoes.php';
require_once '../services/TransacaoService.php';
session_start ();
function processarBoleto($retorno){
	if ($retorno ["CodStatus"] == 1) {
		// ------------------------- DADOS DIN�MICOS DO SEU CLIENTE PARA A GERA��O DO BOLETO (FIXO OU VIA GET) -------------------- //
		// Os valores abaixo podem ser colocados manualmente ou ajustados p/ formul�rio c/ POST, GET ou de BD (MySql,Postgre,etc) //
	
		// DADOS DO BOLETO PARA O SEU CLIENTE
		$dataV = new DateTime ( $retorno ["Model"] ["data_vencimento_boleto"] );
		$dataV = $dataV->format ( 'd/m/y' );
		$dataB = new DateTime ( $retorno ["Model"] ["data_hora_transacao"] );
		$dataB = $dataB->format ( 'd/m/y' );
		$valor_boleto = number_format($retorno ["Model"] ["valor_transacao"],2,",",".");
		
		//$valor_boleto = str_replace ( ".", ",", $retorno ["Model"] ["valor_transacao"] );
		
		
		$dadosboleto ["nosso_numero"] = zerosEsquerda ( $retorno ["Model"]  ["id_origem"] , 1 ) . zerosEsquerda ( $retorno ["Model"] ["fk_pedido_pagamento"], 9 );
		$dadosboleto ["numero_documento"] = zerosEsquerda ( $retorno ["Model"] ["fk_pedido"], 10 ); // Num do pedido ou do documento
		$dadosboleto ["data_vencimento"] = $dataV; // Data de Vencimento do Boleto - REGRA: Formato DD/MM/AAAA
		$dadosboleto ["data_documento"] = $dataB; // Data de emiss�o do Boleto
		$dadosboleto ["data_processamento"] = date ( "d/m/Y" ); // Data de processamento do boleto (opcional)
		$dadosboleto ["valor_boleto"] = $valor_boleto; // Valor do Boleto - REGRA: Com v�rgula e sempre com duas casas depois da virgula
		 
		// DADOS DO SEU CLIENTE
		$dadosboleto ["sacado"] = $retorno ["Model"] ["nome_pagador"];
		$dadosboleto ["endereco1"] = $retorno ["Model"] ["logradouro_pagador"] . ", " . $retorno ["Model"] ["numero_end_pagador"] . ", " . $retorno ["Model"] ["complemento_end_pagador"] . ", " . $retorno ["Model"] ["bairro_pagador"];
		$dadosboleto ["endereco2"] = $retorno ["Model"] ["cidade_pagador"] . " - " . $retorno ["Model"] ["uf_pagador"] . " - " . $retorno ["Model"] ["cep_pagador"];
	
		// INFORMACOES PARA O CLIENTE
		$dadosboleto ["demonstrativo1"] = $retorno ["Model"] ["texto_boleto_html"];
		$dadosboleto ["demonstrativo2"] = "";
		$dadosboleto ["demonstrativo3"] = "";
	
		// INSTRU��ES PARA O CAIXA
		$dadosboleto ["instrucoes1"] = "";
		$dadosboleto ["instrucoes2"] = "";
		$dadosboleto ["instrucoes3"] = "";
		$dadosboleto ["instrucoes4"] = "";
	
		// DADOS OPCIONAIS DE ACORDO COM O BANCO OU CLIENTE
		$dadosboleto ["quantidade"] = "1";
		$dadosboleto ["valor_unitario"] = "1";
		$dadosboleto ["aceite"] = $retorno ["Model"] ["aceite_boleto"];
		$dadosboleto ["especie"] = "R$";
		$dadosboleto ["especie_doc"] = "DM";
	
		// ---------------------- DADOS FIXOS DE CONFIGURA��O DO SEU BOLETO --------------- //
	
		// DADOS DA SUA CONTA - BANCO DO BRASIL
		$dadosboleto ["agencia"] = $retorno ["Model"] ["numero_agencia"]; // Num da agencia, sem digito
		$dadosboleto ["conta"] = $retorno ["Model"] ["numero_conta"]; // Num da conta, sem digito
	
		// DADOS PERSONALIZADOS - BANCO DO BRASIL
		$dadosboleto ["convenio"] = $retorno ["Model"] ["num_convenio_banco_brasil"]; // Num do conv�nio - REGRA: 6 ou 7 ou 8 d�gitos
		$dadosboleto ["contrato"] = $retorno ["Model"] ["num_contrato_bb"]; // Num do seu contrato
		$dadosboleto ["carteira"] = $retorno ["Model"] ["codigo_carteira"];
		$dadosboleto ["variacao_carteira"] = zerosEsquerda ( $retorno ["Model"] ["codigo_variacao_carteira_banco_brasil"], 3 ); // Varia��o da Carteira, com tra�o (opcional)
		 
		// TIPO DO BOLETO
		$dadosboleto ["formatacao_convenio"] = strlen ( $retorno ["Model"] ["num_convenio_banco_brasil"] ); // REGRA: 8 p/ Conv�nio c/ 8 d�gitos, 7 p/ Conv�nio c/ 7 d�gitos, ou 6 se Conv�nio c/ 6 d�gitos
		$dadosboleto ["formatacao_nosso_numero"] = "2"; // REGRA: Usado apenas p/ Conv�nio c/ 6 d�gitos: informe 1 se for NossoN�mero de at� 5 d�gitos ou 2 para op��o de at� 17 d�gitos
	
		/*
		 * #################################################
		 * DESENVOLVIDO PARA CARTEIRA 18
		 *
		 * - Carteira 18 com Convenio de 8 digitos
		 * Nosso n�mero: pode ser at� 9 d�gitos
		 *
		 * - Carteira 18 com Convenio de 7 digitos
		 * Nosso n�mero: pode ser at� 10 d�gitos
		 *
		 * - Carteira 18 com Convenio de 6 digitos
		 * Nosso n�mero:
		 * de 1 a 99999 para op��o de at� 5 d�gitos
		 * de 1 a 99999999999999999 para op��o de at� 17 d�gitos
		 *
		 * #################################################
		 */
	
		// SEUS DADOS
		$endereco = $retorno ["Model"] ["Empresa"] ["LOGRADOURO"]. ", ".$retorno ["Model"] ["Empresa"] ["NUMERO"];
		$endereco .= (($retorno ["Model"] ["Empresa"] ["COMPLEMENTO"] != NULL)? "- ".$retorno ["Model"] ["Empresa"] ["COMPLEMENTO"]:"").", ".$retorno ["Model"] ["Empresa"] ["BAIRRO"];
		$dadosboleto ["identificacao"] = $retorno ["Model"] ["Empresa"] ["RAZAO_SOCIAL"];
		$dadosboleto ["cpf_cnpj"] = $retorno ["Model"] ["Empresa"] ["CNPJ"];
		$dadosboleto ["endereco"] = $endereco;
		$dadosboleto ["cidade_uf"] = $retorno ["Model"] ["Empresa"] ["MUNICIPIO"];
		$dadosboleto ["cedente"] = $retorno ["Model"] ["Empresa"] ["RAZAO_SOCIAL"];
	    $dadosboleto ["id_empresa"] = $retorno ["Model"] ["fk_empresa"];
	
	
		include("../util/boletos/include/funcoes_bb.php");
		include ("../util/boletos/include/layout_bb.php");
		/*
		 * $dadosboleto ["numero_documento"] = "27.030195.10"; // Num do pedido ou do documento
		 * $dadosboleto ["data_vencimento"] = $data_venc; // Data de Vencimento do Boleto - REGRA: Formato DD/MM/AAAA
		 * $dadosboleto ["data_documento"] = date ( "d/m/Y" ); // Data de emiss�o do Boleto
		 * $dadosboleto ["data_processamento"] = date ( "d/m/Y" ); // Data de processamento do boleto (opcional)
		 * $dadosboleto ["valor_boleto"] = $valor_boleto; // Valor do Boleto - REGRA: Com v�rgula e sempre com duas casas depois da virgula
		 *
		 * // DADOS DO SEU CLIENTE
		 * $dadosboleto ["sacado"] = "Nome do seu Cliente";
		 * $dadosboleto ["endereco1"] = "Endere�o do seu Cliente";
		 * $dadosboleto ["endereco2"] = "Cidade - Estado - CEP: 00000-000";
		 *
		 * // INFORMACOES PARA O CLIENTE
		 * $dadosboleto ["demonstrativo1"] = "Pagamento de Compra na Loja Nonononono";
		 * $dadosboleto ["demonstrativo2"] = "Mensalidade referente a nonon nonooon nononon<br>Taxa banc�ria - R$ " . number_format ( $taxa_boleto, 2, ',', '' );
		 * $dadosboleto ["demonstrativo3"] = "BoletoPhp - http://www.boletophp.com.br";
		 *
		 * // INSTRU��ES PARA O CAIXA
		 * $dadosboleto ["instrucoes1"] = "- Sr. Caixa, cobrar multa de 2% ap�s o vencimento";
		 * $dadosboleto ["instrucoes2"] = "- Receber at� 10 dias ap�s o vencimento";
		 * $dadosboleto ["instrucoes3"] = "- Em caso de d�vidas entre em contato conosco: xxxx@xxxx.com.br";
		 * $dadosboleto ["instrucoes4"] = "&nbsp; Emitido pelo sistema Projeto BoletoPhp - www.boletophp.com.br";
		 *
		 * // DADOS OPCIONAIS DE ACORDO COM O BANCO OU CLIENTE
		 * $dadosboleto ["quantidade"] = "10";
		 * $dadosboleto ["valor_unitario"] = "10";
		 * $dadosboleto ["aceite"] = "N";
		 * $dadosboleto ["especie"] = "R$";
		 * $dadosboleto ["especie_doc"] = "DM";
		 *
		 * // ---------------------- DADOS FIXOS DE CONFIGURA��O DO SEU BOLETO --------------- //
		 *
		 * // DADOS DA SUA CONTA - BANCO DO BRASIL
		 * $dadosboleto ["agencia"] = "9999"; // Num da agencia, sem digito
		 * $dadosboleto ["conta"] = "99999"; // Num da conta, sem digito
		 *
		 * // DADOS PERSONALIZADOS - BANCO DO BRASIL
		 * $dadosboleto ["convenio"] = "7777777"; // Num do conv�nio - REGRA: 6 ou 7 ou 8 d�gitos
		 * $dadosboleto ["contrato"] = "999999"; // Num do seu contrato
		 * $dadosboleto ["carteira"] = "18";
		 * $dadosboleto ["variacao_carteira"] = "-019"; // Varia��o da Carteira, com tra�o (opcional)
		 *
		 * // TIPO DO BOLETO
		 * $dadosboleto ["formatacao_convenio"] = "7"; // REGRA: 8 p/ Conv�nio c/ 8 d�gitos, 7 p/ Conv�nio c/ 7 d�gitos, ou 6 se Conv�nio c/ 6 d�gitos
		 * $dadosboleto ["formatacao_nosso_numero"] = "2"; // REGRA: Usado apenas p/ Conv�nio c/ 6 d�gitos: informe 1 se for NossoN�mero de at� 5 d�gitos ou 2 para op��o de at� 17 d�gitos
		 */
		/*
		 * #################################################
		 * DESENVOLVIDO PARA CARTEIRA 18
		 *
		 * - Carteira 18 com Convenio de 8 digitos
		 * Nosso n�mero: pode ser at� 9 d�gitos
		 *
		 * - Carteira 18 com Convenio de 7 digitos
		 * Nosso n�mero: pode ser at� 10 d�gitos
		 *
		 * - Carteira 18 com Convenio de 6 digitos
		 * Nosso n�mero:
		 * de 1 a 99999 para op��o de at� 5 d�gitos
		 * de 1 a 99999999999999999 para op��o de at� 17 d�gitos
		 *
		 * #################################################
		 */
	
		// SEUS DADOS
		// $dadosboleto ["identificacao"] = "BoletoPhp - C�digo Aberto de Sistema de Boletos";
		// $dadosboleto ["cpf_cnpj"] = "";
		// $dadosboleto ["endereco"] = "Coloque o endere�o da sua empresa aqui";
		// $dadosboleto ["cidade_uf"] = "Cidade / Estado";
		// $dadosboleto ["cedente"] = "Coloque a Raz�o Social da sua empresa aqui";
	}
}

if (isset ( $_GET ["idT"] )) {
	
	if (isset ($_SESSION["dados_empresa"]["cnpj"]) && isset($_SESSION["dados_empresa"]["senha"])){
		$usr = $_SESSION["dados_empresa"]["cnpj"];
		$pwd = $_SESSION["dados_empresa"]["senha"];
	}elseif (isset($_GET["usr"]) && isset($_GET["pwd"])){
		$usr = $_GET["usr"];
		$pwd = $_GET["pwd"];
	}else {
		
	}
	$ts = new TransacaoService ();
	$retorno = $ts->getTransacaoAut( $_GET ["idT"], $usr, $pwd );
	processarBoleto($retorno);
	
} else {
	// echo "SERVICO NÃO CATALOGADO";
}


	