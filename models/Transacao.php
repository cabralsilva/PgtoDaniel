<?php

class Transacao{
	private $cod_transacao;
	private $fk_pedido_pagamento;
	private $fk_pedido;
	private $fk_empresa;
	private $fk_forma_pagamento;
	private $fk_operadora;
	
	private $data_hora_pedido;
	private $data_hora_retorno_autorizacao;
	private $data_hora_retorno_autenticacao;
	private $data_hora_retorno_captura;
	private $data_hora_retorno_cancelamento;
	
	private $status_geral;
	
	private $valor_transacao;
	private $valor_liquido;
	private $taxa_operadora;
	private $valor_operadora;
	
	/*ESPECÍFICOS PARA BOLETOS*/
	private $tipo_inscricao_pagador;
	private $inscricao_pagador;
	private $cep_pagador;
	
	function __construct(){
	
	}
	
	public function getCodigo() {
		return $this->cod_transacao;
	}
	public function setCodigo($codigo) {
		$this->cod_transacao = $codigo;
	}
	
	public function getCodFormaPagamento() {
		return $this->fk_forma_pagamento;
	}
	public function setCodFormaPagamento($codFormaPagamento) {
		$this->fk_forma_pagamento = $codFormaPagamento;
	}
	
	public function getCodPedido() {
		return $this->fk_pedido;
	}
	public function setCodPedido($codPedido) {
		$this->fk_pedido = $codPedido;
	}
	
	
}
	