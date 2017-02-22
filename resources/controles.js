// JavaScript Document

var pedido = new Array();
var transacao = new Array();
var pesquisa = null;
var paginacaoa = null;
function converterStringToArray(string1, string2) {
	pedido = string1.split(",");
	transacao = string2.split(",");
	var res = transacao[0].split("<br />");
	if (res[2]) {
		res[2] = res[2].replace("\n", "");
		transacao[0] = res[2];
	}
	// console.log(res);
	// console.log(pedido);
	// console.log(transacao);
}

function startTransaction(btn) {
	var pedidostr = btn.getAttribute("data-pedido");
	var transacaostr = btn.getAttribute("data-transacao");
	converterStringToArray(pedidostr, transacaostr);
	// for ($i = 0; $i < pedido.length; $i++) console.log($i + " - " +
	// pedido[$i]);
	// for ($i = 0; $i < transacao.length; $i++) console.log($i + " - " +
	// transacao[$i]);
	// console.log(transacao);

	$
			.ajax({
				async : true,
				type : 'post',
				url : 'operadoras/controleOperadoras.php',
				beforeSend : function() {
					document.getElementById("btn-" + transacao[0]).style.display = "none";
					$('#btn-loading-' + transacao[0]).html(
							"<img src='../img/loading1.gif' />");

				},
				data : {
					idTransacao : transacao[0],
					idPedido : transacao[1],
					numParcelas : transacao[2],
					valorTransacao : transacao[3],
					tipoFormaPgto : transacao[6],
					descricaoFormaPgto : transacao[5],
					CartaoTitular : transacao[7],
					CartaoNumero : transacao[8],
					CartaoValidade : transacao[9],
					CartaoCodigoSeguranca : transacao[10],
					TaxaTransacao : transacao[11],
					nomeOperadora : transacao[14],
					TotalPedido : pedido[2],
					codCliente : pedido[4],
					nomeCliente : pedido[5],
					cpfCliente : pedido[6],
					dataPedido : pedido[1],
					idOperadora : transacao[16],
					idOperadoraEmpresa : transacao[17],
					paramCapturaAutomatica : transacao[24],
					valorLiquido : transacao[transacao.length - 2],
					operacao : 'startTransaction'
				},
				success : function(e) {
					$("#msgRetorno").html(e);
					$("#msgRetorno").hide();
					$("#msgRetorno").show("slow", "swing");
					// console.log(e);
					if (pesquisa != null)
						search(pesquisa);
					else if (paginacaoa != null)
						paginacao(paginacaoa);
					else
						location.reload();
				}
			});
	return false;
}

function captureTransaction(btn) {
	var pedidostr = btn.getAttribute("data-pedido");
	var transacaostr = btn.getAttribute("data-transacao");
	converterStringToArray(pedidostr, transacaostr);
	// for (var i = 0; i < transacao.length; i++) console.log(i + " -> " +
	// transacao[i]);
	$
			.ajax({
				async : true,
				type : 'post',
				url : 'operadoras/controleOperadoras.php',
				beforeSend : function() {
					document.getElementById("btn-" + transacao[0]).style.display = "none";
					$('#btn-loading-' + transacao[0]).html(
							"<img src='../img/loading1.gif' />");

				},
				data : {
					idTransacao : transacao[0],
					idPedido : transacao[1],
					numParcelas : transacao[2],
					tipoFormaPgto : transacao[6],
					TotalPedido : pedido[2],
					tidTransacao : transacao[12],
					dataAutorizacao : transacao[15],
					idOperadora : transacao[16],
					numSequencial : transacao[18],
					numAutorizacaoRede : transacao[19],
					numComprovanteRede : transacao[20],

					operacao : 'captureTransaction'
				},
				success : function(e) {
					// console.log(e);
					$("#msgRetorno").html(e);
					$("#msgRetorno").hide();
					$("#msgRetorno").show("slow", "swing");
					if (pesquisa != null)
						search(pesquisa);
					else if (paginacaoa != null)
						paginacao(paginacaoa);
					else
						location.reload();
				}
			});
	return false;
}

function requestCancelTransaction(btn) {
	var pedidostr = btn.getAttribute("data-pedido");
	var transacaostr = btn.getAttribute("data-transacao");
	converterStringToArray(pedidostr, transacaostr);
	// for (var i = 0; i < transacao.length; i++) console.log(i + " -> " +
	// transacao[i]);
	$
			.ajax({
				async : true,
				type : 'post',
				url : 'operadoras/controleOperadoras.php',
				beforeSend : function() {
					document.getElementById("btn-" + transacao[0]).style.display = "none";
					$('#btn-loading-' + transacao[0]).html(
							"<img src='../img/loading1.gif' />");
				},
				data : {
					idTransacao : transacao[0],
					idPedido : transacao[1],
					tidTransacao : transacao[12],
					dataAutorizacao : transacao[15],
					idOperadora : transacao[16],
					idOperadoraEmpresa : transacao[17],
					numSequencial : transacao[18],
					numAutorizacaoRede : transacao[19],
					numComprovanteRede : transacao[20],
					numAutenticacaoRede : transacao[21],
					valor : transacao[3],
					capturaAutomatica : transacao[23],
					paramCapturaAutomatica : transacao[24],
					operacao : 'requestCancelTransaction'
				},
				success : function(e) {
					$("#msgRetorno").html(e);
					$("#msgRetorno").hide();
					$("#msgRetorno").show("slow", "swing");
					if (pesquisa != null)
						search(pesquisa);
					else if (paginacaoa != null)
						paginacao(paginacaoa);
					else
						location.reload();
				}
			});
	return false;
}

function email(id) {
	var valores = id.split("#");
	var codigoPedido = valores[0];
	var clienteEmail = valores[1];
	var clienteNome = valores[2];
	var statusTran = valores[3];
	var id = valores[4];

	document.getElementById(id).style.display = "none";
	document.getElementById(codigoPedido).style.display = "block";

	$.post('include/intermCielo.php', {
		'acao' : "email",
		'codigoPedido' : codigoPedido,
		'clienteEmail' : clienteEmail,
		'clienteNome' : clienteNome,
		'statusTran' : statusTran
	}, function(retorno) {
		// alert(retorno);
		document.getElementById(codigoPedido).style.display = "none";
		document.getElementById(id).style.display = "block";
	});
}

function exportExcel() {
	$.ajax({
		async : true,
		type : 'post',
		url : 'controllista.php',
		data : {
			funcao : "exporttoexcel"
		},
		success : function(e) {
			console.log(e);

		}
	});
	return false;
}

function paginacao($pag) {
	pesquisa = null;
	paginacaoa = $pag;
	var qtde = 5;
	var pag = $pag;
	$.ajax({
		async : true,
		type : 'post',
		url : 'controllista.php',
		beforeSend : function() {
			document.getElementById("pedidos").style.display = "none";
			$("#table-loading").html("<img src='../img/loading1.gif' />");
		},
		data : {
			funcao : "paginacao",
			pagina : pag,
			numpedidos : qtde
		},
		success : function(e) {
			// console.log(e);

			var obj = JSON.parse(e);
			console.log(obj);

			var table = contruirTabela(obj);

			// document.getElementById("pedidos").style.display = "table";
			$("#pedidos").html(table);
			$("#table-loading").html("");
			document.getElementById("pedidos").style.display = "table";
			$('[data-toggle="tooltip"]').tooltip();
		}
	});
	return false;
}

function paginacaoPesquisa($pag) {
	// pesquisa = null;
	paginacaoa = $pag;
	var qtde = 5;
	var pag = $pag;
	$.ajax({
		async : true,
		type : 'post',
		url : 'controllista.php',
		beforeSend : function() {
			document.getElementById("pedidos").style.display = "none";
			$("#table-loading").html("<img src='../img/loading1.gif' />");
		},
		data : {
			funcao : "paginacaopesquisa",
			pagina : pag,
			numpedidos : qtde
		},
		success : function(e) {
			var obj = JSON.parse(e);
			var table = contruirTabela(obj);

			$("#pedidos").html(table);
			$("#table-loading").html("");
			document.getElementById("pedidos").style.display = "table";
			$('[data-toggle="tooltip"]').tooltip();
		}
	});
	return false;
}

function search($text) {
	pesquisa = $text;
	paginacao = null;
	var listaSearch;
	$
			.ajax({
				async : true,
				type : 'post',
				url : 'controllista.php',
				beforeSend : function() {
					document.getElementById("pedidos").style.display = "none";
					$("#table-loading").html(
							"<img src='../img/loading1.gif' />");
				},
				data : {
					funcao : "pesquisa",
					text : $text
				},
				success : function(e) {

					// console.log(e);
					var obj = JSON.parse(e);
					/*
					 * //console.log(obj); var table = contruirTabela(obj);
					 * //document.getElementById("pedidos").style.display =
					 * "table"; $("#pedidos").html(table);
					 * $("#table-loading").html("");
					 * document.getElementById("pedidos").style.display =
					 * "table"; $('[data-toggle="tooltip"]').tooltip();
					 */
					var paginar = "<ul class='pagination'>"
							+ "<li>"
							+ "<a href='javascript:paginacaoPesquisa(1);' aria-label='Previous'>"
							+ "<span aria-hidden='true'>&laquo;</span>"
							+ "</a>" + "</li>";
					for (var i = 1; i <= Math.floor(obj.length / 5) + 1; i++) {
						paginar += "<li><a href='javascript:paginacaoPesquisa("
								+ i + ");''>" + i + "</a></li>";
					}
					paginar += "<li>"
							+ "<a href='javascript:paginacaoPesquisa("
							+ (Math.floor(obj.length / 5) + 1)
							+ ");' aria-label='Next'>"
							+ "<span aria-hidden='true'>&raquo;</span>"
							+ "</a>" + "</li>" + "</ul>";
					$("#paginacaoBtn").html(paginar);
					paginacaoPesquisa(1);
				}
			});

	return false;
}

function direcionar() {
	alert("clicou");
	return false;
}

function relatorio() {

	var operadoras = "";
	$("#selecaoOperadora option:selected").each(function(ind, elem) {
		operadoras += elem.value + ",";
	});

	var status = "";
	$("#selecaoStatus option:selected").each(function(ind, elem) {
		status += elem.value + ",";
	});
	// console.log(status);
	var formpgto = "";
	$("#selecaoFormaPagamento option:selected").each(function(ind, elem) {
		formpgto += elem.value + ",";
	});

	var dtai = document.getElementById('dataAutorizacaoI').value;
	var dtaf = document.getElementById('dataAutorizacaoF').value;
	var dtcpi = document.getElementById('dataCapturaI').value;
	var dtcpf = document.getElementById('dataCapturaF').value;
	var dtcci = document.getElementById('dataCancelamentoI').value;
	var dtccf = document.getElementById('dataCancelamentoF').value;
	var dtpi = document.getElementById('dataPedidoI').value;
	var dtpf = document.getElementById('dataPedidoF').value;

	var numParcelas = document.getElementById('numParcelas').value;
	var codPedido = document.getElementById('codPedido').value;
	var valorTransacao = document.getElementById('valorTransacao').value;
	var codigoTransacao = document.getElementById('codTransacao').value;

	$
			.ajax({
				async : true,
				type : 'post',
				url : 'controllista.php',
				data : {
					funcao : "relatorio",
					dataAutorizacaoI : dtai,
					dataAutorizacaoF : dtaf,
					dataCapturaI : dtcpi,
					dataCapturaF : dtcpf,
					dataCancelamentoI : dtcci,
					dataCancelamentoF : dtccf,
					dataPedidoI : dtpi,
					dataPedidoF : dtpf,
					operadoras : operadoras,
					status : status,
					formaPgto : formpgto,
					numeroParcelas : numParcelas,
					codigoPedido : codPedido,
					valorTransacao : valorTransacao,
					codTransacao : codigoTransacao
				},
				success : function(e) {

					// console.log(e);
					var obj = JSON.parse(e);
					// console.log(obj.length);
					if (obj.length == 0)
						$("#conteudo-relatorio")
								.html(
										"<tr><td colspan='12' align='center'>Nenhum Registro encontrado!</td></tr>");
					else
						$("#conteudo-relatorio").html(construirRelatorio(obj));
					// $("#conteudo-relatorio").html(table);

				},
				error : function(error) {
					// console.log(eval(error));
				}
			});
	return false;
}


function construirRelatorio(obj) {
	var trs = "";
	for (i in obj) {
		trs += "<tr class='linha_relatorio'>" + "<td><center>";
		if (obj[i].fk_operadora == 1)
			trs += obj[i].tid_transacao_cielo;
		else if (obj[i].fk_operadora == 2)
			trs += obj[i].num_sequencial_rede;

		trs += "</center></td>" + "<td><center>" + obj[i].nome_operadora
				+ "</center></td>" + "<td><center>";
		if (obj[i].data_hora_retorno_autorizacao) {
			var dt = new Date(obj[i].data_hora_retorno_autorizacao);
			var d, m, mm;
			if (dt.getDate() <= 9)
				d = "0" + dt.getDate();
			else
				d = dt.getDate();
			if ((dt.getMonth() + 1) <= 9)
				m = "0" + (dt.getMonth() + 1);
			else
				m = dt.getMonth() + 1;
			if (dt.getMinutes() <= 9)
				mm = "0" + dt.getMinutes();
			else
				mm = dt.getMinutes();
			trs += d + "/" + m + "/" + dt.getFullYear() + " " + dt.getHours()
					+ ":" + mm;
		}

		trs += "</center></td>" + "<td><center>";
		if (obj[i].data_hora_retorno_captura) {
			var dt = new Date(obj[i].data_hora_retorno_captura);
			var d, m, mm;
			if (dt.getDate() <= 9)
				d = "0" + dt.getDate();
			else
				d = dt.getDate();
			if ((dt.getMonth() + 1) <= 9)
				m = "0" + (dt.getMonth() + 1);
			else
				m = dt.getMonth() + 1;
			if (dt.getMinutes() <= 9)
				mm = "0" + dt.getMinutes();
			else
				mm = dt.getMinutes();
			trs += d + "/" + m + "/" + dt.getFullYear() + " " + dt.getHours()
					+ ":" + mm;
		}
		trs += "</center></td>" + "<td><center>";
		if (obj[i].data_hora_retorno_cancelamento) {
			var dt = new Date(obj[i].data_hora_retorno_cancelamento);
			var d, m, mm;
			if (dt.getDate() <= 9)
				d = "0" + dt.getDate();
			else
				d = dt.getDate();
			if ((dt.getMonth() + 1) <= 9)
				m = "0" + (dt.getMonth() + 1);
			else
				m = dt.getMonth() + 1;
			if (dt.getMinutes() <= 9)
				mm = "0" + dt.getMinutes();
			else
				mm = dt.getMinutes();
			trs += d + "/" + m + "/" + dt.getFullYear() + " " + dt.getHours()
					+ ":" + mm;
		}
		trs += "</center></td>" + "<td><center>";
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

		trs += "</center></td>" + "<td><center>"
				+ obj[i].descricao_forma_pagamento + "</center></td>"
				+ "<td><center>";
		if (obj[i].qtde_parcelas == 0)
			trs += "1";
		else
			trs += obj[i].qtde_parcelas;
		trs += "</center></td>" + "<td><center><a href='lista.php?pedido="
				+ obj[i].fk_pedido + "'>" + // javascript:direcionar(" +
											// obj[i].fk_pedido + ");
				obj[i].fk_pedido + "</a></center></td>" + "<td><center>";
		if (obj[i].data_hora_pedido) {
			var dt = new Date(obj[i].data_hora_pedido);
			var d, m, mm;
			if (dt.getDate() <= 9)
				d = "0" + dt.getDate();
			else
				d = dt.getDate();
			if ((dt.getMonth() + 1) <= 9)
				m = "0" + (dt.getMonth() + 1);
			else
				m = dt.getMonth() + 1;
			if (dt.getMinutes() <= 9)
				mm = "0" + dt.getMinutes();
			else
				mm = dt.getMinutes();
			trs += d + "/" + m + "/" + dt.getFullYear();// + " " + dt.getHours()
														// + ":" + mm;
		}
		trs += "</center></td>" + "<td align='right'>R$ ";
		if (obj[i].valor_transacao.indexOf(".") == -1) {
			var decimais = obj[i].valor_transacao.substr(-2, 2);
			obj[i].valor_transacao = obj[i].valor_transacao.substr(0,
					obj[i].valor_transacao.length - 2);
			obj[i].valor_transacao = (parseFloat(obj[i].valor_transacao) + parseFloat(decimais) / 100)
					.toFixed(2);
		}
		obj[i].valor_transacao = parseFloat(obj[i].valor_transacao).toFixed(2);
		// alert(obj[i].valor_transacao);
		trs += obj[i].valor_transacao.replace(".", ",") + "</td>"
				+ "<td align='right'>R$ ";
		if (obj[i].valor_liquido.indexOf(".") == -1)
			obj[i].valor_liquido = obj[i].valor_liquido.substr(0,
					obj[i].valor_liquido.length - 2);
		obj[i].valor_liquido = parseFloat(obj[i].valor_liquido).toFixed(2);
		trs += obj[i].valor_liquido.replace(".", ",") + "</center></td>"
				+ "</tr>";
	}
	trs += "<tr class='info'>" + "<td colspan=9></td>"
			+ "<td align='right'><b>TOTAL</b></td>" + "<td align='right'>R$ ";
	var total = 0;
	for (i in obj) {
		total += parseFloat(obj[i].valor_transacao);
	}
	total = total.toFixed(2);
	trs += String(total).replace(".", ",") + "</td>"
			+ "<td align='right' style='width: 9%;'>R$ ";
	var totalliquido = 0;
	for (i in obj) {
		totalliquido += parseFloat(obj[i].valor_liquido);
	}
	totalliquido = totalliquido.toFixed(2);
	trs += String(totalliquido).replace(".", ",") + "</td>" + "</tr>";
	return trs;
}

function contruirTabela(obj) {

	var table = "";// "<table id='pedidos' class='table table-hover
					// table-striped table-bordered'>";
	for (i in obj) {
		table += "<tr>" + "<td>" + "<table id='table1'  class='table'>"
				+ "<tr class='info'>" + "<td>"
				+ "<table id='table2' class='table table-hover table-striped'>"
				+ "<thead class='headpedido'>" + "<tr class='info'>"
				+ "<td><center>CÓDIGO</center></td>"
				+ "<td><center>DATA</center></td>"
				+ "<td><center>CLIENTE</center></td>"
				+ "<td><center>VALOR TOTAL</center></td>" + "</tr>"
				+ "</thead>" + "<tbody>" + "<tr class='info'>" + "<td><center>"
				+ obj[i].CodPedido + "</center></td>";
		var dt = moment(obj[i].DataPedido, "YYYY/MM/DD").toDate();
		var d, m;
		if (dt.getDate() <= 8)
			d = "0" + dt.getDate();
		else
			d = dt.getDate();
		if ((dt.getMonth() + 1) <= 9)
			m = "0" + (dt.getMonth() + 1);
		else
			m = dt.getMonth() + 1;
		table += "<td><center>" + d + "/" + m + "/" + dt.getFullYear() + // " "
																			// +
																			// dt.getHours()
																			// +
																			// ":"
																			// +
																			// dt.getMinutes()
																			// +
																			// "</center></td>"
																			// +
		"<td><center>" + obj[i].ClienteNome + "</center></td>"
				+ "<td><center>R$ " + obj[i].TotalPedido.replace(".", ",")
				+ "</center></td>" + "</tr>" + "</tbody>" + "</table>"
				+ "</td>" + "</tr>" + "<tr class='warning'>" + "<td>"
				+ "<table id='table3' class='table'>" + "<tr class='warning'>"
				+ "<td>" + "<table id='table4' class='table table-hover'>"
				+ "<thead class='headtabela'>" + "<tr class='warning'>"
				+ "<td><center>TRANSAÇÃO</center></td>"
				+ "<td><center>STATUS</center></td>"
				+ "<td><center>DATA</center></td>"
				+ "<td><center>OPERADORA</center></td>"
				+ "<td><center>FORMA</center></td>"
				+ "<td><center>PARCELAS</center></td>"
				+ "<td><center>VALOR</center></td>"
				+ "<td><center></center></td>" + "</tr>" + "</thead>"
				+ "<tbody>";
		for (indTransacao in obj[i].listaPagamentos) {
			// console.log(obj[i].listaPagamentos[indTransacao]);
			table += "<tr class='warning'>" + "<td><center>";

			switch (obj[i].listaPagamentos[indTransacao].IdOperadora) {
			case '1':
				if (obj[i].listaPagamentos[indTransacao].TidTransacao) {
					table += obj[i].listaPagamentos[indTransacao].TidTransacao;
				} else
					table += "--";
				break;
			case '2':
				if (obj[i].listaPagamentos[indTransacao].NumSequencialRede) {
					table += obj[i].listaPagamentos[indTransacao].NumSequencialRede;
					// console.log(obj[i].listaPagamentos[indTransacao].IdOperadora);
				} else
					table += "--";
				break;
			default:
				break;
			}
			table += "	</center></td>" + "<td><center>";
			switch (obj[i].listaPagamentos[indTransacao].StatusTransPag) {
			case "0":
				table += "Pendente";
				break;
			case "1":
				table += "Autenticada";
				break;
			case "2":
				table += "Não Autenticada";
				break;
			case "3":
				table += "Autorizada";
				break;
			case "4":
				table += "Não Autorizada";
				break;
			case "5":
				table += "Capturada";
				break;
			case "6":
				table += "Cancelada";
				break;
			case "7":
				table += "Indefinida";
				break;
			case 0:
				table += "Pendente";
				break;
			case 1:
				table += "Autenticada";
				break;
			case 2:
				table += "Não Autenticada";
				break;
			case 3:
				table += "Autorizada";
				break;
			case 4:
				table += "Não Autorizada";
				break;
			case 5:
				table += "Capturada";
				break;
			case 6:
				table += "Cancelada";
				break;
			case 7:
				table += "Indefinido";
				break;
			default:
				table += "Indefinido";
				break;
			}

			table += // " <a href='#' style='color: black;'
						// data-toggle='tooltip' title='" +
						// obj[i].listaPagamentos[indTransacao].MensagemRetorno
						// + "''><span class='glyphicon glyphicon-info-sign'
						// aria-hidden='true'></span></a></center></td>" +
			"<td><center>";
			switch (obj[i].listaPagamentos[indTransacao].StatusTransPag) {
			case "1":
				var dt = new Date(
						obj[i].listaPagamentos[indTransacao].DataRetornoAutenticacao);
				var d, m, mm;
				if (dt.getDate() <= 9)
					d = "0" + dt.getDate();
				else
					d = dt.getDate();
				if ((dt.getMonth() + 1) <= 9)
					m = "0" + (dt.getMonth() + 1);
				else
					m = dt.getMonth() + 1;
				if (dt.getMinutes() <= 9)
					mm = "0" + dt.getMinutes();
				else
					mm = dt.getMinutes();
				table += d + "/" + m + "/" + dt.getFullYear();// + " " +
																// dt.getHours()
																// + ":" + mm;
				break;
			case "2":
				var dt = new Date(
						obj[i].listaPagamentos[indTransacao].DataRetornoAutenticacao);
				var d, m, mm;
				if (dt.getDate() <= 9)
					d = "0" + dt.getDate();
				else
					d = dt.getDate();
				if ((dt.getMonth() + 1) <= 9)
					m = "0" + (dt.getMonth() + 1);
				else
					m = dt.getMonth() + 1;
				if (dt.getMinutes() <= 9)
					mm = "0" + dt.getMinutes();
				else
					mm = dt.getMinutes();
				table += d + "/" + m + "/" + dt.getFullYear();// + " " +
																// dt.getHours()
																// + ":" + mm;
				break;
			case "3":
				var dt = new Date(
						obj[i].listaPagamentos[indTransacao].DataRetornoAutorizacao);
				var d, m, mm;
				if (dt.getDate() <= 9)
					d = "0" + dt.getDate();
				else
					d = dt.getDate();
				if ((dt.getMonth() + 1) <= 9)
					m = "0" + (dt.getMonth() + 1);
				else
					m = dt.getMonth() + 1;
				if (dt.getMinutes() <= 9)
					mm = "0" + dt.getMinutes();
				else
					mm = dt.getMinutes();
				table += d + "/" + m + "/" + dt.getFullYear();// + " " +
																// dt.getHours()
																// + ":" + mm;
				break;
			case "4":
				var dt = new Date(
						obj[i].listaPagamentos[indTransacao].DataRetornoAutorizacao);
				var d, m, mm;
				if (dt.getDate() <= 9)
					d = "0" + dt.getDate();
				else
					d = dt.getDate();
				if ((dt.getMonth() + 1) <= 9)
					m = "0" + (dt.getMonth() + 1);
				else
					m = dt.getMonth() + 1;
				if (dt.getMinutes() <= 9)
					mm = "0" + dt.getMinutes();
				else
					mm = dt.getMinutes();
				table += d + "/" + m + "/" + dt.getFullYear();// + " " +
																// dt.getHours()
																// + ":" + mm;
				break;
			case "5":
				var dt = new Date(
						obj[i].listaPagamentos[indTransacao].DataRetornoCaptura);
				var d, m, mm;
				if (dt.getDate() <= 9)
					d = "0" + dt.getDate();
				else
					d = dt.getDate();
				if ((dt.getMonth() + 1) <= 9)
					m = "0" + (dt.getMonth() + 1);
				else
					m = dt.getMonth() + 1;
				if (dt.getMinutes() <= 9)
					mm = "0" + dt.getMinutes();
				else
					mm = dt.getMinutes();
				table += d + "/" + m + "/" + dt.getFullYear();// + " " +
																// dt.getHours()
																// + ":" + mm;
				break;
			case "6":
				var dt = new Date(
						obj[i].listaPagamentos[indTransacao].DataRetornoCancelamento);
				var d, m, mm;
				if (dt.getDate() <= 9)
					d = "0" + dt.getDate();
				else
					d = dt.getDate();
				if ((dt.getMonth() + 1) <= 9)
					m = "0" + (dt.getMonth() + 1);
				else
					m = dt.getMonth() + 1;
				if (dt.getMinutes() <= 9)
					mm = "0" + dt.getMinutes();
				else
					mm = dt.getMinutes();
				table += d + "/" + m + "/" + dt.getFullYear();// + " " +
																// dt.getHours()
																// + ":" + mm;
				// table +=
				// obj[i].listaPagamentos[indTransacao].DataRetornoCancelamento;
				break;
			case 1:
				var dt = new Date(
						obj[i].listaPagamentos[indTransacao].DataRetornoAutenticacao);
				var d, m, mm;
				if (dt.getDate() <= 9)
					d = "0" + dt.getDate();
				else
					d = dt.getDate();
				if ((dt.getMonth() + 1) <= 9)
					m = "0" + (dt.getMonth() + 1);
				else
					m = dt.getMonth() + 1;
				if (dt.getMinutes() <= 9)
					mm = "0" + dt.getMinutes();
				else
					mm = dt.getMinutes();
				table += d + "/" + m + "/" + dt.getFullYear();// + " " +
																// dt.getHours()
																// + ":" + mm;
				break;
			case 2:
				var dt = new Date(
						obj[i].listaPagamentos[indTransacao].DataRetornoAutenticacao);
				var d, m, mm;
				if (dt.getDate() <= 9)
					d = "0" + dt.getDate();
				else
					d = dt.getDate();
				if ((dt.getMonth() + 1) <= 9)
					m = "0" + (dt.getMonth() + 1);
				else
					m = dt.getMonth() + 1;
				if (dt.getMinutes() <= 9)
					mm = "0" + dt.getMinutes();
				else
					mm = dt.getMinutes();
				table += d + "/" + m + "/" + dt.getFullYear();// + " " +
																// dt.getHours()
																// + ":" + mm;
				break;
			case 3:
				var dt = new Date(
						obj[i].listaPagamentos[indTransacao].DataRetornoAutorizacao);
				var d, m, mm;
				if (dt.getDate() <= 9)
					d = "0" + dt.getDate();
				else
					d = dt.getDate();
				if ((dt.getMonth() + 1) <= 9)
					m = "0" + (dt.getMonth() + 1);
				else
					m = dt.getMonth() + 1;
				if (dt.getMinutes() <= 9)
					mm = "0" + dt.getMinutes();
				else
					mm = dt.getMinutes();
				table += d + "/" + m + "/" + dt.getFullYear();// + " " +
																// dt.getHours()
																// + ":" + mm;
				break;
			case 4:
				var dt = new Date(
						obj[i].listaPagamentos[indTransacao].DataRetornoAutorizacao);
				var d, m, mm;
				if (dt.getDate() <= 9)
					d = "0" + dt.getDate();
				else
					d = dt.getDate();
				if ((dt.getMonth() + 1) <= 9)
					m = "0" + (dt.getMonth() + 1);
				else
					m = dt.getMonth() + 1;
				if (dt.getMinutes() <= 9)
					mm = "0" + dt.getMinutes();
				else
					mm = dt.getMinutes();
				table += d + "/" + m + "/" + dt.getFullYear();// + " " +
																// dt.getHours()
																// + ":" + mm;
				// table +=
				// obj[i].listaPagamentos[indTransacao].DataRetornoAutorizacao;
				break;
			case 5:
				var dt = new Date(
						obj[i].listaPagamentos[indTransacao].DataRetornoCaptura);
				var d, m, mm;
				if (dt.getDate() <= 9)
					d = "0" + dt.getDate();
				else
					d = dt.getDate();
				if ((dt.getMonth() + 1) <= 9)
					m = "0" + (dt.getMonth() + 1);
				else
					m = dt.getMonth() + 1;
				if (dt.getMinutes() <= 9)
					mm = "0" + dt.getMinutes();
				else
					mm = dt.getMinutes();
				table += d + "/" + m + "/" + dt.getFullYear();// + " " +
																// dt.getHours()
																// + ":" + mm;
				break;
			case 6:
				var dt = new Date(
						obj[i].listaPagamentos[indTransacao].DataRetornoCancelamento);
				var d, m, mm;
				if (dt.getDate() <= 9)
					d = "0" + dt.getDate();
				else
					d = dt.getDate();
				if ((dt.getMonth() + 1) <= 9)
					m = "0" + (dt.getMonth() + 1);
				else
					m = dt.getMonth() + 1;
				if (dt.getMinutes() <= 9)
					mm = "0" + dt.getMinutes();
				else
					mm = dt.getMinutes();
				table += d + "/" + m + "/" + dt.getFullYear();// + " " +
																// dt.getHours()
																// + ":" + mm;
				// table +=
				// obj[i].listaPagamentos[indTransacao].DataRetornoCancelamento;
				break;
			default:
				table += "--";
				break;
			}
			table += "</center></td>"
					+

					"<td><center>"
					+ obj[i].listaPagamentos[indTransacao].Operadora
					+ "</center></td>"
					+ "<td><center>"
					+ obj[i].listaPagamentos[indTransacao].FormaPagamento
					+ "</center></td>"
					+ "<td><center>"
					+ obj[i].listaPagamentos[indTransacao].NumeroParcelasPedPag
					+ "</center></td>"
					+ "<td align='right'>R$ "
					+ obj[i].listaPagamentos[indTransacao].ValorParcelaPedPag
							.replace(".", ",") + "</td>" + "<td><center>"
					+ "<div id='btn-loading-"
					+ obj[i].listaPagamentos[indTransacao].Codigo + "'></div>"
					+ "<div class='btn-group' id='btn-"
					+ obj[i].listaPagamentos[indTransacao].Codigo + "'>";
			if (obj[i].listaPagamentos[indTransacao].StatusTransPag == "7") {
				table += "<button type='button' class='btn btn-primary dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' disabled>Aguarde retorno <span class='caret'></span>"
						+ "</button>";
			} else {
				table += "<button type='button' class='btn btn-primary dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>Ações <span class='caret'></span>"
						+ "</button>";
			}

			table += "<ul class='dropdown-menu'>";
			// console.log(obj[i].listaPagamentos[indTransacao].StatusTransPag);
			switch (obj[i].listaPagamentos[indTransacao].StatusTransPag) {
			case "0":
				table += "<li><a data-pedido='"
						+ $.map(obj[i], function(el, ind) {
							if (el == null)
								el = "";
							return el
						})
						+ "' data-transacao='"
						+ $.map(obj[i].listaPagamentos[indTransacao], function(
								el, ind) {
							if (el == null)
								el = "";
							return el
						})
						+ "' data-toggle='dropdown' href='#' onClick='startTransaction(this)'>Autorizar</a></li>";
				break;
			case "1":
				break;
			case "2":
				break;
			case "3":
				table += "<li><a data-pedido='"
						+ $.map(obj[i], function(el, ind) {
							if (el == null)
								el = "";
							return el
						})
						+ "' data-transacao='"
						+ $.map(obj[i].listaPagamentos[indTransacao], function(
								el, ind) {
							if (el == null)
								el = "";
							return el
						})
						+ "' data-toggle='dropdown' href='#' onClick='captureTransaction(this)'>Capturar</a></li>"
						+ "<li><a data-pedido='"
						+ $.map(obj[i], function(el, ind) {
							if (el == null)
								el = "";
							return el
						})
						+ "' data-transacao='"
						+ $.map(obj[i].listaPagamentos[indTransacao], function(
								el, ind) {
							if (el == null)
								el = "";
							return el
						})
						+ "' data-toggle='dropdown' href='#' onClick='requestCancelTransaction(this)'>Cancelar</a></li>"
						+ "<li role='separator' class='divider'></li>"
						+ "<li><a role='button' data-toggle='collapse' href='#historico-"
						+ obj[i].listaPagamentos[indTransacao].Codigo
						+ "' aria-expanded='false' aria-controls='historico'>Transações</a></li>";

				break;
			case "4":
				table += "<li><a data-pedido='"
						+ $.map(obj[i], function(el, ind) {
							if (el == null)
								el = "";
							return el
						})
						+ "' data-transacao='"
						+ $.map(obj[i].listaPagamentos[indTransacao], function(
								el, ind) {
							if (el == null)
								el = "";
							return el
						})
						+ "' data-toggle='dropdown' href='#' onClick='startTransaction(this)'>Autorizar</a></li>"
						+ "<li role='separator' class='divider'></li>"
						+ "<li><a role='button' data-toggle='collapse' href='#historico-"
						+ obj[i].listaPagamentos[indTransacao].Codigo
						+ "' aria-expanded='false' aria-controls='historico'>Transações</a></li>";
				break;
			case "5":
				table += "<li><a data-pedido='"
						+ $.map(obj[i], function(el, ind) {
							if (el == null)
								el = "";
							return el
						})
						+ "' data-transacao='"
						+ $.map(obj[i].listaPagamentos[indTransacao], function(
								el, ind) {
							if (el == null)
								el = "";
							return el
						})
						+ "' data-toggle='dropdown' href='#' onClick='requestCancelTransaction(this)'>Cancelar</a></li>"
						+ "<li role='separator' class='divider'></li>"
						+ "<li><a role='button' data-toggle='collapse' href='#historico-"
						+ obj[i].listaPagamentos[indTransacao].Codigo
						+ "' aria-expanded='false' aria-controls='historico'>Transações</a></li>";
				break;
			case "6":
				table += "<li><a data-pedido='"
						+ $.map(obj[i], function(el, ind) {
							if (el == null)
								el = "";
							return el
						})
						+ "' data-transacao='"
						+ $.map(obj[i].listaPagamentos[indTransacao], function(
								el, ind) {
							if (el == null)
								el = "";
							return el
						})
						+ "' data-toggle='dropdown' href='#' onClick='startTransaction(this)'>Autorizar</a></li>"
						+ "<li role='separator' class='divider'></li>"
						+ "<li><a role='button' data-toggle='collapse' href='#historico-"
						+ obj[i].listaPagamentos[indTransacao].Codigo
						+ "' aria-expanded='false' aria-controls='historico'>Transações</a></li>";
				break;
			case 0:
				table += "<li><a data-pedido='"
						+ $.map(obj[i], function(el, ind) {
							if (el == null)
								el = "";
							return el
						})
						+ "' data-transacao='"
						+ $.map(obj[i].listaPagamentos[indTransacao], function(
								el, ind) {
							if (el == null)
								el = "";
							return el
						})
						+ "' data-toggle='dropdown' href='#' onClick='startTransaction(this)'>Autorizar</a></li>";
				break;
			case 1:
				break;
			case 2:
				break;
			case 3:
				table += "<li><a data-pedido='"
						+ $.map(obj[i], function(el, ind) {
							if (el == null)
								el = "";
							return el
						})
						+ "' data-transacao='"
						+ $.map(obj[i].listaPagamentos[indTransacao], function(
								el, ind) {
							if (el == null)
								el = "";
							return el
						})
						+ "' data-toggle='dropdown' href='#' onClick='captureTransaction(this)'>Capturar</a></li>"
						+ "<li><a data-pedido='"
						+ $.map(obj[i], function(el, ind) {
							if (el == null)
								el = "";
							return el
						})
						+ "' data-transacao='"
						+ $.map(obj[i].listaPagamentos[indTransacao], function(
								el, ind) {
							if (el == null)
								el = "";
							return el
						})
						+ "' data-toggle='dropdown' href='#' onClick='requestCancelTransaction(this)'>Cancelar</a></li>"
						+ "<li role='separator' class='divider'></li>"
						+ "<li><a role='button' data-toggle='collapse' href='#historico-"
						+ obj[i].listaPagamentos[indTransacao].Codigo
						+ "' aria-expanded='false' aria-controls='historico'>Transações</a></li>";

				break;
			case 4:
				table += "<li><a data-pedido='"
						+ $.map(obj[i], function(el, ind) {
							if (el == null)
								el = "";
							return el
						})
						+ "' data-transacao='"
						+ $.map(obj[i].listaPagamentos[indTransacao], function(
								el, ind) {
							if (el == null)
								el = "";
							return el
						})
						+ "' data-toggle='dropdown' href='#' onClick='startTransaction(this)'>Autorizar</a></li>"
						+ "<li role='separator' class='divider'></li>"
						+ "<li><a role='button' data-toggle='collapse' href='#historico-"
						+ obj[i].listaPagamentos[indTransacao].Codigo
						+ "' aria-expanded='false' aria-controls='historico'>Transações</a></li>";
				break;
			case 5:
				table += "<li><a data-pedido='"
						+ $.map(obj[i], function(el, ind) {
							if (el == null)
								el = "";
							return el
						})
						+ "' data-transacao='"
						+ $.map(obj[i].listaPagamentos[indTransacao], function(
								el, ind) {
							if (el == null)
								el = "";
							return el
						})
						+ "' data-toggle='dropdown' href='#' onClick='requestCancelTransaction(this)'>Cancelar</a></li>"
						+ "<li role='separator' class='divider'></li>"
						+ "<li><a role='button' data-toggle='collapse' href='#historico-"
						+ obj[i].listaPagamentos[indTransacao].Codigo
						+ "' aria-expanded='false' aria-controls='historico'>Transações</a></li>";
				break;
			case 6:
				table += "<li><a data-pedido='"
						+ $.map(obj[i], function(el, ind) {
							if (el == null)
								el = "";
							return el
						})
						+ "' data-transacao='"
						+ $.map(obj[i].listaPagamentos[indTransacao], function(
								el, ind) {
							if (el == null)
								el = "";
							return el
						})
						+ "' data-toggle='dropdown' href='#' onClick='startTransaction(this)'>Autorizar</a></li>"
						+ "<li role='separator' class='divider'></li>"
						+ "<li><a role='button' data-toggle='collapse' href='#historico-"
						+ obj[i].listaPagamentos[indTransacao].Codigo
						+ "' aria-expanded='false' aria-controls='historico'>Transações</a></li>";
				break;
			default:
				table += "<li><a data-pedido='"
						+ $.map(obj[i], function(el, ind) {
							if (el == null)
								el = "";
							return el
						})
						+ "' data-transacao='"
						+ $.map(obj[i].listaPagamentos[indTransacao], function(
								el, ind) {
							if (el == null)
								el = "";
							return el
						})
						+ "' data-toggle='dropdown' href='#' onClick='startTransaction(this)'>Autorizar</a></li>";
				break;
			}
			table += "</ul>"
					+ "</div>"
					+ "</center></td>"
					+ "</tr>"
					+

					/*
					 * "<tr class='success'>" + "<td class='zero' colspan='8' ><div
					 * id='historico-" +
					 * obj[i].listaPagamentos[indTransacao].Codigo + "'
					 * class='collapse'>" + "<table id='table5' class='table
					 * table-hover sucess'>" + "<thead class='headhistorico'>" + "<tr class='success'>" + "<td><center>CÓD.
					 * TRANSAÇÃO</center></td>" + "<td><center>OPERADORA</center></td>" + "<td><center>DATA
					 * AUTORIZAÇÃO</center></td>" + "<td><center>DATA
					 * CAPTURA</center></td>" + "<td><center>DATA
					 * CANCELAMENTO</center> </td>" + "<td><center>" + "<a
					 * data-toggle='collapse' href='#historico-" +
					 * obj[i].listaPagamentos[indTransacao].Codigo + "'
					 * aria-expanded='false' aria-controls='collapseExample'><span
					 * class='glyphicon glyphicon-triangle-top'
					 * aria-hidden='true'></span></a>" + "</center></td>" + "</tr>" + "</thead>" + "<tbody>" + "<tr class='success'>" + "<td><center></center></td>" + "<td><center></center></td>" + "<td><center></center></td>" + "<td><center></center></td>" + "<td><center></center></td>" + "<td></td>" + "</tr>" + "</tbody>"+ "</table></div>"+ "</td>"+ "</tr>";
					 */

					"<tr class='success'>"
					+ "<td class='zero' colspan='8' ><div id='historico-"
					+ obj[i].listaPagamentos[indTransacao].Codigo
					+ "' class='collapse'>"
					+ "<table id='table5' class='table table-hover sucess'>"
					+ "<thead class='headhistorico'>"
					+ "<tr class='success'>"
					+ "<td><center>CÓD. TRANSAÇÃO</center></td>"
					+ "<td><center>OPERADORA</center></td>"
					+ "<td><center>DATA AUTORIZAÇÃO</center></td>"
					+ "<td><center>DATA CAPTURA</center></td>"
					+ "<td><center>DATA CANCELAMENTO</center> </td>"
					+ "<td><center>"
					+ "<a data-toggle='collapse' href='#historico-"
					+ obj[i].listaPagamentos[indTransacao].Codigo
					+ "' aria-expanded='false' aria-controls='collapseExample'><span class='glyphicon glyphicon-triangle-top' aria-hidden='true'></span></a>"
					+ "</center></td>" + "</tr>" + "</thead>" + "<tbody>";
			for (indHistorico in obj[i].listaPagamentos[indTransacao].listaHistorico) {
				table += "<tr class='success'>"
						+ "<td><center>"
						+ obj[i].listaPagamentos[indTransacao].listaHistorico[indHistorico].CodigoTransacao
						+ "</center></td>"
						+ "<td><center>"
						+ obj[i].listaPagamentos[indTransacao].listaHistorico[indHistorico].NomeOperadora
						+ "</center></td>" + "<td><center>";
				if (obj[i].listaPagamentos[indTransacao].listaHistorico[indHistorico].DataAutorizacao) {
					var dt = new Date(
							obj[i].listaPagamentos[indTransacao].listaHistorico[indHistorico].DataAutorizacao);
					var d, m, mm;
					if (dt.getDate() <= 9)
						d = "0" + dt.getDate();
					else
						d = dt.getDate();
					if ((dt.getMonth() + 1) <= 9)
						m = "0" + (dt.getMonth() + 1);
					else
						m = dt.getMonth() + 1;
					if (dt.getMinutes() <= 9)
						mm = "0" + dt.getMinutes();
					else
						mm = dt.getMinutes();
					table += d + "/" + m + "/" + dt.getFullYear() + " "
							+ dt.getHours() + ":" + mm;
				}
				table += "</center></td>" + "<td><center>";
				if (obj[i].listaPagamentos[indTransacao].listaHistorico[indHistorico].DataCaptura) {
					var dt = new Date(
							obj[i].listaPagamentos[indTransacao].listaHistorico[indHistorico].DataCaptura);
					var d, m, mm;
					if (dt.getDate() <= 9)
						d = "0" + dt.getDate();
					else
						d = dt.getDate();
					if ((dt.getMonth() + 1) <= 9)
						m = "0" + (dt.getMonth() + 1);
					else
						m = dt.getMonth() + 1;
					if (dt.getMinutes() <= 9)
						mm = "0" + dt.getMinutes();
					else
						mm = dt.getMinutes();
					table += d + "/" + m + "/" + dt.getFullYear() + " "
							+ dt.getHours() + ":" + mm;
				}
				table += "</center></td>" +

				"<td><center>";
				if (obj[i].listaPagamentos[indTransacao].listaHistorico[indHistorico].DataCancelamento) {
					// console.log("Não é nulo");
					var dt = new Date(
							obj[i].listaPagamentos[indTransacao].listaHistorico[indHistorico].DataCancelamento);
					var d, m, mm;
					if (dt.getDate() <= 9)
						d = "0" + dt.getDate();
					else
						d = dt.getDate();
					if ((dt.getMonth() + 1) <= 9)
						m = "0" + (dt.getMonth() + 1);
					else
						m = dt.getMonth() + 1;
					if (dt.getMinutes() <= 9)
						mm = "0" + dt.getMinutes();
					else
						mm = dt.getMinutes();
					table += d + "/" + m + "/" + dt.getFullYear() + " "
							+ dt.getHours() + ":" + mm;
				}

				table += "</center></td>" + "<td></td>" + "</tr>";
			}
			table += "</tbody>" + "</table></div>" + "</td>" + "</tr>";
		}
		table += "</tbody>" + "</table>" + "</td>" + "</tr>" +

		"</table>" + "</td>" + "</tr>" +

		"</table>" + "</td>" + "</tr>";
		// console.log(obj[i].CodPedido);
	}
	// table += "</table>";

	return table;
}