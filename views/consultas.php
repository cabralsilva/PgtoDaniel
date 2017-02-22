<?php
	require_once '../util/constantes.php';
	include '../controllers/consultasController.php';
	$cc = new ConsultasController();
	
	if (!isset($_SESSION["usuario_logado"])){
		$_SESSION["falha_login"] = "Autenticação necessária";
		header("location: " . BaseProjeto . "/");
	}
	//$hc->buscarPagamentosPendentes();
	$cc->buscarOperadorasBoleto();
	$cc->buscarStatus();
	$cc->buscarOrigens();
	date_default_timezone_set ( 'America/Sao_Paulo' );
?>
<!DOCTYPE html>
<html>
	<title>Área administrativa Cielo</title>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<HTTP-EQUIV ="PRAGMA" CONTENT="NO-CACHE"> 
	<link href="<?= BaseProjeto ?>/resources/css/style.css" rel="stylesheet" type="text/css">
	
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<script src="<?= BaseProjeto ?>/resources/bootstrap/js/jquery3.3.1.min.js"></script>
	<script src="<?= BaseProjeto ?>/resources/bootstrap/js/bootstrap3.3.7.min.js"></script>
	
	<!-- DATEPICKER -->
	<script src="<?= BaseProjeto ?>/resources/datepicker-default/js/bootstrap-datepicker.js"></script>
	<script src="<?= BaseProjeto ?>/resources/datepicker-default/locales/bootstrap-datepicker.pt-BR.min.js"></script>
	<link href="<?= BaseProjeto ?>/resources/datepicker-default/css/bootstrap-datepicker.css" rel="stylesheet" type="text/css">
	
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.10.0/css/bootstrap-select.min.css">
<!-- 	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/css/bootstrap-select.min.css"> -->
		
	<!-- Latest compiled and minified JavaScript -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.10.0/js/bootstrap-select.min.js"></script>
<!-- 	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/js/bootstrap-select.min.js"></script> -->
	
	<!-- defaults -->
	<script src="<?= BaseProjeto ?>/resources/default-js.js"></script>
</head>
<body>
	<br>
	<div class="container containerrelatorio">
		<div class="panel-group" id="accordion">
			<div class="panel panel-info">
				<div class="panel-heading">
					<h3 class="panel-title">
						<?php include 'layout/header.php';?>
					</h3>
				</div>
				
				<div class="panel-body">
					<!-- Nav tabs PRINCIPAIS-->
					<div>
						<div id="tableTransacoes">
							<span id="filtrosPesquisados">
							</span>
							<table id="transacoes" class="table table-hover table-striped ">
								<thead>
									<tr>
										<th class="col-md-1"><center>ORIGEM</center></th>
										<th class="col-md-1"><center><span href="#" data-toggle="tooltip" title="Ex.: Nosso número">IDENTIFICADOR</span></center></th>
										<th class="col-md-1"><center>CÓDIGO NA ORIGEM</center></th>
										<th class="col-md-1"><center>CÓDIGO PAGAMENTO</center></th>
										<th class="col-md-1"><center>DATA NA ORIGEM</center></th>
										<th class="col-md-1"><center>DATA ENTRADA</center></th>
										<th class="col-md-1"><center>STATUS</center></th>
										<th class="col-md-1"><center>PAGAMENTO</center></th>
										<th class="col-md-1"><center>OPERADORA</center></th>
										<th class="col-md-1"><center>VALOR</center></th>
										<th class="col-md-1"><center>DATA PAGAMENTO</center></th>
										<th class="col-md-1"><center>VALOR PAGO</center></th>
										<th class="col-md-1" style="text-align: right; vertical-align: top;">
											<div class="btn-group">
												<button type="button" class="btn btn-primary" title="Escolha os filtros..." 
													data-toggle="popover" data-popover-content="#popover-content" 
													data-placement="left" href="#" id="btnFiltros">
													Filtros <span class="caret"></span>
												</button>
												<div id="popover-content" class="hide">
													<form id="frmFiltrosForm" class="form-inline frmFiltros" role="form">
														<div class="form-group"> 
															<label for="selecaoOrigem">Origem</label>
															<select class="form-control selectpicker" id="selecaoOrigem" data-selected-text-format="count" title="Selecione..." 
																data-width="100%" multiple>
																	<?php foreach ($_REQUEST["lstOrigens"] as $origem){?>
																		 <option value="<?= $origem["id_origem"]?>"><?= $origem["descricao_origem"]?></option>
																	<?php }?>
														 </select>
														</div>
														
														<div class="form-group"> 
															<label for="identificadorTransacao">Identificador</label>
															<input id="identificadorTransacao" type="text" class="form-control" aria-label="Text input with multiple buttons" min="0"
																pattern="\d*" step="1" />
														</div>
														
														 <div class="form-group"> 
															<label for="codOrigem">Código Origem</label>
															<input id="codOrigem" type="number" class="form-control" aria-label="Text input with multiple buttons" min="0"
																pattern="\d*" step="1" />
														</div>
														
														 <div class="form-group"> 
															<label for="codPagamento">Código Pagamento</label>
															<input id="codPagamento" type="number" class="form-control" aria-label="Text input with multiple buttons" min="0"
																pattern="\d*" step="1" /> 
														</div>
														
														 <div class="form-group"> 
															<label for="dataOrigemI">Data Origem</label>
															<div class="input-daterange input-group" id="datepicker">
																<input type="text" class="input-sm form-control" id="dataOrigemI" /> 
																<span class="input-group-addon labelrangedate">até</span> 
																<input type="text" class="input-sm form-control" id="dataOrigemF" />
															</div>
														</div>
														
														 <div class="form-group"> 
															<label for="dataEntradaI">Data Entrada</label>
															<div class="input-daterange input-group" id="datepicker">
																<input type="text" class="input-sm form-control" id="dataEntradaI" /> 
																<span class="input-group-addon labelrangedate">até</span> 
																<input type="text" class="input-sm form-control" id="dataEntradaF" />
															</div>
														</div>
														
														 <div class="form-group"> 
															<label for="selecaoStatusConsulta">Status</label>
															<select class="form-control selectpicker" id="selecaoStatusConsulta"
																multiple data-selected-text-format="count" title="Selecione..."
																data-width="100%" multiple>
																<optgroup label="Todos">
																<?php foreach ($_REQUEST["lstStatusTodos"] as $statusTodos){?>
																	 <option value="<?= $statusTodos["id_status"]?>"><?= $statusTodos["estado"]?></option>
																<?php }?>
																 </optgroup>
																	<optgroup label="Boletos">
																<?php foreach ($_REQUEST["lstStatusBoleto"] as $statusBoleto){?>
																	 <option value="<?= $statusBoleto["id_status"]?>"><?= $statusBoleto["estado"]?></option>
																<?php }?>
																 </optgroup>
																<optgroup label="Cartao">
																<?php foreach ($_REQUEST["lstStatusCartao"] as $statusCartao){?>
																	 <option value="<?= $statusCartao["id_status"]?>"><?= $statusCartao["estado"]?></option>
																<?php }?>
																 </optgroup>
															</select>
														</div>
														<div class="form-group"> 
															<label for="selecaoFormaPgtoConsulta">Forma Pagamento</label>
															<select class="form-control selectpicker" id="selecaoFormaPgtoConsulta"
																multiple data-selected-text-format="count" title="Selecione..."
																data-width="100%" multiple>
																<option value="0">Boleto</option>
																<option value="1">Cartão</option>
															</select>
														</div>
														<div class="form-group"> 
															<label for="selecaoOperadoraConsulta">Operadora</label>
															<select class="form-control selectpicker"
																id="selecaoOperadoraConsulta"
																data-selected-text-format="count" title="Selecione..."
																data-width="100%" multiple>
																	<?php foreach ($_REQUEST["lstOperadoras"] as $operadora){?>
																		 <optgroup label="<?= $operadora["nomeOperadora"]?>">
																		<?php foreach ($operadora["contas"] as $contas){?>
																			 <option title="<?= $operadora["nomeOperadora"]?> - Ag: <?= $contas["agencia"]?> - CC:<?= $contas["conta"]?> - Cart: <?= $contas["carteira"]?>" value="<?= $contas["idOperadoraEmp"]?>">
																				"Carteira: <?= $contas["carteira"]?> - Ag.: <?= $contas["agencia"]?> - Conta: <?= $contas["conta"]?>
																			</option>
																		<?php }?>
																		 </optgroup>
																	<?php }?>
																	 </select> 
														</div>
														<div class="form-group"> 
															<label for="valorBrutoTransacao">Valor</label>
															<div class="input-group">
																<span class="input-group-addon labelcifrao">R$ </span> <input width="100%" 
																	id="valorBrutoTransacao" type="text"
																	placeholder="use > ou <" class="form-control inputvalortransacao" aria-label="Valor">
															</div>
														</div>
														<div class="form-group"> 
															<label for="dataPagamentoI">Data Pagamento</label>
															<div class="input-daterange input-group" id="datepicker">
																<input type="text" class="input-sm form-control" id="dataPagamentoI" /> 
																<span class="input-group-addon labelrangedate">até</span> 
																<input type="text" class="input-sm form-control" id="dataPagamentoF" />
															</div>
														</div>
														<div class="form-group"> 
															<label for="valorPago">Valor Pago</label>
															<div class="input-group">
																<span class="input-group-addon labelcifrao">R$ </span> <input width="100%"
																	id="valorPago" type="text"
																	placeholder="use > ou <" class=" form-control 
																	inputvalortransacao" aria-label="Valor">
															</div>
														</div>
														<div class="form-group" style="width: 100% !important;">
															<button onclick="buscarBoletosFiltro()" id="btnFiltrar" type="button" class="btn btn-success">
																	<span class="glyphicon glyphicon-filter"></span>
															</button>
														</div>														
													</form>
												</div>
										   	</div>
										</th>
									</tr>
								</thead>
								<tbody id="conteudo-relatorio">
	                   				<tr><td colspan="14" align="center">Preencha os filtros!</td></tr> 
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="table-loading"></div>
	</div>
	
	<?php include 'layout/modais.php';?>
	
	<script type="text/javascript">
		

		var obj;
		var formSearch = "";
		$(document).ready(function(){
		    $('[data-toggle="tooltip"]').tooltip();   
		    var tmp = $.fn.popover.Constructor.prototype.show;
			$.fn.popover.Constructor.prototype.show = function () {
			  tmp.call(this);
			  if (this.options.callback) {
			    this.options.callback();
			  }
			}
		});

		$("[data-toggle=popover]").popover({
		    html: true, 
			content: function() {
				var clone = $($(this).data('popover-content')).clone(true).removeClass('hide');
				clone.find('.bootstrap-select').replaceWith(function() { return $('select', this); });
		        clone.find('select').selectpicker();
	            return clone.html();
		    },
		    callback: function(){
		    	$(".selectpicker").selectpicker();
		    	$('.input-daterange').datepicker({
		    		endDate: '0d', 
		    	    format: "dd/mm/yyyy",
		    	    weekStart: 0,
		    	    language: "pt-BR",
		    	    multidate: false,
		    	    daysOfWeekHighlighted: "0",
		    	    todayHighlight: false,
		    	    autoclose: true
		    	});
		    }
		});

		function solicitarAlteracaoStatus(elem){

			$.ajax({
		    	url : "<?= BaseProjeto ?>/controllers/consultasController.php",
		        type: 'POST',
		        data: {
			        servico: "alterarStatus",
			        id_transacao: elem.getAttribute('data-idt'),
			        id_status: elem.getAttribute('data-st'),
			        id_remessa: elem.getAttribute('data-rem')
		        },
		        success: function (data) {
		            var obj2 = JSON.parse(data);
					for (i in obj){
						if (obj[i].id_transacao == obj2["Model"]["id_transacao"]){
							obj[i].fk_status = obj2["Model"]["id_status"];
						 	obj[i].descricao_status = obj2["Model"]["descricao_status"];
						}
					}
		            $("#conteudo-relatorio").html(contruirRelatorio(obj));
		        }
		    });
		    return false;
		}
				
		
        function buscarBoletosFiltro() {

        	var operadoras = "";
        	$("#selecaoOperadoraConsulta option:selected").each(function(ind, elem) {
        		operadoras += elem.value + ",";
        	});

        	var status = "";
        	$("#selecaoStatusConsulta option:selected").each(function(ind, elem) {
        		status += elem.text + ",";
        	});

        	var origem = "";
        	$("#selecaoOrigem option:selected").each(function(ind, elem) {
        		origem += elem.value + ",";
        	});

        	var formapgto = "";
        	$("#selecaoFormaPgtoConsulta option:selected").each(function(ind, elem) {
        		formapgto += elem.value + ",";
        	});
        	
        	var dataOrigemI = document.getElementById('dataOrigemI').value;
        	var dataOrigemF = document.getElementById('dataOrigemF').value;
        	var dataEntradaI = document.getElementById('dataEntradaI').value;
        	var dataEntradaF = document.getElementById('dataEntradaF').value;
        	var dataPagamentoI = document.getElementById('dataPagamentoI').value;
        	var dataPagamentoF = document.getElementById('dataPagamentoF').value;

        	var identificador = document.getElementById("identificadorTransacao").value;
        	var codOrigem = document.getElementById('codOrigem').value;
        	var codPagamento = document.getElementById('codPagamento').value;
        	var valorTransacao = document.getElementById('valorBrutoTransacao').value;
        	var valorPago = document.getElementById('valorPago').value;
        	var codigoTransacao = document.getElementById('identificadorTransacao').value;
        	if (dataOrigemI == "" && dataEntradaI == "" && dataPagamentoI == "" && identificador == "") 
            	alert("Selecione algum período de datas!");
        	else{
	        	$.ajax({
	        		async : true,
	        		type : 'POST',
	        		url : "<?= BaseProjeto ?>/controllers/consultasController.php",
	        		data : {
	        			servico: "buscarBoletosFiltro",
	        			lstOrigem: origem,
	        			identificador: identificador,
	        			codOrigem: codOrigem,
	        			codPagamento: codPagamento,
	        			dataOrigemI: dataOrigemI,
	        			dataOrigemF: dataOrigemF,
	        			dataEntradaI: dataEntradaI,
	        			dataEntradaF: dataEntradaF,
	        			dataPagamentoI: dataPagamentoI,
	        			dataPagamentoF: dataPagamentoF,
	        			lstStatus: status,
	        			lstOperadoras: operadoras,
	        			lstFormaPgto: formapgto,
	        			valorTransacao: valorTransacao,
	        			valorPago: valorPago
	        		},
	        		success : function(e) {
		        		console.log(e);
	        			obj = JSON.parse(e);
	        			if (obj.length == 0)
	        				$("#conteudo-relatorio").html("<tr><td colspan='14' align='center'>Nenhum Registro encontrado!</td></tr>");
	        			else
	        				$("#conteudo-relatorio").html(contruirRelatorio(obj));

	        			
        				var resultados = "<div class=\"container\">";
        				resultados += "<div class=\"alert alert-info alert-pesquisa col-md-12\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">" +
        			    	"<span aria-hidden=\"true\">&times;</span>" +
 	       			    	"</button><strong>Filtros: </strong><br>";

        				origem = JSON.parse("[" + origem.substr(0,(origem.length - 1)) + "]");
        				resultados += "<div class=\"col-md-6\"><strong>Origem:</strong> ";
    					<?php foreach ($_REQUEST["lstOrigens"] as $origem){?>
        					for(or in origem){
            					if(origem[or] == "<?= $origem["id_origem"] ?>"){
            						resultados += "<?= $origem["descricao_origem"] ?> / ";
            					}
        					}
        				<?php }?>
        				resultados += "<br><strong>Status:</strong> ";
        				var estados = status.split(",");
    					for(var i = 0; i < estados.length - 1; i++){
        					resultados += estados[i] + " / ";
            			}

    					operadoras = JSON.parse("[" + operadoras.substr(0,(operadoras.length - 1)) + "]");
        				resultados += "<br><strong>Operadoras:</strong> ";
    					<?php foreach ($_REQUEST["lstOperadoras"] as $op){?>
    						<?php foreach ($op["contas"] as $contas){?>
	    						for(op in operadoras){
	            					if(operadoras[op] == "<?= $contas["idOperadoraEmp"] ?>"){
	            						resultados += "<?= $op["nomeOperadora"]?> - Ag: <?= $contas["agencia"]?> - CC:<?= $contas["conta"]?> - Cart: <?= $contas["carteira"]?> / ";
	            					}
	        					}
	    					<?php }?>
        				<?php }?>

        				resultados += "<br><strong>Valor:</strong> ";
        				if (valorTransacao > 0) resultados += "R$ " + valorTransacao;
        				resultados += "<br><strong>Valor Pago:</strong> ";
        				if (valorPago > 0) resultados += "R$ " + valorPago;
        				resultados += "</div><div class=\"col-md-6\"><strong>Identificador:</strong> ";
        				if (resultados != "") resultados += identificador;
        				resultados += "<br><strong>Cód. na Origem:</strong> " + codOrigem;
        				resultados += "<br><strong>Cód. Pagamento:</strong> " + codPagamento;
        				
        				resultados += "<br><strong>Data na Origem: </strong> ";
        				if (dataOrigemI != "") resultados += "de " + dataOrigemI + " até " + dataOrigemF;
        				resultados += "<br><strong>Data Entrada: </strong> ";
        				if (dataEntradaI != "") resultados += "de " + dataEntradaI + " até " + dataEntradaF;
        				resultados += "<br><strong>Data Pagamento: </strong> ";
        				if (dataPagamentoI != "") resultados += "de " + dataPagamentoI + " até " + dataPagamentoF;
        				resultados += "</div>";	
    					resultados += "</div>";	
    					resultados += "</div>";	

        				$("#filtrosPesquisados").html(resultados);
        				
        				$("[data-toggle=popover]").popover('hide');
	        		},
	        		error : function(error) {
	        		}
	        	});
        	}
        	return false;
        }

        function contruirRelatorio(obj){
//         	console.log(obj);
        	var trs = "";
        	for (i in obj){
        		trs += 	"<tr class='linha_relatorio'>" +
			        		"<td class=\"col-md-1\"><center>" + obj[i].descricao_origem + "</center></td>" +
        					"<td class=\"col-md-1\"><center>" + obj[i].identificador + "</center></td>" +
        					
        					"<td class=\"col-md-1\"><center>" + obj[i].codigo_origem + "</a></center></td>" +
        					"<td class=\"col-md-1\"><center>" + obj[i].codigo_pagamento + "</a></center></td>" +
        					"<td class=\"col-md-1\"><center>";
	        					if (obj[i].data_criacao_origem){
		        					var array = obj[i].data_criacao_origem.split("-");
	        						var dt = new Date(array[1] + "-" + array[2] + "-" + array[0]);
        	                    	var d, m, mm;
        	                    	
        	                    	if (dt.getDate() <= 9) d = "0" + dt.getDate(); else d = dt.getDate();
        	                    	if ((dt.getMonth()+1) <= 9) m = "0" + (dt.getMonth()+1); else m = dt.getMonth()+1;
        	                    	if (dt.getMinutes() <= 9) mm = "0" + dt.getMinutes(); else mm = dt.getMinutes();
        	                    	trs +=  d + "/" + m + "/" + dt.getFullYear();
        						}
        					trs += "</center></td>" +
        					"<td class=\"col-md-1\"><center>";
	        					if (obj[i].data_hora_criacao){
	        						obj[i].data_hora_criacao = obj[i].data_hora_criacao.substring(0, 10);
		        					var array = obj[i].data_hora_criacao.split("-");
	        						var dt = new Date(array[1] + "-" + array[2] + "-" + array[0]);
	    	                    	var d, m, mm;
	    	                    	
	    	                    	if (dt.getDate() <= 9) d = "0" + dt.getDate(); else d = dt.getDate();
	    	                    	if ((dt.getMonth()+1) <= 9) m = "0" + (dt.getMonth()+1); else m = dt.getMonth()+1;
	    	                    	if (dt.getMinutes() <= 9) mm = "0" + dt.getMinutes(); else mm = dt.getMinutes();
	    	                    	trs +=  d + "/" + m + "/" + dt.getFullYear();
	    						}
	    					trs += "</center></td>" +
	    					"<td class=\"col-md-1\"><center id=\"status" + obj[i].id_transacao + "\">" + obj[i].estado + "</center></td>" +
	    					"<td class=\"col-md-1\"><center>" + obj[i].descricao_forma_pagamento + "</center></td> " +
        					"<td class=\"col-md-1\"><center>" + obj[i].nome_operadora + " - Ag: " + obj[i].numero_agencia + "- CC: " + obj[i].numero_conta + " - Cart: " + obj[i].codigo_carteira + "</center></td>" +
        					
        					
        					
							"<td class=\"col-md-1\" align='right'>R$ ";
        						obj[i].valor_transacao = parseFloat(obj[i].valor_transacao).toFixed(2);
        						trs += obj[i].valor_transacao.replace(".", ",") +
        					"</td>" +
        					"<td class=\"col-md-1\"><center>";
	        					if (obj[i].data_pagamento){
		        					var array = obj[i].data_pagamento.split("-");
	        						var dt = new Date(array[1] + "-" + array[2] + "-" + array[0]);
	    	                    	var d, m, mm;
	    	                    	
	    	                    	if (dt.getDate() <= 9) d = "0" + dt.getDate(); else d = dt.getDate();
	    	                    	if ((dt.getMonth()+1) <= 9) m = "0" + (dt.getMonth()+1); else m = dt.getMonth()+1;
	    	                    	if (dt.getMinutes() <= 9) mm = "0" + dt.getMinutes(); else mm = dt.getMinutes();
	    	                    	trs +=  d + "/" + m + "/" + dt.getFullYear();
	    						}
	    					trs += "</center></td>" +
        					"<td class=\"col-md-1\" align='right'>R$ ";
        						if (obj[i].valor_pago){
	    							obj[i].valor_pago = parseFloat(obj[i].valor_pago).toFixed(2);
		    						trs += obj[i].valor_pago.replace(".", ",");
        						}else {
									obj[i].valor_pago = parseFloat(0).toFixed(2);
		    						trs += obj[i].valor_pago.replace(".", ",");
        						}
	    					trs += "</td>" +
	    					
        					"<td class=\"col-md-1\" align=\"right\">" +
        						"<div class=\"btn-group\">" +
								"<button type=\"button\"" +
									"class=\"btn btn-success dropdown-toggle\"" +
									"data-toggle=\"dropdown\" aria-haspopup=\"true\"" +
									"aria-expanded=\"false\">" +
									"Ações <span class=\"caret\"></span>" +
								"</button>" +
								"<ul class=\"dropdown-menu\">";
// 								console.log(obj[i].fk_status);
								switch (obj[i].fk_status){
									case "1": //Solicitação de registro
										trs += "<li><a data-idt=\"" + obj[i].id_transacao + "\" data-st=\"4\" data-rem=\"" + obj[i].fk_arquivo + "\" onclick=\"solicitarAlteracaoStatus(this)\" href=\"#\">Cancelar</a></li>";
										trs += "<li role=\"separator\" class=\"divider\"></li>";
										trs += "<li><a data-idt=\"" + obj[i].id_transacao + "\" onclick=\"imprimirBoleto(this)\" href=\"#\">Boleto</a></li>";
										break;
									case "2": //Registrado
										trs += "<li><a data-idt=\"" + obj[i].id_transacao + "\" data-st=\"11\" data-rem=\"" + obj[i].fk_arquivo + "\" onclick=\"solicitarAlteracaoStatus(this)\" href=\"#\">Cancelar</a></li>";
										trs += "<li><a data-idt=\"" + obj[i].id_transacao + "\" data-st=\"12\" data-rem=\"" + obj[i].fk_arquivo + "\" onclick=\"solicitarAlteracaoStatus(this)\" href=\"#\">Protestar</a></li>"; 
										trs += "<li role=\"separator\" class=\"divider\"></li>";
										trs += "<li><a data-idt=\"" + obj[i].id_transacao + "\" onclick=\"imprimirBoleto(this)\" href=\"#\">Boleto</a></li>";
										break;
									case "4":
										trs += "<li class=\"dropdown-header\">Sem ações possíveis</li>";
										break;
									case "8": //Pendente
										trs += "<li><a data-idt=\"" + obj[i].id_transacao + "\" data-st=\"4\" data-rem=\"" + obj[i].fk_arquivo + "\" onclick=\"solicitarAlteracaoStatus(this)\" href=\"#\">Cancelar</a></li>";
										trs += "<li role=\"separator\" class=\"divider\"></li>";
										trs += "<li><a data-idt=\"" + obj[i].id_transacao + "\" onclick=\"imprimirBoleto(this)\" href=\"#\">Boleto</a></li>";
										break;
									default:
										trs += "<li class=\"dropdown-header\">Sem ações possíveis</li>";
										trs += "<li role=\"separator\" class=\"divider\"></li>";
										trs += "<li><a data-idt=\"" + obj[i].id_transacao + "\" onclick=\"imprimirBoleto(this)\" href=\"#\">Boleto</a></li>";
										break;
								}
								
								trs += "</ul>" +
								"</div>" +
							"</td>" + 
						"</tr>";
        	}
        	
        	//TOTALIZADORES
        	trs += 	"<tr class='info'>" +
        				"<td colspan=7></td>" + 
        				"<td class=\"col-md-1\" align='right'><b>TOTAL</b></td>" +
        				"<td colspan=2 align=\"right\">R$ "; 
        					var total = 0;
        					for (i in obj){
        						total += parseFloat(obj[i].valor_transacao);
        					}
        					total = total.toFixed(2);
        					trs += String(total).replace(".", ",") +
        					"</td>" + 
        				"<td colspan=2 align=\"right\">R$ "; 
        					var totalPago = 0;
        					for (i in obj){
        						totalPago += parseFloat(obj[i].valor_pago);
        					}
        					totalPago = totalPago.toFixed(2);
        					trs += String(totalPago).replace(".", ",") +
        					"</td>" +
        				"<td></td>";
        			"</tr>"; 
        	return trs;
        }
	</script>
</body>
</html>