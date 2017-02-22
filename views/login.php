<?php 
	session_start();
	require_once '../util/constantes.php';
	
	if (isset($_SESSION["usuario_logado"])){
		header("location: ".BaseProjeto."/pagamentos-pendentes");
	}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>iBoltSys - Login</title>
<link rel="stylesheet" href="<?= BaseProjeto ?>/resources/css/style-login.css">
</head>
<body>
	<div class="login">
		<div class="login-screen">
			<div class="app-title">
				<h1>iBolt Sistemas</h1><h3>Acesso restrito</h3>
			</div>
			<span class="fail-login">
				<?php 
					(isset($_SESSION["falha_login"]) ? print_r($_SESSION["falha_login"]) : null);
					unset($_SESSION["falha_login"]);
				?>
			</span>
			
			<div class="login-form">
			 	<form id="frmLogin" action="<?= BaseProjeto ?>/controllers/autenticacaoController.php" method="post">
					<div class="control-group">
						<input type="text" class="login-field" placeholder="email" id="email" name="email" required="required"> 
						<label class="login-field-icon fui-user" for="email"></label>
					</div>
	
					<div class="control-group">
						<input type="password" class="login-field" placeholder="senha" id="senha" name="senha" required="required"> 
						<label class="login-field-icon fui-lock" for="login-pass"></label>
					</div>
	
					<button type="submit" class="btn btn-primary btn-large btn-block">Entrar</button>
					<a class="login-link" href="#">Esqueceu sua senha?</a>
				</form>
			</div>
		</div>
	</div>
</body>
</html>
