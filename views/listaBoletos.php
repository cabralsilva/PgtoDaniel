<?php
	require_once '../util/constantes.php';
	include '../controllers/listaBoletosController.php';
	//session_start ();
	date_default_timezone_set ( 'America/Sao_Paulo' );
	getOperadorasBoleto($_SESSION["dados_acesso"][0]["CODIGO"]);
	//print_r($_REQUEST);
?>
<!DOCTYPE html>
<html>
<title>Área administrativa Cielo</title>
<head>
<!--<meta http-equiv="Content-Type" content="text/html; charset=utf-8">-->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<HTTP-EQUIV ="PRAGMA" CONTENT="NO-CACHE">

</head>
<body>
	<script
		src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	<script src="../resources/bootstrap/js/bootstrap.min.js"></script>
	<script src="../resources/js/moment.js"></script>
	<script src="../resources/controles.js"></script>

	<link href="../resources/bootstrap/css/bootstrap.min.css"
		rel="stylesheet">
	<link href="../resources/css/style.css" rel="stylesheet"
		type="text/css">

	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet"
		href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.10.0/css/bootstrap-select.min.css">

	<!-- Latest compiled and minified JavaScript -->
	<script
		src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.10.0/js/bootstrap-select.min.js"></script>

	<!-- DATEPICKER -->
	<script src="../resources/js/datepicker/js/bootstrap-datepicker.js"></script>
	<script
		src="../resources/js/datepicker/locales/bootstrap-datepicker.pt-BR.min.js"></script>
	<link href="../resources/js/datepicker/css/bootstrap-datepicker.css"
		rel="stylesheet" type="text/css">

	<script type="text/javascript">
			function gerarRemessa(){
				var dataRemessa = document.getElementById('diaRemessa').value;
				var operadoras = "";
	        	$("#selecaoOperadora2 option:selected").each(function(ind, elem) {
	        		operadoras += elem.value;
	        	});
	        	console.log(operadoras);
	        	$.ajax({
	        		async : true,
	        		type : "POST",
	        		url : "<?= BaseProjeto ?>/controllers/listaBoletosController.php",
	        		data : {
	        			servico: "gerarRemessaDia",
	        			dataRemessa: dataRemessa,
	        			banco: operadoras 
	        		},
	        		success : function(e) {
						if (e !== 'null'){
							console.log(e);
							
		        			var obj = JSON.parse(e);
		        			//console.log(obj);
		        			switch (obj[0]){
			        			case 0:
				        			break;
			        			case 1:
				        			break;
			        			case 2:
			        				alert("Selecione boletos somente de um banco!");
				        			break;
			        			case 3:
			        				alert("Um arquivo remessa já foi gerado nesta data. O mesmo arquivo será gerado novamente!");
			        				var link = document.createElement('a');
			        				link.href = "<?= BaseProjeto ?>/controllers/"+obj[1];
			        				link.download = obj[1];
			        				document.body.appendChild(link);
			        				link.click();
				        			break;
				        		default:
					        		break;
		        			}
						}else alert("Nenhum boleto encontrado nesta data");
	        		},
	        		error : function(error) {
	        			// console.log(eval(error));
	        		}
	        	});
			}
        
	        function buscarBoletosFiltro() {
	        	var operadoras = "";
	        	$("#selecaoOperadora option:selected").each(function(ind, elem) {
	        		operadoras += elem.value + ",";
	        	});
	
	        	var status = "";
	        	$("#selecaoStatus option:selected").each(function(ind, elem) {
	        		status += elem.value + ",";
	        	});
	        	
	        	var dtai = document.getElementById('dataPeriodoI').value;
	        	var dtaf = document.getElementById('dataPeriodoF').value;
	        	
	        	var codPedido = document.getElementById('codPedido').value;
	        	var valorTransacao = document.getElementById('valorBrutoTransacao').value;
	        	var codigoTransacao = document.getElementById('identificadorTransacao').value;

	        	console.log("Operadoras: " + operadoras); 
	        	console.log("Status: " + status);
	        	console.log("PeriodoI: " + dtai);
	        	console.log("PeriodoF: " + dtaf);
	        	console.log("CodPedio: " + codPedido);
	        	console.log("Valor bruto: " + valorTransacao);
	        	console.log("Identificador: " + codigoTransacao);
	        	if (dtai == "") alert("Preencha o periodo desejado!");
	        	
	        	else{
		        	$.ajax({
		        		async : true,
		        		type : 'POST',
		        		url : "<?= BaseProjeto ?>/controllers/listaBoletosController.php",
		        		data : {
		        			servico: "buscarBoletosFiltro",
		        			dataPeriodoI : dtai,
		        			dataPeriodoF : dtaf,
		        			operadoras : operadoras,
		        			status : status,
		        			codigoPedido : codPedido,
		        			valorTransacao : valorTransacao,
		        			codTransacao : codigoTransacao
		        		},
		        		success : function(e) {
		
		        			//console.log(e);
		        			var obj = JSON.parse(e);
		        			// console.log(obj.length);
		        			if (obj.length == 0)
		        				$("#conteudo-relatorio")
		        						.html(
		        								"<tr><td colspan='12' align='center'>Nenhum Registro encontrado!</td></tr>");
		        			else
		        				$("#conteudo-relatorio").html(construirListaBoleto(obj));
		        			// $("#conteudo-relatorio").html(table);
		
		        		},
		        		error : function(error) {
		        			// console.log(eval(error));
		        		}
		        	});
	        	}
	        	return false;
	        }

	        function construirListaBoleto(obj){
	        	
	        	var trs = "";
	        	for (i in obj){
	        		trs += 	"<tr class='linha_relatorio'>" +
	        					"<td class=\"col-md-1\"><center>" +  ((obj[i].id_transacao != null)? obj[i].id_transacao : "") + "</center></td>" +
	        					"<td class=\"col-md-2\"><center>" + obj[i].nome_operadora + "</center></td>" +
	        					"<td class=\"col-md-1\"><center><a href=\"lista.php?pedido=" + obj[i].fk_pedido + "\">" + obj[i].fk_pedido + "</a></center></td>" +
	        					"<td class=\"col-md-2\"><center>";
		        					if (obj[i].data_hora_pedido){
	        							var dt = new Date(obj[i].data_hora_pedido);
	        	                    	var d, m, mm;
	        	                    	if (dt.getDate() <= 9) d = "0" + dt.getDate(); else d = dt.getDate();
	        	                    	if ((dt.getMonth()+1) <= 9) m = "0" + (dt.getMonth()+1); else m = dt.getMonth()+1;
	        	                    	if (dt.getMinutes() <= 9) mm = "0" + dt.getMinutes(); else mm = dt.getMinutes();
	        	                    	trs +=  d + "/" + m + "/" + dt.getFullYear() + " " + dt.getHours() + ":" + mm;
	        						}
	        					trs += "</center></td>" +
	        					"<td class=\"col-md-2\"><center>";
	        						switch (obj[i].status_geral) {
	                                    case "0":
	                                        trs += "Pendente"; 
	                                        break;
	                                    case "1":
	                                        trs += "Autenticada";
	                                        break;
	                                    case "2":
	                                        trs += "Não Autenticada";
	                                        break;
	                                    case "3":
	                                        trs += "Autorizada";
	                                        break;
	                                    case "4":
	                                        trs += "Não Autorizada";
	                                        break;
	                                    case "5":
	                                        trs += "Capturada";
	                                        break;
	                                    case "6":
	                                        trs += "Cancelada";
	                                        break;
	                                    case "7":
	                                        trs += "Indefinida";
	                                        break;
	                                    case 0:
	                                        trs += "Pendente"; 
	                                        break;
	                                    case 1:
	                                        trs += "Autenticada";
	                                        break;
	                                    case 2:
	                                        trs += "Não Autenticada";
	                                        break;
	                                    case 3:
	                                        trs += "Autorizada";
	                                        break;
	                                    case 4:
	                                        trs += "Não Autorizada";
	                                        break;
	                                    case 5:
	                                        trs += "Capturada";
	                                        break;
	                                    case 6:
	                                        trs += "Cancelada";
	                                        break;
	                                    case 7:
	                                        trs += "Indefinido";
	                                        break;
	                                    default:
	                                    	trs += "Indefinido";
	                                    	break;
	                                }
	        						
	        					trs += "</center></td>" +
								"<td class=\"col-md-2\" align='right'>R$ ";
	        						if (obj[i].valor_transacao.indexOf(".") == -1) {
	        							var decimais = obj[i].valor_transacao.substr(-2, 2);
	        							obj[i].valor_transacao = obj[i].valor_transacao.substr(0, obj[i].valor_transacao.length-2);
	        							obj[i].valor_transacao = (parseFloat(obj[i].valor_transacao) + parseFloat(decimais)/100).toFixed(2);
	        						}
	        						obj[i].valor_transacao = parseFloat(obj[i].valor_transacao).toFixed(2);
	        						//alert(obj[i].valor_transacao);
	        						trs += obj[i].valor_transacao.replace(".", ",") +
	        					"</td>" +
	        					"<td class=\"col-md-2\" align=\"right\">" +
        							"<button type=\"button\" class=\"btn btn-success dropdown-toggle\" data-toggle=\"dropdown\" >Protestar </button>" +
	        						"<button type=\"button\" class=\"btn btn-success dropdown-toggle\" data-toggle=\"dropdown\" >Boleto </button>" +
	        		    		"</td>" + 
							"</tr>";
	        	}
	        	trs += 	"<tr class='info'>" +
	        				"<td colspan=4></td>" + 
	        				"<td align='right'><b>TOTAL</b></td>" +
	        				"<td align='right'>R$ "; 
	        					var total = 0;
	        					for (i in obj){
	        						total += parseFloat(obj[i].valor_transacao);
	        					}
	        					total = total.toFixed(2);
	        					trs += String(total).replace(".", ",") +
	        					"</td>" + 
	        				"<td align='right' style='width: 9%;'>R$ "; 
	        					var totalliquido = 0;
	        					for (i in obj){
	        						totalliquido += parseFloat(obj[i].valor_liquido);
	        					}
	        					totalliquido = totalliquido.toFixed(2);
	        					trs += String(totalliquido).replace(".", ",") +
	        					"</td>" +
	        			"</tr>"; 
	        	return trs;
	        }
			$(document).ready(function() {
				

			    $('.input-daterange').datepicker({
			    	endDate: '0d', 
				    format: "dd/mm/yyyy",
				    weekStart: 0,
				    language: "pt-BR",
				    multidate: false,
				    daysOfWeekHighlighted: "0",
				    todayHighlight: true,
				    autoclose: true
				});

			    
			});

			$('#myModal').on('shown.bs.modal', function () {
			  	$('.modal-body').css('overflow-y', 'auto'); 
			  	$('#datepicker').focus()
			})
        </script>

	<br>
	<div class="container containerrelatorio">
		<div class="panel-group" id="accordion">
			<div class="panel panel-info">
				<div class="panel-heading">
					<h3 class="panel-title">
						<span class="nome-empresa"><?php echo $_SESSION["dados_acesso"][0]["NOME"] ?></span>
						<span class="nome-pagina"> Boletos</span> <span class="usuario">
							<div align="right">Funcionário logado: <?php echo $_SESSION["dados_acesso"][0]["nome_usuario"]?>
							    	<div class="btn-group">
									<button type="button" class="btn btn-success dropdown-toggle"
										data-toggle="dropdown" aria-haspopup="true"
										aria-expanded="false">
										Opções <span class="caret"></span>
									</button>
									<ul class="dropdown-menu">
										<li><a href="../buscardados_transacao.php">Pagamentos agrupados</a></li>
										<li><a href="formtransacao.php">Envio manual</a></li>
										<li role="separator" class="divider"></li>
										<li><a href="../../modulos.php">Sistemas</a></li>
										<li><a href="../../logout.php">Logout</a></li>
									</ul>
								</div>
							</div>
						</span>
					</h3>
				</div>
				<div class="panel-body">
					<div align="right">
						<button type="button" class="btn btn-info " id="btnGerarRemessa"
							data-toggle="modal" data-target="#myModal">

							<!-- 							onclick="gerarRemessa()" -->
							Remessa <span class="glyphicon glyphicon-th-list"></span>
						</button>
						<!-- 						<button type="button" class="btn btn-primary btn-lg" -->
						<!-- 							data-toggle="modal" data-target="#myModal">Launch demo modal</button> -->
<!-- 						<button type="button" class="btn btn-success " id="btnExportExcel" -->
<!-- 							onclick="window.location.href='exportexcel.php'"> -->
<!-- 							Excel <span class="glyphicon glyphicon-export"></span> -->
<!-- 						</button> -->
					</div>
					<div id="tableTransacoes">
						<table id="transacoes" class="table table-hover table-striped ">
							<thead>
								<tr>
									<th class="col-md-1"><center>
											<input id="identificadorTransacao" type="text"
												class="form-control"
												aria-label="Text input with multiple buttons" min="0"
												pattern="\d*" step="1" /> IDENTIFICADOR
										</center></th>
									<th class="col-md-2"><center>
											<select class="form-control selectpicker"
												id="selecaoOperadora" multiple
												data-selected-text-format="count" title="Todos"
												data-width="100%" multiple>
												<?php foreach ($_REQUEST["lstOperadoras"] as $operadora){?>
													<option value="<?= $operadora["id_operadora"]?>"><?= $operadora["nome_operadora"]?></option>
												<?php }?>
											</select> BANCO
										</center></th>
									<th class="col-md-1"><center>
											<input id="codPedido" type="number" class="form-control"
												aria-label="Text input with multiple buttons" min="0"
												pattern="\d*" step="1" /> PEDIDO
										</center></th>
									<th class="col-md-2"><center>
											<div class="input-daterange input-group" id="datepicker">
												<input type="text" class="input-sm form-control"
													id="dataPeriodoI" /> <span
													class="input-group-addon labelrangedate">até</span> <input
													type="text" class="input-sm form-control" id="dataPeriodoF" />
											</div>
											PERÍODO
										</center></th>
									<th class="col-md-1"><center>
											<select class="form-control selectpicker" id="selecaoStatus"
												multiple data-selected-text-format="count" title="Todos"
												data-width="100%" multiple>
												<option value="0">Pendentes</option>
												<option value="1">Autenticada</option>
												<option value="2">Não Autencada</option>
												<option value="3">Autorizadas</option>
												<option value="4">Não Autorizadas</option>
												<option value="5">Capturadas</option>
												<option value="6">Canceladas</option>
											</select> STATUS
										</center></th>
									<th class="col-md-2">
										<div class="input-group">
											<span class="input-group-addon labelcifrao">R$</span> <input
												id="valorBrutoTransacao" type="text"
												placeholder="use > ou <" class=" form-control
												inputvalortransacao" aria-label="Valor">
										</div>
										<center>BRUTO</center>
									</th>
									<th class="col-md-3" style="text-align: right; vertical-align: top;">
										
									</th>
									<th class="col-md-1"
										style="text-align: right; vertical-align: top;">
										<button style="width: 100%;" onclick="buscarBoletosFiltro()"
											id="btnFiltrar" type="button" class="btn btn-success">
											<span class="glyphicon glyphicon-filter"></span>
										</button>
									</th>
								</tr>
							</thead>
							<tbody id="conteudo-relatorio">
                   					<?php foreach ( $_SESSION ["listaTransacoesBoletos"] as $boleto ) {?>
	                   					<tr class='linha_relatorio'>
									<td class="col-md-1"><center><?= $boleto["id_transacao"]?></center></td>
									<td class="col-md-2"><center><?= $boleto["nome_operadora"] ?></center></td>
									<td class="col-md-1"><center>
											<a href='lista.php?pedido=<?= $boleto['fk_pedido']?>'><?= $boleto["fk_pedido"]?></a>
										</center></td>
									<td class="col-md-2"><center><?= date("d/m/Y", strtotime($boleto["data_hora_pedido"]))?></center></td>
									<td class="col-md-2"><center>
									<?php switch ($boleto ['status_geral']) {
											case 0 :
												echo "Pendente";
												break;
											case 1 :
												echo "Autenticada";
												break;
											case 2 :
												echo "Não Autenticada";
												break;
											case 3 :
												echo "Autorizada";
												break;
											case 4 :
												echo "Não Autorizada";
												break;
											case 5 :
												echo "Capturada";
												break;
											case 6 :
												echo "Cancelada";
												break;
											case 7 :
												echo "Indefinida";
												break;
											default :
												echo "Indefinida";
												break;
										}?>
									</center></td>
									<td class="col-md-2" align='right'>R$ <?= number_format($boleto['valor_transacao'], 2, ',', '.'); ?></td>
									<th class="col-md-3" style="text-align: right; vertical-align: top;">
										
									</th>
									<td class="col-md-2" align='right'>
										<button type="button" class="btn btn-success dropdown-toggle"
											data-toggle="dropdown" data-idt="<?= $boleto["id_transacao"]?>" onclick="imprimirBoleto(this)">Boleto</button>
									</td>
								</tr>
                   					<?php }?>
                   				<!-- <tr><td colspan='12' align='center'>Preencha os filtros!</td></tr> -->
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>


		<div id="table-loading"></div>
		<br> Obs.: .
	</div>
	
	<!-- Modal -->
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog"
		aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"
						aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title" id="myModalLabel">Arquivo remessa</h4>
				</div>
				<div class="modal-body">
					<p>Selecione o dia e o banco para gerar a remessa&hellip;</p>
					<form role="form">
						<div class="form-group col-md-4">
							<label for="diaRemessa">Data</label>
							<div class="input-daterange input-group" id="datepicker">
								<input type="text" class="input-sm2 form-control" id="diaRemessa" />
							</div>
						</div>
						<div class="form-group col-md-6">
							<label for="selecaoOperadora2">Banco</label> <select
								class="form-control selectpicker" id="selecaoOperadora2"
								title="Selecione..." data-width="100%">
								<?php foreach ($_REQUEST["lstOperadoras"] as $operadora){?>
									<option value="<?= $operadora["id_operadora"]?>"><?= $operadora["nome_operadora"]?></option>
								<?php }?>
							</select>
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
					<button type="button" class="btn btn-primary" onclick="gerarRemessa()">Gerar remessa</button>
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript">
	
	</script>
</body>
</html>