<?php
	require("consultas.class.php");
	$db = new Consultas();

	$task = $_REQUEST['task'];

	switch($task){

		case 'consultarFormatos':
			$db->consultarFormatos($_REQUEST['fecha']);
		break;

		case 'consultarFecha':
			$db->consultarDocumentos($_REQUEST['fecha']);
		break;
	}
?>