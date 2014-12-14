<?php
require_once 'Mail.php';
include_once "../tools/config.php";

if(!isset($_POST["mail"])){
    echo "ERROR: no mail send";
    exit;
}

$GLOBALS["dbconnec"]=connectDB();
$login_gmail=$password_gmail="";
$resultats=$GLOBALS["dbconnec"]->query("SELECT value,name FROM config WHERE name IN ('login_gmail','password_gmail')");
$resultats->setFetchMode(PDO::FETCH_OBJ);
while( $resultat = $resultats->fetch() )
{
    switch(strtolower($resultat->name)){
        case 'login_gmail':
            $login_gmail=$resultat->value;
            break;
        case 'password_gmail':
            $password_gmail=$resultat->value;
            break;
        default:
    }
}

$from =     $login_gmail;
$to =       $_POST["mail"];
$subject =  "Test Mail Domokine";
$body =     "\n\nIl s'agit d'un test d'envoi de mail";
$host =     "ssl://smtp.gmail.com";
$port =     "465";
$username = $login_gmail;
$password = $password_gmail;

$headers = array (
         'From' => $from,
         'To' => $to,
         'Subject' => $subject);

$smtp = Mail::factory('smtp',
      array (
            'host' => $host,
            'port' => $port,
            'auth' => true,
            'username' => $username,
            'password' => $password));

$mail = $smtp->send($to, $headers, $body);

if (PEAR::isError($mail)) die($mail->getMessage());
?>
