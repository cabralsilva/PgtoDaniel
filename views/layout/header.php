<span class="nome-empresa"><?= $_SESSION["sistema"]["nome_sistema"] ?></span>
<span class="nome-pagina"> <?= Page ?></span> <span class="usuario">
	<div align="right">Funcionário logado: <?= $_SESSION["usuario_logado"]["nome_usuario"] ?>
    	<div class="btn-group">
			<button type="button" class="btn btn-success dropdown-toggle"
				data-toggle="dropdown" aria-haspopup="true"
				aria-expanded="false">
				Opções <span class="caret"></span>
			</button>
			<ul class="dropdown-menu drop-down-canto">
				<li><a href="pagamentos-pendentes">Pagamentos pendentes</a></li>
				<li><a href="buscas-pagamentos">Buscas e totalizadores</a></li>
				
				<li role="separator" class="divider"></li>
				<li><a href="novo-boleto">Gerar Boleto</a></li>
				<li><a href="#modalRemessa" data-toggle="modal" data-target="#modalRemessa">Gerar remessa</a></li>
				<li><a href="#modalRetorno" data-toggle="modal" data-target="#modalRetorno">Processar retorno</a></li>
				<li role="separator" class="divider"></li>
<!-- 				<li><a href="../sistemas/">Sistemas</a></li> -->
				<li><a href="<?= BaseProjeto ?>/logout/">Logout</a></li>
			</ul>
		</div>
	</div>
</span>