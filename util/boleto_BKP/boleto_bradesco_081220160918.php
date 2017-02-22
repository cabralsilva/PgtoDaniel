<?php

	//header("Content-Type: text/html; charset=ISO-8859-1",true) ;
	header('Content-Type: text/html; charset=utf-8');
	?>
	<!--<meta http-equiv="content-type" content="text/html;charset=utf-8" />-->
	<?php
	require_once("../class/constantes.php");
	require_once("../class/filemaker.class.php");
	require_once("../actions/funcoes.php");
	require_once("../actions/funcoesWS.php");
	include("../class/PHPMailer-master/class.phpmailer.php");

	if($_GET["id"] != ""){

		$_pedido = array(
			'codigoPedido' => $_GET["id"],
			'usr' => 'pdroqtl',
			'pwd' => 'jck9com*'
		); 
		$_resposta = sendWsJson(json_encode($_pedido), UrlWs . "getPedidoCodigo");
		
		if ($_resposta->codStatus == 1){
			
			$data1 = date("m/d/Y");

			// DADOS DO BOLETO PARA O SEU CLIENTE
			$dias_de_prazo_para_pagamento = 7;
			$taxa_boleto = "";
			$data_venc = date("d/m/Y", time() + ($dias_de_prazo_para_pagamento * 86400));  // Prazo de X dias OU informe data: "13/04/2006"; 
			$valor = $_resposta->model->ValorFinal; // Primeiro tira os pontos
			
			$valor_cobrado = $valor; // Valor - REGRA: Sem pontos na milhar e tanto faz com "." ou "," ou com 1 ou 2 ou sem casa decimal
			$valor_cobrado = str_replace(",", ".",$valor_cobrado);
			$valor_boleto=number_format($valor_cobrado+$taxa_boleto, 2, ',', '');
			
			$data_inverter = explode("-",$_resposta->model->DataVencimento);
			$x = $data_inverter[2].'/'. $data_inverter[1].'/'. $data_inverter[0];
			  
			$dadosboleto["nosso_numero"] =  $_resposta->model->codigoPedido; // Nosso numero sem o DV - REGRA: Máximo de 11 caracteres!****************
			$dadosboleto["numero_documento"] = $dadosboleto["nosso_numero"];	// Num do pedido ou do documento = Nosso numero
			$dadosboleto["data_vencimento"] =  $x; // Data de Vencimento do Boleto - REGRA: Formato DD/MM/AAAA
			$dadosboleto["data_documento"] = date("d/m/Y"); // Data de emissão do Boleto
			$dadosboleto["data_processamento"] = date("d/m/Y"); // Data de processamento do boleto (opcional)
			$dadosboleto["valor_boleto"] = $valor_boleto; 	// Valor do Boleto - REGRA: Com vírgula e sempre com duas casas depois da virgula

			// DADOS DO SEU CLIENTE
			if($_resposta->model->CodigoCliente == "" or $_resposta->model->CodigoCliente == null){
				$dadosboleto["sacado"] = $_resposta->model->ClienteNome;
				$dadosboleto["endereco1"] = $_resposta->model->ClienteRua ."-".$_resposta->model->ClienteBairro."-".$_resposta->model->ClienteNumero;
				$dadosboleto["endereco2"] = $_resposta->model->ClienteMunicipio."-".$_resposta->model->ClienteUf."-". "CEP:".$_resposta->model->ClienteCep;

			}else{
				$_cliente = array(
					'codigoCliente' => $_resposta->model->CodigoCliente
				);
				$_respostaCliente = sendWsJson(json_encode($_cliente), UrlWs . "getClienteCodigo");
			
				$dadosboleto["sacado"] = $_respostaCliente->model->Nome;
				$dadosboleto["endereco1"] = $_respostaCliente->model->Logradouro."-".$_respostaCliente->model->Bairro."-".$_respostaCliente->model->Numero;
				$dadosboleto["endereco2"] = $_respostaCliente->model->Municipio."-".$_respostaCliente->model->Uf."-". "CEP:".$_respostaCliente->model->Cep;

			}
			
			// INFORMACOES PARA O CLIENTE
			$dadosboleto["demonstrativo1"] = "Pagamento de Compra na Loja PLander";
			$dadosboleto["demonstrativo2"] = "";
			$dadosboleto["demonstrativo3"] = "Plander - http://www.plander.com.br";
			$dadosboleto["instrucoes1"] = "- Sr.Caixa, não receber após a data de vencimento";
			$dadosboleto["instrucoes2"] = "- Cliente: Não efetuando o pagamento dentro do prazo de 2 dias corridos, o pedido será cancelado.";
			$dadosboleto["instrucoes3"] = "- Em caso de dúvidas entre em contato conosco: contato@plander.com.br";
			$dadosboleto["instrucoes4"] = "- &nbsp; Emitido pelo sistema Plander - www.Plander.com.br";

			if($_resposta->model->ValorFrete=="Transportadora"){
				$dadosboleto["instrucoes5"] = "- Valor de frete não incluso! O mesmo será cobrado na hora da entrega ";
			}else
				$dadosboleto["instrucoes5"] = "";
			
			// DADOS OPCIONAIS DE ACORDO COM O BANCO OU CLIENTE
			$dadosboleto["quantidade"] = $_resposta->model->Quantidade;
			$dadosboleto["valor_unitario"] = $valor_boleto;
			$dadosboleto["aceite"] = "N";		
			$dadosboleto["especie"] = "R$";
			$dadosboleto["especie_doc"] = "DM";
			
			
			// ---------------------- DADOS FIXOS DE CONFIGURAÇÃO DO SEU BOLETO --------------- //
			
			
			// DADOS DA SUA CONTA - Bradesco
			$dadosboleto["agencia"] = "2160"; // Num da agencia, sem digito
			$dadosboleto["agencia_dv"] = "1"; // Digito do Num da agencia
			$dadosboleto["conta"] = "7292"; 	// Num da conta, sem digito
			$dadosboleto["conta_dv"] = "3"; 	// Digito do Num da conta
			
			// DADOS PERSONALIZADOS - Bradesco
			$dadosboleto["conta_cedente"] = "7292"; // ContaCedente do Cliente, sem digito (Somente Números)**********
			$dadosboleto["conta_cedente_dv"] = "3"; // Digito da ContaCedente do Cliente ************
			$dadosboleto["carteira"] = "25";  // Código da Carteira: pode ser 06 ou 03 
			
			// SEUS DADOS
			$dadosboleto["identificacao"] = "Plander - 33233636 ";//***************
			$dadosboleto["cpf_cnpj"] = "02.600.446.0001-82";
			$dadosboleto["endereco"] = "R. Alferes Poli, 620 - Centro - Curitiba | PR ";//***********
			$dadosboleto["cep"] = "CEP 802330-090.";
			$dadosboleto["cidade_uf"] = "Curitiba / Paraná";//************
			$dadosboleto["cedente"] = "ORQUESTRAL PRODUTOS MUSICAIS LTDA";//*******
	
			// NÃO ALTERAR!
			include("include/funcoes_bradesco.php"); 
			include("include/layout_bradesco.php");

		} else {
			$_conteudo = "<strong><u>Mensagem de Log Erro do site www.plander.com.br</u></strong><br><br><br>";
			$_conteudo .= "<strong>Descrição: </strong>Falha ao gerar o boleto <u>" . $_resposta->msg . "</u> <br><br>";
			$_conteudo .= "<strong>Data: </strong>" . date('d/M/y G:i:s') . "<br><br>";
			$_conteudo .= "<strong>Página Anterior: </strong>" . (isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:"Não identificada") . "<br><br>";
			$_conteudo .= "<strong>Página Atual: </strong>" . $_SERVER['PHP_SELF'] . "<br><br>";
			$_conteudo .= "<strong>URL: </strong>" . $_SERVER['SERVER_NAME'] . $_SERVER ['REQUEST_URI'] . "<br><br>";
			$_conteudo .= "<strong>IP Cliente: </strong>" . $_SERVER["REMOTE_ADDR"] . "<br><br>";
			$_conteudo .= "<strong>Browser: </strong>" . getBrowser() . "<br><br>";
			$_conteudo .= "<strong>Sistema Operacional: </strong>" . php_uname() . "<br><br>";

			sendEmailLog($_conteudo);
			echo "<script> alert('Erro ao gerar boleto tente mais tarde');</script>>";
			echo "<script>parent.window.location.href =\"../carrinhoPagamento\";</script>";
		}
		
	} else {
		echo "<script> alert('Boleto inexistente.');</script>";
	}

?>
