<?phpoperadoras_cartao("configuracoes/constantesRedeCar.php");
class TransacaoRedeCar{
	
	private $url_ws = "";
	private $url_wsdl = "";
	private $ambiente = "";
	private $retorno_ws = "";
	private $metodo_ws = "";
	private $parametros_soap;
	
	/*VARIÃ�VEIS DE ENVIO*/
	private $total_transacao = "";
	private $tipo_transacao = "";
	private $num_parcelas = "";
	private $num_filiacao = "";
	private $num_pedido = "";
	private $num_cartao = "";
	private $num_cvc2_cartao = "";
	private $mes_venc_cartao = "";
	private $ano_venc_cartao = "";
	private $nome_portador_cartao = "";
	private $dados_add = "";
	private $conf_txn = "";
	private $user_rede = "";
	private $pwd_rede = "";
	
	/*VARIÃ�VEIS DE RETORNO*/
	private $cod_retorno_autorizacao = "";
	private $msg_retorno_autorizacao = "";
	private $data_retorno_autorizacao = "";
	private $num_retorno_autorizacao = "";
	private $num_retorno_comprov_venda = "";
	private $num_retorno_autenticacao = "";
	private $num_sequenc_retorno = "";
	private $num_origem_bin = "";
	private $conf_cod_retorno = ""; //VERIFICAR SE ESTÃ� VARIÃ�VEL DE RETORNO Ã‰ A MESMA DO RETORNO DE CONFPREAUTHORIZATION
	private $conf_msg_retorno = ""; //VERIFICAR SE ESTÃ� VARIÃ�VEL DE RETORNO Ã‰ A MESMA DO RETORNO DE CONFPREAUTHORIZATION
	private $cod_ret_estorno = "";
	private $msg_ret_estorno = "";
	private $status_geral = 0;
	private $codigo_operadora = 2;
	private $codigo_pedido_pagamento = NULL;

	private $confirmacao_automatica = NULL;

	private $data_retorno_captura = NULL;
	private $data_retorno_cancelamento = NULL;

	private $dados_pd_data_hora_retorno = NULL;
	private $tipo_forma_pagamento;
	private $taxa = NULL;
	private $liquido = NULL;

	private $cod_erro_retorno = NULL;
	private $msg_erro_retorno = NULL;

	/*GETTERS*/
	public function getUrlWs(){
		return $this->url_ws;
	}
	public function getUrlWsdl(){
		return $this->url_wsdl;
	}
	public function getAmbiente(){
		return $this->ambiente;
	}
	public function getRetornoWs(){
		return $this->retorno_ws;
	}
	public function getMetodoWs(){
		return $this->metodo_ws;
	}
	public function getParametrosSoap(){
		return $this->parametros_soap;
	}
	public function getTotalTransacao(){
		return $this->total_transacao;
	}
	public function getTipoTransacao(){
		return $this->tipo_transacao;
	}
	public function getNumParcelas(){
		return $this->num_parcelas;
	}
	public function getNumFiliacao(){
		return $this->num_filiacao;
	}
	public function getNumPedido(){
		return $this->num_pedido;
	}
	public function getNumCartao(){
		return $this->num_cartao;
	}
	public function getNumCvc2Cartao(){
		return $this->num_cvc2_cartao;
	}
	public function getMesVencCartao(){
		return $this->mes_venc_cartao;
	}
	public function getAnoVencCartao(){
		return $this->ano_venc_cartao;
	}
	public function getNomePortadorCartao(){
		return $this->nome_portador_cartao;
	}
	public function getDadosAdd(){
		return $this->dados_add;
	}	
	public function getCodRetAutorizacao(){
		return $this->cod_retorno_autorizacao;
	}
	public function getMsgRetAutorizacao(){
		return $this->msg_retorno_autorizacao;
	}
	public function getDataRetAutorizacao(){
		return $this->data_retorno_autorizacao;
	}
	public function getDataRetCaptura(){
		return $this->data_retorno_captura;
	}
	public function getDataRetCancelamento(){
		return $this->data_retorno_cancelamento;
	}
	public function getNumRetAutorizacao(){
		return $this->num_retorno_autorizacao;
	}
	public function getNumRetComprovVenda(){
		return $this->num_retorno_comprov_venda;
	}
	public function getNumRetAutenticacao(){
		return $this->num_retorno_autenticacao;
	}
	public function getNumSequencRet(){
		return $this->num_sequenc_retorno;
	}
	public function getNumOrigemBin(){
		return $this->num_origem_bin;
	}
	public function getConfTxn(){
		return $this->conf_txn;
	}
	public function getConfCodRet(){
		return $this->conf_cod_retorno;
	}
	public function getConfMsgRet(){
		return $this->conf_msg_retorno;
	}
	public function getUserRede(){
		return $this->user_rede;
	}
	public function getPwdRede(){
		return $this->pwd_rede;
	}
	public function getCodRetEstorno(){
		return $this->cod_ret_estorno;
	}
	public function getMsgRetEstorno(){
		return $this->msg_ret_estorno;
	}
	public function getCodigoOperadora(){
		return $this->codigo_operadora;
	}
	public function getCodPedidoPagamento(){
		return $this->codigo_pedido_pagamento;
	}
	public function getStatusGeral(){
		return $this->status_geral;
	}
	public function getConfirmacaoAutomatica(){
		return $this->confirmacao_automatica;
	}	
	public function getTipoFormaPagamento(){
		return $this->tipo_forma_pagamento;
	}
	public function getDados_Pd_Data_Hora_Retorno(){
		return $this->dados_pd_data_hora_retorno;
	}
	public function getTaxa(){
		return $this->taxa;
	}	
	public function getLiquido(){
		return $this->liquido;
	}
	public function getCodErroRetorno(){
		return $this->cod_erro_retorno;
	}
	public function getMsgErroRetorno(){
		return $this->msg_erro_retorno;
	}
		

	/*SETTERS*/
	public function setCodErroRetorno($param){
		$this->cod_erro_retorno = $param;
	}
	public function setMsgErroRetorno($param){
		$this->msg_erro_retorno = $param;
	}
	public function setLiquido($param){
		$this->liquido = $param;
	}
	public function setTaxa($param){
		$this->taxa = $param;
	}
	public function setDados_Pd_Data_Hora_Retorno($dadosPdDtHoraRetorno){
		$this->dados_pd_data_hora_retorno = $dadosPdDtHoraRetorno;
	}
	public function setTipoFormaPagamento($param){
		$this->tipo_forma_pagamento = $param;
	}		
	public function setUrlWs($param){
		$this->url_ws = $param;
	}
	public function setUrlWsdl($param){
		$this->url_wsdl = $param;
	}
	public function setAmbiente($param){
		$this->ambiente = $param;
	}
	public function setRetornoWs($param){
		$this->retorno_ws = $param;
	}
	public function setMetodoWs($param){
		$this->metodo_ws = $param;
	}
	public function setParametrosSoap($param){
		$this->parametros_soap = $param;
	}
	public function setTotalTransacao($param){
		$this->total_transacao = $param;
	}
	public function setTipoTransacao($param){
		$this->tipo_transacao = $param;
	}
	public function setNumParcelas($param){
		$this->num_parcelas = $param;
	}
	public function setNumFiliacao($param){
		$this->num_filiacao = $param;
	}
	public function setNumPedido($param){
		$this->num_pedido = $param;
	}
	public function setNumCartao($param){
		$this->num_cartao = $param;
	}
	public function setNumCvc2Cartao($param){
		$this->num_cvc2_cartao = $param;
	}
	public function setMesVencCartao($param){
		$this->mes_venc_cartao = $param;
	}
	public function setAnoVencCartao($param){
		$this->ano_venc_cartao = $param;
	}
	public function setNomePortadorCartao($param){
		$this->nome_portador_cartao = $param;
	}
	public function setDadosAdd($param){
		$this->dados_add = $param;
	}	
	public function setCodRetAutorizacao($param){
		$this->cod_retorno_autorizacao = $param;
	}
	public function setMsgRetAutorizacao($param){
		$this->msg_retorno_autorizacao = $param;
	}
	public function setDataRetAutorizacao($param){
		$this->data_retorno_autorizacao = $param;
	}
	public function setDataRetCaptura($param){
		$this->data_retorno_captura = $param;
	}
	public function setDataRetCancelamento($param){
		$this->data_retorno_cancelamento = $param;
	}
	public function setNumRetAutorizacao($param){
		$this->num_retorno_autorizacao = $param;
	}
	public function setNumRetComprovVenda($param){
		$this->num_retorno_comprov_venda = $param;
	}
	public function setNumRetAutenticacao($param){
		$this->num_retorno_autenticacao = $param;
	}
	public function setNumSequencRet($param){
		$this->num_sequenc_retorno = $param;
	}
	public function setNumOrigemBin($param){
		$this->num_origem_bin = $param;
	}
	public function setConfTxn($param){
		$this->conf_txn = $param;
	}
	public function setConfCodRet($param){
		$this->conf_cod_retorno = $param;
	}
	public function setConfMsgRet($param){
		$this->conf_msg_retorno = $param;
	}
	public function setUserRede($param){
		$this->user_rede = $param;
	}
	public function setPwdRede($param){
		$this->pwd_rede = $param;
	}
	public function setCodRetEstorno($param){
		$this->cod_ret_estorno = $param;
	}
	public function setMsgRetEstorno($param){
		$this->msg_ret_estorno = $param;
	}
	public function setCodigoOperadora($param){
		$this->codigo_operadora = $param;
	}
	public function setCodPedidoPagamento($codPedidoPagamento){
		$this->codigo_pedido_pagamento = $codPedidoPagamento;
	}
	public function setConfirmacaoAutomatica($param){
		$this->confirmacao_automatica = $param;
	}

	/*
	0 - PENDENTE
	1 - AUTENTICADA
	2 - NÃO AUTENTICADA	
	3 - AUTORIZADA
	4 - NÃO AUTORIZADA
	5 - CAPTURADA
	6 - CANCELADA
	7 - INDEFINIDO
	*/
	public function setStatusGeral(){
		if (($this->getCodRetEstorno() == "") or ($this->getCodRetEstorno() == NULL)){

			if (($this->getConfirmacaoAutomatica() != NULL) and ($this->getConfirmacaoAutomatica() != "")){ //NÃO É CAPTURA AUTOMÁTICA
				if ($this->getCodRetAutorizacao() == 0) $this->status_geral = 3;
				else if ($this->getCodRetAutorizacao() >= 50) $this->status_geral = 4;
			}else{ //É CAPTURA AUTOMÁTICA
				if ($this->getCodRetAutorizacao() == 0) $this->status_geral = 5;
				else if ($this->getCodRetAutorizacao() >= 50) $this->status_geral = 4;
			}
		}else{
			if ($this->getCodRetEstorno() == 0) $this->status_geral = 6;
		}
	}




	/*
	public function setStatusGeral(){
		if (($this->getCodRetEstorno() == "") or ($this->getCodRetEstorno() == NULL)){
			switch ($this->getTipoTransacao()) {
				case 73:
					if ($this->getCodRetAutorizacao() == 0) $this->status_geral = 3;
					else if ($this->getCodRetAutorizacao() >= 50) $this->status_geral = 4;
					break;
				
				default:
					if ($this->getCodRetAutorizacao() == 0) $this->status_geral = 5;
					else if ($this->getCodRetAutorizacao() >= 50) $this->status_geral = 4;
					break;
			}	
		}else{
			if ($this->getCodRetEstorno() == 0) $this->status_geral = 6;
		}
	}*/

	
	/*CONSTRUCTER*/
	function __construct() {
		$this->url_ws = WS_REDECAR_TESTE;
		$this->url_wsdl = WSDL_REDECAR_TESTE;
		$this->conf_txn = CONF_TXN;
		$this->num_filiacao = FILIACAO_TESTE;
		$this->user_rede = USER_REDE_TESTE;
		$this->pwd_rede = PWD_REDE_TESTE;
		
		$this->total_transacao = "0.01";
		$this->tipo_transacao = "04";
		$this->num_parcelas = "00";
		$this->num_pedido = "123456789";
		$this->num_cartao = "1234567890123456";
		$this->num_cvc2_cartao = "123";
		$this->mes_venc_cartao = "05";
		$this->ano_venc_cartao = "20";
		$this->nome_portador_cartao = "TESTE";
		$this->ambiente = "TESTE";
	}
	
	public function setDados($idPedido, $numCartao, $numCvc2Cartao, $mesVencCartao, $anoVencCartao, $nomePortador, $totalTransacao, $tipoTransacao, $numParcelas, $confirmacaoAutomatica){
		$this->url_ws = WS_REDECAR;
		$this->url_wsdl = WSDL_REDECAR;
		$this->num_filiacao = FILIACAO;
		$this->user_rede = USER_REDE;
		$this->pwd_rede = PWD_REDE;
		$this->ambiente = "HOMOLOGACAO";
		
		$this->num_pedido = $idPedido;
		$this->num_cartao = $numCartao;
		$this->num_cvc2_cartao = $numCvc2Cartao;
		$this->mes_venc_cartao = $mesVencCartao;
		$this->ano_venc_cartao = $anoVencCartao;
		$this->nome_portador_cartao = $nomePortador;
		$this->total_transacao = $totalTransacao;
		$this->tipo_transacao = $tipoTransacao;
		$this->confirmacao_automatica = $confirmacaoAutomatica;
		$this->num_parcelas = $numParcelas;
	}

	public function setDadosTeste($idPedido, $numCartao, $numCvc2Cartao, $mesVencCartao, $anoVencCartao, $nomePortador, $totalTransacao, $tipoTransacao, $numParcelas, $confirmacaoAutomatica){
		$this->url_ws = WS_REDECAR_TESTE;
		$this->url_wsdl = WSDL_REDECAR_TESTE;
		$this->num_filiacao = FILIACAO_TESTE;
		$this->user_rede = USER_REDE_TESTE;
		$this->pwd_rede = PWD_REDE_TESTE;
		$this->ambiente = "TESTE";
		
		$this->num_pedido = $idPedido;
		$this->num_cartao = $numCartao;
		$this->num_cvc2_cartao = $numCvc2Cartao;
		$this->mes_venc_cartao = $mesVencCartao;
		$this->ano_venc_cartao = $anoVencCartao;
		$this->nome_portador_cartao = $nomePortador;
		//$this->total_transacao = $totalTransacao;
		$this->tipo_transacao = $tipoTransacao;
		$this->num_parcelas = $numParcelas;
		$this->confirmacao_automatica = $confirmacaoAutomatica;
		$this->total_transacao = $totalTransacao;
	}
	
	
	private function ProcessarRetorno(){
		$this->setCodRetAutorizacao($this->retorno_ws->CODRET);
		$this->setMsgRetAutorizacao($this->retorno_ws->MSGRET);
		if($this->getCodRetAutorizacao() == "0"){
			$this->setDataRetAutorizacao($this->retorno_ws->DATA);
			$this->setNumRetAutorizacao($this->retorno_ws->NUMAUTOR);
			$this->setNumRetComprovVenda($this->retorno_ws->NUMCV);
			$this->setNumRetAutenticacao($this->retorno_ws->NUMAUTENT);
			$this->setNumSequencRet($this->retorno_ws->NUMSQN);
			$this->setNumOrigemBin($this->retorno_ws->ORIGEM_BIN);
			$this->setStatusGeral();
			$this->setCodigoOperadora(2);
			if ($this->getConfTxn() == "S") {
				$this->setConfCodRet($this->retorno_ws->CONFCODRET);
				$this->setConfMsgRet($this->retorno_ws->CONFMSGRET);
			}
		}
	}
	
	private function ProcessarRetornoConf(){
		$this->setConfCodRet($this->retorno_ws->root->codret);
		$this->setConfMsgRet($this->retorno_ws->root->msgret);
		$this->setStatusGeral();
	}
	
	private function ProcessarRetornoEstorno(){
		$this->setCodRetEstorno($this->retorno_ws->root->codret);
		$this->setMsgRetEstorno($this->retorno_ws->root->msgret);
		$this->setStatusGeral();
	}

	private function ProcessarRetornoEstornoPreAutorizacao(){
		$this->setCodRetEstorno($this->retorno_ws->root->codret);
		$this->setMsgRetEstorno($this->retorno_ws->root->msgret);
		$this->setStatusGeral();
	}	
	
	
	
	
	private function EnviarSOAP(){
		$ws = array("location" => $this->url_ws);
		$soap = new SoapClient($this->url_wsdl, array("trace" => 1, "exceptions" => 1, "soap_version" => SOAP_1_1, "encoding" => "UTF-8"));
		$this->retorno_ws = $soap->__soapCall($this->metodo_ws, $this->parametros_soap, $ws);
		if (is_soap_fault($this->retorno_ws)) {
			trigger_error("SOAP Fault: (faultcode: {$result->faultcode}, faultstring: {$result->faultstring})", E_USER_ERROR);
			return NULL;
		}else{
			//echo $soap->__getLastRequest();
			 
			$xmlRetorno = "<?xml version='1.0' encoding='UTF-8'?>";
			$funcao = $this->metodo_ws ."Result";
			//echo "<br />" . $funcao . "<br />";
			$xmlRetorno .= $this->retorno_ws->$funcao->any;
			$this->retorno_ws = simplexml_load_string($xmlRetorno);
			//var_dump($this->retorno_ws);
			return $this->retorno_ws;	
		}
		return NULL;
	}
	
	private function MontarParametrosAutorizacaoDireta(){

		if (($this->confirmacao_automatica != NULL) and ($this->confirmacao_automatica != "")){
			if ($this->ambiente == "TESTE"){
				$this->parametros_soap = array($this->metodo_ws => array(
										"Total"   	=> "0.01",
										"Transacao" => $this->confirmacao_automatica,
										"Parcelas"  => "00", //VERIFICAR COM A REDECAR PORQUE NÃƒO ACEITA ESTE CAMPO VAZIO COMO O MANUAL DESCREVE
										"Filiacao"  => $this->num_filiacao,
										"NumPedido"  => $this->num_pedido,
										"Nrcartao"  => $this->num_cartao,
										"CVC2"  => $this->num_cvc2_cartao,
										"Mes"  => $this->mes_venc_cartao,
										"Ano"  => $this->ano_venc_cartao,
										"Portador"  => $this->nome_portador_cartao,
										"IATA"  => "",
										"Distribuidor"  => "",
										"Concentrador"  => "",
										"TaxaEmbarque"  => "",
										"Pax1"  => "",
										"Pax2"  => "",
										"Pax3"  => "",
										"Pax4"  => "",
										"Entrada"  => "",
										"Numdoc1"  => "",
										"Numdoc2"  => "",
										"Numdoc3"  => "",
										"Numdoc4"  => "",
										"ConfTxn"  => CONF_TXN,
										"Add_Data"  => ""
									));
			}else{
				$this->parametros_soap = array($this->metodo_ws => array(
										"Total"   	=> $this->total_transacao,
										"Transacao" => $this->confirmacao_automatica,
										"Parcelas"  => "00", //VERIFICAR COM A REDECAR PORQUE NÃƒO ACEITA ESTE CAMPO VAZIO COMO O MANUAL DESCREVE
										"Filiacao"  => $this->num_filiacao,
										"NumPedido"  => $this->num_pedido,
										"Nrcartao"  => $this->num_cartao,
										"CVC2"  => $this->num_cvc2_cartao,
										"Mes"  => $this->mes_venc_cartao,
										"Ano"  => $this->ano_venc_cartao,
										"Portador"  => $this->nome_portador_cartao,
										"IATA"  => "",
										"Distribuidor"  => "",
										"Concentrador"  => "",
										"TaxaEmbarque"  => "",
										"Pax1"  => "",
										"Pax2"  => "",
										"Pax3"  => "",
										"Pax4"  => "",
										"Entrada"  => "",
										"Numdoc1"  => "",
										"Numdoc2"  => "",
										"Numdoc3"  => "",
										"Numdoc4"  => "",
										"ConfTxn"  => CONF_TXN,
										"Add_Data"  => ""
									));
			}
			
		}else{
			if ($this->ambiente == "TESTE"){
				$this->parametros_soap = array($this->metodo_ws => array(
										"Total"   	=> "0.01",
										"Transacao" => $this->tipo_transacao,
										"Parcelas"  => $this->num_parcelas,
										"Filiacao"  => $this->num_filiacao,
										"NumPedido"  => $this->num_pedido,
										"Nrcartao"  => $this->num_cartao,
										"CVC2"  => $this->num_cvc2_cartao,
										"Mes"  => $this->mes_venc_cartao,
										"Ano"  => $this->ano_venc_cartao,
										"Portador"  => $this->nome_portador_cartao,
										"IATA"  => "",
										"Distribuidor"  => "",
										"Concentrador"  => "",
										"TaxaEmbarque"  => "",
										"Pax1"  => "",
										"Pax2"  => "",
										"Pax3"  => "",
										"Pax4"  => "",
										"Entrada"  => "",
										"Numdoc1"  => "",
										"Numdoc2"  => "",
										"Numdoc3"  => "",
										"Numdoc4"  => "",
										"ConfTxn"  => CONF_TXN,
										"Add_Data"  => ""
									));
			}else{
				$this->parametros_soap = array($this->metodo_ws => array(
										"Total"   	=> $this->total_transacao,
										"Transacao" => $this->tipo_transacao,
										"Parcelas"  => $this->num_parcelas,
										"Filiacao"  => $this->num_filiacao,
										"NumPedido"  => $this->num_pedido,
										"Nrcartao"  => $this->num_cartao,
										"CVC2"  => $this->num_cvc2_cartao,
										"Mes"  => $this->mes_venc_cartao,
										"Ano"  => $this->ano_venc_cartao,
										"Portador"  => $this->nome_portador_cartao,
										"IATA"  => "",
										"Distribuidor"  => "",
										"Concentrador"  => "",
										"TaxaEmbarque"  => "",
										"Pax1"  => "",
										"Pax2"  => "",
										"Pax3"  => "",
										"Pax4"  => "",
										"Entrada"  => "",
										"Numdoc1"  => "",
										"Numdoc2"  => "",
										"Numdoc3"  => "",
										"Numdoc4"  => "",
										"ConfTxn"  => CONF_TXN,
										"Add_Data"  => ""
									));

			}
			
		}

		
	}	
	
	private function MontarParametrosEstornoDireto(){
		$this->parametros_soap = array($this->metodo_ws => array(
										"Total"   	=> $this->total_transacao,
										"Filiacao"  => $this->num_filiacao,
										"NumCV" 	=> $this->num_retorno_comprov_venda,
										"NumAutor"	=> $this->num_retorno_autorizacao,
										"Concentrador" => "",
										"Usr" 		=> $this->user_rede,
										"Pwd" 		=> $this->pwd_rede
									));
	}	


	private function MontarParametrosEstornoPreAutorizacao(){
		$this->parametros_soap = array($this->metodo_ws => array(
										"Total"   	=> $this->total_transacao,
										"Filiacao"  => $this->num_filiacao,
										"NumCV" 	=> $this->num_retorno_comprov_venda,
										"NumAutor"	=> $this->num_retorno_autorizacao,
										"Concentrador" => "",
										"Distribuidor" => "",
										"Data" => $this->getDataRetAutorizacao(),
										"Usr" 		=> $this->user_rede,
										"Pwd" 		=> $this->pwd_rede
									));
	}		
	
	private function MontarParametrosPreAutorizacao(){
		$this->parametros_soap = array($this->metodo_ws => array(
										"Total"   	=> $this->total_transacao,
										"Transacao" => "73",
										"Parcelas"  => "00", //VERIFICAR COM A REDECAR PORQUE NÃƒO ACEITA ESTE CAMPO VAZIO COMO O MANUAL DESCREVE
										"Filiacao"  => $this->num_filiacao,
										"NumPedido"  => $this->num_pedido,
										"Nrcartao"  => $this->num_cartao,
										"CVC2"  => $this->num_cvc2_cartao,
										"Mes"  => $this->mes_venc_cartao,
										"Ano"  => $this->ano_venc_cartao,
										"Portador"  => $this->nome_portador_cartao,
										"IATA"  => "",
										"Distribuidor"  => "",
										"Concentrador"  => "",
										"TaxaEmbarque"  => "",
										"Pax1"  => "",
										"Pax2"  => "",
										"Pax3"  => "",
										"Pax4"  => "",
										"Entrada"  => "",
										"Numdoc1"  => "",
										"Numdoc2"  => "",
										"Numdoc3"  => "",
										"Numdoc4"  => "",
										"ConfTxn"  => CONF_TXN,
										"Add_Data"  => ""
									));
	}
	
	private function MontarParametrosConfAutorizacao(){		$this->parametros_soap = array($this->metodo_ws => array(
										"Filiacao"  => $this->num_filiacao,
										"Distribuidor"  => "",
										"Total"   	=> $this->total_transacao,
										"TransOrig" => $this->tipo_transacao,
										"Parcelas"  => $this->num_parcelas,
										"Data"   	=> $this->data_retorno_autorizacao,
										"NumAutor" => $this->num_retorno_autorizacao,
										"NumCV" => $this->num_retorno_comprov_venda,
										"Concentrador" => "",
										"Usr" => $this->user_rede,
										"Pwd" => $this->pwd_rede
									));
	}
	
	public function RequisicaoAutorizacaoDireta(){
		if ($this->ambiente != "TESTE") $this->metodo_ws = "GetAuthorized";
		else $this->metodo_ws = "GetAuthorizedTst";
		
		$this->MontarParametrosAutorizacaoDireta();
		
		if ($this->EnviarSOAP() != NULL){
			$this->ProcessarRetorno();
			if($this->getCodRetAutorizacao() == "0"){
				if ($this->getConfirmacaoAutomatica() == 1) $this->setDataRetCaptura(date('y-m-d G:i:s'));
			}
			return true;
		}else{
			echo "<br />ERRO AO ENVIAR";
			return false;
		}
		return false;
		
	}
	
	public function RequisicaoPreAutorizacao(){
		if ($this->ambiente != "TESTE") $this->metodo_ws = "GetAuthorized";
		else $this->metodo_ws = "GetAuthorizedTst";
		
		$this->MontarParametrosPreAutorizacao();
		
		if ($this->EnviarSOAP() != NULL){
			$this->ProcessarRetorno();
			return true;
		}else{
			echo "<br />ERRO AO ENVIAR SOAP";
			return false;
		}
		return false;
		
	}

	public function EstornoPreAutorizacao(){
		if ($this->ambiente != "TESTE") $this->metodo_ws = "VoidPreAuthorization";
		else $this->metodo_ws = "VoidPreAuthorizationTst";
		
		$this->MontarParametrosEstornoPreAutorizacao();
		
		if ($this->EnviarSOAP() != NULL){
			$this->ProcessarRetornoEstornoPreAutorizacao();
			return true;
		}else{
			echo "<br />ERRO AO ENVIAR SOAP";
			return false;
		}
		return false;
	}
	
	public function ConfirmarAutorizacao(){
		if ($this->ambiente != "TESTE") $this->metodo_ws = "ConfPreAuthorization";
		else $this->metodo_ws = "ConfPreAuthorizationTst";
		
		$this->MontarParametrosConfAutorizacao();
		
		if ($this->EnviarSOAP() != NULL){
			$this->ProcessarRetornoConf();
			return true;
		}else{
			echo "<br />ERRO AO ENVIAR SOAP";
			return false;
		}
		return false;
	}
	
	public function EstornoDireto(){
		if ($this->ambiente != "TESTE") $this->metodo_ws = "VoidTransaction";
		else $this->metodo_ws = "VoidTransactionTst";
		
		$this->MontarParametrosEstornoDireto();
		
		if ($this->EnviarSOAP() != NULL){
			$this->ProcessarRetornoEstorno();
			return true;
		}else{
			echo "<br />ERRO AO ENVIAR SOAP";
			return false;
		}
		return false;
	}

	
	public function ConfirmarAutorizacaoParam($filiacao, $total, $parcelas, $data, $numautor, $numcv, $usr, $pwd, $transorigem){
		$this->num_filiacao = $filiacao;
		$this->total_transacao = $total;
		$this->num_parcelas = $parcelas;
		$this->data_retorno_autorizacao = $data;
		$this->num_retorno_autorizacao = $numautor;
		$this->num_retorno_comprov_venda = $numcv;
		$this->user_rede = $usr;
		$this->pwd_rede = $pwd;
		$this->tipo_transacao = $transorigem;
		
		if ($this->ambiente != "TESTE") $this->metodo_ws = "ConfPreAuthorization";
		else $this->metodo_ws = "ConfPreAuthorizationTst";
		
		$this->MontarParametrosConfAutorizacao();
		
		if ($this->EnviarSOAP() != NULL){
			$this->ProcessarRetornoConf();
			return true;
		}else{
			echo "<br />ERRO AO ENVIAR SOAP";
			return false;
		}
		return false;
	}
}
?>