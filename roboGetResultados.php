<?php
/**
 * Programa para recupara os dados da tabnet
 * 
 * @author Willians Paulo Pedroso
 * <willianspedroso@gmail.com>
 * @since meados de março 2015
 * 
 */

#configuração
require_once 'config.php';

//inclui o classa de raspagem
require_once 'Raspagem.class.php';

#class de acesso ao banco de dados
require_once 'DAO.class.php';

//instancia a class que irá realizar a raspagem de dados
$rasp = new Raspagem();
//inicia a raspagem
$rasp->startResultados();


?>
