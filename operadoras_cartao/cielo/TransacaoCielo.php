<?phpoperadoras_cartao("configuracoes/constantesCielo.php");
class TransacaoCielo {
	
	/*CONSTRUCTER*/
	function __construct() {
		$this->dados_ec_numero = CIELO_NUMERO_TESTE;
		$this->dados_ec_chave = CHAVE_CIELO_TESTE;
		$this->url_ws_cielo = URL_CIELO_TESTE;
		$this->ambiente = "TESTE";
	}
	public function setDados($idPedido, $dadosPortadorNumero, $dadosPortadorValidade, $dadosPortadorIndicadorCodSeg, $dadosPortadorCodSeguranca, $dadosPdValor, $dadosPdDataHora, $dadosPdOrigem,$dadosPdBandeira, $dadosPdFormaPgto, $dadosPdQtdeParcelas, $codAutorizarTransacao, $capturarTransacao){
		$this->id_pedido = idPedido;
		$this->dados_ec_numero = CIELO_NUMERO;
		$this->dados_ec_chave = CHAVE_CIELO;
		
		$this->dados_portador_numero = $dadosPortadorNumero;
		$this->dados_portador_validade = $dadosPortadorValidade;
		$this->dados_portador_indicador = $dadosPortadorIndicadorCodSeg;
		if ($dadosPortadorIndicadorCodSeg != 1) $this->dados_portador_cod_seguranca = $dadosPortadorCodSeguranca;
		else $this->dados_portador_cod_seguranca = NULL;
		
		$this->dados_pd_numero = $idPedido;
		$this->dados_pd_valor = $dadosPdValor;
		$this->dados_pd_moeda = MOEDA;
		$this->dados_pd_data_hora = $dadosPdDataHora;
		$this->dados_pd_descricao = $dadosPdOrigem;
		$this->dados_pd_idioma = IDIOMA;
		$this->dados_forma_pgto_bandeira = $dadosPdBandeira;
		$this->dados_forma_pgto_produto = $dadosPdFormaPgto;
		$this->dados_forma_pgto_parcelas = $dadosPdQtdeParcelas;
		
		$this->autorizar = $codAutorizarTransacao;
		$this->capturar = $capturarTransacao;
		
		$this->dados_ec_numero = CIELO_NUMERO;
		$this->dados_ec_chave = CHAVE_CIELO;
		$this->url_ws_cielo = URL_CIELO;
		$this->ambiente = "HOMOLOGACAO";
	}

	public function setDadosTeste($idPedido, $dadosPortadorNumero, $dadosPortadorValidade, $dadosPortadorIndicadorCodSeg, $dadosPortadorCodSeguranca, $dadosPdValor, $dadosPdDataHora, $dadosPdOrigem,$dadosPdBandeira, $dadosPdFormaPgto, $dadosPdQtdeParcelas, $codAutorizarTransacao, $capturarTransacao){
		$this->dados_ec_numero = CIELO_NUMERO_TESTE;
		$this->dados_ec_chave = CHAVE_CIELO_TESTE;
		$this->url_ws_cielo = URL_CIELO_TESTE;
		$this->ambiente = "TESTE";

		$this->id_pedido = $idPedido;
		
		$this->dados_portador_numero = "4012001037141112";
		$this->dados_portador_validade = "201805";
		$this->dados_portador_indicador = 1;
		if ($dadosPortadorIndicadorCodSeg == 1) $this->dados_portador_cod_seguranca = "123";
		else $this->dados_portador_cod_seguranca = NULL;
		
		$this->dados_pd_numero = $idPedido;
		$dadosPdValor = substr($dadosPdValor, 0, -2);
		$this->dados_pd_valor = $dadosPdValor. "00";
		$this->dados_pd_moeda = MOEDA;
		$this->dados_pd_data_hora = $dadosPdDataHora;
		$this->dados_pd_descricao = $dadosPdOrigem;
		$this->dados_pd_idioma = IDIOMA;
		$this->dados_forma_pgto_bandeira = $dadosPdBandeira;
		$this->dados_forma_pgto_produto = $dadosPdFormaPgto;
		$this->dados_forma_pgto_parcelas = $dadosPdQtdeParcelas;
		
		$this->autorizar = $codAutorizarTransacao;
		$this->capturar = $capturarTransacao;
		
		$this->dados_ec_numero = CIELO_NUMERO;
		$this->dados_ec_chave = CHAVE_CIELO;
	}
	/*DESTRUCT*/
	function __destruct(){
		
	}
	private $id_pedido = "1";
	private $dados_ec_numero = NULL;
	private $dados_ec_chave = NULL;
	private $dados_portador_numero = "4012001037141112";
	private $dados_portador_validade = "201805";
	private $dados_portador_indicador = "1";
	private $dados_portador_cod_seguranca = "123";
	private $dados_pd_numero = "178148599";
	private $dados_pd_valor = "10000";
	private $dados_pd_moeda = "986";
	private $dados_pd_data_hora = "2011-12-27T11:43:37";
	private $dados_pd_descricao = "[origem:10.50.54.156]";
	private $dados_pd_idioma = "PT";
	private $dados_forma_pgto_bandeira = "visa";
	private $dados_forma_pgto_produto = "1";
	private $dados_forma_pgto_parcelas = "1";
	private $url_retorno = "http://www.iboltsys.com.br";
	private $autorizar = "3";
	private $capturar = "false";
	private $xml_envio_requisicao = "";
	
	private $ambiente = "TESTE";
	private $url_ws_cielo = "";
	
	
	private $xml_retorno_cielo = "";
	private $tid_retorno = NULL;
	private $pan_retorno = NULL;
	private $dados_pd_data_hora_retorno = NULL;
	private $dados_pd_taxa_embarque_retorno = NULL;
	private $status_retorno = NULL;
	private $url_autenticacao_retorno = NULL;
	private $autenticacao_codigo_retorno = NULL;
	private $autenticacao_mensagem_retorno = NULL;
	private $autenticacao_data_hora_retorno = NULL;
	private $autenticacao_eci_retorno = NULL;
	private $autorizacao_codigo_retorno = NULL;
	private $autorizacao_mensagem_retorno = NULL;
	private $autorizacao_data_hora_retorno = NULL;
	private $autorizacao_lr_retorno = NULL;
	private $autorizacao_arp_retorno = NULL;
	private $autorizacao_nsu_retorno = NULL;
	private $cancelamento_codigo_retorno = NULL;
	private $cancelamento_mensagem_retorno = NULL;
	private $cancelamento_data_hora_retorno = NULL;
	private $cancelamento_valor_retorno = NULL;
	private $captura_codigo_retorno = NULL;
	private $captura_mensagem_retorno = NULL;
	private $captura_data_hora_retorno = NULL;
	private $captura_valor_retorno = NULL;
	
	//XML DE ERRO
	private $codigo_erro_retorno = NULL;
	private $msg_erro_retorno = NULL;
	
	private $xml_envio_consulta = "";
	private $xml_envio_cancelar = "";
	private $xml_envio_captura = "";
	
	private $codigo_pedido_pagamento = NULL;
	private $status_geral = 0;
	private $codigo_operadora = 1;

	private $tipo_forma_pagamento;

	private $taxa = NULL;
	private $liquido = NULL;
	
	/*SETTERS*/
	function setId_Pedido($idPedido){
		$this->id_pedido = $idPedido;
	}
	public function setDados_EC_Numero($dadosECNumero){
		$this->dados_ec_numero = $dadosECNumero;
	}
	public function setDados_EC_Chave($dadosECChave){
		$this->dados_ec_chave = $dadosECChave;
	}
	public function setDados_Portador_Numero($dadosPortadorNumero){
		$this->dados_portador_numero = $dadosPortadorNumero;
	}
	public function setDados_Portador_Validade($dadosPortadorValidade){
		$this->dados_portador_validade = $dadosPortadorValidade;
	}
	public function setDados_Portador_Indicador($dadosPortadorIndicador){
		$this->dados_portador_indicador = $dadosPortadorIndicador;
	}
	public function setDados_Portador_Cod_Seguranca($dadosPortadorCodSeguranca){
		$this->dados_portador_cod_seguranca = $dadosPortadorCodSeguranca;
	}
	public function setDados_Pd_Numero($dadosPdNumero){
		$this->dados_pd_numero = $dadosPdNumero;
	}
	public function setDados_Pd_Valor($dadosPdValor){
		$this->dados_pd_valor = $dadosPdValor;
	}
	public function setDados_Pd_Moeda($dadosPdMoeda){
		$this->dados_pd_moeda = $dadosPdMoeda;
	}
	public function setDados_Pd_Data_Hora($dadosPdDataHora){
		$this->dados_pd_data_hora = $dadosPdDataHora;
	}
	public function setDados_Pd_Descricao($dadosPdDescricao){
		$this->dados_pd_descricao = $dadosPdDescricao;
	}
	public function setDados_Pd_Idioma($dadosPdIdioma){
		$this->dados_pd_idioma = $dadosPdIdioma;
	}
	public function setDados_Forma_Pgto_Bandeira($dadosFormaPgtoBandeira){
		$this->dados_forma_pgto_bandeira = $dadosFormaPgtoBandeira;
	}
	public function setDados_Forma_Pgto_Produto($dadosFormaPgtoProduto){
		$this->dados_forma_pgto_produto = $dadosFormaPgtoProduto;
	}
	public function setDados_Forma_Pgto_Parcelas($dadosFormaPgtoParcelas){
		$this->dados_forma_pgto_parcelas = $dadosFormaPgtoParcelas;
	}
	public function setUrl_Retorno($urlRetorno){
		$this->url_retorno = $urlRetorno;
	}
	public function setAutorizar($autorizar){
		$this->autorizar = $autorizar;
	}
	public function setCapturar($capturar){
		$this->capturar = $capturar;
	}
	public function setXml_Envio_Requisicao($xmlEnvioRequisicao){
		$this->xml_envio_requisicao = $xmlEnvioRequisicao;
	}	
	public function setUrl_WS_Cielo($urlCielo){
		$this->url_ws_cielo = $urlCielo;
	}
	public function setAmbiente($ambiente){
		$this->ambiente = $ambiente;
	}
	public function setXml_Retorno_Cielo($xmlRetornoCIelo){
		$this->xml_retorno_cielo = $xmlRetornoCIelo;
	}
	public function setDados_Pd_Data_Hora_Retorno($dadosPdDtHoraRetorno){
		$this->dados_pd_data_hora_retorno = $dadosPdDtHoraRetorno;
	}
	public function setDados_Pd_Taxa_Embarque_Retorno($dadosPdTaxaEmbarqueRetorno){
		$this->dados_pd_taxa_embarque_retorno = $dadosPdTaxaEmbarqueRetorno;
	}
	public function setStatus_Retorno($statusRetorno){
		$this->status_retorno = $statusRetorno;
	}
	public function setUrl_Autenticacao_Retorno($urlAutenticacaoRetorno){
		$this->url_autenticacao_retorno = $urlAutenticacaoRetorno;
	}
	public function setTid_Retorno($tidRetorno){
		$this->tid_retorno = $tidRetorno;
	}
	public function setPan_Retorno($panRetorno){
		$this->pan_retorno = $panRetorno;
	}
	public function setCodigo_Erro_Retorno($codigoErroRetorno){
		$this->codigo_erro_retorno = $codigoErroRetorno;
	}
	public function setMsg_Erro_Retorno($msgErroRetorno){
		$this->msg_erro_retorno = $msgErroRetorno;
	}
	public function setXml_Envio_Consulta($xmlEnvioConsulta){
		$this->xml_envio_consulta = $xmlEnvioConsulta;
	}
	public function setXml_Envio_Cancelar($xmlEnvioCancelar){
		$this->xml_envio_cancelar = $xmlEnvioCancelar;
	}
	public function setAutenticacao_Codigo_Retorno($autenticacaoCodigoRetorno){
		$this->autenticacao_codigo_retorno = $autenticacaoCodigoRetorno;
	}
	public function setAutenticacao_Mensagem_Retorno($autenticacaoMensagemRetorno){
		$this->autenticacao_mensagem_retorno = $autenticacaoMensagemRetorno;
	}
	public function setAutenticacao_data_hora_Retorno($autenticacaoDataHoraRetorno){
		$this->autenticacao_data_hora_retorno = $autenticacaoDataHoraRetorno;
	}
	public function setAutenticacao_Eci_Retorno($autenticacaoEciRetorno){
		$this->autenticacao_eci_retorno = $autenticacaoEciRetorno;
	}
	public function setAutorizacao_Codigo_Retorno($autorizacaoCodigoRetorno){
		$this->autorizacao_codigo_retorno = $autorizacaoCodigoRetorno;
	}
	public function setAutorizacao_Mensagem_Retorno($autorizacaoMensagemRetorno){
		$this->autorizacao_mensagem_retorno = $autorizacaoMensagemRetorno;
	}
	public function setAutorizacao_Data_Hora_Retorno($autorizacaoDataHoraRetorno){
		$this->autorizacao_data_hora_retorno = $autorizacaoDataHoraRetorno;
	}
	public function setAutorizacao_Lr_Retorno($autorizacaoLrRetorno){
		$this->autorizacao_lr_retorno = $autorizacaoLrRetorno;
	}
	public function setAutorizacao_Arp_Retorno($autorizacaoArpRetorno){
		$this->autorizacao_arp_retorno = $autorizacaoArpRetorno;
	}
	public function setAutorizacao_Nsu_Retorno($autorizacaoNsuRetorno){
		$this->autorizacao_nsu_retorno = $autorizacaoNsuRetorno;
	}
	public function setCodPedidoPagamento($codPedidoPagamento){
		$this->codigo_pedido_pagamento = $codPedidoPagamento;
	}
	public function setCancelamentoCodRetorno($param){
		$this->cancelamento_codigo_retorno = $param;
	}
	public function setCancelamentoMsgRetorno($param){
		$this->cancelamento_mensagem_retorno = $param;
	}
	public function setCancelamentoDataHoraRetorno($param){
		$this->cancelamento_data_hora_retorno = $param;
	}
	public function setCancelamentoValorRetorno($param){
		$this->cancelamento_valor_retorno = $param;
	}
	public function setCodigoOperadora($param){
		$this->codigo_operadora = $param;
	}
	public function setXml_Envio_Captura($param){
		$this->xml_envio_captura = $param;
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
		switch ($this->getStatus_Retorno()) {
			case 0 :
				$this->status_geral = 0;
				break;
			case 1 :
				$this->status_geral = 0;
				break;
			case 2 :
				$this->status_geral = 1;
				break;
			case 3 :
				$this->status_geral = 2;
				break;
			case 4 :
				$this->status_geral = 3;
				break;
			case 5 :
				$this->status_geral = 4;
				break;
			case 6 :
				$this->status_geral = 5;
				break;
			case 9 :
				$this->status_geral = 6;
				break;
			default:
				$this->status_geral = 7;
				break;
		}
	}
	public function setCodCapturaRetorno($param){
		$this->captura_codigo_retorno = $param;
	}
	public function setMsgCapturaRetorno($param){
		$this->captura_mensagem_retorno = $param;
	}
	public function setDataHoraCapturaRetorno($param){
		$this->captura_data_hora_retorno = $param;
	}
	public function setValorCapturaRetorno($param){
		$this->captura_valor_retorno = $param;
	}
	public function setTipoFormaPagamento($param){
		$this->tipo_forma_pagamento = $param;
	}
	public function setTaxa($param){
		$this->taxa = $param;
	}
	public function setLiquido($param){
		$this->liquido = $param;
	}	


	
	/*GETTERS*/
	public function getLiquido(){
		return $this->liquido;
	}
	public function getTaxa(){
		return $this->taxa;
	}	
	public function getTipoFormaPagamento(){
		return $this->tipo_forma_pagamento;
	}
	public function getId_Pedido(){
		return $this->id_pedido;
	}
	public function getDados_EC_Numero(){
		return $this->dados_ec_numero;
	}
	public function getDados_EC_Chave(){
		return $this->dados_ec_chave;
	}
	public function getDados_Portador_Numero(){
		return $this->dados_portador_numero;
	}
	public function getDados_Portador_Validade(){
		return $this->dados_portador_validade;
	}
	public function getDados_Portador_Indicador(){
		return $this->dados_portador_indicador;
	}
	public function getDados_Portador_Cod_Seguranca(){
		return $this->dados_portador_cod_seguranca;
	}
	public function getDados_Pd_Numero(){
		return $this->dados_pd_numero;
	}
	public function getDados_Pd_Valor(){
		return $this->dados_pd_valor;
	}
	public function getDados_Pd_Moeda(){
		return $this->dados_pd_moeda;
	}
	public function getDados_Pd_Data_Hora(){
		return $this->dados_pd_data_hora;
	}
	public function getDados_Pd_Descricao(){
		return $this->dados_pd_descricao;
	}
	public function getDados_Pd_Idioma(){
		return $this->dados_pd_idioma;
	}
	public function getDados_Forma_Pgto_Bandeira(){
		return $this->dados_forma_pgto_bandeira;
	}
	public function getDados_Forma_Pgto_Produto(){
		return $this->dados_forma_pgto_produto;
	}
	public function getDados_Forma_Pgto_Parcelas(){
		return $this->dados_forma_pgto_parcelas;
	}
	public function getUrl_Retorno(){
		return $this->url_retorno;
	}
	public function getAutorizar(){
		return $this->autorizar;
	}
	public function getCapturar(){
		return $this->capturar;
	}
	public function getXml_Envio_Requisicao(){
		return $this->xml_envio_requisicao;
	}
	public function getUrlCielo(){
		return $this->url_ws_cielo;
	}
	public function getAmbiente(){
		return $this->ambiente;
	}
	public function getXml_Retorno_Cielo(){
		return $this->xml_retorno_cielo;
	}
	public function getDados_Pd_Data_Hora_Retorno(){
		return $this->dados_pd_data_hora_retorno;
	}
	public function getDados_Pd_Taxa_Embarque_Retorno(){
		return $this->dados_pd_taxa_embarque_retorno;
	}
	public function getStatus_Retorno(){
		return $this->status_retorno;
	}
	public function getUrl_Autenticacao_Retorno(){
		return $this->url_autenticacao_retorno;
	}
	public function getTid_Retorno(){
		return $this->tid_retorno;
	}
	public function getPan_Retorno(){
		return $this->pan_retorno;
	}
	public function getCodigo_Erro_Retorno(){
		return $this->codigo_erro_retorno;
	}
	public function getMsg_Erro_Retorno(){
		return $this->msg_erro_retorno;
	}
	public function getXml_Envio_Consulta(){
		return $this->xml_envio_consulta;
	}
	public function getXml_Envio_Cancelar(){
		return $this->xml_envio_cancelar ;
	}
	public function getAutenticacao_Codigo_Retorno(){
		return $this->autenticacao_codigo_retorno;
	}
	public function getAutenticacao_Mensagem_Retorno(){
		return $this->autenticacao_mensagem_retorno;
	}
	public function getAutenticacao_data_hora_Retorno(){
		return $this->autenticacao_data_hora_retorno;
	}
	public function getAutenticacao_Eci_Retorno(){
		return $this->autenticacao_eci_retorno;
	}
	public function getAutorizacao_Codigo_Retorno(){
		return $this->autorizacao_codigo_retorno;
	}
	public function getAutorizacao_Mensagem_Retorno(){
		return $this->autorizacao_mensagem_retorno;
	}
	public function getAutorizacao_Data_Hora_Retorno(){
		return $this->autorizacao_data_hora_retorno;
	}
	public function getAutorizacao_Lr_Retorno(){
		return $this->autorizacao_lr_retorno;
	}
	public function getAutorizacao_Arp_Retorno(){
		return $this->autorizacao_arp_retorno;
	}
	public function getAutorizacao_Nsu_Retorno(){
		return $this->autorizacao_nsu_retorno;
	}
	public function getCodPedidoPagamento(){
		return $this->codigo_pedido_pagamento;
	}
	public function getCancelamentoCodRetorno(){
		return $this->cancelamento_codigo_retorno;
	}
	public function getCancelamentoMsgRetorno(){
		return $this->cancelamento_mensagem_retorno;
	}
	public function getCancelamentoDataHoraRetorno(){
		return $this->cancelamento_data_hora_retorno;
	}
	public function getCancelamentoValorRetorno(){
		return $this->cancelamento_valor_retorno;
	}
	public function getStatusGeral(){
		return $this->status_geral;
	}
	public function getCodigoOperadora(){
		return $this->codigo_operadora;
	}
	public function getXml_Envio_Captura(){
		return $this->xml_envio_captura;
	}
	public function getCodCapturaRetorno(){
		return $this->captura_codigo_retorno;
	}
	public function getMsgCapturaRetorno(){
		return $this->captura_mensagem_retorno;
	}
	public function getDataHoraCapturaRetorno(){
		return $this->captura_data_hora_retorno;
	}
	public function getValorCapturaRetorno(){
		return $this->captura_valor_retorno;
	}


	private function MontarXmlRequisicao(){
		$this->xml_envio_requisicao = 
				"<?xml version='1.0' encoding='ISO-8859-1'?>" . "\n".
			 	"<requisicao-transacao id='".$this->id_pedido."' versao='1.2.1' xmlns='http://ecommerce.cbmp.com.br'>". "\n".
					"<dados-ec>" . "\n".
						"<numero>".$this->dados_ec_numero."</numero>" . "\n".
						"<chave>".$this->dados_ec_chave."</chave>" . "\n".
					"</dados-ec>" . "\n".
					"<dados-portador>" . "\n".
						"<numero>".$this->dados_portador_numero."</numero>" . "\n".
						"<validade>".$this->dados_portador_validade."</validade>" . "\n".
						"<indicador>".$this->dados_portador_indicador."</indicador>" . "\n".
						"<codigo-seguranca>".$this->dados_portador_cod_seguranca."</codigo-seguranca>" . "\n".
						"<token/>" . "\n".
					"</dados-portador>" . "\n".
					"<dados-pedido>" . "\n".
						"<numero>".$this->dados_pd_numero."</numero>" . "\n".
						"<valor>".$this->dados_pd_valor."2</valor>" . "\n".
						"<moeda>".$this->dados_pd_moeda."</moeda>" . "\n".
						"<data-hora>".$this->dados_pd_data_hora."</data-hora>" . "\n".
						"<descricao>".$this->dados_pd_descricao."</descricao>" . "\n".
						"<idioma>".$this->dados_pd_idioma."</idioma>" . "\n".
					"</dados-pedido>" . "\n".
					"<forma-pagamento>" . "\n".
						"<bandeira>".$this->dados_forma_pgto_bandeira."</bandeira>" . "\n".
						"<produto>".$this->dados_forma_pgto_produto."</produto>" . "\n".
						"<parcelas>".$this->dados_forma_pgto_parcelas."</parcelas>" . "\n".
					"</forma-pagamento>" . "\n".
					"<url-retorno>".$this->url_retorno."</url-retorno>" . "\n".
					"<autorizar>".$this->autorizar."</autorizar>" . "\n".
					"<capturar>".$this->capturar."</capturar>" . "\n".
				"</requisicao-transacao>";
		//echo $this->xml_envio_requisicao;
	}
	
	private function MontarXmlConsulta(){
		$this->xml_envio_consulta =
				'<?xml version="1.0" encoding="ISO-8859-1" ?>' . "\n" .
				'<requisicao-consulta id="'.$this->id_pedido.'" versao="1.2.1">' . "\n" .
					'<tid>'.$this->tid_retorno.'</tid>' . "\n" .
					'<dados-ec>' . "\n" .
						  '<numero>'.$this->dados_ec_numero.'</numero>' . "\n" .
						  '<chave>'.$this->dados_ec_chave.'</chave>' . "\n" .
					'</dados-ec>' . "\n" .
				'</requisicao-consulta>';
	}
	
	private function MontarXmlCancelar(){
		$this->xml_envio_cancelar =
			'<?xml version="1.0" encoding="ISO-8859-1" ?>' . "\n" .
			'<requisicao-cancelamento id="'.$this->id_pedido.'" versao="1.2.1">' . "\n" .
				'<tid>'.$this->tid_retorno.'</tid>' . "\n" .
				'<dados-ec>' . "\n" .
					  '<numero>'.$this->dados_ec_numero.'</numero>' . "\n" .
					  '<chave>'.$this->dados_ec_chave.'</chave>' . "\n" .
				'</dados-ec>' . "\n" .
			'</requisicao-cancelamento>';
	}

	private function MontarXmlCaptura(){
		$this->xml_envio_captura =
			'<?xml version="1.0" encoding="ISO-8859-1" ?>' . "\n" .
			'<requisicao-captura id="'.$this->id_pedido.'" versao="1.2.1">' . "\n" .
				'<tid>'.$this->tid_retorno.'</tid>' . "\n" .
				'<dados-ec>' . "\n" .
					  '<numero>'.$this->dados_ec_numero.'</numero>' . "\n" .
					  '<chave>'.$this->dados_ec_chave.'</chave>' . "\n" .
				'</dados-ec>' . "\n" .
			'</requisicao-captura>';
	}
	
	/*ProcessarRetorno
	* @Descrição				- Todo xml enviado terá um xml de retorno e esta função irá processar o xml de retorno e atualizará as propriedades da classe
	* @return 	'000' 			- O arquivo xml foi foi processado com sucesso
	*			'001' até '099' - Ouve um erro no arquivo xml enviado para a operadora
	*			'100' 			- Ouve um erro no processamento do xml recebido
	*/
	private function ProcessarRetorno(){
		if($this->xml_retorno_cielo->tid){
			$this->setTid_Retorno($this->xml_retorno_cielo->tid);
			$this->setPan_Retorno($this->xml_retorno_cielo->pan);
			//$this->setDados_Pd_Data_Hora_Retorno($this->xml_retorno_cielo->{'dados-pedido'}->{'data-hora'});
			$this->setDados_Pd_Taxa_Embarque_Retorno($this->xml_retorno_cielo->{'dados-pedido'}->{'taxa-embarque'});
			$this->setStatus_Retorno($this->xml_retorno_cielo->status);
			$this->setStatusGeral();
			$this->setCodigoOperadora(1);
			if (($this->xml_retorno_cielo->{'url-autenticacao'}) != NULL) 
				$this->setUrl_Autenticacao_Retorno($this->xml_retorno_cielo->{'url-autenticacao'});
			
			if($this->xml_retorno_cielo->autenticacao){
				$this->setAutenticacao_Codigo_Retorno($this->xml_retorno_cielo->autenticacao->codigo);
				$this->setAutenticacao_Mensagem_Retorno($this->xml_retorno_cielo->autenticacao->mensagem);
				$this->setAutenticacao_data_hora_Retorno($this->xml_retorno_cielo->autenticacao->{'data-hora'});
				$this->setAutenticacao_Eci_Retorno($this->xml_retorno_cielo->autenticacao->eci);
			}
			if($this->xml_retorno_cielo->autorizacao){
				$this->setAutorizacao_Codigo_Retorno($this->xml_retorno_cielo->autorizacao->codigo);
				$this->setAutorizacao_Mensagem_Retorno($this->xml_retorno_cielo->autorizacao->mensagem);
				$this->setAutorizacao_Data_Hora_Retorno($this->xml_retorno_cielo->autorizacao->{'data-hora'});
				$this->setAutorizacao_Lr_Retorno($this->xml_retorno_cielo->autorizacao->lr);
				$this->setAutorizacao_Arp_Retorno($this->xml_retorno_cielo->autorizacao->arp);
				$this->setAutorizacao_Nsu_Retorno($this->xml_retorno_cielo->autorizacao->nsu);
			}	
			if($this->xml_retorno_cielo->cancelamentos){
				$this->setCancelamentoCodRetorno($this->xml_retorno_cielo->cancelamentos->cancelamento->codigo);
				$this->setCancelamentoMsgRetorno($this->xml_retorno_cielo->cancelamentos->cancelamento->mensagem);
				$this->setCancelamentoDataHoraRetorno($this->xml_retorno_cielo->cancelamentos->cancelamento->{'data-hora'});
				$this->setCancelamentoValorRetorno($this->xml_retorno_cielo->cancelamentos->cancelamento->valor);
			}
			if($this->xml_retorno_cielo->captura){
				$this->setCodCapturaRetorno($this->xml_retorno_cielo->captura->codigo);
				$this->setMsgCapturaRetorno($this->xml_retorno_cielo->captura->mensagem);
				$this->setDataHoraCapturaRetorno($this->xml_retorno_cielo->captura->{'data-hora'});
				$this->setValorCapturaRetorno($this->xml_retorno_cielo->captura->valor);
			}
			
			return "000"; //dados processados com sucesso
		}else if ($this->xml_retorno_cielo->codigo){
			$this->setCodigo_Erro_Retorno($this->xml_retorno_cielo->codigo);
			$this->setMsg_Erro_Retorno($this->xml_retorno_cielo->mensagem);
			return $this->getCodigo_Erro_Retorno(); //erro no xml enviado
		}else {
			return "100"; // falha no processamento do xml recebido
		}
	}
	
	/*EnviarXML
	* @Descrição		- Todos os xmls enviados a operadora são realizados por esta função.
	* @return 	NULL 	- Ouve um erro ao enviar o arquivo, verificar configurações inseridas no envelope HTTP
	*			$this->getXml_Retorno_Cielo() - O xml devolvido pela operadora
	*/
	private function EnviarXML($urlServico, $xml){
		$this->setXml_Retorno_Cielo(NULL);
		$envelopeHTTP = curl_init();
		if ( is_resource( $envelopeHTTP ) ){
			curl_setopt( $envelopeHTTP , CURLOPT_HEADER , 0 );
			curl_setopt( $envelopeHTTP , CURLOPT_RETURNTRANSFER , 1 );
			curl_setopt( $envelopeHTTP , CURLOPT_FOLLOWLOCATION , 1 );
			curl_setopt( $envelopeHTTP , CURLOPT_URL , $urlServico );
			curl_setopt( $envelopeHTTP , CURLOPT_POST , 1 );
			curl_setopt( $envelopeHTTP , CURLOPT_POSTFIELDS , http_build_query( array( 'mensagem' => $xml ) ) );
			curl_setopt( $envelopeHTTP , CURLOPT_SSL_VERIFYPEER, 0);
			$this->setXml_Retorno_Cielo(curl_exec( $envelopeHTTP ));
			$erro_numero = curl_errno( $envelopeHTTP ); 
			$erro_descricao = curl_error( $envelopeHTTP );
			curl_close( $envelopeHTTP );
			//echo $this->getXml_Retorno_Cielo();
			if ( (bool) $erro_numero ) {
				return NULL;
			}
			else {
				//echo $this->getXml_Retorno_Cielo();
				try {
					$this->setXml_Retorno_Cielo(simplexml_load_string($this->getXml_Retorno_Cielo())) ;
				} catch (Exception $e) {
					$this->setCodigo_Erro_Retorno(100);
					$this->setMsg_Erro_Retorno("Erro de estrutura do xml enviado ao webservice. Contate o Administrador do sistema");
					return null;
				}
				return $this->getXml_Retorno_Cielo();
			}
		}
		return NULL;	
	}
	
	
	
	/*
	* @return 	true 	- O arquivo xml foi enviado com sucesso e a resposta foi processada
	*			false 	- O arquivo não pode ser enviado devido a uma falha de configuração do curl ou inatividade do servidor. 
	*/
	public function RequisicaoTransacao(){
		$this->MontarXmlRequisicao();
		$this->setXml_Retorno_Cielo($this->EnviarXML($this->getUrlCielo(), $this->getXml_Envio_Requisicao() ));
		
		if ($this->getXml_Retorno_Cielo() != NULL){

			$this->ProcessarRetorno();
			return true;
		}


		
		return false;
	}
	
	public function ConsultarTransacao(){
		if (($this->getTid_Retorno() == NULL) or ($this->getId_Pedido() == NULL)) return false;
		else{
			$this->MontarXmlConsulta();
			$this->setXml_Retorno_Cielo($this->EnviarXML($this->getUrlCielo(), $this->getXml_Envio_Consulta() ));
			if ($this->getXml_Retorno_Cielo() != NULL){
				$this->ProcessarRetorno();
				return true;
			}
		}
			
		return false;
	}
	
	public function ConsultarTransacaoParametro($idPedido, $tidTransacao){
		if (($tidTransacao == NULL) or ($idPedido == NULL)) return false;
		else{
			$this->setId_Pedido($idPedido);
			$this->setTid_Retorno($tidTransacao);
			$this->MontarXmlConsulta();
			$this->setXml_Retorno_Cielo($this->EnviarXML($this->getUrlCielo(), $this->getXml_Envio_Consulta() ));
			if ($this->getXml_Retorno_Cielo() != NULL){
				$this->ProcessarRetorno();
				return true;
			}
		}
			
		return false;
	}
	
	public function CancelarTransacao () {
		if (($this->getTid_Retorno() == NULL) or ($this->getId_Pedido() == NULL)) return false;
		else{
			$this->MontarXmlCancelar();
			$this->setXml_Retorno_Cielo($this->EnviarXML($this->getUrlCielo(), $this->getXml_Envio_Cancelar() ));
			if ($this->getXml_Retorno_Cielo() != NULL){
				$this->ProcessarRetorno();
				return true;
			}
		}
		
		return false;
	}
	
	public function CancelarTransacaoParametro($idPedido, $tidTransacao){
		if (($tidTransacao == NULL) or ($idPedido == NULL)) return false;
		else{
			$this->setId_Pedido($idPedido);
			$this->setTid_Retorno($tidTransacao);
			$this->MontarXmlCancelar();
			$this->setXml_Retorno_Cielo($this->EnviarXML($this->getUrlCielo(), $this->getXml_Envio_Cancelar() ));
			if ($this->getXml_Retorno_Cielo() != NULL){
				$this->ProcessarRetorno();
				return true;
			}
		}
			
		return false;
	}
	
	
	public function AutenticarTransacao(){
		/*
		* Indicador de autorização: 
		* 	0 – Não autorizar (somente autenticar). 
		*	1 – Autorizar somente se autenticada. 
		*	2 – Autorizar autenticada e não autenticada. 
		*	3 – Autorizar sem passar por autenticação (somente para crédito) – também conhecida como “Autorização Direta”. Obs.: Para Diners, Discover, Elo, 				
				Amex, Aura e JCB o valor será sempre “3”, pois estas bandeiras não possuem programa de autenticação. 
		*	4 – Transação Recorrente (Somente válido para crédito à vista).
		*/
		if ($this->getAutorizar() != "3" ){
			if ($this->getUrl_Autenticacao_Retorno() != NULL){
				header("location:". $this->getUrl_Autenticacao_Retorno());
			} 
		}
		return false;
	}

	public function CapturarTransacao(){
		if (($this->getTid_Retorno() == NULL) or ($this->getId_Pedido() == NULL)) return false;
		else{
			$this->MontarXmlCaptura();
			$this->setXml_Retorno_Cielo($this->EnviarXML($this->getUrlCielo(), $this->getXml_Envio_Captura() ));
			if ($this->getXml_Retorno_Cielo() != NULL){
				$this->ProcessarRetorno();
				return true;
			}
		}
			
		return false;
	}
}	
?>