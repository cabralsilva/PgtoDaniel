<?php
	include "../services/configBD.php";
	
	
	function listarTodosContatos(){
		openConnection();
		$sql = "SELECT id_contato, nome_contato, bairro_contato, cidade_contato FROM contatos";
		
		$listaContatos = mysqli_query($GLOBALS['conexao'], $sql);	
		
		return $listaContatos;
		closeConnection();
	}
	
	
	
?>