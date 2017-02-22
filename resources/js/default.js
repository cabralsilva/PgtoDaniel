	abaSelecionada = "";
	$(function() {
		$("#div_full").fadeIn(0, function(){
			$("#div_wait").fadeIn(0);
			$("#tabs").tabs({
							select: function(event, ui) {
								abaSelecionada = ui.panel.id;
								
								$("#div_produtos").html("");
								//$("#div_banners").html("");
								$("#div_galeria").html("");
								$("#div_pedidos").html("");
								//$("#div_parceiros").html("");
								//$("#"+abaSelecionada).html("");
								
								if(abaSelecionada=="div_produtos"){
									$("#"+abaSelecionada).load("forms/produtos.php");
							}else if(abaSelecionada=="div_galeria"){
								 	$("#"+abaSelecionada).load("forms/galeria.php");
								}else if(abaSelecionada=="div_pedidos"){
									$("#"+abaSelecionada).load("forms/pedido.php");
								/*}else if(abaSelecionada=="div_parceiros"){
									$("#"+abaSelecionada).load("forms/parceiros.php");*/
								}/*else if(abaSelecionada=="div_pagamentos"){
									$("#"+abaSelecionada).load("forms/pagamentos.php");
								}*/
						    }
						});
			$("#div_produtos").load('forms/produtos.php');
			$("#div_wait").fadeOut(0);
			$("#div_full").fadeOut(1000);

		});
	});

	function buscarProdutos(pag){
		$("#div_resultado_produtos").html("<img src='images/carregando.gif' style='margin:50px 0 0 350px;'>");
		
		if (pag)
		
			$("#div_resultado_produtos").load("include/pesquisar.php?tabela=ProdutoWeb&pagina=" + pag, { "codigo": $("#codigo_produto").val(), "cod_grupo": $("#cod_grupo").val(), "cod_categoria": $("#cod_categoria").val(), "descricao": $("#descricao_produto").val() });
			
		else
		
			$("#div_resultado_produtos").load("include/pesquisar.php?tabela=ProdutoWeb", { "codigo": $("#codigo_produto").val(), "cod_grupo": $("#cod_grupo").val(), "cod_categoria": $("#cod_categoria").val(), "descricao": $("#descricao_produto").val() });
			
	}
	function buscarGaleria(pag){
		$("#div_resultado_galerias").html("<img src='images/carregando.gif' style='margin:50px 0 0 350px;'>");
		
		if (pag)
		
			$("#div_resultado_galerias").load("include/galerias.php?tabela=galeria&pagina=" + pag);
			
		else
		
			$("#div_resultado_galerias").load("include/galerias.php?tabela=galeria");
			
	}
	function buscarPedido(pag){
		$("#div_resultado_pedido").html("<img src='images/carregando.gif' style='margin:50px 0 0 350px;'>");
		
		if (pag)
		
			$("#div_resultado_pedido").load("include/pedidos.php?tabela=Pedido&pagina=" + pag);
			
		else
		
			$("#div_resultado_pedido").load("include/pedidos.php?tabela=Pedido");
			
	}
	
	
	function abreModal(div, titleDiv, loadDiv, w, h){
		$("<div/>", { id: div, style: 'width: '+ w +'px; height: '+ h +'px; border: solid #999999 1px; background: #F1F1F1;' }).appendTo("body");
		
		var dialogOptions = {
			"title" : titleDiv,
			"width" : w,
			"height" : h,
			"modal" : false,
			"resizable" : true,
			"draggable" : true,
			"zIndex" : 200000,
			"closeOnEscape" : true,
			"minHeight": w,
			"minWidth": h,
			"close" : function(){ $(this).remove(); }
		};
		
		var dialogExtendOptions = {
			"close" : true,
			"maximize" : true,
			"minimize" : true,
			"dblclick" : 'maximize'
		};
		
		$("#"+div).load(loadDiv);
		$("#"+div).dialog(dialogOptions).dialogExtend(dialogExtendOptions);
		
		return;
		document.body.style.overflowY = 'hidden';
		
		var maskHeight = $(document).height();
		var maskWidth = $(window).width();
		
		$('#mask').css({'width':maskWidth,'height':maskHeight});
		
		$('#mask').fadeIn(200);
		$('#mask').fadeTo("slow",0.5);
		
		var winH = $(window).height();
		var winW = $(window).width();
		
		if (w!="")
			$(div).css('width',  w);
		
		if (h!="")
			$(div).css('height', h);
		
		$(div).css('top',  winH/2-$(div).height()/2);
		$(div).css('left', winW/2-$(div).width()/2);
		
		$(div).fadeIn(200);
		
		$('#mask, .close').click(function (e) {
			fechaModal(div);
		});
	}
	
	function carregaLoad(div, path, json, w, h){
		if (json)
			$(div).load(path, json);
		else
			$(div).load(path);
		
		if (w!=""){
			var winW = $(window).width();
			$(div).css('width',  w);
			$(div).css('left', winW/2-$(div).width()/2);
		}
			
		if (h!=""){
			var winH = $(window).height();
			$(div).css('height',  h);
			$(div).css('top',  winH/2-$(div).height()/2);
		}
	}
	
	function fechaModal(div, save, file){
		$(div).html('');
		document.body.style.overflowY = 'scroll';
		$('#mask').off('click');
		$('#mask').hide();
		$('.window').hide();
		
		if (save=="1")
			$('#conteudo').load(file)
	}
	
	function listaFiles(files, lista, loading, botoes){
		var input = files;
		var list = document.getElementById(lista);
		var btn = document.getElementById(botoes);
		list.innerHTML = "";
		
		if (input.files.length>0){
			for (var x = 0; x<input.files.length; x++) {
				var span = document.createElement('span');
				span.setAttribute("style", "background:#FF0; margin:4px; font-size:11px;");
				
				tmp = input.files[x].name;
				
				if (tmp.length>15)
					nome = tmp.substr(0,11) + "..." + tmp.substr(-3);
				else
					nome = tmp;
				
				if (x==(input.files.length-1))
					span.innerHTML = nome;
				else
					span.innerHTML = nome + "<br>";
				
				list.appendChild(span);
			}
			
			var botao = document.createElement('button');
			botao.setAttribute("style", "margin-left:20px; font-size:12px; background:green; color:#FFFFFF;");
			botao.setAttribute("type", "submit");
			botao.setAttribute("onclick", "document.getElementById('"+loading+"').innerHTML = \"<img style='float:left; margin:10px 0 0 10px;' src='../images/loading.gif'> <span style='float:left; margin:15px 0 0 12px;'>ENVIANDO ARQUIVOS</span>\";");
			botao.innerHTML = "ENVIAR ARQUIVOS";
			btn.appendChild(botao);
			
			var botao2 = document.createElement('button');
			botao2.setAttribute("style", "margin-left:20px; font-size:12px; background:#FF0000; color:#FFFFFF;");
			botao2.setAttribute("type", "button");
			//botao.setAttribute("onclick", "document.getElementById('"+lista+"').innerHTML = \"\"; ");
			botao2.setAttribute("onclick", "document.getElementById('"+lista+"').innerHTML = \"\"; document.getElementById('"+botoes+"').innerHTML = \"\"; document.getElementById('"+files.getAttribute("id")+"').value = \"\";");
			botao2.innerHTML = "CANCELAR";
			btn.appendChild(botao2);
		}
	}
	
	function execLoad(varLoad){
		eval(varLoad);
	}