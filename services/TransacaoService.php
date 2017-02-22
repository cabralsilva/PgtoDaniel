<?php
require_once '../util/Banco.php';
class TransacaoService {
	private $banco;
	function __construct() {
		$this->banco = new BancoDados ();
		try {
			$this->banco->connect ();
		} catch ( Exception $e ) {
			echo "Falha na Conexão com Base de Dados" . $e->getMessage ();
		}
	}
	private function gerarEspacosBrancos($qtd) {
		$espacos = "";
		for($i = 0; $i < $qtd; $i ++)
			$espacos .= " ";
		return $espacos;
	}
	private function gerarEspacosZeros($qtd) {
		$zeros = "";
		for($i = 0; $i < $qtd; $i ++)
			$zeros .= "0";
		return $zeros;
	}
	private function removePontosVirgulas($number, $sizeField) {
		$number = str_replace ( ",", "", $number );
		$number = str_replace ( ".", "", $number );
		$number = str_pad ( $number, $sizeField, '0', STR_PAD_LEFT );
		return $number;
	}
	private function zerosEsquerda($number, $sizeField) {
		$number = str_pad ( $number, $sizeField, '0', STR_PAD_LEFT );
		return $number;
	}
	private function brancosDireita($texto, $sizeField) {
		$mb_diff = mb_strlen ( $texto, 'UTF-8' ) - strlen ( $texto );
		$texto = str_pad ( $texto, $sizeField - $mb_diff, ' ', STR_PAD_RIGHT );
		return $texto;
	}
	private function calculoDigitoNossoNumero($carteira, $nossoNumero) {
		$numero = $carteira . $nossoNumero;
		if (strlen ( $numero ) == 13) {
			$multiplicador = 2;
			$soma = 0;
			$multiplicacao = 0;
			for($i = 12; $i >= 0; $i --) {
				$multiplicacao = (substr ( $numero, $i, 1 )) * $multiplicador;
				$multiplicador ++;
				if ($multiplicador > 7)
					$multiplicador = 2;
				$soma += $multiplicacao;
			}
			$resto = $soma % 11;
			switch ($resto) {
				case 0 :
					return '0';
					break;
				case 1 :
					return 'P';
					break;
				default :
					return 11 - $resto;
			}
		} else
			return 'E';
	}
	private function buscaTransacoesPersonalizada($sql, $codEmpresa) {
		$sql .= " AND operadora_empresa.fk_empresa = $codEmpresa order by id_transacao desc";
		$consulta = $this->banco->getConexaoBanco ()->query ( $sql );
		$lstBoletos = array ();
		while ( $linha = $consulta->fetch_array ( MYSQLI_ASSOC ) ) {
			array_push ( $lstBoletos, $linha );
		}
		$consulta->close ();
		return $lstBoletos;
	}
	
	private function executarSQLFiltro($sql, $codEmpresa) {
		$sql .= " AND operadora_empresa.fk_empresa = $codEmpresa order by id_transacao desc";
		$consulta = $this->banco->getConexaoBanco ()->query ( $sql );
		$lstTransacoes = array ();
		while ( $linha = $consulta->fetch_array ( MYSQLI_ASSOC ) ) {
			array_push ( $lstTransacoes, $linha );
		}
		$consulta->close ();
		return $lstTransacoes;
	}
	
	private function updateSequencial($sequencial, $operadoraEmpresa) {
// 		print_r( $sequencial);
		$sql = "UPDATE operadora_empresa SET operadora_empresa.num_sequencial_remessa = " . ($sequencial + 1) . " WHERE operadora_empresa.id_operadora_empresa = " . $operadoraEmpresa;
		
		$result = $this->banco->getConexaoBanco ()->query ( $sql );
	}
	private function getSequencial($operadoraEmpresa) {
		$sql = "SELECT operadora_empresa.num_sequencial_remessa
					FROM operadora_empresa WHERE operadora_empresa.id_operadora_empresa = " . $operadoraEmpresa;
		$consulta = $this->banco->getConexaoBanco ()->query ( $sql );
		$sequencial = $consulta->fetch_array ( MYSQLI_ASSOC );
		$consulta->close ();
		return $sequencial;
	}
	private function updateTransacaoRemessa($lstT, $dataArquivo, $nomeArquivo, $sequencial, $operadoraEmpresa, $idRemessa, $dataReferencia) {
		$dataFormatada = date ( 'Y-m-d', strtotime ( $dataArquivo ) );
		if ($dataReferencia) $dataReferencia = date ("Y-m-d", strtotime($dataReferencia));
		try {
			if ($idRemessa == 0){
				$sql = "INSERT INTO arquivos (
							data_banco, data_entrada, sequencial, nome_arquivo, fk_operadora_empresa
						)VALUES ( 
							DATE '" . $dataReferencia . "', 
							DATE '" . $dataFormatada . "', " .
							$sequencial . ", '" .
							$nomeArquivo . "', " .
							$operadoraEmpresa . "
						)";
					if ($this->banco->getConexaoBanco ()->query ( $sql )) {
						$id = $this->banco->getConexaoBanco ()->insert_id;
						
// 						print_r($lstT);
						foreach ( $lstT as $key => $value ) {
							
							if ($lstT [$key] ["fk_status"] == 8){
								$lstT [$key] ["novo_status"] = 1;
							}elseif ($lstT [$key] ["fk_status"] == 11){
								$lstT [$key] ["novo_status"] = 3;
							}elseif ($lstT [$key] ["fk_status"] == 12){
								$lstT [$key] ["novo_status"] = 5;
							}
							$sql = "INSERT INTO movimentacao (fk_transacao, fk_arquivo, fk_status)
									VALUES (".$lstT [$key] ["id_transacao"].", $id, " . $lstT [$key] ["novo_status"] . ")";
							$result = $this->banco->getConexaoBanco ()->query ( $sql );
							
							
							$sql = "UPDATE movimentacao SET 
									movimentacao.fk_arquivo = $id WHERE movimentacao.id_movimentacao = " . $lstT [$key] ["id_movimentacao"];
							$result = $this->banco->getConexaoBanco ()->query ( $sql );
							
							$this->atualizaEstadoTransacao($lstT [$key] ["id_transacao"]);
						}
					
// 						$dataFormatada = date ( 'Y-m-d', strtotime ( $dataArquivo ) );
// 						foreach ( $lstT as $key => $value ) {
// 							$sql = "UPDATE transacao SET " . "transacao.sequencial = " . $sequencial . "," . "transacao.data_arquivo = '" . $dataFormatada . "'," . "transacao.nome_arquivo = '" . $nomeArquivo . "' " . "WHERE transacao.id_transacao = " . $lstT [$key] ["id_transacao"];
// 							$result = $this->banco->getConexaoBanco ()->query ( $sql );
// 						}
						return array (
								'CodStatus' => 1,
								'Msg' => "Success!",
								'Model' => $id
						);
					} else {
						$consulta->close ();
						return array (
								'CodStatus' => 2,
								'Msg' => "Falha ao inserir novo registro!"
						);
					}
			}else{
				$sql = "UPDATE arquivos set arquivos.data_entrada = '" . $dataFormatada . "' WHERE arquivos.id_arquivo = $idRemessa";
				$this->banco->getConexaoBanco ()->query ( $sql );
// 				if ($this->banco->getConexaoBanco ()->query ( $sql )) {
// 					print_r($lstT);
// 					foreach ( $lstT as $key => $value ) {

// 						if ($lstT [$key] ["fk_status"] == 8){
// 							$lstT [$key] ["novo_status"] = 1;
// 						}elseif ($lstT [$key] ["fk_status"] == 11){
// 							$lstT [$key] ["novo_status"] = 3;
// 						}elseif ($lstT [$key] ["fk_status"] == 12){
// 							$lstT [$key] ["novo_status"] = 5;
// 						}
						
// 						print_r($lstT[$key]);
// 						$sql = "UPDATE movimentacao SET
// 							movimentacao.fk_remessa = $idRemessa WHERE movimentacao.id_movimentacao = " . $lstT [$key] ["id_movimentacao"];
// 						$result = $this->banco->getConexaoBanco ()->query ( $sql );
						
// 						$sql = "INSERT INTO movimentacao (fk_transacao, fk_remessa, fk_status)
// 									VALUES (".$lstT [$key] ["id_transacao"].", $id, " . $lstT [$key] ["novo_status"] . ")";
// 						$result = $this->banco->getConexaoBanco ()->query ( $sql );
													
// 						$sql = "UPDATE transacao SET 
// 									transacao.fk_remessa = $idRemessa 
// 									,movimentacao.fk_status = " . $lstT [$key] ["novo_status"] . " WHERE transacao.id_transacao = " . $lstT [$key] ["id_transacao"];
// 						$result = $this->banco->getConexaoBanco ()->query ( $sql );
// 					}
						
// 					$dataFormatada = date ( 'Y-m-d', strtotime ( $dataArquivo ) );
// 					foreach ( $lstT as $key => $value ) {
// 						$sql = "UPDATE transacao SET " . "transacao.sequencial = " . $sequencial . "," . "transacao.data_arquivo = '" . $dataFormatada . "'," . "transacao.nome_arquivo = '" . $nomeArquivo . "' " . "WHERE transacao.id_transacao = " . $lstT [$key] ["id_transacao"];
// 						$result = $this->banco->getConexaoBanco ()->query ( $sql );
// 					}
					return array (
							'CodStatus' => 1,
							'Msg' => "Success!",
							'Model' => $idRemessa
					);
				}
				
// 			}
			
		
					
		} catch ( Exception $e ) {
// 			echo "\n\n$sql\n\n";
			return array (
					'CodStatus' => 3,
					'Msg' => $e->getMessage ()
			);
		}
		
		
		
	}
	
	private function replaceCharAscensionToUpper($string){
		$string = preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/"),explode(" ","a A e E i I o O u U n N"),$string);
		return strtoupper(iconv( "UTF-8" , "ASCII//TRANSLIT//IGNORE" , $string ));
	}
	public function getTransacoesBoletos($codEmpresa) {
		// echo " -------------- PRIMEIRO SELECT ------------ -> " . $transacao["Codigo"];
		/*
		 * $sql = "SELECT transacao.*, operadoras.*, operadora_empresa.* FROM TRANSACAO
		 * INNER JOIN operadoras ON operadoras.id_operadora = transacao.fk_operadora
		 * INNER JOIN operadora_empresa ON operadora_empresa.fk_operadora = transacao.fk_operadora AND operadora_empresa.fk_empresa = transacao.fk_empresa
		 * WHERE transacao.fk_forma_pagamento = 22 AND transacao.fk_empresa = $codEmpresa ORDER BY transacao.id_transacao DESC";
		 */
		// echo $sql;
		$sql = "SELECT transacao.*, operadoras.nome_operadora, operadora_empresa.* FROM TRANSACAO
					left outer join forma_pagamento_operadora_empresa on transacao.fk_forma_pagamento_operadora_empresa = forma_pagamento_operadora_empresa.id_forma_pagamento_operadora_empresa
					INNER JOIN operadora_empresa ON forma_pagamento_operadora_empresa.fk_operadora_empresa = operadora_empresa.id_operadora_empresa
				    INNER JOIN operadoras ON operadora_empresa.fk_operadora = operadoras.id_operadora
				WHERE forma_pagamento_operadora_empresa.fk_forma_pagamento = 22 AND operadora_empresa.fk_empresa = $codEmpresa ORDER BY transacao.id_transacao DESC";
		// echo $sql;
		$consulta = $this->banco->getConexaoBanco ()->query ( $sql );
		$lstBoletos = array ();
		while ( $linha = $consulta->fetch_array ( MYSQLI_ASSOC ) ) {
			array_push ( $lstBoletos, $linha );
		}
		$consulta->close ();
		return $lstBoletos;
	}
	
	
	
	//ESTA FUNÇÃO BUSCA AS TRANSAÇÕES PARA GERAÇÃO DO ARQUIVO REMESSA
	public function montarSQLFiltros($codEmpresa, $dataI, $dataF, $operadoras, $status, $codPedido, $valor, $codTransacao, $remessa) {
		$previous = false;
	
		$sql = "SELECT  transacao.*, operadoras.nome_operadora, operadoras.codigo_banco,
						forma_pagamento.*, operadora_empresa.*, empresa.NOME, empresa.CNPJ, movimentacao.*, arquivos.*
							FROM TRANSACAO
						LEFT OUTER JOIN forma_pagamento_operadora_empresa ON transacao.fk_forma_pagamento_operadora_empresa = forma_pagamento_operadora_empresa.id_forma_pagamento_operadora_empresa
						INNER JOIN operadora_empresa ON forma_pagamento_operadora_empresa.fk_operadora_empresa = operadora_empresa.id_operadora_empresa
						INNER JOIN operadoras ON operadora_empresa.fk_operadora = operadoras.id_operadora
						INNER JOIN empresa ON operadora_empresa.fk_empresa = empresa.CODIGO
						INNER JOIN forma_pagamento ON forma_pagamento_operadora_empresa.fk_forma_pagamento = forma_pagamento.id_forma_pagamento
	
						INNER JOIN movimentacao ON movimentacao.fk_transacao = transacao.id_transacao 
						LEFT JOIN arquivos ON movimentacao.fk_arquivo = arquivos.id_arquivo ";
// 						AND movimentacao.id_movimentacao = ( 
// 							SELECT movimentacao.id_movimentacao FROM movimentacao
// 								WHERE movimentacao.fk_transacao = transacao.id_transacao ORDER BY movimentacao.id_movimentacao DESC LIMIT 1 
// 							)  
						
	
	
		if ($operadoras [0] !== "") {
			$previous = true;
			$whereoperadoras = " WHERE (";
			for($i = 0; $i < count ( $operadoras ); $i ++) {
				if ($i == (count ( $operadoras ) - 1))
					$whereoperadoras .= "operadora_empresa.id_operadora_empresa = " . $operadoras [$i] . "";
				else
					$whereoperadoras .= "operadora_empresa.id_operadora_empresa = " . $operadoras [$i] . " or ";
			}
			$whereoperadoras .= ")";
			$sql .= $whereoperadoras;
		}
	
		if (!$remessa){
			if (isset ( $dataI )) {
				if ($previous)
					$sql .= " AND ";
				else
					$sql .= " WHERE ";
				$previous = true;
				$wheredataPeriodo = "(movimentacao.data_movimentacao = '" . $dataI . "' AND '" . $dataF . "')";
				$sql .= $wheredataPeriodo;
			}
		}else{
			if ($previous)
				$sql .= " AND ";
			else
				$sql .= " WHERE ";
			$previous = true;
			$wheredataPeriodo = "(movimentacao.fk_arquivo = " . $remessa["id_arquivo"] . ")";
			$sql .= $wheredataPeriodo;
		}
	
		if ($codPedido != "" && $codPedido != 0) {
			if ($previous)
				$sql .= " and ";
			else
				$sql .= " WHERE ";
			$previous = true;
			$wherePedido = "(transacao.codigo_origem = " . $codPedido . ")";
			$sql .= $wherePedido;
		}
		// echo "<br> $valor";
		if ($valor != "") {
			if ($previous)
				$sql .= " and ";
			else
				$sql .= " WHERE ";
			$previous = true;
			if ((strstr ( $valor, ">" )) or (strstr ( $valor, "<" )))
				$whereValor = "(transacao.valor_transacao " . $valor . ")";
			else
				$whereValor = "(transacao.valor_transacao = " . $valor . ")";
			$sql .= $whereValor;
		}
	
		if ($codTransacao != "" && $codTransacao != 0) {
			if ($previous)
				$sql .= " and ";
			else
				$sql .= " WHERE ";
			$previous = true;
			$whereValor = "(transacao.identificador = '" . $codTransacao . "')";
			$sql .= $whereValor;
		}
	
		if ($remessa == null)
			$sql .= " AND (movimentacao.fk_status IN (8, 11, 12))";
		else
			$sql .= " AND (movimentacao.fk_status IN (4, 8, 11, 12))";
		return $sql;
	}
	
	//ESTA FUNÇÃO BUSCA AS TRANSAÇÕES PARA GERAÇÃO DO ARQUIVO REMESSA
	public function getTransacoesBoletosFiltro($codEmpresa, $dataI, $dataF, $operadoras, $status, $codPedido, $valor, $codTransacao, $remessa) {
		$previous = false;
		
		$sql = "SELECT  transacao.*, operadoras.nome_operadora, operadoras.codigo_banco, 
						forma_pagamento.*, operadora_empresa.*, empresa.NOME, empresa.CNPJ, movimentacao.*, arquivos.*   
							FROM TRANSACAO 
						LEFT OUTER JOIN forma_pagamento_operadora_empresa ON transacao.fk_forma_pagamento_operadora_empresa = forma_pagamento_operadora_empresa.id_forma_pagamento_operadora_empresa
						INNER JOIN operadora_empresa ON forma_pagamento_operadora_empresa.fk_operadora_empresa = operadora_empresa.id_operadora_empresa
						INNER JOIN operadoras ON operadora_empresa.fk_operadora = operadoras.id_operadora 
						INNER JOIN empresa ON operadora_empresa.fk_empresa = empresa.CODIGO
						INNER JOIN forma_pagamento ON forma_pagamento_operadora_empresa.fk_forma_pagamento = forma_pagamento.id_forma_pagamento 
						
						INNER JOIN movimentacao ON movimentacao.fk_transacao = transacao.id_transacao 
						LEFT JOIN arquivos ON movimentacao.fk_arquivo = arquivos.id_arquivo ";
// 							AND movimentacao.id_movimentacao = ( 
// 							SELECT movimentacao.id_movimentacao FROM movimentacao
// 								WHERE movimentacao.fk_transacao = transacao.id_transacao ORDER BY movimentacao.id_movimentacao DESC LIMIT 1 
// 							)  ";
		
		
		if ($operadoras [0] !== "") {
			$previous = true;
			$whereoperadoras = " WHERE (";
			for($i = 0; $i < count ( $operadoras ); $i ++) {
				if ($i == (count ( $operadoras ) - 1))
					$whereoperadoras .= "operadora_empresa.id_operadora_empresa = " . $operadoras [$i] . "";
				else
					$whereoperadoras .= "operadora_empresa.id_operadora_empresa = " . $operadoras [$i] . " or ";
			}
			$whereoperadoras .= ")";
			$sql .= $whereoperadoras;
		}
		
		if (!$remessa){
			if (isset ( $dataI )) {
				if ($previous)
					$sql .= " AND ";
				else
					$sql .= " WHERE ";
				$previous = true;
				$wheredataPeriodo = "(movimentacao.data_movimentacao = '" . $dataI . "' AND '" . $dataF . "')";
				$sql .= $wheredataPeriodo;
			}
		}else{
			if ($previous)
				$sql .= " AND ";
			else
				$sql .= " WHERE ";
				$previous = true;
				$wheredataPeriodo = "(movimentacao.fk_arquivo = " . $remessa["id_arquivo"] . ")";
				$sql .= $wheredataPeriodo;
		}
		
		if ($codPedido != "" && $codPedido != 0) {
			if ($previous)
				$sql .= " and ";
			else
				$sql .= " WHERE ";
			$previous = true;
			$wherePedido = "(transacao.codigo_origem = " . $codPedido . ")";
			$sql .= $wherePedido;
		}
		// echo "<br> $valor";
		if ($valor != "") {
			if ($previous)
				$sql .= " and ";
			else
				$sql .= " WHERE ";
			$previous = true;
			if ((strstr ( $valor, ">" )) or (strstr ( $valor, "<" )))
				$whereValor = "(transacao.valor_transacao " . $valor . ")";
			else
				$whereValor = "(transacao.valor_transacao = " . $valor . ")";
			$sql .= $whereValor;
		}
		
		if ($codTransacao != "" && $codTransacao != 0) {
			if ($previous)
				$sql .= " and ";
			else
				$sql .= " WHERE ";
			$previous = true;
			$whereValor = "(transacao.identificador = '" . $codTransacao . "')";
			$sql .= $whereValor;
		}
		
		if ($remessa == null)
			$sql .= " AND (movimentacao.fk_status IN (8, 11, 12))";
		else 
			$sql .= " AND (movimentacao.fk_status IN (4, 8, 11, 12))";
// 		echo "$sql -- ";
		return $this->buscaTransacoesPersonalizada ( $sql, $_SESSION["dados_empresa"]["cod_empresa"] );
	}
	
	private function verificarSeJaExisteRemessa($dataI, $dataF, $banco, $empresa ){
		$sql = "SELECT arquivos.* FROM arquivos
					INNER JOIN operadora_empresa ON arquivos.fk_operadora_empresa = operadora_empresa.id_operadora_empresa
					INNER JOIN empresa ON operadora_empresa.fk_empresa = empresa.CODIGO
					WHERE (arquivos.data_banco BETWEEN '$dataI' AND '$dataF') AND empresa.CODIGO = $empresa";
// 		echo  "--- $sql ---";
		$consulta = $this->banco->getConexaoBanco ()->query ( $sql );
		$lstRemessas = array ();
		while ( $linha = $consulta->fetch_array ( MYSQLI_ASSOC ) ) {
			array_push ( $lstRemessas, $linha );
		}
		$consulta->close ();
		
		if (count($lstRemessas) == 1) return $lstRemessas[0];
		else return false;
	}
	
	
	public function remontarRemessaBradesco400($lstTransacoes) {
		/*
		 * Remessa: Registro 0 - Header Label
		 * Registro 1 - Transação
		 * Registro 2 - Mensagem (opcional)
		 * Registro 3 - Rateio de Crédito (opcional)
		 * Registro 7 – Pagador Avalista (opcional)
		 * Registro 9 - Trailler
		 */
		$nome = $lstTransacoes [0] ["nome_arquivo"];
		$fp = fopen ( $nome, "w" );
		
		$carteira = $lstTransacoes [0] ["codigo_carteira"];
		$agencia = $lstTransacoes [0] ["numero_agencia"];
		$conta = $lstTransacoes [0] ["numero_conta"];
		$dg_conta = $lstTransacoes [0] ["digito_conta"];
		$percentual_multa = number_format ( $lstTransacoes [0] ["percentual_atraso_bradesco"], 2 );
		
		$valor_desconto_dia = $this->removePontosVirgulas ( number_format ( $lstTransacoes [0] ["valor_desconto"], 2 ), 10 );
		$resp_emitir_boleto = $lstTransacoes [0] ["responsavel_emitir_boleto_bradesco"];
		$emitir_boleto_deb_aut = $lstTransacoes [0] ["emite_boleto_debito_automatico_bradesco"];
		$rateio = ($lstTransacoes [0] ["emite_boleto_debito_automatico_bradesco"] == "") ? $this->gerarEspacosBrancos ( 1 ) : $lstTransacoes [0] ["emite_boleto_debito_automatico_bradesco"];
		$emite_aviso_end_deb_aut = $this->zerosEsquerda ( $lstTransacoes [0] ["aviso_end_debito_automatico_bradesco"], 1 );
		$ocorrencia = $this->zerosEsquerda ( $lstTransacoes [0] ["identificacao_ocorrencia_bradesco"], 2 );
		
		$especie_titulo = $this->zerosEsquerda ( $lstTransacoes [0] ["especie_titulo"], 2 );
		$primeira_instrucao = $this->zerosEsquerda ( $lstTransacoes [0] ["primeira_instrucao_codificada"], 2 );
		$segunda_instrucao = $this->zerosEsquerda ( $lstTransacoes [0] ["segunda_instrucao_codificada"], 2 );
		
		$valor_mora = $this->removePontosVirgulas ( number_format ( 2.12, 2 ), 13 );
		
		$valor_iof = $this->zerosEsquerda ( 0, 13 );
		$valor_abatimento = $this->zerosEsquerda ( 0, 13 );
		$dataAtual = date ( 'dmy' );
		$reg0 = "" . "0" . // IDENTIFICAÇÃO DO REGISTRO - TAMANHO 1 / 001-001 DEFAULT '0'
"1" . // IDENTIFICAÇÃO DO ARQUIVO REMESSA - TAMANHO 1 / 002-002 DEFAULT '1'
"REMESSA" . // LITERAL REMESSA - TAMANHO 7 - 003-009 ALFA DEFAULT 'REMESSA'
"01" . // CODIGO DO SERVIÇO - TAMANAHO 2 / 010-011 NUM DEFAULT '01
$this->brancosDireita ( "COBRANCA", 15 ) . // LITERAL SERVIÇO - TAMANHO 15 / 012-026 ALFA DEFAULT 'COBRANCA'
$this->zerosEsquerda ( $lstTransacoes [0] ["codigo_empresa_bradesco"], 20 ) . // CODIGO EMPRESA - TAMANHO 20 / 027-046 NUM
$this->brancosDireita ( $lstTransacoes [0] ["NOME"], 30 ) . // NOME EMPRESA - TAMANHO 30 / 047-076 ALFA
$this->zerosEsquerda ( $lstTransacoes [0] ["codigo_banco"], 3 ) . // CÓDIGO DO BANCO NA COMPENSAÇÃO - TAMANHO 3 / 077-079 NUM
$this->brancosDireita ( "BRADESCO", 15 ) . // NOME DO BANCO NA COMPENSAÇÃO - TAMANHO 15 / 080-094 ALFA
$dataAtual . // DATA GRAVAÇÃO DO ARQUIBO - TAMANHO 6 / 095-100 NUM
$this->gerarEspacosBrancos ( 8 ) . // USO FEBRABAN - TAMANHO 8 / 101-108
"MX" . // IDENTIFICAÇÃO DO SISTEMA - TAMANHO 2 / 109-110 ALFA DEFAULT 'MX'
$this->zerosEsquerda ( $lstTransacoes [0] ["num_sequencial_remessa"], 7 ) . // NÚMERO SEQUENCIAL REMESSA - TAMANHO 7 / 111-117 NUM
$this->gerarEspacosBrancos ( 277 ) . // USO FEBRABAN - TAMANHO 277 / 118-394
"000001"; // NÚMERO SEQUENCIAL DE UM REGISTRO - TAMANHO 6 / 395-400 DEFAULT '000001'
		
		$regTransacao = "";
		$_i = 1;
		for($_i; $_i <= count ( $lstTransacoes ); $_i ++) {
			$nosso_numero = $this->zerosEsquerda ( $lstTransacoes [($_i - 1)] ["codigo_origem"], 11 );
			// $data = strtotime($transacao["DataRetornoCaptura"]);
			// echo date("d/m/Y G:i", $data);
			$data_vencimento = new DateTime ( $lstTransacoes [($_i - 1)] ["data_vencimento_boleto"] );
			// $data_vencimento->add(new DateInterval('P5D'));
			$valor = $lstTransacoes [($_i - 1)] ["valor_transacao"];
			$valor = $this->removePontosVirgulas ( $valor, 13 );
			
			$dias_desconto = 0;
			$data_limite_desconto = clone $data_vencimento;
			$data_limite_desconto->sub ( new DateInterval ( 'P' . $dias_desconto . 'D' ) );
			$valor_desconto_boleto = $this->removePontosVirgulas ( number_format ( $lstTransacoes [($_i - 1)] ["valor_desconto"], 2 ), 13 );
			$tipo_inscricao_pagador = $this->zerosEsquerda ( $lstTransacoes [($_i - 1)] ["tipo_inscricao_pagador"], 2 );
			$cpf_cnpj = $this->zerosEsquerda ( $lstTransacoes [($_i - 1)] ["inscricao_pagador"], 14 );
			$cep_pagador = explode ( "-", $lstTransacoes [($_i - 1)] ["cep_pagador"] );
			$prefixo_cep_pagador = str_replace ( ".", "", $cep_pagador [0] );
			$prefixo_cep_pagador = $this->zerosEsquerda ( $prefixo_cep_pagador, 5 );
			$sufixo_cep_pagador = $this->zerosEsquerda ( $cep_pagador [1], 3 );
			
			$regTransacao .= "1" . // IDENTIFICAÇÃO DO REGISTRO - TAMANHO 1 / 001-001 NUM DEFAULT '1'
$this->gerarEspacosZeros ( 19 )
								
							/*IDENTIFICAÇÃO EMPRESA BENEFICIÁRIA - TAMANHO 17 / 021-037 ALFA*/
					. "0" . // ZERO DEFAUL '0'
$this->zerosEsquerda ( $carteira, 3 ) . // CARTEIRA
$this->zerosEsquerda ( $agencia, 5 ) . // AGENCIA SEM DIGITO
$this->zerosEsquerda ( $conta, 7 ) . // CONTA CORRENTE
$this->zerosEsquerda ( $dg_conta, 1 ) . // DIGITO CONTA
			/* FIM IDENTIFICAÇÃO EMPRESA BENEFICIÁRIA - TAMANHO 17 / 021-037 ALFA */
			
			$this->zerosEsquerda ( $lstTransacoes [($_i - 1)] ["id_transacao"], 25 ) . // NÚM. CONTROLE PARTICIPANTE - TAMANHO 25 / 038-062 ALFA (será o código da tabela transação)
"000" . // CÓDIGO DO BANCO A SER DEBITADO NA COMPENSAÇÃO - TAMANHO 3 / 063-065 NUM
$this->zerosEsquerda ( $lstTransacoes [($_i - 1)] ["tem_multa_bradesco"], 1 ) . // CAMPO DE MULTA - TAMANHO 1 / 066-066 NUM (0->Sem multa; 2->Com multa)
$this->removePontosVirgulas ( $percentual_multa, 4 ) . // PERCENTUAL DE MULTA - TAMANHO 4 / 067-070 NUM
$this->zerosEsquerda ( $nosso_numero, 11 ) . // IDENTIFICAÇÃO DO TÍTULO NO BANCO - TAMANHO 11 / 071-081 NUM (NOSSO NÚMERO - CÓDIGO PEDIDO - VERIFICAR SE PODE SER CÓDIGO DO PEDIDOPAGAMENTO)
$this->calculoDigitoNossoNumero ( $carteira, $nosso_numero ) . // DIGITO AUTOCONFERENCIA NÚMERO BANCÁRIO - TAMANHO 1 / 082-082 ALFA
$valor_desconto_dia . // DESCONTO BONIFICAÇÃO POR DIA - TAMANHO 11 / 083-092 NUM
$resp_emitir_boleto . // CONDIÇÃO PARA EMISSAO DO PAPEL BOLETO - TAMANHO 1 / 093-093 NUM
$emitir_boleto_deb_aut . // IDENTIFICAÇÃO EMITE BOLETO PARA DÉBITO - TAMANHO 1 / 094-094 ALFA
$this->gerarEspacosBrancos ( 10 ) . // USO FEBRABAN - TAMANHO 10 / 095-104 BRANCOS
$rateio . // IDENTIFICAÇÃO RATEIO DE CRÉDITO (OPCIONAL) - TAMANHO 1 / 105-105 ALFA
$emite_aviso_end_deb_aut . // ENDEREÇ. AVISO DEB. AUTOM. EM C/C (OPCIONAL) - TAMANHO 1 / 106-106 NUM
$this->gerarEspacosBrancos ( 2 ) . // USO FEBRABAN - TAMANHO 2 / 107-108 BRANCOS
$ocorrencia . // IDENTIFICAÇÃO OCORRÊNCIA - TAMANHO 2 / 109-110 NUM

			"ABCDEFGHIJ" . // NÚMERO DO DOCUMENTO - TAMANHO 10 / 111-120 ALFA
$data_vencimento->format ( 'dmy' ) . // DATA VENCIMENTO TITULO - TAMANHO 6 / 121-126 NUM
$valor . // VALOR DO TÍTULO - TAMANHO 13 / 127-139 NUM
$this->gerarEspacosZeros ( 3 ) . // BANCO ENCARREGADO COBRANÇA - TAMANHO 3 / 140-142 NUM ZEROS
$this->gerarEspacosZeros ( 5 ) . // AGENCIA DEPOSITÁRIA - TAMANHO 5 / 143-147 NUM
$especie_titulo . // ESPÉCIE DE TITULO - TAMANHO 2 / 148-149 NUM
"N" . // IDENTIFICAÇÃO - TAMANHO 1 / 150-150 ALFA SEMPRE 'N'
date ( 'dmy' ) . // DATA EMISSÃO TITULO - TAMANHO 6 / 151-156 NUM
$primeira_instrucao . // PRIMEIRA INSTRUÇÃO - TAMANHO 2 / 157-158 NUM
$segunda_instrucao . // SEGUNDA INSTRUÇÃO - TAMANHO 2 / 159-160 NUM
$valor_mora . // VALOR ACRESC POR DIA DE ATRASO - TAMANHO 13 / 161-173 NUM
$data_limite_desconto->format ( 'dmy' ) . // DATA LIMITE PARA CONCESSÃO DESCONTO - TAMANHO 6 / 174-179 NUM
$valor_desconto_boleto . // VALOR DESCONTO - TAMANHO 13 / 180-192 NUM
$valor_iof . // VALOR IOF - TAMANHO 13 / 192-205 NUM
$valor_abatimento . // VALOR ABATIMENTO A SER CONCEDIDO/CANCELADO - TAMANHO 13 / 206-218 NUM
$tipo_inscricao_pagador . // IDENTIFICAÇÃO TIPO INSCRIÇÃO DO PAGADOR - TAMANHO 2 / 219-220 NUM
$cpf_cnpj . // NÚMERO INSCRIÇÃO DO PAGADOR - TAMANHO 14 / 221-234 NUM
$this->brancosDireita ( $lstTransacoes [($_i - 1)] ["nome_pagador"], 40 ) . // NOME PAGADOR - TAMANHO 40 / 235-274 ALFA
$this->gerarEspacosBrancos ( 40 ) . // ENDEREÇO COMPLETO - TAMANHO 40 / 275-314 ALFA
			                                    // MENSAGEM IMPRESSA NO BOLETO
			$this->brancosDireita ( $lstTransacoes [($_i - 1)] ["mensagem_boleto"], 12 ) . // PRIMEIRA MENSAGEM - TAMANHO 12 / 315-326 ALFA
			                                                                               // FIM MENSAGEM BOLETO
			$prefixo_cep_pagador . // CEP PAGADOR - TAMANHO 5 / 327-331 NUM
$sufixo_cep_pagador . // SUFIXO CEP - TAMANHO 3 / 332-334 NUM
$this->gerarEspacosBrancos ( 60 ) . // SACADOR/AVALISTA OU SEGUNDA MENSAGEM - TAMANHO 60 / 335-394 ALFA
$this->zerosEsquerda ( ($_i + 1), 6 ) . // NÚMERO SEQUENCIAL DO REGISTRO - TAMANHO 6 / 395-400 NUM
"\r\n";
		}
		
		$reg9 = "" . "9" . // IDENTIFICAÇÃO DO REGISTRO - TAMANHO 1 / 001-001 NUM DEFAULT '9'
$this->gerarEspacosBrancos ( 393 ) . // USO FEBRABAN - TAMANHO 393 / 002-394 BRANCOS
$this->zerosEsquerda ( ($_i + 1), 6 ); // NÚMERO SEQUENCIAL DO REGISTRO - TAMANHO 6 / 395-400 NUM
		
		$conteudo = $reg0 . "\r\n" . $regTransacao . $reg9;
		$escreve = fwrite ( $fp, $conteudo );
		// Fecha o arquivo
		fclose ( $fp );
		
		$this->updateSequencial ( $lstTransacoes [0] ["num_sequencial_remessa"], $lstTransacoes [0] ["id_operadora_empresa"] );
		$this->updateTransacaoRemessa ( $lstTransacoes, $dataAtual, $nome, $lstTransacoes [0] ["num_sequencial_remessa"] );
	}
	public function gerarRemessaBradescos400($lstTransacoes) {
		$sequencial = $this->getSequencial ( $lstTransacoes [0] ["id_operadora_empresa"] );
		// echo $sequencial["num_sequencial_remessa"];
		/*
		 * foreach ( $lstTransacoes as $chave => $value ) {
		 * if ($lstTransacoes [$chave] ["sequencial_remessa"] == "" || $lstTransacoes [$chave] ["sequencial_remessa"] == null) {
		 * $lstTransacoes [$chave] ["num_sequencial_remessa"] = $sequencial ["num_sequencial_remessa"];
		 * } else {
		 * return "3";
		 * break;
		 * }
		 * }
		 */
		// print_r($lstTransacoes);
		
		/*
		 * Remessa: Registro 0 - Header Label
		 * Registro 1 - Transação
		 * Registro 2 - Mensagem (opcional)
		 * Registro 3 - Rateio de Crédito (opcional)
		 * Registro 7 – Pagador Avalista (opcional)
		 * Registro 9 - Trailler
		 */
		$dia = date ( 'd' );
		$mes = date ( 'm' );
		$seq = $this->zerosEsquerda ( dechex ( $lstTransacoes [0] ["num_sequencial_remessa"] ), 2 );
		$nome = "CB$dia$mes$seq.REM";
		$fp = fopen ( $nome, "w" );
		
		$carteira = $lstTransacoes [0] ["codigo_carteira"];
		$agencia = $lstTransacoes [0] ["numero_agencia"];
		$conta = $lstTransacoes [0] ["numero_conta"];
		$dg_conta = $lstTransacoes [0] ["digito_conta"];
		$percentual_multa = number_format ( $lstTransacoes [0] ["percentual_atraso_bradesco"], 2 );
		
		$valor_desconto_dia = $this->removePontosVirgulas ( number_format ( $lstTransacoes [0] ["valor_desconto"], 2 ), 10 );
		$resp_emitir_boleto = $lstTransacoes [0] ["responsavel_emitir_boleto_bradesco"];
		$emitir_boleto_deb_aut = $lstTransacoes [0] ["emite_boleto_debito_automatico_bradesco"];
		$rateio = ($lstTransacoes [0] ["emite_boleto_debito_automatico_bradesco"] == "") ? $this->gerarEspacosBrancos ( 1 ) : $lstTransacoes [0] ["emite_boleto_debito_automatico_bradesco"];
		$emite_aviso_end_deb_aut = $this->zerosEsquerda ( $lstTransacoes [0] ["aviso_end_debito_automatico_bradesco"], 1 );
		$ocorrencia = $this->zerosEsquerda ( $lstTransacoes [0] ["identificacao_ocorrencia_bradesco"], 2 );
		
		$especie_titulo = $this->zerosEsquerda ( $lstTransacoes [0] ["especie_titulo"], 2 );
		$primeira_instrucao = $this->zerosEsquerda ( $lstTransacoes [0] ["primeira_instrucao_codificada"], 2 );
		$segunda_instrucao = $this->zerosEsquerda ( $lstTransacoes [0] ["segunda_instrucao_codificada"], 2 );
		
		$valor_mora = $this->removePontosVirgulas ( number_format ( 2.12, 2 ), 13 );
		
		$valor_iof = $this->zerosEsquerda ( 0, 13 );
		$valor_abatimento = $this->zerosEsquerda ( 0, 13 );
		$dataAtual = date ( 'dmy' );
		$reg0 = "" . "0" . // IDENTIFICAÇÃO DO REGISTRO - TAMANHO 1 / 001-001 DEFAULT '0'
"1" . // IDENTIFICAÇÃO DO ARQUIVO REMESSA - TAMANHO 1 / 002-002 DEFAULT '1'
"REMESSA" . // LITERAL REMESSA - TAMANHO 7 - 003-009 ALFA DEFAULT 'REMESSA'
"01" . // CODIGO DO SERVIÇO - TAMANAHO 2 / 010-011 NUM DEFAULT '01
$this->brancosDireita ( "COBRANCA", 15 ) . // LITERAL SERVIÇO - TAMANHO 15 / 012-026 ALFA DEFAULT 'COBRANCA'
$this->zerosEsquerda ( $lstTransacoes [0] ["codigo_empresa_bradesco"], 20 ) . // CODIGO EMPRESA - TAMANHO 20 / 027-046 NUM
$this->brancosDireita ( $lstTransacoes [0] ["NOME"], 30 ) . // NOME EMPRESA - TAMANHO 30 / 047-076 ALFA
$this->zerosEsquerda ( $lstTransacoes [0] ["codigo_banco"], 3 ) . // CÓDIGO DO BANCO NA COMPENSAÇÃO - TAMANHO 3 / 077-079 NUM
$this->brancosDireita ( "BRADESCO", 15 ) . // NOME DO BANCO NA COMPENSAÇÃO - TAMANHO 15 / 080-094 ALFA
$dataAtual . // DATA GRAVAÇÃO DO ARQUIBO - TAMANHO 6 / 095-100 NUM
$this->gerarEspacosBrancos ( 8 ) . // USO FEBRABAN - TAMANHO 8 / 101-108
"MX" . // IDENTIFICAÇÃO DO SISTEMA - TAMANHO 2 / 109-110 ALFA DEFAULT 'MX'
$this->zerosEsquerda ( $lstTransacoes [0] ["num_sequencial_remessa"], 7 ) . // NÚMERO SEQUENCIAL REMESSA - TAMANHO 7 / 111-117 NUM
$this->gerarEspacosBrancos ( 277 ) . // USO FEBRABAN - TAMANHO 277 / 118-394
"000001"; // NÚMERO SEQUENCIAL DE UM REGISTRO - TAMANHO 6 / 395-400 DEFAULT '000001'
		
		$regTransacao = "";
		$_i = 1;
		for($_i; $_i <= count ( $lstTransacoes ); $_i ++) {
			$nosso_numero = $this->zerosEsquerda ( $lstTransacoes [($_i - 1)] ["codigo_origem"], 11 );
			// $data = strtotime($transacao["DataRetornoCaptura"]);
			// echo date("d/m/Y G:i", $data);
			$data_vencimento = new DateTime ( $lstTransacoes [($_i - 1)] ["data_vencimento_boleto"] );
			// $data_vencimento->add(new DateInterval('P5D'));
			$valor = $lstTransacoes [($_i - 1)] ["valor_transacao"];
			$valor = $this->removePontosVirgulas ( $valor, 13 );
			
			$dias_desconto = 0;
			$data_limite_desconto = clone $data_vencimento;
			$data_limite_desconto->sub ( new DateInterval ( 'P' . $dias_desconto . 'D' ) );
			$valor_desconto_boleto = $this->removePontosVirgulas ( number_format ( $lstTransacoes [($_i - 1)] ["valor_desconto"], 2 ), 13 );
			$tipo_inscricao_pagador = $this->zerosEsquerda ( $lstTransacoes [($_i - 1)] ["tipo_inscricao_pagador"], 2 );
			$cpf_cnpj = $this->zerosEsquerda ( $lstTransacoes [($_i - 1)] ["inscricao_pagador"], 14 );
			$cep_pagador = explode ( "-", $lstTransacoes [($_i - 1)] ["cep_pagador"] );
			$prefixo_cep_pagador = str_replace ( ".", "", $cep_pagador [0] );
			$prefixo_cep_pagador = $this->zerosEsquerda ( $prefixo_cep_pagador, 5 );
			$sufixo_cep_pagador = $this->zerosEsquerda ( $cep_pagador [1], 3 );
			$regTransacao .= "1" . // IDENTIFICAÇÃO DO REGISTRO - TAMANHO 1 / 001-001 NUM DEFAULT '1'
$this->gerarEspacosZeros ( 19 )
																	
			/*IDENTIFICAÇÃO EMPRESA BENEFICIÁRIA - TAMANHO 17 / 021-037 ALFA*/
			. "0" . // ZERO DEFAUL '0'
$this->zerosEsquerda ( $carteira, 3 ) . // CARTEIRA
$this->zerosEsquerda ( $agencia, 5 ) . // AGENCIA SEM DIGITO
$this->zerosEsquerda ( $conta, 7 ) . // CONTA CORRENTE
$this->zerosEsquerda ( $dg_conta, 1 ) . // DIGITO CONTA
			/* FIM IDENTIFICAÇÃO EMPRESA BENEFICIÁRIA - TAMANHO 17 / 021-037 ALFA */
			
			$this->zerosEsquerda ( $lstTransacoes [($_i - 1)] ["id_transacao"], 25 ) . // NÚM. CONTROLE PARTICIPANTE - TAMANHO 25 / 038-062 ALFA (será o código da tabela transação)
"000" . // CÓDIGO DO BANCO A SER DEBITADO NA COMPENSAÇÃO - TAMANHO 3 / 063-065 NUM
$this->zerosEsquerda ( $lstTransacoes [($_i - 1)] ["tem_multa_bradesco"], 1 ) . // CAMPO DE MULTA - TAMANHO 1 / 066-066 NUM (0->Sem multa; 2->Com multa)
$this->removePontosVirgulas ( $percentual_multa, 4 ) . // PERCENTUAL DE MULTA - TAMANHO 4 / 067-070 NUM
$this->zerosEsquerda ( $nosso_numero, 11 ) . // IDENTIFICAÇÃO DO TÍTULO NO BANCO - TAMANHO 11 / 071-081 NUM (NOSSO NÚMERO - CÓDIGO PEDIDO - VERIFICAR SE PODE SER CÓDIGO DO PEDIDOPAGAMENTO)
$this->calculoDigitoNossoNumero ( $carteira, $nosso_numero ) . // DIGITO AUTOCONFERENCIA NÚMERO BANCÁRIO - TAMANHO 1 / 082-082 ALFA
$valor_desconto_dia . // DESCONTO BONIFICAÇÃO POR DIA - TAMANHO 11 / 083-092 NUM
$resp_emitir_boleto . // CONDIÇÃO PARA EMISSAO DO PAPEL BOLETO - TAMANHO 1 / 093-093 NUM
$emitir_boleto_deb_aut . // IDENTIFICAÇÃO EMITE BOLETO PARA DÉBITO - TAMANHO 1 / 094-094 ALFA
$this->gerarEspacosBrancos ( 10 ) . // USO FEBRABAN - TAMANHO 10 / 095-104 BRANCOS
$rateio . // IDENTIFICAÇÃO RATEIO DE CRÉDITO (OPCIONAL) - TAMANHO 1 / 105-105 ALFA
$emite_aviso_end_deb_aut . // ENDEREÇ. AVISO DEB. AUTOM. EM C/C (OPCIONAL) - TAMANHO 1 / 106-106 NUM
$this->gerarEspacosBrancos ( 2 ) . // USO FEBRABAN - TAMANHO 2 / 107-108 BRANCOS
$ocorrencia . // IDENTIFICAÇÃO OCORRÊNCIA - TAMANHO 2 / 109-110 NUM

			"ABCDEFGHIJ" . // NÚMERO DO DOCUMENTO - TAMANHO 10 / 111-120 ALFA
$data_vencimento->format ( 'dmy' ) . // DATA VENCIMENTO TITULO - TAMANHO 6 / 121-126 NUM
$valor . // VALOR DO TÍTULO - TAMANHO 13 / 127-139 NUM
$this->gerarEspacosZeros ( 3 ) . // BANCO ENCARREGADO COBRANÇA - TAMANHO 3 / 140-142 NUM ZEROS
$this->gerarEspacosZeros ( 5 ) . // AGENCIA DEPOSITÁRIA - TAMANHO 5 / 143-147 NUM
$especie_titulo . // ESPÉCIE DE TITULO - TAMANHO 2 / 148-149 NUM
"N" . // IDENTIFICAÇÃO - TAMANHO 1 / 150-150 ALFA SEMPRE 'N'
date ( 'dmy' ) . // DATA EMISSÃO TITULO - TAMANHO 6 / 151-156 NUM
$primeira_instrucao . // PRIMEIRA INSTRUÇÃO - TAMANHO 2 / 157-158 NUM
$segunda_instrucao . // SEGUNDA INSTRUÇÃO - TAMANHO 2 / 159-160 NUM
$valor_mora . // VALOR ACRESC POR DIA DE ATRASO - TAMANHO 13 / 161-173 NUM
$data_limite_desconto->format ( 'dmy' ) . // DATA LIMITE PARA CONCESSÃO DESCONTO - TAMANHO 6 / 174-179 NUM
$valor_desconto_boleto . // VALOR DESCONTO - TAMANHO 13 / 180-192 NUM
$valor_iof . // VALOR IOF - TAMANHO 13 / 192-205 NUM
$valor_abatimento . // VALOR ABATIMENTO A SER CONCEDIDO/CANCELADO - TAMANHO 13 / 206-218 NUM
$tipo_inscricao_pagador . // IDENTIFICAÇÃO TIPO INSCRIÇÃO DO PAGADOR - TAMANHO 2 / 219-220 NUM
$cpf_cnpj . // NÚMERO INSCRIÇÃO DO PAGADOR - TAMANHO 14 / 221-234 NUM
$this->brancosDireita ( $lstTransacoes [($_i - 1)] ["nome_pagador"], 40 ) . // NOME PAGADOR - TAMANHO 40 / 235-274 ALFA
$this->gerarEspacosBrancos ( 40 ) . // ENDEREÇO COMPLETO - TAMANHO 40 / 275-314 ALFA
			                                    // MENSAGEM IMPRESSA NO BOLETO
			$this->brancosDireita ( $lstTransacoes [($_i - 1)] ["mensagem_boleto"], 12 ) . // PRIMEIRA MENSAGEM - TAMANHO 12 / 315-326 ALFA
			                                                                               // FIM MENSAGEM BOLETO
			$prefixo_cep_pagador . // CEP PAGADOR - TAMANHO 5 / 327-331 NUM
$sufixo_cep_pagador . // SUFIXO CEP - TAMANHO 3 / 332-334 NUM
$this->gerarEspacosBrancos ( 60 ) . // SACADOR/AVALISTA OU SEGUNDA MENSAGEM - TAMANHO 60 / 335-394 ALFA
$this->zerosEsquerda ( ($_i + 1), 6 ) . // NÚMERO SEQUENCIAL DO REGISTRO - TAMANHO 6 / 395-400 NUM
"\r\n";
		}
		
		$reg9 = "" . "9" . // IDENTIFICAÇÃO DO REGISTRO - TAMANHO 1 / 001-001 NUM DEFAULT '9'
$this->gerarEspacosBrancos ( 393 ) . // USO FEBRABAN - TAMANHO 393 / 002-394 BRANCOS
$this->zerosEsquerda ( ($_i + 1), 6 ); // NÚMERO SEQUENCIAL DO REGISTRO - TAMANHO 6 / 395-400 NUM
		
		$conteudo = $reg0 . "\r\n" . $regTransacao . $reg9;
		$escreve = fwrite ( $fp, $conteudo );
		// Fecha o arquivo
		fclose ( $fp );
		
		$this->updateSequencial ( $lstTransacoes [0] ["num_sequencial_remessa"], $lstTransacoes [0] ["id_operadora_empresa"] );
		$this->updateTransacaoRemessa ( $lstTransacoes, $dataAtual, $nome, $lstTransacoes [0] ["num_sequencial_remessa"] );
	}
	
	
	public function gerarRemessa($dataI, $dataF, $banco, $empresa) {
		$remessa = $this->verificarSeJaExisteRemessa($dataI, $dataF, $banco, $empresa);
		$sql = $this->montarSQLFiltros($empresa, $dataI, $dataF, $banco, "", "", "", "", $remessa);

		$lstTransacoes = $this->executarSQLFiltro ( $sql, $empresa);
		$retorno = array ();
		if ($remessa != null){
			$lstTransacoesCanceladas = array();
				
			//CRIAR ARRAY COM CANCELADOS
			foreach ( $lstTransacoes as $chave => $value ) {
				if ($lstTransacoes[$chave]["fk_status"] == 4){
					array_push($lstTransacoesCanceladas, $lstTransacoes[$chave]);
				}
			}
				
			//REMOVER DO ARRAY PRINCIPAL OS CANCELADOS
			foreach ($lstTransacoesCanceladas as $chave => $valor){
				foreach ($lstTransacoes as $key => $value){
					if ($lstTransacoes[$key]["id_transacao"] == $lstTransacoesCanceladas[$chave]["id_transacao"]){
						unset($lstTransacoes[$key]);
					}
				}
			}
			$lstTransacoes = array_values($lstTransacoes);
				
			if (count ( $lstTransacoes ) > 0) {
				//REMONTA REMESSA
				$this->remontarRemessa ( $lstTransacoes, $empresa, $remessa, $lstTransacoes [0] ["fk_operadora"]);
				array_push ( $retorno, 3 );
				array_push ( $retorno, $remessa ["nome_arquivo"] );
				array_push ( $retorno, $empresa );
				return $retorno;
				break;
			}else return null;
		}else{
			if (count ( $lstTransacoes ) > 0) {
				//remover transações que iriam para a remessa mas foram canceladas antes de gerar o arquivo
				foreach ( $lstTransacoes as $chave => $value ) {
					$sql = "SELECT movimentacao.* FROM movimentacao
								WHERE movimentacao.fk_transacao = " . $lstTransacoes[$chave]["id_transacao"] . " AND movimentacao.fk_status = 4";
						
					$consulta = $this->banco->getConexaoBanco ()->query ( $sql );
					if ($consulta->num_rows){
						unset($lstTransacoes[$chave]);
					}
				}
				$lstTransacoes = array_values($lstTransacoes);
	
				$sequencial = $this->getSequencial ( $lstTransacoes [0] ["id_operadora_empresa"] )["num_sequencial_remessa"];
	
				$nomeArquivo = $this->montarRemessa( $lstTransacoes, $empresa, $sequencial, $dataI, $lstTransacoes [0] ["fk_operadora"] );
				array_push ( $retorno, 1 );
				array_push ( $retorno, $nomeArquivo );
				array_push ( $retorno, $empresa );
				return $retorno;
				break;
			}else return null;
		}
	}
	
	
	public function montarConteudoRemessa($lstTransacoes, $dataAtual, $empresa, $sequencialR, $idOperadora ){
		//ELIMINA OS ARQUIVO SALVOS NA APLICAÇÃO QUE FORAM CRIADOS A MAIS DE UM MINUTO
		foreach (glob($empresa."/*.REM") as $file) {
			$modificado = new DateTime(date("Y-m-d\TH:i:s", filemtime($file)));
			$atual = new DateTime();
			$diff = $atual->diff($modificado);
			$minutos = $diff->format("%i");
			if ($minutos > 1){
				unlink($file);
			}
		}
		
		
		$carteira = $lstTransacoes [0] ["codigo_carteira"];
		$agencia = $lstTransacoes [0] ["numero_agencia"];
		$dg_agencia = $lstTransacoes [0] ["digito_agencia"];
		$conta = $lstTransacoes [0] ["numero_conta"];
		$dg_conta = $lstTransacoes [0] ["digito_conta"];
		$percentual_multa = number_format ( $lstTransacoes [0] ["multa"], 2 );
		
		$valor_desconto_dia = $this->removePontosVirgulas ( number_format ( $lstTransacoes [0] ["valor_desconto"], 2 ), 10 );
		$resp_emitir_boleto = $lstTransacoes [0] ["responsavel_emitir_boleto_bradesco"];
		$emitir_boleto_deb_aut = $lstTransacoes [0] ["emite_boleto_debito_automatico_bradesco"];
		$rateio = ($lstTransacoes [0] ["emite_boleto_debito_automatico_bradesco"] == "") ? $this->gerarEspacosBrancos ( 1 ) : $lstTransacoes [0] ["emite_boleto_debito_automatico_bradesco"];
		$emite_aviso_end_deb_aut = $this->zerosEsquerda ( $lstTransacoes [0] ["aviso_end_debito_automatico_bradesco"], 1 );
		$ocorrencia = $this->zerosEsquerda ( $lstTransacoes [0] ["identificacao_ocorrencia_bradesco"], 2 );
		
		$especie_titulo = $this->zerosEsquerda ( $lstTransacoes [0] ["especie_titulo"], 2 );
		$primeira_instrucao = $this->zerosEsquerda ( $lstTransacoes [0] ["primeira_instrucao_codificada"], 2 );
		$segunda_instrucao = $this->zerosEsquerda ( $lstTransacoes [0] ["segunda_instrucao_codificada"], 2 );
		
		$valor_mora = $this->removePontosVirgulas ( number_format ( $lstTransacoes [0] ["valor_mora"], 2 ), 13 );
		
		$valor_iof = $this->zerosEsquerda ( 0, 13 );
		$valor_abatimento = $this->zerosEsquerda ( 0, 13 );
		
		$reg0 = "0" . // IDENTIFICAÇÃO DO REGISTRO - TAMANHO 1 / 001-001 DEFAULT '0'
				"1" . // IDENTIFICAÇÃO DO ARQUIVO REMESSA - TAMANHO 1 / 002-002 DEFAULT '1'
				$this->brancosDireita ( "REMESSA", 7 ) . // LITERAL REMESSA - TAMANHO 7 - 003-009 ALFA DEFAULT 'REMESSA' - PARA ARQUIVOS DE TESTE ENVIAR 'TESTE' NESTE CAMPO
				"01" . // CODIGO DO SERVIÇO - TAMANAHO 2 / 010-011 NUM DEFAULT '01
				$this->brancosDireita ( "COBRANCA", 8 ) . // LITERAL SERVIÇO - TAMANHO 8 / 012-019 ALFA DEFAULT 'COBRANCA'
				$this->gerarEspacosBrancos ( 7 ) . // brancos - TAMANHO 7 / 020-026 NUM
				$this->zerosEsquerda ( $agencia, 4 ) . // agencia s/ digito - TAMANHO 4 / 027-030 ALFA
				$this->zerosEsquerda ( $dg_agencia, 1 ) . // digito agencia - TAMANHO 1 / 031-031 ALFA
				$this->zerosEsquerda ( $conta, 8 ) . // conta corrente - TAMANHO 8 / 032-039 ALFA
				$this->zerosEsquerda ( $dg_conta, 1 ) . // conta corrente - TAMANHO 1 / 040-040 ALFA
				"000000" . // complemento do registro - TAMANHO 6 / 041-046
				$this->brancosDireita ( $this->replaceCharAscensionToUpper($lstTransacoes [0] ["NOME"]), 30 ) . // NOME EMPRESA - TAMANHO 30 / 047-076 ALFA
				$this->brancosDireita ( "001BANCO DO BRASIL", 18 ) . // 001BANCODOBRASIL - TAMANHO 18 / 077-094 ALFA
				$dataAtual . // DATA GRAVAÇÃO DO ARQUIBO - TAMANHO 6 / 095-100 NUM
				$this->zerosEsquerda ( $sequencialR, 7 ) . // NÚMERO SEQUENCIAL REMESSA - TAMANHO 7 / 101-107 NUM
				$this->gerarEspacosBrancos ( 22 ) . // USO FEBRABAN - TAMANHO 22 / 108-129
				$this->zerosEsquerda ( $lstTransacoes [0] ["num_convenio_lider_banco_brasil"], 7 ) . // Número do Convênio Líder - TAMANHO 7 / 130-136 ALFA
				$this->gerarEspacosBrancos ( 258 ) . // USO FEBRABAN - TAMANHO 277 / 137-394
				"000001"; // NÚMERO SEQUENCIAL DE UM REGISTRO - TAMANHO 6 / 395-400 DEFAULT '000001'
		
				$regTransacao = "";
				$_i = 1;
				$_seq = 1;
		for($_i; $_i <= count ( $lstTransacoes ); $_i ++) {
			$nosso_numero = $this->zerosEsquerda ( $lstTransacoes [($_i - 1)] ["codigo_origem"], 11 );
			// $data = strtotime($transacao["DataRetornoCaptura"]);
			// echo date("d/m/Y G:i", $data);
			$dataTitulo = new DateTime ( $lstTransacoes [($_i - 1)] ["data_criacao_origem"] );
			$data_vencimento = new DateTime ( $lstTransacoes [($_i - 1)] ["data_vencimento_boleto"] );
			// $data_vencimento->add(new DateInterval('P5D'));
			$valor = $lstTransacoes [($_i - 1)] ["valor_transacao"];
			$valor = number_format($valor, 2, '.', ',');
			$valor = $this->removePontosVirgulas ( $valor, 13 );

			$dias_desconto = 0;
			$data_limite_desconto = clone $data_vencimento;
			$data_limite_desconto->sub ( new DateInterval ( 'P' . $dias_desconto . 'D' ) );
			$valor_desconto_boleto = $this->removePontosVirgulas ( number_format ( $lstTransacoes [($_i - 1)] ["valor_desconto"], 2 ), 13 );
			$tipo_inscricao_pagador = $this->zerosEsquerda ( $lstTransacoes [($_i - 1)] ["tipo_inscricao_pagador"], 2 );
			$cpf_cnpj_pagador = $this->zerosEsquerda ( $lstTransacoes [($_i - 1)] ["inscricao_pagador"], 14 );
			$tipo_inscricao_beneficiario = "02";
			$cpf_cnpj_beneficiario = $this->zerosEsquerda ( $lstTransacoes [($_i - 1)] ["CNPJ"], 14 );
			$cep_pagador = explode ( "-", $lstTransacoes [($_i - 1)] ["cep_pagador"] );
			$prefixo_cep_pagador = str_replace ( ".", "", $cep_pagador [0] );
			$prefixo_cep_pagador = $this->zerosEsquerda ( $prefixo_cep_pagador, 5 );
			$sufixo_cep_pagador = $this->zerosEsquerda ( $cep_pagador [1], 3 );
			$variacaoCarteira = str_replace ( "-", "", $lstTransacoes [($_i - 1)] ["codigo_variacao_carteira_banco_brasil"] );
				
				
			$regTransacao .=
				/*IDENTIFICAÇÃO EMPRESA BENEFICIÁRIA - TAMANHO 17 / 021-037 ALFA*/
				"7" . // IDENTIFICAÇÃO DO REGISTRO - TAMANHO 1 / 001-001 NUM DEFAULT '7'
				$tipo_inscricao_beneficiario . // IDENTIFICAÇÃO TIPO INSCRIÇÃO DO BENEFICIARIO - TAMANHO 2 / 002-003 NUM
				$cpf_cnpj_beneficiario . // NÚMERO INSCRIÇÃO DO BENEFICIARIO - TAMANHO 14 / 004-017 NUM
				$this->zerosEsquerda ( $agencia, 4 ) . // agencia s/ digito - TAMANHO 4 / 018-021 ALFA
				$this->zerosEsquerda ( $dg_agencia, 1 ) . // digito agencia - TAMANHO 1 / 022-022 ALFA
				$this->zerosEsquerda ( $conta, 8 ) . // conta corrente - TAMANHO 8 / 023-030 ALFA
				$this->zerosEsquerda ( $dg_conta, 1 ) . // conta corrente - TAMANHO 1 / 031-031 ALFA
				$this->zerosEsquerda ( $lstTransacoes [0] ["num_convenio_banco_brasil"], 7 ) . // Número do Convênio - TAMANHO 7 / 032-038 ALFA
				// $this->gerarEspacosZeros ( 19 )
				/* FIM IDENTIFICAÇÃO EMPRESA BENEFICIÁRIA - TAMANHO 17 / 021-037 ALFA */
					
				$this->zerosEsquerda ( $lstTransacoes [($_i - 1)] ["id_transacao"], 25 ) . // CÓDIGO DE CONTROLE DA EMPRESA - TAMANHO 25 / 039-063 ALFA (será o código da tabela transação)
	
				// NOSSO NÚMERO
				$this->zerosEsquerda ( $lstTransacoes [($_i - 1)] ["num_convenio_banco_brasil"], 7 ) . // Número convênio - TAMANHO 7 / 064-070
				$this->zerosEsquerda ( $lstTransacoes [($_i - 1)] ["fk_origem"] , 1 ) .
				$this->zerosEsquerda ( $lstTransacoes [($_i - 1)] ["codigo_pagamento"], 9 ) . // IDENTIFICAÇÃO DO TÍTULO NO BANCO - TAMANHO 9 / 071-080 NUM (NOSSO NÚMERO)
				// FIM NOSSO NÚMERO
					
				$this->gerarEspacosZeros ( 2 ) . // número da prestação - TAMANHO 2 / 081-082 NUM
				$this->gerarEspacosZeros ( 2 ) . // grupo de valor - TAMANHO 2 / 083-084 NUM
				$this->gerarEspacosBrancos ( 2 ); // USO FEBRABAN - TAMANHO 3 / 085-086 BRANCOS
					
				switch ($lstTransacoes[($_i - 1)] ["fk_status"]){
					case 8: //REGISTRAR
						$regTransacao .= "00"; //ausencia de instruções
						break;
					case 11: //CANCELAR
						$regTransacao .= "44";//BAIXA
						break;
					case 12: //PROTESTAR
						$regTransacao .= "00";//protestar em 5 dias úteis
						break;
				}
				// 					$this->gerarEspacosBrancos ( 1 ) . // Indicativo de Mensagem ou Sacador/Avalista - TAMANHO 1 / 088-088 branco ( Poderá ser informada nas posições 352 a 391 qualquer mensagem para ser impressa no boleto.)
					
			$regTransacao .= $this->gerarEspacosBrancos ( 3 ) . // Indicativo de Mensagem ou Sacador/Avalista - TAMANHO 3 / 089-091 branco
				$this->zerosEsquerda ( $variacaoCarteira, 3 ) . // Variação da carteira - tamanho 3 / 092-094
				$this->gerarEspacosZeros ( 1 ) . // conta caução - tamanho 1 / 095-095 "0"
				$this->gerarEspacosZeros ( 6 ); // número borderô - tamanho 6 / 096-101
			if ($lstTransacoes[($_i - 1)]["tipo_cobranca_banco_brasil"] != null)
				$regTransacao .= $this->zerosEsquerda ( $lstTransacoes [($_i - 1)] ["tipo_cobranca_banco_brasil"], 5 );
			else
				$regTransacao .= $this->gerarEspacosBrancos(5);
				// 				($lstTransacoes[($_i - 1)]["tipo_cobranca_banco_brasil"] == null)?($this->zerosEsquerda ( $lstTransacoes [($_i - 1)] ["tipo_cobranca_banco_brasil"], 5 )):$this->gerarEspacosBrancos(5) . // Tipo de Cobrança - tamanho 5 / 102-106
				$regTransacao .= $this->zerosEsquerda ( $lstTransacoes [($_i - 1)] ["codigo_carteira"], 2 ) ; // Carteira - tamanho 2 / 107-108
				switch ($lstTransacoes[($_i - 1)] ["fk_status"]){
					case 8: //REGISTRAR
						$regTransacao .= "01";
						break;
					case 11: //CANCELAR
						$regTransacao .= "02";
						break;
					case 12: //PROTESTAR
						$regTransacao .= "09";
						break;
				}
				// 				"01" . // COMANDO VIDE MANUAL - tamanho 2 / 109-110
			$regTransacao .= $this->zerosEsquerda ( $lstTransacoes [($_i - 1)] ["fk_origem"] , 1 ) .
				$this->zerosEsquerda ( $lstTransacoes [($_i - 1)] ["codigo_origem"], 9 ) . // Seu Número/Número do Título Atribuído pelo Cedente - TAMANHO 10 / 111-120 NUM
				$data_vencimento->format ( 'dmy' ) . // DATA VENCIMENTO TITULO - TAMANHO 6 / 121-126 NUM
					
				$valor . // VALOR DO TÍTULO - TAMANHO 13 / 127-139 NUM
				$this->zerosEsquerda ( $lstTransacoes [0] ["codigo_banco"], 3 ) . // CÓDIGO DO BANCO NA COMPENSAÇÃO - TAMANHO 3 / 140-142 NUM
					
				$this->gerarEspacosZeros ( 4 ) . // prefixo agencia cobradora - TAMANHO 4 / 143-146 NUM
				$this->gerarEspacosBrancos ( 1 ) . // digito agencia cobradora - TAMANHO 1 / 147-147
				$especie_titulo . // ESPECIE titulo - tamanho 2 / 148-149
				$this->brancosDireita ( $lstTransacoes [($_i - 1)] ["aceite_boleto"], 1 ) . // aceito do título - tamanho 1 / 150-150
				$dataTitulo->format ( 'dmy' ) . // data do titulo - tamanho 6 / 151-156
					
				$this->zerosEsquerda ( $lstTransacoes [($_i - 1)] ["primeira_instrucao_codificada"], 2 ) . // tamanho 2 / 157-158
				$this->zerosEsquerda ( $lstTransacoes [($_i - 1)] ["segunda_instrucao_codificada"], 2 ) . // tamanho 2 / 159-160
				$valor_mora . // VALOR ACRESC POR DIA DE ATRASO - TAMANHO 13 / 161-173 NUM
				$this->gerarEspacosZeros ( 6 ) .
				// 				$data_limite_desconto->format ( 'dmy' ) . // DATA LIMITE PARA CONCESSÃO DESCONTO - TAMANHO 6 / 174-179 NUM
				$valor_desconto_boleto . // VALOR DESCONTO - TAMANHO 13 / 180-192 NUM
				$valor_iof . // VALOR IOF - TAMANHO 13 / 192-205 NUM
				$valor_abatimento . // VALOR ABATIMENTO A SER CONCEDIDO/CANCELADO - TAMANHO 13 / 206-218 NUM
					
				$tipo_inscricao_pagador . // tamanho 2 / 219-220
				$cpf_cnpj_pagador . // tamanho 14 / 221-234
					
				$this->brancosDireita ( $this->replaceCharAscensionToUpper($lstTransacoes [($_i - 1)] ["nome_pagador"]), 37 ) . // NOME PAGADOR - TAMANHO 37 / 235-271 ALFA
				$this->gerarEspacosBrancos ( 3 ) . // tamanho 6 / 272-274
				$this->brancosDireita($this->replaceCharAscensionToUpper($lstTransacoes [($_i - 1)] ["logradouro_pagador"]). " " . $lstTransacoes [($_i - 1)] ["numero_end_pagador"]. " " . $this->replaceCharAscensionToUpper($lstTransacoes [($_i - 1)] ["complemento_end_pagador"]), 40) .
				// 				$this->gerarEspacosBrancos ( 40 ) . // ENDEREÇO COMPLETO - TAMANHO 40 / 275-314 ALFA
				$this->brancosDireita($this->replaceCharAscensionToUpper($lstTransacoes [($_i - 1)] ["bairro_pagador"]), 12) .
				// 				$this->gerarEspacosBrancos ( 12 ) . // Bairro pagador - TAMANHO 12 / 315-326 ALFA
				$prefixo_cep_pagador . // CEP PAGADOR - TAMANHO 5 / 327-331 NUM
				$sufixo_cep_pagador . // SUFIXO CEP - TAMANHO 3 / 332-334 NUM
				$this->brancosDireita($this->replaceCharAscensionToUpper($lstTransacoes [($_i - 1)] ["cidade_pagador"]), 15) .
				// 				$this->gerarEspacosBrancos ( 15 ) . // CIDADE PAGADOR - TAMANHO 15 / 335-349 ALFA
				$this->brancosDireita($this->replaceCharAscensionToUpper($lstTransacoes [($_i - 1)] ["uf_pagador"]), 2) .
				// 				$this->gerarEspacosBrancos ( 2 ) . // uf PAGADOR - TAMANHO 2 / 350-351 ALFA
				$this->gerarEspacosBrancos ( 40 ) . // MENSAGEM AO PAGADOR - TAMANHO 2 / 352-391 ALFA
				$this->gerarEspacosBrancos ( 2 ) . // NÚMERO DE DIAS PARA PROTESTO CASO “Comando” = 01 E “instrução codificada” = 06 - TAMANHO 2 / 392-393 ALFA
				$this->gerarEspacosBrancos ( 1 ) . // BRANCOS - TAMANHO 2 / 350-351 ALFA
					
				$this->zerosEsquerda ( ($_seq + 1), 6 ) . // NÚMERO SEQUENCIAL DO REGISTRO - TAMANHO 6 / 395-400 NUM
				"\r\n";
					
				if ($lstTransacoes [($_i - 1)] ["fk_status"] == 8){
					if ($lstTransacoes [($_i - 1)] ["tem_multa"]){
						$_seq++;
						$multa = number_format($lstTransacoes [($_i - 1)]  ["multa"],2);
						if ($lstTransacoes [($_i - 1)] ["tem_multa"] == 2)  {
							$multa = "00000".$this->removePontosVirgulas($multa, 7);
						}
						else $multa = $this->removePontosVirgulas($multa, 12);
						$regTransacao .= "5" .
							"99" . //COBRANÇA DE MULTA
							$this->zerosEsquerda($lstTransacoes [($_i - 1)] ["tem_multa"], 1) .
							$data_vencimento->add(new DateInterval('P1D'))->format ( 'dmy' ) .
							$multa .
							$this->gerarEspacosBrancos(372) .
							$this->zerosEsquerda(($_seq + 1), 6) .
							"\r\n";
					}
				}
	
	
				$_seq++;
			}
	
	
		$reg9 = "9" . // IDENTIFICAÇÃO DO REGISTRO - TAMANHO 1 / 001-001 NUM DEFAULT '5'
				// 			"03" . // TIPO DE SERVIÇO - TAMANHO 2 / 002-003 DEFAULT '03'
		$this->gerarEspacosBrancos ( 17 ) . // IDENTIFICAÇÃO DO TITULO - TAMANHO 15 / 004-018 BRANCOS
		$this->gerarEspacosBrancos ( 376 ) . // USO FEBRABAN - TAMANHO 376 / 019-394 BRANCOS
		$this->zerosEsquerda ( ($_seq + 1), 6 ); // NÚMERO SEQUENCIAL DO REGISTRO - TAMANHO 6 / 395-400 NUM

		$conteudo = $reg0 . "\r\n" . $regTransacao . $reg9;

		return $conteudo;
	}
	
	
	
	public function montarRemessa($lstTransacoes, $empresa, $sequencial, $dataReferencia, $idOperadora){
		$dia = date ( 'd' );
		$mes = date ( 'm' );
		$seq = $this->zerosEsquerda ( dechex ( $sequencial ), 2 );
		switch ($idOperadora){
			case 3://BRADESCO
				$nome = "CB$dia$mes$seq.REM";
				break;
			case 4://BANCO DO BRASIL
				$nome = "BB$dia$mes$seq.REM";
				
		}
		
		$fp = fopen ( $empresa."/".$nome, "w" );
		
		$dataAtual = date ( 'dmy' );
		
		$escreve = fwrite ( $fp, $this->montarConteudoRemessa($lstTransacoes, $dataAtual, $empresa, $sequencial, $idOperadora ) );
		// Fecha o arquivo
		fclose ( $fp );
		
		$this->updateSequencial ( $sequencial, $lstTransacoes [0] ["id_operadora_empresa"] );
		$retorno = $this->updateTransacaoRemessa ( $lstTransacoes, $dataAtual, $nome, $sequencial , $lstTransacoes [0] ["id_operadora_empresa"], 0, $dataReferencia);
		// 		updateTransacaoRemessa inserir data de referencia na tabela remessas
		if ($retorno["CodStatus"] == 1)
			return $nome;
		return $retorno["Msg"];
	}
	
	public function remontarRemessa($lstTransacoes, $empresa, $remessa, $idOperadora){
		$nome = $remessa ["nome_arquivo"];
		$fp = fopen ( $empresa."/".$nome, "w" );
	
		$dataAtual = date ( 'dmy' );
	
		$escreve = fwrite ( $fp, $this->montarConteudoRemessa($lstTransacoes, $dataAtual, $empresa, $remessa["sequencial"], $idOperadora) );
		// Fecha o arquivo
		fclose ( $fp );
	
		//$this->updateSequencial ( $sequencial, $lstTransacoes [0] ["id_operadora_empresa"] );
		$retorno = $this->updateTransacaoRemessa ( $lstTransacoes, $dataAtual, $nome, $remessa["sequencial"] , $lstTransacoes [0] ["id_operadora_empresa"], $remessa ["id_arquivo"], null);
		if ($retorno["CodStatus"] == 1)
			return $nome;
		return $retorno["Msg"];
		
	}
		


	//operadoras boleto
	public function getOperadorasBoleto($codEmpresa) {
		$sql = "SELECT operadoras.*, operadora_empresa.* FROM operadoras
					INNER JOIN operadora_empresa ON operadoras.id_operadora = operadora_empresa.fk_operadora  
					LEFT OUTER JOIN forma_pagamento_operadora_empresa ON operadora_empresa.id_operadora_empresa = forma_pagamento_operadora_empresa.fk_operadora_empresa
					
				WHERE forma_pagamento_operadora_empresa.fk_forma_pagamento = 22 AND operadora_empresa.fk_empresa = $codEmpresa ORDER BY operadoras.id_operadora";
		
		$consulta = $this->banco->getConexaoBanco ()->query ( $sql );
		$lstOperadoras = array ();
		while ( $linha = $consulta->fetch_array ( MYSQLI_ASSOC ) ) {
			array_push ( $lstOperadoras, $linha );
		}
		$consulta->close ();
		
		return $lstOperadoras;
	}
	public function getFormasPagamento() {
		$sql = "SELECT forma_pagamento.id_forma_pagamento, forma_pagamento.descricao_forma_pagamento, forma_pagamento.ativo FROM forma_pagamento ORDER BY id_forma_pagamento DESC";
		// echo $sql;
		$consulta = $this->banco->getConexaoBanco ()->query ( $sql );
		$lstFormas = array ();
		while ( $linha = $consulta->fetch_array ( MYSQLI_ASSOC ) ) {
			array_push ( $lstFormas, $linha );
		}
		$consulta->close ();
		return $lstFormas;
	}
	public function autenticarInsert($usr, $pwd) {
		$sql = "SELECT empresa.* FROM empresa WHERE empresa.CNPJ = '" . $usr . "' AND empresa.SENHA = '" . $pwd . "'";
		
		$consulta = $this->banco->getConexaoBanco ()->query ( $sql );
		$lstEmpresa = array ();
		while ( $linha = $consulta->fetch_array ( MYSQLI_ASSOC ) ) {
			array_push ( $lstEmpresa, $linha );
		}
		$consulta->close ();
		if (count ( $lstEmpresa ) != 1)
			return null;
		
		return $lstEmpresa;
	}
	public function getFormaPagamentoOperadoraEmpresa($codEmpresa, $formaPagamento, $codOpEmpresa) {
		$sql = "SELECT forma_pagamento_operadora_empresa.id_forma_pagamento_operadora_empresa, forma_pagamento.descricao_forma_pagamento, operadora_empresa.num_convenio_banco_brasil 
				FROM forma_pagamento_operadora_empresa
					LEFT OUTER JOIN operadora_empresa ON forma_pagamento_operadora_empresa.fk_operadora_empresa = operadora_empresa.id_operadora_empresa
					LEFT OUTER JOIN forma_pagamento ON forma_pagamento_operadora_empresa.fk_forma_pagamento = forma_pagamento.id_forma_pagamento
				WHERE operadora_empresa.fk_empresa = $codEmpresa AND forma_pagamento_operadora_empresa.fk_forma_pagamento = $formaPagamento AND operadora_empresa.id_operadora_empresa = $codOpEmpresa";
		
		$consulta = $this->banco->getConexaoBanco ()->query ( $sql );
		$lstFormaPagOpeEmp = array ();
		while ( $linha = $consulta->fetch_array ( MYSQLI_ASSOC ) ) {
			array_push ( $lstFormaPagOpeEmp, $linha );
		}
		$consulta->close ();
		$totalF = count ( $lstFormaPagOpeEmp );
		if ($totalF != 1) {
			if ($totalF > 1)
				return array (
						'CodStatus' => 2,
						'Msg' => "Há mais de uma operadora cadastrada para esta forma de pagamento. Corrija e tente novamente" 
				);
			else
				return array (
						'CodStatus' => 3,
						'Msg' => "Nenhuma operadora cadastrada para esta forma de pagamento!" 
				);
		} else
			return array (
					'CodStatus' => 1,
					'Msg' => "Success!",
					'Model' => $lstFormaPagOpeEmp [0] 
			);
	}
	public function insertTransaction($dados, $fkFormaPgOpEm) {
		$identificador = "";
		if ($fkFormaPgOpEm["descricao_forma_pagamento"] == "Boleto")
			$identificador = $fkFormaPgOpEm["num_convenio_banco_brasil"] . $dados ['origem'] . $this->zerosEsquerda($dados ['codigoPagamento'] , 9);
		try {
			$sql = "INSERT INTO transacao (	
						codigo_pagamento, codigo_origem, fk_forma_pagamento_operadora_empresa, valor_transacao, data_criacao_origem,
						data_vencimento_boleto, tipo_inscricao_pagador, inscricao_pagador, cep_pagador, nome_pagador, fk_origem, num_parcelas, valor_parcela,
						logradouro_pagador, complemento_end_pagador, numero_end_pagador, bairro_pagador, cidade_pagador, uf_pagador, identificador)
					VALUES (" . $dados ['codigoPagamento'] . ", " . $dados ['codigoOrigem'] . ", " . $fkFormaPgOpEm["id_forma_pagamento_operadora_empresa"] . ", " . 
						$dados ['valorDocumento'] . ", DATE '" . $dados ['dataDocumento'] . "', DATE '" . $dados ['dataVencimento'] . "', " . 
						$dados ['tipoInscricaoPagador'] . ", '" . $dados ['inscricaoPagador'] . "', '" . $dados ['cep'] . "', '" . $dados ['nomePagador'] . "', " . 
						$dados ['origem'] . ", " . $dados ['numParcelas'] . ", " . $dados ['valorParcelas'] . ", '" . $dados ['logradouro'] . "', '" . 
						$dados ['complemento'] . "', '" . $dados ['numero'] . "', '" . $dados ['bairro'] . "', '" . $dados ['cidade'] . "', '" . $dados ['uf'] . 
						"', '$identificador')";
			
			if ($this->banco->getConexaoBanco ()->query ( $sql )) {
				$id = $this->banco->getConexaoBanco ()->insert_id;
							
				
				$sql = 	"INSERT INTO movimentacao (
							fk_transacao, fk_status, data_movimentacao)
						VALUES ($id, 8, DATE '" . $dados ['dataDocumento'] . "')";
				$this->banco->getConexaoBanco ()->query ( $sql );
				return array (
						'CodStatus' => 1,
						'Msg' => "Success!",
						'Model' => $id 
				);
			} else {
				$consulta->close ();
				return array (
						'CodStatus' => 2,
						'Msg' => "Falha ao inserir novo registro!" 
				);
			}
		} catch ( Exception $e ) {
			return array (
					'CodStatus' => 3,
					'Msg' => "$sql - " . $e->getMessage () 
			);
		}
	}
	public function getOperadorasPorEmpresa($formaP, $usr, $pwd) {
		try {
			$sql = "SELECT empresa.* FROM empresa WHERE empresa.CNPJ = '" . $usr . "' AND empresa.SENHA = '" . $pwd . "'";
			$consulta = $this->banco->getConexaoBanco ()->query ( $sql );
			$lstEmpresa = array ();
			while ( $linha = $consulta->fetch_array ( MYSQLI_ASSOC ) ) {
				array_push ( $lstEmpresa, $linha );
			}
			
			if (count ( $lstEmpresa ) != 1)
				return array (
						'CodStatus' => 2,
						'Msg' => 'Falha na autenticação da solicitação!' 
				);
			
			$sql = "SELECT operadoras.id_operadora, operadoras.nome_operadora, operadora_empresa.id_operadora_empresa, operadora_empresa.codigo_carteira, operadora_empresa.numero_agencia,
					operadora_empresa.numero_conta FROM operadoras
						LEFT OUTER JOIN operadora_empresa ON operadoras.id_operadora = operadora_empresa.fk_operadora
					    LEFT OUTER JOIN forma_pagamento_operadora_empresa ON operadora_empresa.id_operadora_empresa = forma_pagamento_operadora_empresa.fk_operadora_empresa 
					WHERE operadora_empresa.fk_empresa = " . $lstEmpresa [0] ["CODIGO"] . " AND forma_pagamento_operadora_empresa.fk_forma_pagamento = $formaP";
			// echo $sql;
			$consulta = $this->banco->getConexaoBanco ()->query ( $sql );
			$lstOperadoras = array ();
			while ( $linha = $consulta->fetch_array ( MYSQLI_ASSOC ) ) {
				array_push ( $lstOperadoras, $linha );
			}
			$consulta->close ();
			return array (
					'CodStatus' => 1,
					'Msg' => 'Sucess',
					'Model' => $lstOperadoras 
			);
		} catch ( Exception $e ) {
			return array (
					'CodStatus' => 3,
					'Msg' => $e->getMessage () 
			);
		}
	}
	public function getTransacaoAut($idTransacao, $usr, $pwd) {
		try {
			$sql = "SELECT empresa.* FROM empresa WHERE empresa.CNPJ = '" . $usr . "' AND empresa.SENHA = '" . $pwd . "'";
			$consulta = $this->banco->getConexaoBanco ()->query ( $sql );
			$lstEmpresa = array ();
			while ( $linha = $consulta->fetch_array ( MYSQLI_ASSOC ) ) {
				array_push ( $lstEmpresa, $linha );
			}
			
			if (count ( $lstEmpresa ) != 1)
				return array (
						'CodStatus' => 2,
						'Msg' => 'Falha na autenticação da solicitação!' 
				);
			
			$sql = "SELECT transacao.*, operadora_empresa.* FROM transacao 
					LEFT OUTER JOIN forma_pagamento_operadora_empresa ON transacao.fk_forma_pagamento_operadora_empresa = forma_pagamento_operadora_empresa.id_forma_pagamento_operadora_empresa
					INNER JOIN operadora_empresa ON forma_pagamento_operadora_empresa.fk_operadora_empresa = operadora_empresa.id_operadora_empresa
					WHERE transacao.id_transacao = $idTransacao";
			$consulta = $this->banco->getConexaoBanco ()->query ( $sql );
			$lstTransacao = array ();
			while ( $linha = $consulta->fetch_array ( MYSQLI_ASSOC ) ) {
				array_push ( $lstTransacao, $linha );
			}
			$consulta->close ();
			
			if (count ( $lstTransacao ) != 1)
				return array (
						'CodStatus' => 2,
						'Msg' => 'Falha ao identificar o documento' 
				);
			else {
				$lstTransacao [0] ["Empresa"] = $lstEmpresa [0];
				return array (
						'CodStatus' => 1,
						'Msg' => 'Sucess',
						'Model' => $lstTransacao [0] 
				);
			}
		} catch ( Exception $e ) {
			return array (
					'CodStatus' => 3,
					'Msg' => $e->getMessage () 
			);
		}
	}
	public function getPagamentosPendentes($codEmpresa) {
		$sql = "SELECT transacao.*, operadoras.nome_operadora, operadora_empresa.*, forma_pagamento.*, status.*, movimentacao.*, origens.* FROM TRANSACAO
			LEFT OUTER JOIN forma_pagamento_operadora_empresa on transacao.fk_forma_pagamento_operadora_empresa = forma_pagamento_operadora_empresa.id_forma_pagamento_operadora_empresa
			INNER JOIN forma_pagamento ON forma_pagamento_operadora_empresa.fk_forma_pagamento = forma_pagamento.id_forma_pagamento
			INNER JOIN operadora_empresa ON forma_pagamento_operadora_empresa.fk_operadora_empresa = operadora_empresa.id_operadora_empresa
			INNER JOIN operadoras ON operadora_empresa.fk_operadora = operadoras.id_operadora
			
			INNER JOIN movimentacao ON movimentacao.fk_transacao = transacao.id_transacao AND movimentacao.id_movimentacao = ( 
				SELECT movimentacao.id_movimentacao FROM movimentacao
					WHERE movimentacao.fk_transacao = transacao.id_transacao ORDER BY movimentacao.id_movimentacao DESC LIMIT 1 
			)
			
			INNER JOIN status ON movimentacao.fk_status = status.id_status 
			INNER JOIN origens ON transacao.fk_origem = origens.id_origem 
			WHERE movimentacao.fk_status IN ( 1, 3, 5, 8, 11, 12) AND operadora_empresa.fk_empresa = $codEmpresa ORDER BY transacao.id_transacao LIMIT 10";
		$consulta = $this->banco->getConexaoBanco ()->query ( $sql );
		$lstPgto = array ();
		while ( $linha = $consulta->fetch_array ( MYSQLI_ASSOC ) ) {
			$nossonumero = $this->zerosEsquerda($linha["num_convenio_banco_brasil"], 7);
			$nossonumero .= $this->zerosEsquerda($linha["id_origem"], 1);
			$nossonumero .= $this->zerosEsquerda($linha["codigo_pagamento"], 9);
			$linha["nosso_numero"] = $nossonumero;
			array_push ( $lstPgto, $linha );
		}
		$consulta->close ();
		return $lstPgto;
	}
	
	public function buscarPersonalizadaPagamentos($param) {
		$previous = false;
		
		$sql = "SELECT  transacao.*, operadoras.nome_operadora, operadoras.codigo_banco,
						forma_pagamento.*, operadora_empresa.*, empresa.NOME, empresa.CNPJ, status.*, movimentacao.*, origens.*
							FROM TRANSACAO
						LEFT OUTER JOIN forma_pagamento_operadora_empresa ON transacao.fk_forma_pagamento_operadora_empresa = forma_pagamento_operadora_empresa.id_forma_pagamento_operadora_empresa
						INNER JOIN operadora_empresa ON forma_pagamento_operadora_empresa.fk_operadora_empresa = operadora_empresa.id_operadora_empresa
						INNER JOIN operadoras ON operadora_empresa.fk_operadora = operadoras.id_operadora
						INNER JOIN empresa ON operadora_empresa.fk_empresa = empresa.CODIGO
						INNER JOIN forma_pagamento ON forma_pagamento_operadora_empresa.fk_forma_pagamento = forma_pagamento.id_forma_pagamento
						INNER JOIN movimentacao ON movimentacao.fk_transacao = transacao.id_transacao AND movimentacao.id_movimentacao = ( 
							SELECT movimentacao.id_movimentacao FROM movimentacao
								WHERE movimentacao.fk_transacao = transacao.id_transacao ORDER BY movimentacao.id_movimentacao DESC LIMIT 1 
						)
						INNER JOIN status ON movimentacao.fk_status = status.id_status 
						INNER JOIN origens ON transacao.fk_origem = origens.id_origem ";
		
		if ($param["identificador"] != "" && $param["identificador"] != 0) {
			if ($previous)
				$sql .= " AND ";
			else
				$sql .= " WHERE ";
			$previous = true;
			$whereIdentificador = "(transacao.identificador = '".$param["identificador"]."')";
			$sql .= $whereIdentificador;
		}
		
		if ($param["lstOrigem"] [0] !== "") {
			if ($previous)
				$sql .= " AND ";
			else
				$sql .= " WHERE ";
			
			$previous = true;
			$whereOrigem = "(";
			for($i = 0; $i < (count ( $param["lstOrigem"] ) - 1); $i ++) {
				if ($i == (count ( $param["lstOrigem"] ) - 2))
					$whereOrigem .= "transacao.fk_origem = " . $param["lstOrigem"] [$i] . "";
				else
					$whereOrigem .= "transacao.fk_origem = " . $param["lstOrigem"] [$i] . " OR ";
			}
			$whereOrigem .= ")";
			$sql .= $whereOrigem;
		}
		
		if ($param["codOrigem"] != "" && $param["codOrigem"] != 0){
			if ($previous)
				$sql .= " AND ";
			else
				$sql .= " WHERE ";
			$previous = true;
			$whereCodOrigem = "(transacao.codigo_origem = " . $param["codOrigem"] . ")";
			$sql .= $whereCodOrigem;
		}
		
		if ($param["codPagamento"] != "" && $param["codPagamento"] != 0){
			if ($previous)
				$sql .= " AND ";
			else
				$sql .= " WHERE ";
			$previous = true;
			$whereCodPagamento = "(transacao.codigo_pagamento = " . $param["codPagamento"] . ")";
			$sql .= $whereCodPagamento;
		}
		
		if ($param["lstOperadoras"] [0] !== "") {
			if ($previous)
				$sql .= " AND ";
			else
				$sql .= " WHERE ";
					
			$previous = true;
			$whereOperadora = "(";
			for($i = 0; $i < (count ( $param["lstOperadoras"] ) - 1); $i ++) {
				if ($i == (count ( $param["lstOperadoras"] ) - 2))
					$whereOperadora .= "operadora_empresa.id_operadora_empresa = " . $param["lstOperadoras"] [$i] . "";
				else
					$whereOperadora .= "operadora_empresa.id_operadora_empresa = " . $param["lstOperadoras"] [$i] . " OR ";
			}
			$whereOperadora .= ")";
			$sql .= $whereOperadora;
		}
				
		if (isset ( $param["dataOrigemI"] )) {
			if ($previous)
				$sql .= " and ";
			else
				$sql .= " WHERE ";
			$previous = true;
			$wheredataPeriodo = "(transacao.data_criacao_origem BETWEEN '" . $param["dataOrigemI"] . "' AND '" . $param["dataOrigemF"] . "')";
			$sql .= $wheredataPeriodo;
		}
		
		if (isset ( $param["dataEntradaI"] )) {
			if ($previous)
				$sql .= " AND ";
			else
				$sql .= " WHERE ";
			$previous = true;
			$wheredataEntrada = "(transacao.data_hora_criacao BETWEEN '" . $param["dataEntradaI"] . "' AND '" . $param["dataEntradaF"] . "')";
			$sql .= $wheredataEntrada;
		}
		
		if (isset ( $param["dataPagamentoI"] )) {
			if ($previous)
				$sql .= " and ";
			else
				$sql .= " WHERE ";
			$previous = true;
			$wheredataPagamento = "(transacao.data_pagamento BETWEEN '" . $param["dataPagamentoI"] . "' AND '" . $param["dataPagamentoF"] . "')";
			$sql .= $wheredataPagamento;
		}
		
		if ($param["lstStatus"] [0] !== "") {
			if ($previous)
				$sql .= " AND ";
			else
				$sql .= " WHERE ";
					
			$previous = true;
			$whereStatus = "(";
			for($i = 0; $i < (count ( $param["lstStatus"] ) - 1); $i ++) {
				if ($i == (count ( $param["lstStatus"] ) - 2))
					$whereStatus .= "transacao.estado = '" . $param["lstStatus"] [$i] . "'";
				else
					$whereStatus .= "transacao.estado = '" . $param["lstStatus"] [$i] . "' OR ";
			}
			$whereStatus .= ")";
			$sql .= $whereStatus;
		}
		
		
		if ($param["lstFormaPgto"] [0] !== "") {
			if ($previous)
				$sql .= " AND ";
			else
				$sql .= " WHERE ";
						
			$previous = true;
			$whereFormPgto = "(";
			for($i = 0; $i < (count ( $param["lstFormaPgto"] ) - 1); $i ++) {
				if ($i == (count ( $param["lstFormaPgto"] ) - 2)){
					if ($param["lstFormaPgto"] [$i] == 0)
						$whereFormPgto .= "forma_pagamento.descricao_forma_pagamento = 'Boleto'";
					else 
						$whereFormPgto .= "forma_pagamento.descricao_forma_pagamento <> 'Boleto'";
				}else{
					if ($param["lstFormaPgto"] [$i] == 0)
						$whereFormPgto .= "forma_pagamento.descricao_forma_pagamento = 'Boleto' OR ";
					else
						$whereFormPgto .= "forma_pagamento.descricao_forma_pagamento <> 'Boleto' OR ";
				}
			}
			$whereFormPgto .= ")";
			$sql .= $whereFormPgto;
		}
		
		if ($param["valorTransacao"] != "") {
			if ($previous)
				$sql .= " AND ";
			else
				$sql .= " WHERE ";
			$previous = true;
			
			if ((strstr ( $param["valorTransacao"], ">" )) or (strstr ( $param["valorTransacao"], "<" )))
				$whereValor = "(transacao.valor_transacao " . $param["valorTransacao"] . ")";
			else
				$whereValor = "(transacao.valor_transacao = " . $param["valorTransacao"] . ")";
			$sql .= $whereValor;
		}
		
		if ($param["valorPago"] != "") {
			if ($previous)
				$sql .= " AND ";
			else
				$sql .= " WHERE ";
			$previous = true;
				
			if ((strstr ( $param["valorPago"], ">" )) or (strstr ( $param["valorPago"], "<" )))
				$whereValor = "(transacao.valor_pago " . $param["valorPago"] . ")";
			else
				$whereValor = "(transacao.valor_pago = " . $param["valorPago"] . ")";
			$sql .= $whereValor;
		}
		
// 		echo $sql;
		return $this->buscaTransacoesPersonalizada ( $sql, $_SESSION["dados_empresa"]["cod_empresa"] );
	}
	
	public function liquidarPagamento($cod_empresa, $origem, $fk_pagamento, $data_pagamento, $valor_pagamento, $newStatus){
		$data_pag = new DateTime( date("dmy", strtotime($data_pagamento)) );
		$valor = ltrim($valor_pagamento, '0');
		$valor = ($valor/100);

		$sql = "UPDATE transacao 
				LEFT OUTER JOIN forma_pagamento_operadora_empresa ON transacao.fk_forma_pagamento_operadora_empresa = forma_pagamento_operadora_empresa.id_forma_pagamento_operadora_empresa
				LEFT OUTER JOIN operadora_empresa ON forma_pagamento_operadora_empresa.fk_operadora_empresa = operadora_empresa.id_operadora_empresa
				INNER JOIN empresa ON operadora_empresa.fk_empresa = empresa.CODIGO
				SET 
					movimentacao.fk_status = $newStatus,
					transacao.data_pagamento = '".$data_pag->format('Y-m-d')."',
					transacao.valor_pago = $valor
				WHERE empresa.CODIGO = $cod_empresa AND transacao.fk_origem = $origem AND transacao.codigo_pagamento = $fk_pagamento";
		$result = $this->banco->getConexaoBanco ()->query ( $sql );
	}
	
	
	
	public function getTransacaoByNossoNumero($cod_empresa, $origem, $fk_pagamento){
		$sql = "SELECT transacao.nome_pagador FROM TRANSACAO
					LEFT OUTER JOIN forma_pagamento_operadora_empresa ON transacao.fk_forma_pagamento_operadora_empresa = forma_pagamento_operadora_empresa.id_forma_pagamento_operadora_empresa
					LEFT OUTER JOIN operadora_empresa ON forma_pagamento_operadora_empresa.fk_operadora_empresa = operadora_empresa.id_operadora_empresa
					INNER JOIN empresa ON operadora_empresa.fk_empresa = empresa.CODIGO
				WHERE empresa.CODIGO = $cod_empresa AND transacao.fk_origem = $origem AND transacao.codigo_pagamento = $fk_pagamento
					ORDER BY transacao.id_transacao";
		// echo "<br>".$sql."<br>";
		$consulta = $this->banco->getConexaoBanco ()->query ( $sql );
		$lstPgto = array ();
		while ( $linha = $consulta->fetch_array ( MYSQLI_ASSOC ) ) {
			array_push ( $lstPgto, $linha );
		}
		$consulta->close ();
		return $lstPgto[0];
	}
	public function getTransacao($idTransacaoPai) {
		$sql = "SELECT * FROM transacao WHERE transacao.id_transacao = $idTransacaoPai";
		$consulta = $this->banco->getConexaoBanco()->query($sql);
		$trasacaoPai = array ();
		while ( $linha = $consulta->fetch_array ( MYSQLI_ASSOC ) ) {
			array_push ( $trasacaoPai, $linha );
		}
		$consulta->close ();
		
		if (count ( $trasacaoPai ) == 1){
			return $trasacaoPai[0];
		}
	}
	
	public function insertTransactionBaixa($transacaoPai){
		$dataTransacao = new DateTime();
// 		echo "inserindo registro novo";
		try {
			$sql = "INSERT INTO transacao (	
						codigo_pagamento, codigo_origem, fk_origem, fk_forma_pagamento_operadora_empresa, valor_transacao, data_criacao_origem,
						data_vencimento_boleto, tipo_inscricao_pagador, inscricao_pagador, cep_pagador, nome_pagador, 
						logradouro_pagador, complemento_end_pagador, numero_end_pagador, bairro_pagador, cidade_pagador, uf_pagador, fk_transacao,
						status_geral, num_parcelas, valor_parcela
					)VALUES (" . 
						$transacaoPai ['codigo_pagamento'] . ", " . 
						$transacaoPai ['codigo_origem'] . ", " . 
						$transacaoPai ['fk_origem'] . ", " . 
						$transacaoPai ['fk_forma_pagamento_operadora_empresa'] . ", " .
						$transacaoPai ['valor_transacao'] . ", 
						DATE '" . $dataTransacao->format ( 'Y-m-d' ) . "', 
						DATE '" . $transacaoPai ['data_vencimento_boleto'] . "', " . 
						$transacaoPai ['tipo_inscricao_pagador'] . ", '" . 
						$transacaoPai ['inscricao_pagador'] . "', '" . 
						$transacaoPai ['cep_pagador'] . "', '" . 
						$transacaoPai ['nome_pagador'] . "', '" . 
						$transacaoPai ['logradouro_pagador'] . "', '" . 
						$transacaoPai ['complemento_end_pagador'] . "', '" . 
						$transacaoPai ['numero_end_pagador'] . "', '" . 
						$transacaoPai ['bairro_pagador'] . "', '" . 
						$transacaoPai ['cidade_pagador'] . "', '" . 
						$transacaoPai ['uf_pagador'] . "', " .
						$transacaoPai ['id_transacao'] . ", " .
						16 . ", " .
						$transacaoPai ['num_parcelas'] . ", " .
						$transacaoPai ['valor_parcela'] . "
					)";
				
			if ($this->banco->getConexaoBanco ()->query ( $sql )) {
				$id = $this->banco->getConexaoBanco ()->insert_id;
				
				$sql = "UPDATE transacao SET transacao.status_geral = 99 WHERE transacao.id_transacao = " . $transacaoPai["id_transacao"];
				$result = $this->banco->getConexaoBanco ()->query ( $sql );
				
				return array (
						'CodStatus' => 1,
						'Msg' => "Success!",
						'Model' => $id
				);
			} else {
				$consulta->close ();
				return array (
						'CodStatus' => 2,
						'Msg' => "Falha ao inserir novo registro!"
				);
			}
		} catch ( Exception $e ) {
			return array (
					'CodStatus' => 3,
					'Msg' => $e->getMessage ()
			);
		}
	}
	
	public function updateStatusTransaction($idT, $newS, $fkRemessa){
		$dataRemessa = new DateTime();
		$sql = "INSERT INTO movimentacao (fk_transacao, fk_status, fk_arquivo, data_movimentacao) VALUES ($idT, $newS, $fkRemessa, DATE '" . $dataRemessa->format ( 'Y-m-d' ) . "')";
		$result = $this->banco->getConexaoBanco ()->query ( $sql );
		
		$sql = "UPDATE transacao 
					INNER JOIN movimentacao ON movimentacao.fk_transacao = transacao.id_transacao AND movimentacao.id_movimentacao = ( 
						SELECT movimentacao.id_movimentacao FROM movimentacao
							WHERE movimentacao.fk_transacao = transacao.id_transacao ORDER BY movimentacao.id_movimentacao DESC LIMIT 1 
					)
				    INNER JOIN status ON status.id_status = movimentacao.fk_status
				SET transacao.estado = status.estado
				WHERE transacao.id_transacao = $idT";
		$result = $this->banco->getConexaoBanco ()->query ( $sql );
	}
	
	public function updateStatusTransactionReturn($transacao, $newS, $fkArquivo){
		$dataEntrada = new DateTime();
		$dataProcessamento = new DateTime( date("d/m/Y", strtotime($transacao["data_processamento"])) );
		$sql = "INSERT INTO movimentacao (fk_transacao, fk_status, fk_arquivo, data_movimentacao, data_processamento_operadora) VALUES (" . 
					$transacao["id_transacao"] . 
					", $newS, $fkArquivo, DATE '" . 
					$dataEntrada->format ( 'Y-m-d' ) . 
					"', DATE '" . $dataProcessamento->format ( 'Y-m-d' ) . "')";
		$result = $this->banco->getConexaoBanco ()->query ( $sql );
		
		$this->atualizaEstadoTransacao($transacao["id_transacao"]);
// 		$sql = "UPDATE transacao 
// 					INNER JOIN movimentacao ON movimentacao.fk_transacao = transacao.id_transacao AND movimentacao.id_movimentacao = ( 
// 						SELECT movimentacao.id_movimentacao FROM movimentacao
// 							WHERE movimentacao.fk_transacao = transacao.id_transacao ORDER BY movimentacao.id_movimentacao DESC LIMIT 1 
// 					)
// 				    INNER JOIN status ON status.id_status = movimentacao.fk_status
// 				SET transacao.estado = status.estado
// 				WHERE transacao.id_transacao = " . $transacao["id_transacao"];
// 		$result = $this->banco->getConexaoBanco ()->query ( $sql );
	}
	
	private function atualizaEstadoTransacao($idTransacao){
		$sql = "UPDATE transacao
					INNER JOIN movimentacao ON movimentacao.fk_transacao = transacao.id_transacao AND movimentacao.id_movimentacao = (
						SELECT movimentacao.id_movimentacao FROM movimentacao
							WHERE movimentacao.fk_transacao = transacao.id_transacao ORDER BY movimentacao.id_movimentacao DESC LIMIT 1
					)
				    INNER JOIN status ON status.id_status = movimentacao.fk_status
				SET transacao.estado = status.estado
				WHERE transacao.id_transacao = " . $idTransacao;
		$result = $this->banco->getConexaoBanco ()->query ( $sql );
	}
	
	
	public function updateStatusTransactionPaymentReturn($transacao, $newS, $fkArquivo){
		$dataEntrada = new DateTime();
		$dataProcessamento = new DateTime( date("d/m/Y", strtotime($transacao["data_processamento"])) );
		$dataPagamento = new DateTime( date("d/m/Y", strtotime($transacao["data_pagamento"])));
		$sql = "INSERT INTO movimentacao (fk_transacao, fk_status, fk_arquivo, data_movimentacao, data_processamento_operadora) VALUES (" . 
					$transacao["id_transacao"] . 
					", $newS, $fkArquivo, DATE '" . 
					$dataEntrada->format ( 'Y-m-d' ) . 
					"', DATE '" . $dataProcessamento->format ( 'Y-m-d' ) . "')";
		$result = $this->banco->getConexaoBanco ()->query ( $sql );
		
		$sql = "UPDATE transacao 
					INNER JOIN movimentacao ON movimentacao.fk_transacao = transacao.id_transacao AND movimentacao.id_movimentacao = ( 
						SELECT movimentacao.id_movimentacao FROM movimentacao
							WHERE movimentacao.fk_transacao = transacao.id_transacao ORDER BY movimentacao.id_movimentacao DESC LIMIT 1 
					)
				    INNER JOIN status ON status.id_status = movimentacao.fk_status
				SET 
					transacao.estado = status.estado,
					transacao.data_pagamento = '" . $dataPagamento->format('Y-m-d') . "',
					transacao.valor_pago = " . $transacao["valor_pago"] . "
				WHERE transacao.id_transacao = " . $transacao["id_transacao"];
		$result = $this->banco->getConexaoBanco ()->query ( $sql );
	}
	
	
	public function getDescricaoStatusTransaction($idS){
		
		$sql = "SELECT status.descricao_status FROM status WHERE status.id_status = $idS";
	
		$consulta = $this->banco->getConexaoBanco ()->query ( $sql );
		$retorno = array ();
		while ( $linha = $consulta->fetch_array ( MYSQLI_ASSOC ) ) {
			array_push ( $retorno, $linha );
		}
		$consulta->close ();
		
		return $retorno[0];
	}
	
	
}
	