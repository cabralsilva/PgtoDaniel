<?php
	ini_set('default_charset','UTF-8');
	include("cielo/TransacaoCielo.php");
	include("rede/TransacaoRede.php");
	include("../../Banco.php");
	include("../../odbc.php");
	session_start();
	if ($_POST["operacao"] == 'startTransaction'){
		if ($_POST["idOperadora"] == 1){//cielo
			$transacao = new TransacaoCielo();
			$transacao->setTipoFormaPagamento($_POST["tipoFormaPgto"]);
			$transacao->setCodPedidoPagamento($_POST["idTransacao"]); 
			$transacao->setTaxa($_POST["TaxaTransacao"]);
			$transacao->setLiquido($_POST["valorLiquido"]);
			$transacao->setDados_Pd_Data_Hora_Retorno($_POST["dataPedido"]);
			$formPag = pegarBandeira();
			if ($_POST["numParcelas"] <= 1) $tipoPagamento = $formPag["cod_cielo_a_vista"];
			elseif ($_POST["numParcelas"] > 1) $tipoPagamento = $formPag["cod_cielo_prazo"];

			$validade = tratarValidade($_POST["CartaoValidade"]);
			$valor = tratarValorTotal($_POST["valorTransacao"]);
			$data = tratarData($_POST["dataPedido"] . " 12:00:00");
			$transacao->setDadosTeste($_POST["idPedido"], $_POST["CartaoNumero"], $validade, 1, $_POST["CartaoCodigoSeguranca"], $valor, $data, "Teste Origem", $formPag["bandeira_cartao"], $tipoPagamento, $_POST["numParcelas"], 3, $_POST["paramCapturaAutomatica"]);
			if ($transacao->RequisicaoTransacao()){
				//atualizar banco mysql e filemaker
				$bancoMysql = new BancoDados();
				$bancoMysql->setTransacao($transacao, $_SESSION["dados_empresa"]);
				$bancoFilemaker = new BancoODBC();
				$bancoFilemaker->atualizarPedidoPagamento($transacao);

				for ($i = 0; $i < count($_SESSION["listaPedidos"]); $i++){
					for($j = 0; $j < count($_SESSION["listaPedidos"][$i]["listaPagamentos"]); $j++){
						if ($_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["Codigo"] == $_POST["idTransacao"]){

							//print_r($_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]);

							$status = $transacao->getStatusGeral();
							$tid = $transacao->getTid_Retorno();
							$pan = $transacao->getPan_Retorno();
							$dataPedido = $transacao->getDados_Pd_Data_Hora_Retorno();
							$dataAutorizacao = $transacao->getAutorizacao_Data_Hora_Retorno();
							$dataAutenticacao = $transacao->getAutenticacao_data_hora_Retorno();
							$dataCaptura = $transacao->getDataHoraCapturaRetorno();
							$dataCancelamento = $transacao->getCancelamentoDataHoraRetorno();

							//print_r($dataCaptura);

							$_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["StatusTransPag"] = $status;
							$_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["TidTransacao"] = (string) $tid[0];
							$_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["PanTransacao"] = (string) $pan[0];


							if (($status == 1) or ($status == 2)) $_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["DataRetornoAutenticacao"] = (string) $dataAutenticacao[0];
							elseif (($status == 3) or ($status == 4)) $_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["DataRetornoAutorizacao"] = (string) $dataAutorizacao[0];
							elseif ($status == 5) $_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["DataRetornoCaptura"] = (string) $dataCaptura[0];							
							elseif ($status == 6) $_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["DataRetornoCancelamento"] = (string) $dataCancelamento[0];
							else $_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["DataRetorno"] = (string) $dataPedido[0];

							//VERIFICAR SE FOI PEDIDO UMA NOVA TRANSAÇÃO PARA INSERIR NO HISTÓRICO OU SOMENTE ATUALIZAR O STATUS DA TRANSAÇÃO ATUAL NO HISTÓRICO
							if (array_key_exists(0, $_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["listaHistorico"])) {
								if ($_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["listaHistorico"][0]["CodigoTransacao"] == $tid){
									$_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["listaHistorico"][0]["DataAutorizacao"] = (string) $dataAutorizacao[0];
									$_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["listaHistorico"][0]["DataAutenticacao"] = (string) $dataAutenticacao[0];
									$_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["listaHistorico"][0]["DataCaptura"] = (string) $dataCaptura[0];
									$_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["listaHistorico"][0]["DataCancelamento"] = (string) $dataCancelamento[0];
								}else{
									$novoHistorico = array();
									$novoHistorico["CodigoTransacao"] = (string) $tid[0];
									$novoHistorico["NomeOperadora"] = $_POST["nomeOperadora"];
									$novoHistorico["DataAutorizacao"] = (string) $dataAutorizacao[0];
									$novoHistorico["DataAutenticacao"] = (string) $dataAutenticacao[0];
									$novoHistorico["DataCaptura"] = (string) $dataCaptura[0];
									$novoHistorico["DataCancelamento"] =(string) $dataCancelamento[0];
									array_unshift($_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["listaHistorico"], $novoHistorico);
								}
							}else{
								$novoHistorico = array();
								$novoHistorico["CodigoTransacao"] = (string) $tid[0];
								$novoHistorico["NomeOperadora"] = $_POST["nomeOperadora"];
								$novoHistorico["DataAutorizacao"] = (string) $dataAutorizacao[0];
								$novoHistorico["DataAutenticacao"] = (string) $dataAutenticacao[0];
								$novoHistorico["DataCaptura"] = (string) $dataCaptura[0];
								$novoHistorico["DataCancelamento"] =(string) $dataCancelamento[0];
								$_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["listaHistorico"][0] = $novoHistorico;
							}
						}
					}
				}
				if ($transacao->getCodigo_Erro_Retorno() !== "null") msgRetorno("info", "Não Foi possível realizar a transação: ERRO {$transacao->getCodigo_Erro_Retorno()} -- {$transacao->getMsg_Erro_Retorno()}");
				else {
					switch ($transacao->getStatusGeral()) {
						case 1:
							msgRetorno("success", "Autenticação: " . $transacao->getAutenticacao_Mensagem_Retorno());
							break;
						case 2:
							msgRetorno("warning", "Autenticação: " . $transacao->getAutenticacao_Mensagem_Retorno());
							break;
						case 3:
							msgRetorno("success", "Autenticação: " . $transacao->getAutenticacao_Mensagem_Retorno() . " --- Autorização: " . $transacao->getAutorizacao_Mensagem_Retorno());
							break;
						case 4:
							msgRetorno("warning", "Autenticação: " . $transacao->getAutenticacao_Mensagem_Retorno() . " --- Autorização: " . $transacao->getAutorizacao_Mensagem_Retorno());
							break;
						case 5:
							msgRetorno("success", "Autenticação: " . $transacao->getAutenticacao_Mensagem_Retorno() . " --- Autorização: " . $transacao->getAutorizacao_Mensagem_Retorno() . " --- CAPTURA: " . $transacao->getMsgCapturaRetorno());
							break;
						default:
							msgRetorno("danger", "Houve um Erro na transação, contate o administrador do sistema {$transacao->getStatusGeral()}");
							break;
					}
				}
			}else ErroCielo($transacao->getCodigo_Erro_Retorno());
			
		}elseif ($_POST["idOperadora"] == 2) {//rede
			$transacao = new TransacaoRedeCar();
			$transacao->setTipoFormaPagamento($_POST["tipoFormaPgto"]);
			$transacao->setCodPedidoPagamento($_POST["idTransacao"]); 
			$transacao->setTaxa($_POST["TaxaTransacao"]);
			$transacao->setLiquido($_POST["valorLiquido"]);
			$transacao->setDados_Pd_Data_Hora_Retorno($_POST["dataPedido"]);
			$validade = tratarValidade($_POST["CartaoValidade"]);
			$formPag = pegarBandeira();
			if ($_POST["numParcelas"] <= 1) {
				$tipoPagamento = $formPag["cod_rede_a_vista"];
				$numParcelas = "00";
			}
			elseif ($_POST["numParcelas"] > 1) {
				$tipoPagamento = $formPag["cod_rede_prazo"];
				$numParcelas = $_POST["numParcelas"];
			}

			$transacao->setDadosTeste($_POST["idPedido"], $_POST["CartaoNumero"], $_POST["CartaoCodigoSeguranca"], $validade[0], $validade[1], $_POST["CartaoTitular"], $_POST["valorTransacao"], $tipoPagamento, $numParcelas,  $_POST["paramCapturaAutomatica"]);
			if ($transacao->RequisicaoAutorizacaoDireta()){

				if ($transacao->getCodRetAutorizacao() == 0){
					
					$transacao->setDataRetAutorizacao(date("Y-m-d", strtotime($transacao->getDataRetAutorizacao())) . ' ' . date("G:i:s"));

					$bancoMysql = new BancoDados();
					$bancoMysql->setTransacao($transacao, $_SESSION["dados_empresa"]);
					$bancoFilemaker = new BancoODBC();
					$bancoFilemaker->atualizarPedidoPagamento($transacao);

					for ($i = 0; $i < count($_SESSION["listaPedidos"]); $i++){
						for($j = 0; $j < count($_SESSION["listaPedidos"][$i]["listaPagamentos"]); $j++){
							if ($_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["Codigo"] == $_POST["idTransacao"]){
								$status = $transacao->getStatusGeral();
								$numSequencial = $transacao->getNumSequencRet();
								$numAutorizacao = $transacao->getNumRetAutorizacao();
								$numComprovante = $transacao->getNumRetComprovVenda();
								$numAutenticacao = $transacao->getNumRetAutenticacao();
								$dataAutorizacao = $transacao->getDataRetAutorizacao();

								$dataCaptura = $transacao->getDataRetCaptura();
								$dataCancelamento = $transacao->getDataRetCancelamento();

								$_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["StatusTransPag"] = $status;
								$_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["NumSequencialRede"] = (string)$numSequencial;
								
								$_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["NumAutorizacaoRede"] = (string)$numAutorizacao;
								$_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["NumComprovanteRede"] = (string)$numComprovante;
								$_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["NumAutenticacaoRede"] = (string)$numAutenticacao;

								if (($status == 3) or ($status == 4)) $_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["DataRetornoAutorizacao"] = (string) $dataAutorizacao;
								elseif ($status == 5) $_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["DataRetornoCaptura"] = (string) $dataCaptura;						
								else $_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["DataRetorno"] = (string) $dataAutorizacao;

								//VERIFICAR SE FOI PEDIDO UMA NOVA TRANSAÇÃO PARA INSERIR NO HISTÓRICO OU SOMENTE ATUALIZAR O STATUS DA TRANSAÇÃO ATUAL NO HISTÓRICO
								if (array_key_exists(0, $_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["listaHistorico"])) {

									if ($_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["listaHistorico"][0]["CodigoTransacao"] == $numSequencial){
									
										$_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["listaHistorico"][0]["DataAutorizacao"] = (string) $dataAutorizacao;
										$_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["listaHistorico"][0]["DataCaptura"] = (string) $dataCaptura;
										$_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["listaHistorico"][0]["DataCancelamento"] = (string) $dataCancelamento;
									
									}else{
										$novoHistorico = array();
										$novoHistorico["CodigoTransacao"] = (string)$numSequencial;
										$novoHistorico["NomeOperadora"] = $_POST["nomeOperadora"];
										$novoHistorico["DataAutorizacao"] = (string) $dataAutorizacao;
										$novoHistorico["DataCancelamento"] = (string) $dataCancelamento;
										$novoHistorico["DataCaptura"] = (string) $dataCaptura;
										array_unshift($_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["listaHistorico"], $novoHistorico);
									}
								}else{
									$novoHistorico = array();
									$novoHistorico["CodigoTransacao"] = (string)$numSequencial;
									$novoHistorico["NomeOperadora"] = $_POST["nomeOperadora"];
									$novoHistorico["DataAutorizacao"] = (string) $dataAutorizacao;
									$novoHistorico["DataCaptura"] = (string) $dataCaptura;
									$novoHistorico["DataCancelamento"] =(string) $dataCancelamento;
									$_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["listaHistorico"][0] = $novoHistorico;
								}
							}
						}
					}
					switch ($transacao->getStatusGeral()) {
						case 3:
							msgRetorno("success", "Autorização: " . urldecode($transacao->getMsgRetAutorizacao()));
							break;
						case 4:
							msgRetorno("warning", "Autorização: " . urldecode($transacao->getMsgRetAutorizacao()));
							break;
						case 5:
							msgRetorno("success", "CAPTURA: " . urldecode($transacao->getConfMsgRet()));
							break;
						case 6:
							msgRetorno("success", "Cancelamento: " . urldecode($transacao->getMsgRetEstorno()));
							break;
						default:
							msgRetorno("danger", "Houve um Erro na transação, contate o administrador do sistema");
							break;
					}
					//msgRetorno("success", "Transação Efetuada com sucesso");
				}else ErroRede($transacao->getCodRetAutorizacao());
			}else ErroRede($transacao->getCodRetAutorizacao());
		}
		
	}else if ($_POST["operacao"] == 'requestCancelTransaction'){
		if ($_POST["idOperadora"] == 1){//cielo
			$transacao = new TransacaoCielo();
			$transacao->setCodPedidoPagamento($_POST["idTransacao"]);
			if ($transacao->CancelarTransacaoParametro($_POST["idPedido"], $_POST["tidTransacao"])){

				//atualizar banco mysql e filemaker
				$bancoMysql = new BancoDados();
				$bancoMysql->setTransacao($transacao, $_SESSION["dados_empresa"]);
				$bancoFilemaker = new BancoODBC();
				$bancoFilemaker->atualizarPedidoPagamento($transacao);



				for ($i = 0; $i < count($_SESSION["listaPedidos"]); $i++){
					for($j = 0; $j < count($_SESSION["listaPedidos"][$i]["listaPagamentos"]); $j++){
						if ($_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["Codigo"] == $_POST["idTransacao"]){
							$status = $transacao->getStatusGeral();
							$tid = $transacao->getTid_Retorno();
							$pan = $transacao->getPan_Retorno();
							$dataPedido = $transacao->getDados_Pd_Data_Hora_Retorno();
							$dataAutorizacao = $transacao->getAutorizacao_Data_Hora_Retorno();
							$dataAutenticacao = $transacao->getAutenticacao_data_hora_Retorno();
							$dataCaptura = $transacao->getDataHoraCapturaRetorno();
							$dataCancelamento = $transacao->getCancelamentoDataHoraRetorno();

							$_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["StatusTransPag"] = $status;
							$_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["TidTransacao"] = (string) $tid[0];
							$_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["PanTransacao"] = (string) $pan[0];


							if (($status == 1) or ($status == 2)) $_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["DataRetornoAutenticacao"] = (string) $dataAutenticacao[0];
							elseif (($status == 3) or ($status == 4)) $_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["DataRetornoAutorizacao"] = (string) $dataAutorizacao[0];
							elseif ($status == 5) $_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["DataRetornoCaptura"] = (string) $dataCaptura[0];							
							elseif ($status == 6) $_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["DataRetornoCancelamento"] = (string) $dataCancelamento[0];
							else $_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["DataRetorno"] = (string) $dataPedido[0];


							//VERIFICAR SE FOI PEDIDO UMA NOVA TRANSAÇÃO PARA INSERIR NO HISTÓRICO OU SOMENTE ATUALIZAR O STATUS DA TRANSAÇÃO ATUAL NO HISTÓRICO
							if ($_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["listaHistorico"][0]["CodigoTransacao"] == $tid){
								$_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["listaHistorico"][0]["DataAutorizacao"] = (string) $dataAutorizacao[0];
								$_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["listaHistorico"][0]["DataAutenticacao"] = (string) $dataAutenticacao[0];
								$_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["listaHistorico"][0]["DataCaptura"] = (string) $dataCaptura[0];
								$_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["listaHistorico"][0]["DataCancelamento"] = (string) $dataCancelamento[0];
							}else{
								$novoHistorico = array();
								$novoHistorico["CodigoTransacao"] = (string) $tid[0];
								$novoHistorico["NomeOperadora"] = $_POST["nomeOperadora"];
								$novoHistorico["DataAutorizacao"] = (string) $dataAutorizacao[0];
								$novoHistorico["DataAutenticacao"] = (string) $dataAutenticacao[0];
								$novoHistorico["DataCaptura"] = (string) $dataCaptura[0];
								$novoHistorico["DataCancelamento"] =(string) $dataCancelamento[0];
								array_unshift($_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["listaHistorico"], $novoHistorico);
							}
						}

					}
				}
				if ($transacao->getCodigo_Erro_Retorno() !== "null") msgRetorno("info", "Não Foi possível realizar a transação: ERRO {$transacao->getCodigo_Erro_Retorno()} -- {$transacao->getMsg_Erro_Retorno()}");
				else {
					switch ($transacao->getStatusGeral()) {
						case 1:
							msgRetorno("success", "Autenticação: " . $transacao->getAutenticacao_Mensagem_Retorno());
							break;
						case 2:
							msgRetorno("warning", "Autenticação: " . $transacao->getAutenticacao_Mensagem_Retorno());
							break;
						case 3:
							msgRetorno("success", "Autenticação: " . $transacao->getAutenticacao_Mensagem_Retorno() . " --- Autorização: " . $transacao->getAutorizacao_Mensagem_Retorno());
							break;
						case 4:
							msgRetorno("warning", "Autenticação: " . $transacao->getAutenticacao_Mensagem_Retorno() . " --- Autorização: " . $transacao->getAutorizacao_Mensagem_Retorno());
							break;
						case 5:
							msgRetorno("success", "Autenticação: " . $transacao->getAutenticacao_Mensagem_Retorno() . " --- Autorização: " . $transacao->getAutorizacao_Mensagem_Retorno() . " --- CAPTURA: " . $transacao->getMsgCapturaRetorno());
							break;
						case 6:
							msgRetorno("success", "Cancelamento: " . $transacao->getCancelamentoMsgRetorno());
							break;
						default:
							msgRetorno("danger", "Houve um Erro na transação, contate o administrador do sistema {$transacao->getStatusGeral()}");
							break;
					}
				}
			}else ErroCielo($transacao->getCodigo_Erro_Retorno());
			
		}elseif ($_POST["idOperadora"] == 2) { //rede
			$transacao = new TransacaoRedeCar();
			$transacao->setCodPedidoPagamento($_POST["idTransacao"]);
			$transacao->setNumRetAutorizacao($_POST["numAutorizacaoRede"]);
			$transacao->setNumRetComprovVenda($_POST["numComprovanteRede"]);
			$transacao->setNumSequencRet($_POST["numSequencial"]);
			$transacao->setTotalTransacao($_POST["valor"]);
			$transacao->setDataRetAutorizacao(date("Ymd", strtotime($_POST["dataAutorizacao"])));
			
			if ($_POST["capturaAutomatica"] != 1){
				//REALIZA O CANCELAMENTO DA PRE AUTORIZAÇÃO 
				/*Essa operação tem como objetivo cancelar a sensibilização do saldo do cartão do portador utilizando o método VoidPreAuthorization.
				*/
				//echo "Realizar o cancelamento do primeiro passo";
				if ($transacao->EstornoPreAutorizacao()){
					
					if ($transacao->getCodRetEstorno() == 0){
						$bancoMysql = new BancoDados();
						$transacaobanco = $bancoMysql->buscarTransacaoRede($transacao->getNumSequencRet());
						
						$data = date('Y-m-d G:i:s');
						$transacao->setStatusGeral();
						$transacao->setCodigoOperadora(array_key_exists("id_operadora", $transacaobanco) ? $transacaobanco["id_operadora"] : $transacaobanco["fk_operadora"]);
						$transacao->setCodRetAutorizacao($transacaobanco["cod_retorno_autorizacao_rede"]);
						$transacao->setDadosAdd($transacaobanco["dados_adicionais_rede"]);
						$transacao->setDataRetAutorizacao($transacaobanco["data_hora_retorno_autorizacao"]);
						$transacao->setDataRetCaptura($transacaobanco["data_hora_retorno_captura"]);
						$transacao->setDataRetCancelamento($data);
						

						$bancoMysql->setTransacao($transacao, $_SESSION["dados_empresa"]);

						$bancoFilemaker = new BancoODBC();
						$bancoFilemaker->atualizarPedidoPagamento($transacao);		



						for ($i = 0; $i < count($_SESSION["listaPedidos"]); $i++){
							for($j = 0; $j < count($_SESSION["listaPedidos"][$i]["listaPagamentos"]); $j++){
								if ($_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["Codigo"] == $_POST["idTransacao"]){
									$status = $transacao->getStatusGeral();
									$numSequencial = $transacao->getNumSequencRet();
									$numAutorizacao = $transacao->getNumRetAutorizacao();
									$numComprovante = $transacao->getNumRetComprovVenda();
									$numAutenticacao = $transacao->getNumRetAutenticacao();
									$dataAutorizacao = $transacao->getDataRetAutorizacao();
									$dataCaptura = $transacao->getDataRetCaptura();
									$dataCancelamento = $transacao->getDataRetCancelamento();

									$_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["StatusTransPag"] = $status;
									$_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["NumSequencialRede"] = (string)$numSequencial;
									
									$_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["NumAutorizacaoRede"] = (string)$numAutorizacao;
									$_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["NumComprovanteRede"] = (string)$numComprovante;
									$_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["NumAutenticacaoRede"] = (string)$numAutenticacao;

									if (($status == 3) or ($status == 4)) $_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["DataRetornoAutorizacao"] = (string) $dataAutorizacao;
									elseif ($status == 5) $_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["DataRetornoCaptura"] = (string) $dataCaptura;		
									elseif ($status == 6) $_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["DataRetornoCancelamento"] = (string) $dataCancelamento;				
									else $_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["DataRetorno"] = (string) $dataAutorizacao;

									//VERIFICAR SE FOI PEDIDO UMA NOVA TRANSAÇÃO PARA INSERIR NO HISTÓRICO OU SOMENTE ATUALIZAR O STATUS DA TRANSAÇÃO ATUAL NO HISTÓRICO
									if ($_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["listaHistorico"][0]["CodigoTransacao"] == $numSequencial){

										//$_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["listaHistorico"][0]["DataAutorizacao"] = (string) $dataAutorizacao;
										//$_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["listaHistorico"][0]["DataCaptura"] = (string) $dataCaptura;
										$_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["listaHistorico"][0]["DataCancelamento"] = (string) $dataCancelamento;
									
									}else{
										$novoHistorico = array();
										$novoHistorico["CodigoTransacao"] = (string)$numSequencial;
										$novoHistorico["NomeOperadora"] = $_POST["nomeOperadora"];
										$novoHistorico["DataAutorizacao"] = (string) $dataAutorizacao;
										$novoHistorico["DataCancelamento"] = (string) $dataCancelamento;
										$novoHistorico["DataCaptura"] = (string) $dataCaptura;
										array_unshift($_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["listaHistorico"], $novoHistorico);
									}
								}
							}
						}
						switch ($transacao->getStatusGeral()) {
							case 3:
								msgRetorno("success", "Autorização: " . urldecode($transacao->getMsgRetAutorizacao()));
								break;
							case 4:
								msgRetorno("warning", "Autorização: " . urldecode($transacao->getMsgRetAutorizacao()));
								break;
							case 5:
								msgRetorno("success", "CAPTURA: " . urldecode($transacao->getConfMsgRet()));
								break;
							case 6:
								msgRetorno("success", "Cancelamento: " . urldecode($transacao->getMsgRetEstorno()));
								break;
							default:
								msgRetorno("danger", "Houve um Erro na transação, contate o administrador do sistema");
								break;
						}
						//msgRetorno("success", "Transação Cancelada com sucesso");
					}else ErroRede($transacao->getCodRetEstorno());//echo " REDE RETORNOU ERRO --------- " . urldecode($transacao->getMsgRetEstorno());//utf8_decode(urldecode($transacao->getMsgRetEstorno()));
				}
			}else{
				if ($transacao->EstornoDireto()){
					//echo "</br>EstornoDireto</br>";
					//atualizar banco mysql e filemaker
					if ($transacao->getCodRetEstorno() == 0){
						//echo "</br>retorno sucesso</br>";
						$bancoMysql = new BancoDados();
						$transacaobanco = $bancoMysql->buscarTransacaoRede($transacao->getNumSequencRet());

						$data = date('Y-m-d G:i:s');
						$transacao->setStatusGeral();
						$transacao->setCodigoOperadora(array_key_exists("id_operadora", $transacaobanco) ? $transacaobanco["id_operadora"] : $transacaobanco["fk_operadora"]);
						$transacao->setCodRetAutorizacao($transacaobanco["cod_retorno_autorizacao_rede"]);
						$transacao->setDadosAdd($transacaobanco["dados_adicionais_rede"]);
						$transacao->setDataRetAutorizacao($transacaobanco["data_hora_retorno_autorizacao"]);
						$transacao->setDataRetCaptura($transacaobanco["data_hora_retorno_captura"]);
						$transacao->setDataRetCancelamento($data);

						$bancoMysql->setTransacao($transacao, $_SESSION["dados_empresa"]);

						$bancoFilemaker = new BancoODBC();
						$bancoFilemaker->atualizarPedidoPagamento($transacao);
						
						for ($i = 0; $i < count($_SESSION["listaPedidos"]); $i++){
							for($j = 0; $j < count($_SESSION["listaPedidos"][$i]["listaPagamentos"]); $j++){
								if ($_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["Codigo"] == $_POST["idTransacao"]){
									$status = $transacao->getStatusGeral();
									$numSequencial = $transacao->getNumSequencRet();
									$numAutorizacao = $transacao->getNumRetAutorizacao();
									$numComprovante = $transacao->getNumRetComprovVenda();
									$numAutenticacao = $transacao->getNumRetAutenticacao();
									$dataAutorizacao = $transacao->getDataRetAutorizacao();
									$dataCaptura = $transacao->getDataRetCaptura();
									$dataCancelamento = $transacao->getDataRetCancelamento();

									$_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["StatusTransPag"] = $status;
									$_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["NumSequencialRede"] = (string)$numSequencial;
									
									$_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["NumAutorizacaoRede"] = (string)$numAutorizacao;
									$_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["NumComprovanteRede"] = (string)$numComprovante;
									$_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["NumAutenticacaoRede"] = (string)$numAutenticacao;

									if (($status == 3) or ($status == 4)) $_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["DataRetornoAutorizacao"] = (string) $dataAutorizacao;
									elseif ($status == 5) $_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["DataRetornoCaptura"] = (string) $dataCaptura;		
									elseif ($status == 6) $_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["DataRetornoCancelamento"] = (string) $dataCancelamento;				
									else $_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["DataRetorno"] = (string) $dataAutorizacao;

									//VERIFICAR SE FOI PEDIDO UMA NOVA TRANSAÇÃO PARA INSERIR NO HISTÓRICO OU SOMENTE ATUALIZAR O STATUS DA TRANSAÇÃO ATUAL NO HISTÓRICO
									if ($_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["listaHistorico"][0]["CodigoTransacao"] == $numSequencial){

										$_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["listaHistorico"][0]["DataAutorizacao"] = (string) $dataAutorizacao;
										$_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["listaHistorico"][0]["DataCaptura"] = (string) $dataCaptura;
										$_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["listaHistorico"][0]["DataCancelamento"] = (string) $dataCancelamento;
									
									}else{
										$novoHistorico = array();
										$novoHistorico["CodigoTransacao"] = (string)$numSequencial;
										$novoHistorico["NomeOperadora"] = $_POST["nomeOperadora"];
										$novoHistorico["DataAutorizacao"] = (string) $dataAutorizacao;
										$novoHistorico["DataCancelamento"] = (string) $dataCancelamento;
										$novoHistorico["DataCaptura"] = (string) $dataCaptura;
										array_unshift($_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["listaHistorico"], $novoHistorico);
									}
								}
							}
						}
						switch ($transacao->getStatusGeral()) {
							case 3:
								msgRetorno("success", "Autorização: " . urldecode($transacao->getMsgRetAutorizacao()));
								break;
							case 4:
								msgRetorno("warning", "Autorização: " . urldecode($transacao->getMsgRetAutorizacao()));
								break;
							case 5:
								msgRetorno("success", "CAPTURA: " . urldecode($transacao->getConfMsgRet()));
								break;
							case 6:
								msgRetorno("success", "Cancelamento: " . urldecode($transacao->getMsgRetEstorno()));
								break;
							default:
								msgRetorno("danger", "Houve um Erro na transação, contate o administrador do sistema");
								break;
						}	
						//msgRetorno("success", "Transação Cancelada com sucesso");
					}else ErroRede($transacao->getCodRetEstorno());
				}
			}
			
		}
	}else if ($_POST["operacao"] == 'captureTransaction'){

		if ($_POST["idOperadora"] == 1){//cielo

			$transacao = new TransacaoCielo();
			$transacao->setCodPedidoPagamento($_POST["idTransacao"]);
			$transacao->setTid_Retorno($_POST["tidTransacao"]);
			$transacao->setId_Pedido($_POST["idPedido"]);
			if ($transacao->CapturarTransacao()){
				//echo "entrou";
				//atualizar banco mysql e filemaker
				$bancoMysql = new BancoDados();
				$bancoMysql->setTransacao($transacao, $_SESSION["dados_empresa"]);
				$bancoFilemaker = new BancoODBC();
				$bancoFilemaker->atualizarPedidoPagamento($transacao);



				for ($i = 0; $i < count($_SESSION["listaPedidos"]); $i++){
					for($j = 0; $j < count($_SESSION["listaPedidos"][$i]["listaPagamentos"]); $j++){
						if ($_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["Codigo"] == $_POST["idTransacao"]){
							$status = $transacao->getStatusGeral();
							$tid = $transacao->getTid_Retorno();
							$pan = $transacao->getPan_Retorno();
							$dataPedido = $transacao->getDados_Pd_Data_Hora_Retorno();
							$dataAutorizacao = $transacao->getAutorizacao_Data_Hora_Retorno();
							$dataAutenticacao = $transacao->getAutenticacao_data_hora_Retorno();
							$dataCaptura = $transacao->getDataHoraCapturaRetorno();
							$dataCancelamento = $transacao->getCancelamentoDataHoraRetorno();
							$_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["StatusTransPag"] = $status;
							$_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["TidTransacao"] = (string) $tid[0];
							$_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["PanTransacao"] = (string) $pan[0];


							if (($status == 1) or ($status == 2)) $_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["DataRetornoAutenticacao"] = (string) $dataAutenticacao[0];
							elseif (($status == 3) or ($status == 4)) $_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["DataRetornoAutorizacao"] = (string) $dataAutorizacao[0];
							elseif ($status == 5) $_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["DataRetornoCaptura"] = (string) $dataCaptura[0];							
							elseif ($status == 6) $_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["DataRetornoCancelamento"] = (string) $dataCancelamento[0];
							else $_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["DataRetorno"] = (string) $dataPedido[0];

							//VERIFICAR SE FOI PEDIDO UMA NOVA TRANSAÇÃO PARA INSERIR NO HISTÓRICO OU SOMENTE ATUALIZAR O STATUS DA TRANSAÇÃO ATUAL NO HISTÓRICO
							if ($_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["listaHistorico"][0]["CodigoTransacao"] == $tid){
								$_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["listaHistorico"][0]["DataAutorizacao"] = (string) $dataAutorizacao[0];
								$_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["listaHistorico"][0]["DataAutenticacao"] = (string) $dataAutenticacao[0];
								$_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["listaHistorico"][0]["DataCaptura"] = (string) $dataCaptura[0];
								$_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["listaHistorico"][0]["DataCancelamento"] = (string) $dataCancelamento[0];
							}else{
								$novoHistorico = array();
								$novoHistorico["CodigoTransacao"] = (string) $tid[0];
								$novoHistorico["NomeOperadora"] = $_POST["nomeOperadora"];
								$novoHistorico["DataAutorizacao"] = (string) $dataAutorizacao[0];
								$novoHistorico["DataAutenticacao"] = (string) $dataAutenticacao[0];
								$novoHistorico["DataCaptura"] = (string) $dataCaptura[0];
								$novoHistorico["DataCancelamento"] =(string) $dataCancelamento[0];
								array_unshift($_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["listaHistorico"], $novoHistorico);
							}
						}

					}
				}
				if ($transacao->getCodigo_Erro_Retorno() !== "null") msgRetorno("info", "Não Foi possível realizar a transação: ERRO {$transacao->getCodigo_Erro_Retorno()} -- {$transacao->getMsg_Erro_Retorno()}");
				else {
					switch ($transacao->getStatusGeral()) {
						case 1:
							msgRetorno("success", "Autenticação: " . $transacao->getAutenticacao_Mensagem_Retorno());
							break;
						case 2:
							msgRetorno("warning", "Autenticação: " . $transacao->getAutenticacao_Mensagem_Retorno());
							break;
						case 3:
							msgRetorno("success", "Autenticação: " . $transacao->getAutenticacao_Mensagem_Retorno() . " --- Autorização: " . $transacao->getAutorizacao_Mensagem_Retorno());
							break;
						case 4:
							msgRetorno("warning", "Autenticação: " . $transacao->getAutenticacao_Mensagem_Retorno() . " --- Autorização: " . $transacao->getAutorizacao_Mensagem_Retorno());
							break;
						case 5:
							msgRetorno("success", "Autenticação: " . $transacao->getAutenticacao_Mensagem_Retorno() . " --- Autorização: " . $transacao->getAutorizacao_Mensagem_Retorno() . " --- CAPTURA: " . $transacao->getMsgCapturaRetorno());
							break;
						case 6:
							msgRetorno("success", "Cancelamento: " . $transacao->getCancelamentoMsgRetorno());
							break;
						default:
							msgRetorno("danger", "Houve um Erro na transação, contate o administrador do sistema {$transacao->getStatusGeral()}");
							break;
					}
				}
			}else ErroCielo($transacao->getCodigo_Erro_Retorno());
		}elseif ($_POST["idOperadora"] == 2) {//rede

			//print_r($transacao);
			//desenvolver integração rede
			//echo "</br>A transação será pela REDE</br>";
			//print_r($_POST);
			$transacao = new TransacaoRedeCar();
			$transacao->setCodPedidoPagamento($_POST["idTransacao"]);
			$transacao->setTotalTransacao($_POST["TotalPedido"]);
			$transacao->setDataRetAutorizacao(date("Ymd", strtotime($_POST["dataAutorizacao"])));
			$transacao->setNumRetAutorizacao($_POST["numAutorizacaoRede"]);
			$transacao->setNumRetComprovVenda($_POST["numComprovanteRede"]);
			$transacao->setNumSequencRet($_POST["numSequencial"]);
			$formPag = pegarBandeira();
			if ($_POST["numParcelas"] <= 1) {
				$tipoPagamento = $formPag["cod_rede_a_vista"];
				$numParcelas = "00";
			}
			elseif ($_POST["numParcelas"] > 1) {
				$tipoPagamento = $formPag["cod_rede_prazo"];
				$numParcelas = $_POST["numParcelas"];
			}
			$transacao->setTipoTransacao($tipoPagamento);
			$transacao->setNumParcelas($numParcelas);
			
			if ($transacao->ConfirmarAutorizacao()){
				//echo "CodigoRetorno: " .$transacao->getConfCodRet() . " - Msg: " . $transacao->getConfMsgRet();
				//print_r($transacao);

				if ($transacao->getConfCodRet() == 0){

					$bancoMysql = new BancoDados();
					
					$transacaobanco = $bancoMysql->buscarTransacaoRede($transacao->getNumSequencRet());
					$data = date('Y-m-d G:i:s');
					$transacao->setDataRetAutorizacao($transacaobanco["data_hora_retorno_autorizacao"]);
					$transacao->setDataRetCaptura($data);
					$transacao->setDataRetCancelamento($transacaobanco["data_hora_retorno_cancelamento"]);

					$bancoMysql->setTransacao($transacao, $_SESSION["dados_empresa"]);
					
					//$bancoMysql = new BancoDados();
					//$bancoMysql->setTransacao($transacao, $_SESSION["dados_empresa"]);
					$bancoFilemaker = new BancoODBC();
					$bancoFilemaker->atualizarPedidoPagamento($transacao);
					

					for ($i = 0; $i < count($_SESSION["listaPedidos"]); $i++){
						for($j = 0; $j < count($_SESSION["listaPedidos"][$i]["listaPagamentos"]); $j++){
							if ($_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["Codigo"] == $_POST["idTransacao"]){
								$status = $transacao->getStatusGeral();
								$numSequencial = $transacao->getNumSequencRet();
								$numAutorizacao = $transacao->getNumRetAutorizacao();
								$numComprovante = $transacao->getNumRetComprovVenda();
								$numAutenticacao = $transacao->getNumRetAutenticacao();
								$dataAutorizacao = $transacao->getDataRetAutorizacao();
								$dataCaptura = $transacao->getDataRetCaptura();
								$dataCancelamento = $transacao->getDataRetCancelamento();

								$_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["StatusTransPag"] = $status;
								$_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["NumSequencialRede"] = (string)$numSequencial;
								
								$_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["NumAutorizacaoRede"] = (string)$numAutorizacao;
								$_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["NumComprovanteRede"] = (string)$numComprovante;
								$_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["NumAutenticacaoRede"] = (string)$numAutenticacao;

								if (($status == 3) or ($status == 4)) $_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["DataRetornoAutorizacao"] = (string) $dataAutorizacao;
								elseif ($status == 5) $_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["DataRetornoCaptura"] = (string) $dataCaptura;		
								elseif ($status == 6) $_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["DataRetornoCancelamento"] = (string) $dataCancelamento;				
								else $_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["DataRetorno"] = (string) $dataAutorizacao;

								//VERIFICAR SE FOI PEDIDO UMA NOVA TRANSAÇÃO PARA INSERIR NO HISTÓRICO OU SOMENTE ATUALIZAR O STATUS DA TRANSAÇÃO ATUAL NO HISTÓRICO
								if ($_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["listaHistorico"][0]["CodigoTransacao"] == $numSequencial){

									$_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["listaHistorico"][0]["DataAutorizacao"] = (string) $dataAutorizacao;
									$_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["listaHistorico"][0]["DataCaptura"] = (string) $dataCaptura;
									$_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["listaHistorico"][0]["DataCancelamento"] = (string) $dataCancelamento;
								
								}else{
									$novoHistorico = array();
									$novoHistorico["CodigoTransacao"] = (string)$numSequencial;
									$novoHistorico["NomeOperadora"] = $_POST["nomeOperadora"];
									$novoHistorico["DataAutorizacao"] = (string) $dataAutorizacao;
									$novoHistorico["DataCancelamento"] = (string) $dataCancelamento;
									$novoHistorico["DataCaptura"] = (string) $dataCaptura;
									array_unshift($_SESSION["listaPedidos"][$i]["listaPagamentos"][$j]["listaHistorico"], $novoHistorico);
								}
							}
						}
					}
					switch ($transacao->getStatusGeral()) {
						case 3:
							msgRetorno("success", "Autorização: " . urldecode($transacao->getMsgRetAutorizacao()));
							break;
						case 4:
							msgRetorno("warning", "Autorização: " . urldecode($transacao->getMsgRetAutorizacao()));
							break;
						case 5:
							msgRetorno("success", "CAPTURA: " . urldecode($transacao->getConfMsgRet()));
							break;
						case 6:
							msgRetorno("success", "Cancelamento: " . urldecode($transacao->getMsgRetEstorno()));
							break;
						default:
							msgRetorno("danger", "Houve um Erro na transação, contate o administrador do sistema");
							break;
					}
					//msgRetorno("success", "Transação Capturada com sucesso");
				}else ErroRede($transacao->getConfCodRet());
			}
		}
	}

	function tratarValidade($validade){
		$array = explode('/', $validade);

		switch ($array[0]) {
			case 'Janeiro':
				$array[0] = "01";
				break;
			case 'Fevereiro':
				$array[0] = "02";
				break;
			case 'Março':
				$array[0] = "03";
				break;
			case 'Abril':
				$array[0] = "04";
				break;		
			case 'Maio':
				$array[0] = "05";
				break;
			case 'Junho':
				$array[0] = "06";
				break;
			case 'Julho':
				$array[0] = "07";
				break;		
			case 'Agosto':
				$array[0] = "08";
				break;
			case 'Setembro':
				$array[0] = "09";
				break;
			case 'Outubro':
				$array[0] = "10";
				break;
			case 'Novembro':
				$array[0] = "11";
				break;
			case 'Dezembro':
				$array[0] = "12";
				break;	
		}
		switch($_POST["idOperadora"]){
			case 1: //CIELO
				return $array[1] . $array[0];
				break;
			case 2: //REDE
				//[0] -> MES
				//[1] -> ANO
				$array[1] = substr($array[1], -2); 
				//print_r($array);
				return $array;	
				break;
		}
		
	}
	
	function tratarValorTotal($valor){
		return str_replace(".", "", $valor); 
	}
	
	function tratarData($data){
		return str_replace(" ", "T", $data); 
	}

	function pegarBandeira(){
		$bancoMysql = new BancoDados();
		$retorno = array();
		try { 
			
			$retorno = $bancoMysql->getBandeiraCartao($_POST["tipoFormaPgto"]); 
			return $retorno;
		} catch (Exception $e) { 
			echo "Falha na Conexão com Base de Dados " .$e->getMessage(); 
		} 
	}

	function ErroRede($cod){
		if ($cod == 50 or $cod == 52 or $cod == 54 or $cod == 55 or $cod == 57 or $cod == 59 or $cod == 61 or $cod == 62 or $cod == 64 or $cod == 66 or $cod == 67 or $cod == 68 or $cod == 70 or $cod == 71 or $cod == 73 or $cod == 75 or $cod == 78 or $cod == 79 or $cod == 80 or $cod == 82 or $cod == 83 or $cod == 84 or $cod == 85 or $cod == 87 or $cod == 89 or $cod == 90 or $cod == 91 or $cod == 93 or $cod == 94 or $cod == 95 or $cod == 97 or $cod == 99) msgRetorno("warning", "({$cod}) Transação não foi Aprovada!");
		elseif($cod == 51 or $cod == 92 or $cod == 98) msgRetorno("warning", "({$cod}) Estabelecimento Inválido. Por favor, entre em contato com o Suporte Técnico do Komerci para analisar os parâmetros e cadastro.");
		elseif($cod == 53) msgRetorno("warning", "({$cod}) Transação Inválida. Por favor, entre em contato com o Suporte Técnico para analisar o seu cadastro");

		elseif($cod == 56 or $cod == 74 or $cod == 76 or $cod == 86) msgRetorno("warning", "({$cod}) Refaça a Transação/Operação. Sua transação/Operação não pode ser concluída. Por favor, tente novamente.");

		elseif($cod == 58 or $cod == 63 or $cod == 65 or $cod == 69 or $cod == 72 or $cod == 77 or $cod == 96) msgRetorno("warning", "({$cod}) Problemas com o cartão. Por favor, verifique os dados de seu cartão. Caso o erro persista, entre em contato com a central de atendimento de seu cartão.");
		elseif($cod == 60) msgRetorno("warning", "({$cod}) Valor Inválido. Verifique se o parâmetro foi informado corretamente.");
		
		else msgRetorno("warning", "Erro ({$cod}) não catalogado. Entre em contato com administrador do sistema!");
	}

	function ErroCielo($cod){
		if ($cod == 001) msgRetorno("warning", "Mensagem inválida. A mensagem XML está fora do formato especificado pelo arquivo ecommerce.xsd");
		elseif($cod == 100) msgRetorno("danger", "Erro de estrutura do xml enviado ao webservice. Contate o Administrador do sistema");
	}

	function msgRetorno($tipo, $str){
		$html = "<div class='alert alert-" . $tipo . " navbar-fixed-top'>".
				  "<button onClick='limparMsg();' type='button' class='close' data-dismiss='alert'>×</button>".
				  "<h4>Atenção!</h4>" . $str . 
				"</div>";
		echo $html;
	}
?>