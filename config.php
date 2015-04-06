<?php
/**
 * Arquivo de configuracao do banco de dados
 * 
 */
$con = mysql_connect("localhost", "root", "") or die(mysql_error($con));
mysql_select_db("tabnet", $con);

?>
