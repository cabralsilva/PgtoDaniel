<?php
	include '../controllers/remessaController.php';
	
	$rc = new RemessaController();
	$rc->buscarOperadorasBoleto ();
?>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet"
	href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.10.0/css/bootstrap-select.min.css">

<!-- Latest compiled and minified JavaScript -->
<script
	src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.10.0/js/bootstrap-select.min.js"></script>
<!-- Modal Remessa-->
<div class="modal fade" id="modalRemessa" tabindex="-1" role="dialog"
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
						<label for="selecaoOperadoraRemessa">Banco</label> <select
							class="form-control selectpicker" id="selecaoOperadoraRemessa"
							title="Selecione..." data-width="100%">
							
								<?php foreach ($_REQUEST["lstOperadoras"] as $operadora){?>
									<optgroup label="<?= $operadora["nomeOperadora"]?>">
										<?php foreach ($operadora["contas"] as $contas){?>
											<option title="<?= $operadora["nomeOperadora"]?> - Ag: <?= $contas["agencia"]?> - CC:<?= $contas["conta"]?> - Cart: <?= $contas["carteira"]?>" value="<?= $contas["idOperadoraEmp"]?>">
												Carteira: <?= $contas["carteira"]?> - Ag.: <?= $contas["agencia"]?> - Conta: <?= $contas["conta"]?>
											</option>
										<?php }?>
										
									</optgroup>
									
								<?php }?>
							</select>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
				<button type="button" class="btn btn-primary" onclick="gerarRemessa('<?= BaseProjeto ?>')">Gerar remessa</button>
			</div>
		</div>
	</div>
</div>

<!-- Modal Retorno-->
<div class="modal fade" id="modalRetorno" tabindex="-1" role="dialog"
	aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"
					aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title" id="myModalLabel">Arquivo retorno</h4>
			</div>
			<div class="modal-body">
				<p>Selecione o arquivo enviado pelo banco...</p>
				<form class="form-inline" id="formulario" role="form" method="post" enctype="multipart/form-data">
					<div class="row">
						<div class="col-md-4">
							<input type="text" name="servico" value="carregarRetorno" class="hidden" />
		  					<label for="exampleInputFile">Arquivo de retorno</label>
		  					<input id="arquivo" name="arquivo" type="file" accept=".RET" required="required"/>
						</div>
						<div class="col-md-6">
							<button type="button" class="btn btn-default" onclick="carregarRetorno()">Carregar</button>
						</div>
					</div>
					<br>
					<div id="tableRetorno" class="row">
						
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button id="btnProcessarRetorno" type="button" class="btn btn-primary hidden" onclick="processarRetorno()">Processar</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	var listaRegistros;
	
	$('#modalRemessa').on('show.bs.modal', function () {
	  	$('.modal-body').css('overflow-y', 'auto'); 
	  	$('#datepicker').focus();
	});
	$('#modalRemessa').on('hidden.bs.modal', function () {
		location.reload();
	});
	$('#modalRetorno').on('hidden.bs.modal', function () {
		location.reload();
	});
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

	function gerarRemessa(baseProjeto) {
		
		var dataRemessa = document.getElementById('diaRemessa').value;
		var operadoras = "";
		$("#selecaoOperadoraRemessa option:selected").each(function(ind, elem) {
			operadoras += elem.value;
		});
//		console.log(operadoras);
		$.ajax({
			async : true,
			type : "POST",
			url : baseProjeto + "/controllers/remessaController.php",
			data : {
				servico : "gerarRemessaDia",
				dataRemessa : dataRemessa,
				banco : operadoras
			},
			success : function(e) {
// 				console.log(e);
				if (e !== 'null') {
					var obj = JSON.parse(e);
// 					console.log(obj);
					switch (obj[0]) {
						case 0:
							break;
						case 1:
							var link = document.createElement('a');
	        				link.href = baseProjeto + "/controllers/"+obj[2]+"/"+obj[1];
	        				link.download = obj[1];
	        				document.body.appendChild(link);
	        				link.click();
							break;
						case 2:
							alert("Selecione boletos somente de um banco!");
							break;
						case 3:
							alert("Um arquivo remessa já foi gerado nesta data. O mesmo arquivo será gerado novamente!");
							var link = document.createElement('a');
							link.href = baseProjeto + "/controllers/"+obj[2]+"/"+obj[1];
							link.download = obj[1];
							document.body.appendChild(link);
							link.click();
							break;
						default:
							break;
					}
				} else
					alert("Nenhum boleto encontrado nesta data");
			},
			error : function(error) {
				// console.log(eval(error));
			}
		});
	}

	function carregarRetorno(){
		if ($('#arquivo').val() != "")
			$("#formulario").submit();
		else
			alert("Selecione o arquivo retorno enviado pelo banco!");
	}

	function processarRetorno(){
		$.ajax({
	    	url : "<?= BaseProjeto ?>/controllers/retornoController.php",
	        type: 'POST',
	        data: {
		        servico: "processarTransacoes",
		        lstTransacoes: JSON.stringify(listaRegistros)
	        },
	        success: function (e) {
	        	if (e !== 'null') {
					var obj = JSON.parse(e);
					switch (obj[0]) {
						case 1:
							$('#modalRetorno').modal('hide');
			 	        	location.reload();
							break;
						case 2:
							alert("Este retorno já foi processado anteriormente no sistema!");
							break;
						default:
							break;
					}
				} else
					alert("Ops, houve uma falha. Tente novamente mais tarde!");
	        }
	    });
	}
	
	$("#formulario").submit(function () {
	    var formData = new FormData(this);

	    $.ajax({
	    	url : "<?= BaseProjeto ?>/controllers/retornoController.php",
	        type: 'POST',
	        data: formData,
	        success: function (data) {
	            console.log(data);
	            var obj = JSON.parse(data);
	            console.log(obj);
	            listaRegistros = obj;
    			if (obj.transacoes.length == 0){
        			$("#tableRetorno").html("<table id=\"transacoes\" class=\"table table-hover table-striped \"><tr><td colspan='12' align='center'>Nenhum Registro encontrado!</td></tr></tbody></table>");
    				$("#btnProcessarRetorno").addClass("hidden");
        		}else{
    				$("#tableRetorno").html(construirLista(obj));
    				$("#btnProcessarRetorno").removeClass("hidden");
        		}
	        },
	        cache: false,
	        contentType: false,
	        processData: false
	    });
	    return false;
	});

	function construirLista(obj){
		var table = "<table id=\"transacoes\" class=\"table table-hover table-striped \">" +
						"<thead>" +
						"<tr>" +
							"<th class=\"col-md-1\"><center>NOSSO NÚMERO</center></th>" +
							"<th class=\"col-md-1\"><center>NOME</center></th>" +
							"<th class=\"col-md-1\"><center>DATA VENC.</center></th>" +
							"<th class=\"col-md-1\"><center>VALOR</center></th>" +
							"<th class=\"col-md-1\"><center>DATA PAGTO</center></th>" +
							"<th class=\"col-md-1\"><center>PAGO</center></th>" +
							"<th class=\"col-md-1\"><center>DATA CRÉDITO</center></th>" +
							"<th class=\"col-md-1\"><center>TAXA</center></th>" +
							"<th class=\"col-md-1\"><center>DESCRIÇÃO</center></th>" +
// 							"<th class=\"col-md-1\"><center></center></th>" +
						"</tr>" +
					"</thead>" +
					"<tbody id=\"conteudo-relatorio\">";
    	
    	var trs = "";
    	for (i in obj.transacoes){
        	
    		trs += "<tr class=\"linha_relatorio\">" +
						"<td class=\"col-md-1 valign\"><center>" + obj.transacoes[i].nosso_numero + "</center></td>" +
						"<td class=\"col-md-1 valign\"><center>" + obj.transacoes[i].nome_pagador + "</center></td>" +
						"<td class=\"col-md-1 valign\"><center>";
				    		if (obj.transacoes[i].data_vencimento){
				    			var from = obj.transacoes[i].data_vencimento.split("/");
				    			var dt = new Date(from[2], from[1] - 1, from[0]);
				            	var d, m;
				            	if (dt.getDate() <= 9) d = "0" + dt.getDate(); else d = dt.getDate();
				            	if ((dt.getMonth()+1) <= 9) m = "0" + (dt.getMonth()+1); else m = dt.getMonth()+1;
				            	trs +=  d + "/" + m + "/" + dt.getFullYear();
							} 
						trs += "</center></td>" +
						"<td class=\"col-md-1 valign\"><center>R$ ";
						obj.transacoes[i].valor_titulo = parseFloat(obj.transacoes[i].valor_titulo).toFixed(2);
						trs += obj.transacoes[i].valor_titulo.replace(".", ",") + "</center></td>" +
						"<td class=\"col-md-1 valign\"><center>";
							if (obj.transacoes[i].valor_pago != 0){
					    		if (obj.transacoes[i].data_pagamento){
					    			var from = obj.transacoes[i].data_pagamento.split("/");
					    			var dt = new Date(from[2], from[1] - 1, from[0]);
					            	var d, m;
					            	if (dt.getDate() <= 9) d = "0" + dt.getDate(); else d = dt.getDate();
					            	if ((dt.getMonth()+1) <= 9) m = "0" + (dt.getMonth()+1); else m = dt.getMonth()+1;
					            	trs +=  d + "/" + m + "/" + dt.getFullYear();
								} 
							}else trs += " -- ";
						trs += "</center></td>" +
						"<td class=\"col-md-1 valign\"><center>R$ ";
						obj.transacoes[i].valor_pago = parseFloat(obj.transacoes[i].valor_pago).toFixed(2);
						trs += obj.transacoes[i].valor_pago.replace(".", ",") +"</center></td>" +
						"<td class=\"col-md-1 valign\"><center>";
				    		if (obj.transacoes[i].data_credito){
				    			var from = obj.transacoes[i].data_credito.split("/");
				    			var dt = new Date(from[2], from[1] - 1, from[0]);
				            	var d, m;
				            	if (dt.getDate() <= 9) d = "0" + dt.getDate(); else d = dt.getDate();
				            	if ((dt.getMonth()+1) <= 9) m = "0" + (dt.getMonth()+1); else m = dt.getMonth()+1;
				            	trs +=  d + "/" + m + "/" + dt.getFullYear();
							} 
						trs += "</center></td>" +
						"<td class=\"col-md-1 valign\"><center>R$ ";
						obj.transacoes[i].taxa = parseFloat(obj.transacoes[i].taxa).toFixed(2);
						trs += obj.transacoes[i].taxa.replace(".", ",") +"</center></td>" +
						"<td class=\"col-md-1 valign\"><center>" + obj.transacoes[i].comando + " - " + obj.transacoes[i].descricao_comando + "</center></td>" +
					"</tr>";
			
    	}
    	table += trs + "</tbody></table>";
    	return table;
	}
</script>