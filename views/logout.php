<?php 
	require_once '../util/constantes.php';
	session_start();
	unset( $_SESSION['usuario_logado'] );;
	unset( $_SESSION['dados_empresa'] );
	unset( $_SESSION['dados_acesso']);
	unset( $_SESSION["sistema"]);
	header("location: ".BaseProjeto."/login");
?>