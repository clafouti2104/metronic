<?php
/*
 * Supprime l'historique antérieur au jour (table temperature) pour les devices incrémentaux
 */
include("../tools/config.php");

$GLOBALS["dbconnec"] = connectDB();

//Requete SQL
$sqlHistorique="DELETE FROM temperature WHERE deviceid IN (";
$sqlHistorique.=" SELECT id FROM device WHERE incremental=1";
$sqlHistorique.=") ";
$sqlHistorique.=" AND date < '".date('Y-m-d 00:00:00')."' ";
$stmt = $GLOBALS["dbconnec"]->prepare($sqlHistorique);
$stmt->execute(array());
//echo $sqlHistorique;

?>