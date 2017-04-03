<?php
// +----------------------------------------------------------------------+
// | BoletoPhp - Vers�o Beta |
// +----------------------------------------------------------------------+
// | Este arquivo est� dispon�vel sob a Licen�a GPL dispon�vel pela Web |
// | em http://pt.wikipedia.org/wiki/GNU_General_Public_License |
// | Voc� deve ter recebido uma c�pia da GNU Public License junto com |
// | esse pacote; se n�o, escreva para: |
// | |
// | Free Software Foundation, Inc. |
// | 59 Temple Place - Suite 330 |
// | Boston, MA 02111-1307, USA. |
// +----------------------------------------------------------------------+

// +----------------------------------------------------------------------+
// | Originado do Projeto BBBoletoFree que tiveram colabora��es de Daniel |
// | William Schultz e Leandro Maniezo que por sua vez foi derivado do |
// | PHPBoleto de Jo�o Prado Maia e Pablo Martins F. Costa |
// | |
// | Se vc quer colaborar, nos ajude a desenvolver p/ os demais bancos :-)|
// | Acesse o site do Projeto BoletoPhp: www.boletophp.com.br |
// +----------------------------------------------------------------------+

// +---------------------------------------------------------------------------------+
// | Equipe Coordena��o Projeto BoletoPhp: <boletophp@boletophp.com.br> |
// | Desenvolvimento Boleto Banco do Brasil: Daniel William Schultz / Leandro Maniezo|
// +---------------------------------------------------------------------------------+
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<title><?php echo $dadosboleto["identificacao"]; ?></title>
<meta http-equiv="Content-Type" content="text/html" charset="UTF-8" />
<meta name="Generator" content="Boletos iBoltSys" />

<link href="<?= BaseProjeto ?>/util/boletos/include/layout.css" rel="stylesheet" type="text/css">
		
</head>
<body>
	<div id="container">
		<div id="instr_header">
			<img height="65" width="160"
				src="http://<?= $_SERVER['HTTP_HOST'] ?>/sii/iboltpag/resources/logos/<?= $dadosboleto ["id_empresa"] ?>.png" />
			<h1><?php echo $dadosboleto["identificacao"]; ?> <?php echo isset($dadosboleto["cpf_cnpj"]) ? $dadosboleto["cpf_cnpj"] : '' ?></h1>
			<address><?php //echo $dadosboleto["endereco"]; ?><br>
			
			</address>
			<address><?php //echo $dadosboleto["cidade_uf"]; ?></address>
		</div>
		<!-- id="instr_header" -->

		<div id="instructions"></div>

		<div id="boleto">
<!-- 			<div class="cut"> -->
<!-- 				<p>Corte na linha pontilhada</p> -->
<!-- 			</div> -->
			<table cellspacing=0 cellpadding=0 width=666 border=0>
				<tbody>
					<TR>
						<TD class=ct width=666>
							<div align=right>
								<b class=cp>Recibo do Pagador</b>
							</div>
						</TD>
					</tr>
				</tbody>
			</table>
			<table class="header" border=0 cellspacing="0" cellpadding="0">
				<tbody>
					<tr>
						<td width=150>
							<?php switch ($dadosboleto ["operadora_boleto"]){
								case 3://BRADESCO
									echo "<IMG SRC=\"http://" . $_SERVER['HTTP_HOST'] . "/iboltpag/util/boletos/imagens/logobradesco.jpg\">";
									break;
								case 4://BANCO DO BRASIL
									echo "<IMG SRC=\"http://" . $_SERVER['HTTP_HOST'] . "/iboltpag/util/boletos/imagens/logobb.jpg\">";
									break;
							}?>
						</td>
						<td width=50>
							<div class="field_cod_banco"><?php echo $dadosboleto["codigo_banco_com_dv"]?></div>
						</td>
						<td class="linha_digitavel"><?php echo $dadosboleto["linha_digitavel"]?></td>
					</tr>
				</tbody>
			</table>

			<table class="line" cellspacing="0" cellpadding="0">
				<tbody>
					<tr class="titulos">
						<td class="cedente">Beneficiário</TD>
						<td class="ag_cod_cedente">Agência / Código do
							Beneficiário</td>
						<td class="especie">Espécie</TD>
						<td class="qtd">Quantidade</TD>
						<td class="nosso_numero">Nosso número</td>
					</tr>

					<tr class="campos">
						<td class="cedente"><?php echo $dadosboleto["cedente"]; ?>&nbsp;</td>
						<td class="ag_cod_cedente"><?php echo $dadosboleto["agencia_codigo"]?> &nbsp;</td>
						<td class="especie"><?php echo $dadosboleto["especie"]?>&nbsp;</td>
						<TD class="qtd"><?php echo $dadosboleto["quantidade"]?>&nbsp;</td>
						<TD class="nosso_numero"><?php echo $dadosboleto["nosso_numero"]?>&nbsp;</td>
					</tr>
				</tbody>
			</table>

			<table class="line" cellspacing="0" cellPadding="0">
				<tbody>
					<tr class="titulos">
						<?php switch ($dadosboleto ["operadora_boleto"]){
							case 3://Bradesco
								echo "<td class=\"num_doc\">Número do documento</td>";
								echo "<td class=\"vazio\"></td>";
								break;
							case 4://BANCO DO BRASIL
								echo "<td class=\"num_doc\">Número do documento</td>";
								echo "<td class=\"contrato\">Contrato</td>";
								break;
						}?>
						
						<td class="cpf_cei_cnpj">CPF/CEI/CNPJ</TD>
						<td class="vencmento">Vencimento</TD>
						<td class="valor_doc">Valor documento</TD>
					</tr>
					<tr class="campos">
						
						<?php switch ($dadosboleto ["operadora_boleto"]){
							case 3://Bradesco
								echo "<td class=\"num_doc\" colspan=2>" . $dadosboleto["numero_documento"] . "</td>";
								break;
							case 4://BANCO DO BRASIL
								echo "<td class=\"num_doc\">" . $dadosboleto["numero_documento"] . "</td>";
								echo "<td class=\"contrato\">" . $dadosboleto["contrato"] . "</td>";
								break;
						}?>
						<td class="cpf_cei_cnpj"><?php echo $dadosboleto["cpf_cnpj"]?></td>
						<td class="vencimento"><?php echo $dadosboleto["data_vencimento"]?></td>
						<td class="valor_doc"><?php echo $dadosboleto["valor_boleto"]?></td>
					</tr>
				</tbody>
			</table>

			<table class="line" cellspacing="0" cellPadding="0">
				<tbody>
					<tr class="titulos">
						<td class="desconto">(-) Desconto / Abatimento</td>
						<td class="outras_deducoes">(-) Outras dedu&ccedil;&otilde;es</td>
						<td class="mora_multa">(+) Mora / Multa</td>
						<td class="outros_acrescimos">(+) Outros acr&eacute;scimos</td>
						<td class="valor_cobrado">(=) Valor cobrado</td>
					</tr>
					<tr class="campos">
						<td class="desconto">&nbsp;</td>
						<td class="outras_deducoes">&nbsp;</td>
						<td class="mora_multa">&nbsp;</td>
						<td class="outros_acrescimos">&nbsp;</td>
						<td class="valor_cobrado">&nbsp;</td>
					</tr>
				</tbody>
			</table>


			<table class="line" cellspacing="0" cellpadding="0">
				<tbody>
					<tr class="titulos">
						<td class="sacado">Pagador</td>
					</tr>
					<tr class="campos">
						<td class="sacado"><?php echo $dadosboleto["sacado"]?></td>
					</tr>
				</tbody>
			</table>

			<div class="footer">
				<p>Autentica&ccedil;&atilde;o mec&acirc;nica</p>
			</div>



			<div class="cut">
				<p>Corte na linha pontilhada</p>
			</div>


			<table class="header" border=0 cellspacing="0" cellpadding="0">
				<tbody>
					<tr>
						<td width=150>
							<?php switch ($dadosboleto ["operadora_boleto"]){
								case 3://BRADESCO
									echo "<IMG SRC=\"http://" . $_SERVER['HTTP_HOST'] . "/iboltpag/util/boletos/imagens/logobradesco.jpg\">";
									break;
								case 4://BANCO DO BRASIL
									echo "<IMG SRC=\"http://" . $_SERVER['HTTP_HOST'] . "/iboltpag/util/boletos/imagens/logobb.jpg\">";
									break;
							}?>
						</td>
						<td width=50>
							<div class="field_cod_banco"><?php echo $dadosboleto["codigo_banco_com_dv"]?></div>
						</td>
						<td class="linha_digitavel"><?php echo $dadosboleto["linha_digitavel"]?></td>
					</tr>
				</tbody>
			</table>

			<table class="line" cellspacing="0" cellpadding="0">
				<tbody>
					<tr class="titulos">
						<td class="local_pagto">Local de pagamento</td>
						<td class="vencimento2">Vencimento</td>
					</tr>
					<tr class="campos">
						<td class="local_pagto">QUALQUER BANCO</td>
						<td class="vencimento2"><?php echo $dadosboleto["data_vencimento"]?></td>
					</tr>
				</tbody>
			</table>

			<table class="line" cellspacing="0" cellpadding="0">
				<tbody>
					<tr class="titulos">
						<td class="cedente2">Beneficiário</td>
						<td class="ag_cod_cedente2">Ag&ecirc;ncia/C&oacute;digo
							beneficiário</td>
					</tr>
					<tr class="campos">
						<td class="cedente2"><?php echo $dadosboleto["cedente"]?></td>
						<td class="ag_cod_cedente2"><?php echo $dadosboleto["agencia_codigo"]?></td>
					</tr>
				</tbody>
			</table>

			<table class="line" cellspacing="0" cellpadding="0">
				<tbody>
					<tr class="titulos">
						<td class="data_doc">Data do documento</td>
						<td class="num_doc2">No. documento</td>
						<td class="especie_doc">Esp&eacute;cie doc.</td>
						<td class="aceite">Aceite</td>
						<td class="data_process">Data process.</td>
						<td class="nosso_numero2">Nosso n&uacute;mero</td>
					</tr>
					<tr class="campos">
						<td class="data_doc"><?php echo $dadosboleto["data_documento"]?></td>
						<td class="num_doc2"><?php echo $dadosboleto["numero_documento"]?></td>
						<td class="especie_doc"><?php echo $dadosboleto["especie_doc"]?></td>
						<td class="aceite"><?php echo $dadosboleto["aceite"]?></td>
						<td class="data_process"><?php echo $dadosboleto["data_processamento"]?></td>
						<td class="nosso_numero2"><?php echo $dadosboleto["nosso_numero"]?></td>
					</tr>
				</tbody>
			</table>

			<table class="line" cellspacing="0" cellPadding="0">
				<tbody>
					<tr class="titulos">
						<td class="reservado">Uso do banco</td>
						<td class="carteira">Carteira</td>
						<td class="especie2">Esp&eacute;cie</td>
						<td class="qtd2">Quantidade</td>
						<td class="xvalor">x Valor</td>
						<td class="valor_doc2">(=) Valor documento</td>
					</tr>
					<tr class="campos">
						<td class="reservado">&nbsp;</td>
						<td class="carteira"><?php echo $dadosboleto["carteira"]?> <?php echo isset($dadosboleto["variacao_carteira"]) ? $dadosboleto["variacao_carteira"] : '&nbsp;' ?></td>
						<td class="especie2"><?php echo $dadosboleto["especie"]?></td>
						<td class="qtd2"><?php echo $dadosboleto["quantidade"]?></td>
						<td class="xvalor"><?php echo $dadosboleto["valor_unitario"]?></td>
						<td class="valor_doc2"><?php echo $dadosboleto["valor_boleto"]?></td>
					</tr>
				</tbody>
			</table>


			<table class="line" cellspacing="0" cellpadding="0">
				<tbody>
					<tr>
						<td class="last_line" rowspan="6">
							<table class="line" cellspacing="0" cellpadding="0">
								<tbody>
									<tr class="titulos">
										<td class="instrucoes">Instru&ccedil;&otilde;es (Texto de
											responsabilidade do beneficiário)</td>
									</tr>
									<tr class="campos">
										<td class="instrucoes" rowspan="5">
											<p><?php echo $dadosboleto["demonstrativo1"]; ?></p>
											<p><?php echo $dadosboleto["demonstrativo2"]; ?></p>
											<p><?php echo $dadosboleto["demonstrativo3"]; ?></p>
											<p><?php echo $dadosboleto["instrucoes1"]; ?></p>
											<p><?php echo $dadosboleto["instrucoes2"]; ?></p>
											<p><?php echo $dadosboleto["instrucoes3"]; ?></p>
											<p><?php echo $dadosboleto["instrucoes4"]; ?></p>
										</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>

					<tr>
						<td>
							<table class="line" cellspacing="0" cellpadding="0">
								<tbody>
									<tr class="titulos">
										<td class="desconto2">(-) Desconto / Abatimento</td>
									</tr>
									<tr class="campos">
										<td class="desconto2">&nbsp;</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>

					<tr>
						<td>
							<table class="line" cellspacing="0" cellpadding="0">
								<tbody>
									<tr class="titulos">
										<td class="outras_deducoes2">(-) Outras dedu&ccedil;&otilde;es</td>
									</tr>
									<tr class="campos">
										<td class="outras_deducoes2">&nbsp;</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>

					<tr>
						<td>
							<table class="line" cellspacing="0" cellpadding="0">
								<tbody>
									<tr class="titulos">
										<td class="mora_multa2">(+) Mora / Multa</td>
									</tr>
									<tr class="campos">
										<td class="mora_multa2">&nbsp;</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>

					<tr>
						<td>
							<table class="line" cellspacing="0" cellpadding="0">
								<tbody>
									<tr class="titulos">
										<td class="outros_acrescimos2">(+) Outros Acr&eacute;scimos</td>
									</tr>
									<tr class="campos">
										<td class="outros_acrescimos2">&nbsp;</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>

					<tr>
						<td class="last_line">
							<table class="line" cellspacing="0" cellpadding="0">
								<tbody>
									<tr class="titulos">
										<td class="valor_cobrado2">(=) Valor cobrado</td>
									</tr>
									<tr class="campos">
										<td class="valor_cobrado2">&nbsp;</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
				</tbody>
			</table>


			<table class="line" cellspacing="0" cellPadding="0">
				<tbody>
					<tr class="titulos">
						<td class="sacado2">Pagador</td>
					</tr>
					<tr class="campos">
						<td class="sacado2">
							<p><?php echo $dadosboleto["sacado"]?></p>
							<p><?php echo $dadosboleto["endereco1"]?></p>
							<p><?php echo $dadosboleto["endereco2"]?></p>
						</td>
					</tr>
				</tbody>
			</table>

			<table class="line" cellspacing="0" cellpadding="0">
				<tbody>
					<tr class="titulos">
						<td class="sacador_avalista" colspan="2">Sacador/Avalista</td>
					</tr>
					<tr class="campos">
						<td class="sacador_avalista">&nbsp;</td>
						<td class="cod_baixa">C&oacute;d. baixa</td>
					</tr>
				</tbody>
			</table>
			<table cellspacing=0 cellpadding=0 width=666 border=0>
				<tbody>
					<TR>
						<TD width=666 align=right><font style="font-size: 10px;">Autentica&ccedil;&atilde;o
							mec&acirc;nica - Ficha de Compensação</font></TD>
					</tr>
				</tbody>
			</table>
			<div class="barcode">
				<p><?php fbarcode($dadosboleto["codigo_barras"]); ?></p>
			</div>
			<div class="cut">
				<p>Corte na linha pontilhada</p>
			</div>

		</div>

	</div>

</body>

</html>
