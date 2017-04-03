<?php
	require_once '../util/constantes.php';
// 	include '../controllers/formTransacaoController.php';
	include '../controllers/formController.php';
	$fc = new FormController ();
	if (!isset($_SESSION["usuario_logado"])){
		$_SESSION["falha_login"] = "Autenticação necessária";
		header("location: " . BaseProjeto . "/");
	}
	date_default_timezone_set ( 'America/Sao_Paulo' );
	getOperadorasBoleto($_SESSION["dados_empresa"]["cod_empresa"]);
	getFormasPagamento();
?>
<!DOCTYPE html>
<html>
	
	<head>
		<title>Área administrativa </title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
		<script src="<?= BaseProjeto ?>/resources/bootstrap/js/bootstrap.min.js"></script>
		<script src="<?= BaseProjeto ?>/resources/js/moment.js"></script>
		<script src="<?= BaseProjeto ?>/resources/controles.js"></script>
	
		<link href="<?= BaseProjeto ?>/resources/bootstrap/css/bootstrap.min.css" rel="stylesheet">
		<link href="<?= BaseProjeto ?>/resources/css/style.css" rel="stylesheet" type="text/css">
	
		<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet"
			href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.10.0/css/bootstrap-select.min.css">
	
		<!-- Latest compiled and minified JavaScript -->
		<script
			src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.10.0/js/bootstrap-select.min.js"></script>
	
		<!-- DATEPICKER -->
		<script src="<?= BaseProjeto ?>/resources/js/datepicker/js/bootstrap-datepicker.js"></script>
		<script
			src="<?= BaseProjeto ?>/resources/js/datepicker/locales/bootstrap-datepicker.pt-BR.min.js"></script>
			
		<link href="<?= BaseProjeto ?>/resources/js/datepicker/css/bootstrap-datepicker.css"
			rel="stylesheet" type="text/css">
			
		<script src="<?= BaseProjeto ?>/resources/js/jquery-maskinput-1.1.4.js" defer></script>
		<script src="<?= BaseProjeto ?>/resources/js/jquery.maskMoney.min.js" defer></script>
		
		
			
	</head>
	<body>
		<div class="container containerrelatorio">
			<div class="panel-group" id="accordion">
				<div class="panel panel-info">
					<div class="panel-heading">
                    	<h3 class="panel-title">
							<?php include 'layout/header.php';?>
						</h3>
                    </div>
					<div class="panel-body">
						<div class="progress hidden">
							<div class="progress-bar progress-bar-striped active"
								role="progressbar" aria-valuenow="100" aria-valuemin="0"
								aria-valuemax="100" style="width: 100%">Carregando...</div>
						</div>
						<div class="row impressao hide">
							<div id="msgs" class="alert alert-info" role="alert"></div>
						</div>
						<button class="btn btn-info hidden successRegister" onclick="imprimirBoleto(this)">Imprimir boleto</button>
						<button class="btn btn-success hidden successRegister" onclick="newDocument()">Novo</button>
						<form id="frmTransaction">
							<div class="row">
								
								<div class="col-xs-2">
									<label for="origem">Origem</label>
									<select class="form-control selectpicker" id="origem" data-width="100%" title="Selecione..." disabled="true">
										<option value="0" selected>Avulso</option>
									</select>
								</div>
								<div class="col-xs-2">
									<label for="id-pedido">Código da origem</label><a href="#" data-toggle="tooltip" data-placement="top" title="Código do Pedido, Faturamento ou etc a que este pagamento pertence">   <span class='glyphicon glyphicon-alert'></a>
									<input id="id-pedido" type="number" class="form-control" disabled="true">
								</div>
								<div class="col-xs-2">
									<label for="id-pagamento">Código do pagamento</label><a href="#" data-toggle="tooltip" data-placement="top" title="Código que identifica o pagamento">   <span class='glyphicon glyphicon-alert'></a>
									<input id="id-pagamento" type="number" class="form-control" required>
								</div>
<!-- 								<div class="col-xs-2 hide"> -->
<!-- 									<label for="id-pagamento">Data do documento</label><a href="#" data-toggle="tooltip" data-placement="top" title="Data em que o documento foi criado">   <span class='glyphicon glyphicon-alert'></a> -->
<!-- 									<div class="input-daterange input-group" id="datepicker"> -->
<!-- 										<input type="text" class="form-control " id="dataDocumento"  required/> -->
<!-- 									</div> -->
<!-- 								</div> -->
							</div>
							<hr/>
							<div class="row linha-form">
								<div class="col-xs-2">
									<label for="forma-pagamento">Forma pagamento</label>
									<select class="form-control selectpicker" id="forma-pagamento" data-width="100%" title="Selecione..." required>
										<?php foreach ($_REQUEST["lstFormasPagamento"] as $formas){?>
											<option value="<?= $formas["id_forma_pagamento"]?>" <?= ($formas["ativo"] == 0)? "disabled": "" ?>><?= $formas["descricao_forma_pagamento"]?></option>
										<?php }?>
									</select>
								</div>
								<div class="col-xs-3">
									<label for="operador">Operador(a)</label>
									<select class="form-control selectpicker" id="operador" data-width="100%" title="Selecione..." required>
										
									</select>
								</div>
								<div class="col-xs-2">
									<label for="valor">Valor do documento</label><a href="#" data-toggle="tooltip" data-placement="top" title="Ex.: R$ 123.123,99">   <span class='glyphicon glyphicon-alert'></a>
									<div class="input-group">
										<span class="input-group-addon">R$</span>
										<input id="valor" class="form-control" type="text" required>
									</div>
								</div>
								<div class="col-xs-2">
									<label for="dataVencimentoDocumento">Data de vencimento</label>
									<div class="input-vencimento input-group" id="datepicker2">
										<input type="text" class="form-control " id="dataVencimentoDocumento" required/>
									</div>
								</div>
<!-- 								<div class="col-xs-1"> -->
<!-- 									<label for="num-parcelas">Parcelas</label><a href="#" data-toggle="tooltip" data-placement="top" title="Quantidade de parcelas">   <span class='glyphicon glyphicon-alert'></a> -->
<!-- 									<input id="num-parcelas" class="form-control" type="number" value=1 required> -->
<!-- 								</div> -->
<!-- 								<div class="col-xs-2"> -->
<!-- 									<label for="valor-parcela">Valor por parcela</label><a href="#" data-toggle="tooltip" data-placement="top" title="Ex.: R$ 123.123,99">   <span class='glyphicon glyphicon-alert'></a> -->
<!-- 									<div class="input-group"> -->
<!-- 										<span class="input-group-addon">R$</span> -->
<!-- 										<input id="valor-parcela" class="form-control" type="text" required> -->
<!-- 									</div> -->
<!-- 								</div> -->
							</div>
							<hr/>
							<div class="row linha-form">
								<div class="col-xs-2">
									<label for="nome_pagador">Nome pagador</label>
									<input id="nome_pagador" type="text" class="form-control" placeholder="Ex.: João da Silva" required>
								</div>
								<div class="col-xs-2">
									<label for="tipo-inscricao">Tipo inscrição pagador</label><a href="#" data-toggle="tooltip" data-placement="top" title="CPF/CNPJ">   <span class='glyphicon glyphicon-alert'></a>
									<div class="col-xs-4 inscricaopagador">
										<select class="form-control selectpicker" id="tipo-inscricao" data-width="100%" title="..." required>
												<option value="1">CPF</option>
												<option value="2">CNPJ</option>
										</select>
									</div>
									<div class="col-xs-8 inscricaopagador">
										<input type="text" class="form-control" id="inscricao-pagador"  placeholder="Inscrição pagador" required/>
									</div>
								</div>
								<div class="col-xs-1">
									<label for="cep_pagador">Cep</label>
									<input id="cep_pagador" type="text" class="form-control ceppagador" required>
								</div>
								<div class="col-xs-2">
									<label for="logradouro_pagador">Logradouro</label>
									<input id="logradouro_pagador" type="text" class="form-control" required>
								</div>
								<div class="col-xs-2">
									<label for="complemento_pagador">Complemento</label>
									<input id="complemento_pagador" type="text" class="form-control" required>
								</div>
								<div class="col-xs-1">
									<label for="num_pagador">Número</label>
									<input id="num_pagador" type="number" class="form-control" required>
								</div>
								
								
							</div>
							<div class="row linha-form">
								<div class="col-xs-2">
									<label for="bairro_pagador">Bairro</label>
									<input id="bairro_pagador" type="text" class="form-control" required>
								</div>
								<div class="col-xs-2">
									<label for="cidade_pagador">Cidade</label>
									<input id="cidade_pagador" type="text" class="form-control" required>
								</div>
								<div class="col-xs-1">
									<label for="uf_pagador">UF</label>
									<input id="uf_pagador" type="text" class="form-control" required>
								</div>
							</div>
							<hr/>
							<button class="btn btn-default send" onclick="sendTransaction()">Enviar</button>
						</form>
						
						
					</div>
				</div>
			</div>
		</div>
		<?php include 'layout/modais.php';?>
	<script type="text/javascript">
		var isAvulso = false;
		var valor, valorP;
		var qtdeP = 1;
		$(document).ready(function(){
		    $('[data-toggle="tooltip"]').tooltip();  
		    $('.input-daterange').datepicker({
		    	endDate: '0d', 
				startDate: '0d',
			    format: "dd/mm/yyyy",
			    weekStart: 0,
			    language: "pt-BR",
			    multidate: false,
			    daysOfWeekHighlighted: "0",
			    todayHighlight: true,
			    autoclose: true
			});
		    
		    $('.input-vencimento').datepicker({

		    	startDate: '0d', 
			    format: "dd/mm/yyyy",
			    weekStart: 0,
			    language: "pt-BR",
			    multidate: false,
			    daysOfWeekHighlighted: "0",
			    todayHighlight: true,
			    autoclose: true
			});
		    $('#inscricao-pagador').prop('disabled', true);
		    $("#cep_pagador").mask("99.999-999"); 
		    $("#uf_pagador").mask("aa"); 
		   
		    $("#valor").maskMoney({
		    	symbol:'R$ ', 
		    	showSymbol:true, 
		    	thousands:'.', 
		    	decimal:',', 
		    	symbolStay: true
		    });
		    
		    $("#valor-parcela").maskMoney({
		    	symbol:'R$ ', 
		    	showSymbol:true, 
		    	thousands:'.', 
		    	decimal:',', 
		    	symbolStay: true
		    });
			
		    $("#valor").on("blur",  function() {
		    	valor = $("#valor").val().replace(/\./g, "");
		    	valor = valor.replace(/,/g, '.');
// 			    var numP = $("#num-parcelas").val();
				var numP = qtdeP;
			    valorP = (valor / numP).toFixed(2);
			    var textoValorP = String(valorP).replace(/\./g, "");
// 			    $("#valor-parcela").val(textoValorP);
// 		        $("#valor-parcela").maskMoney('mask');
		    });
		    
		    $("#num-parcelas").on("blur",  function() {
		    	valor = $("#valor").val().replace(/\./g, "");
		    	valor = valor.replace(/,/g, '.');
// 			    var numP = $("#num-parcelas").val();
				var numP = qtdeP;
			    valorP = (valor / numP).toFixed(2);
			    var textoValorP = String(valorP).replace(/\./g, "");
// 			    $("#valor-parcela").val(textoValorP);
// 		        $("#valor-parcela").maskMoney('mask');
		    });
		    
		});
		
		$('#origem').on('changed.bs.select', function (e, clickedIndex) {
			var nomeOrigem = $(this).find("option:selected")[0].label;
			var codOrigem = $(this).find("option:selected")[0].value;
			if (codOrigem == 0) {
				$('#id-pedido').prop('disabled', true);
				isAvulso = true;
			}else {
				$('#id-pedido').prop('disabled', false);
				isAvulso = false;
			}
		});

		$('#forma-pagamento').on('changed.bs.select', function (e, clickedIndex) {
			var nomeFP = $(this).find("option:selected")[0].label;
			var codFP = $(this).find("option:selected")[0].value;

			$.ajax({
				async : true,
        		type : 'POST',
        		url : "<?= BaseProjeto ?>/controllers/formTransacaoController.php",
        		data : {
            		servico: "getOperadoras",
            		codFP: codFP
        		},
        		beforeSend: function() {
					$(".progress").removeClass("hidden");
        		},
        		success: function(e){
					var objOP = JSON.stringify(eval("("+e+")"));
					var objOP2 = JSON.parse(objOP);
					var operadoras = [];
					if (objOP2.CodStatus == 1){
						var html = "";
						for (var key in objOP2.Model) {
							var conta = {
									idOperadoraEmp: objOP2.Model[key]["id_operadora_empresa"],
									carteira: objOP2.Model[key]["codigo_carteira"],
									agencia: objOP2.Model[key]["numero_agencia"],
									conta: objOP2.Model[key]["numero_conta"]
							};
							var obj = {
									idOperadora: objOP2.Model[key]["id_operadora"],
									nomeOperadora: objOP2.Model[key]["nome_operadora"],
									contas: [conta]
							};

							var existe = operadoras.some(function(el, i){
							    return el.idOperadora === obj.idOperadora;
							});
							
							
							if (!existe){
								operadoras.push(obj);	
							}else{
								operadoras.forEach(function(el, i){
								    if(el.idOperadora === obj.idOperadora) {
								        el.contas.push(conta);
								    }
								});
							}
						}

						for (var ind in operadoras) {
							html += "<optgroup label='" + operadoras[ind].nomeOperadora + "'>";
							operadoras[ind].contas.forEach(function(valor, chave){
							    html += "<option title='" + operadoras[ind].nomeOperadora + " Ag: " + valor.agencia + "- CC: " + valor.conta + "- Cart: " + valor.carteira + "' value='" + valor.idOperadoraEmp + "'>Carteira: " + valor.carteira + " - Ag.: " + valor.agencia + " - Conta: " + valor.conta + "</option>";
							});
							html += "</optgroup>";
						}
						
						
						$("#operador").html(html);
						$("#operador").selectpicker('refresh');
					}
        			$(".progress").addClass("hidden");
        		}
			});
			//$("#operador").html('<option>city1</option><option>city2</option>').selectpicker('refresh');
		});

		
		$('#tipo-inscricao').on('changed.bs.select', function (e, clickedIndex) {
			var nomeInscricao = $(this).find("option:selected")[0].label;
			var codInscricao = $(this).find("option:selected")[0].value;
			if (codInscricao == 1) {
				$("#inscricao-pagador").mask("999.999.999-99");
				$('#inscricao-pagador').prop('disabled', false);
			}else if(codInscricao == 2){
				$("#inscricao-pagador").mask("99.999.999/9999-99");
				$('#inscricao-pagador').prop('disabled', false);
			}else $('#inscricao-pagador').prop('disabled', false);
		});

		$("#cep_pagador").on("blur",  function() {
			var cep = $(this).val().replace(/\./g, "");
			cep = cep.replace(/-/g, "");
			$.ajax({
				async : true,
        		type : 'POST',
        		url : "<?= BaseProjeto ?>/controllers/formTransacaoController.php",
        		data : {
            		servico: "getAddress",
            		cep: cep
        		},
        		beforeSend: function() {
					$(".progress").removeClass("hidden");
        		},
        		success: function(e){
        			var objOP = JSON.stringify(eval("("+e+")"));
					var objOP2 = JSON.parse(objOP);
					if (objOP2.CodStatus == 1){
						for (var key in objOP2.Model) {
							$("#logradouro_pagador").val(objOP2.Model[key]["Logradouro"]);
		        			$("#bairro_pagador").val(objOP2.Model[key]["Bairro"]);
		        			$("#cidade_pagador").val(objOP2.Model[key]["Cidade"]);
		        			$("#uf_pagador").val(objOP2.Model[key]["Uf"]);	
						}
					}
					$("#complemento_pagador").focus();
        			$(".progress").addClass("hidden");
        		}
			});
			
		});

		
		function boletos(){
			$.ajax({
				url: "<?= BaseProjeto ?>/controllers/listaBoletosController.php", 
				async: false,
				type: "POST",
				data:{
					servico: "buscarBoletos"
				},
				success: function(success){
					//console.log(success);
					window.location.href = "<?= BaseProjeto ?>/views/listaBoletos.php";
		    	}
	    	});
		}
		
		function sendTransaction(){
			var isValid = true;
			$("#frmTransaction :input.form-control").each(function(){
				if (($(this).val() == "") || ($(this).val() == null) || ($(this).val() == undefined)){
					if ($(this).attr("id") == "id-pedido"){
						if($("#origem").find("option:selected")[0].value != 0) isValid = false;
					}else isValid = false;
				}
			});
			if (isValid){
				$.ajax({
					async : true,
	        		type : 'POST',
	        		url : "<?= BaseProjeto ?>/controllers/formTransacaoController.php",
	        		data : {
	        			servico: "createTransacao",
	        			origem: $("#origem").find("option:selected")[0].value,
	        			codigoOrigem: $("#id-pagamento").val(),
	        			codigoPagamento: $("#id-pagamento").val(),
// 	        			dataDocumento: $("#dataDocumento").val(),
						dataDocumento: moment().format('D/M/Y'),
	        			formaPagamento: $("#forma-pagamento").find("option:selected")[0].value,
	        			operadoraEmpresa: $("#operador").find("option:selected")[0].value,
	        			valorDocumento: valor,
	        			dataVencimento: $("#dataVencimentoDocumento").val(),
// 	        			numParcelas: $("#num-parcelas").val(),
						numParcelas: qtdeP,
	        			valorParcelas: valorP,
	        			nomePagador: $("#nome_pagador").val(),
	        			tipoInscricaoPagador: $("#tipo-inscricao").find("option:selected")[0].value,
	        			inscricaoPagador: $("#inscricao-pagador").val(),
	        			cep: $("#cep_pagador").val(),
	        			logradouro: $("#logradouro_pagador").val(),
	        			complemento: $("#complemento_pagador").val(),
	        			numero: $("#num_pagador").val(),
	        			bairro: $("#bairro_pagador").val(),
	        			cidade: $("#cidade_pagador").val(),
	        			uf: $("#uf_pagador").val(),
	        			
	        		},
	        		beforeSend: function() {
						$(".progress").removeClass("hidden");
	        		},
	        		success : function(e) {
						
	        			//console.log(e);
	        			var objOP = JSON.stringify(eval("("+e+")"));
						var obj = JSON.parse(objOP);
	        			if(obj["CodStatus"] == 1){
							//console.log("Inserido com sucesso");
							//console.log(obj.Model);
							var info = document.getElementById("msgs");
							var pai = info.parentElement;
							$(pai).removeClass("hide");
							$(info).removeClass("alert-info");
							$(info).addClass("alert-success");
							info.innerHTML = "Documento enviado com sucesso. (" + obj.Model + ")";

							$(".successRegister").attr('data-idt', obj.Model);
							$(".successRegister").removeClass("hidden");
							$(".send").addClass("hidden");
		        		}else{
		        			var info = document.getElementById("msgs");
							var pai = info.parentElement;
							$(pai).removeClass("hide");
							$(info).removeClass("alert-info");
							$(info).addClass("alert-warning");
							info.innerHTML = "Falha ao enviar o documento! Tente novamente. (" + obj.Msg + ")";
							$(".successRegister").attr('data-idt', "");
							$(".successRegister").addClass("hidden");
							$(".send").removeClass("hidden");
		        		}
	        			
	        			$(".progress").addClass("hidden");
	
	        		}
				});
			}
			
			return false;
			
		}
		function newDocument(){
			$("#frmTransaction :input.form-control").each(function(){
				$(this).val("");
			});
			$(".send").removeClass("hidden");
			$(".successRegister").addClass("hidden");
			$("#msgs").addClass("hide");
		}

		function imprimirBoleto(elem){
			console.log(elem.getAttribute('data-idt'));
			window.open("../controllers/boletoController.php?idT="+elem.getAttribute('data-idt')+"","janelaBloq", "width=800, height=650, top=0, left=0, scrollbars=no, status=no, resizable=no, directories=no, location=no, menubar=no, titlebar=no, toolbar=no");
		}

		$("#frmTransaction").submit(function(e) {
		    e.preventDefault();  
		});
	</script>
	</body>
</html>