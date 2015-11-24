<?php
require_once 'Mail.php';
require_once 'Mail/mime.php';
include_once "../tools/config.php";

/*if(!isset($_POST["mail"])){
    echo "ERROR: no mail send";
    exit;
}*/
$general_mail="nico.gyss@gmail.com";

$GLOBALS["dbconnec"]=connectDB();
$login_gmail=$password_gmail="";
$resultats=$GLOBALS["dbconnec"]->query("SELECT value,name FROM config WHERE name IN ('login_gmail','password_gmail','general_email')");
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
        case 'general_email':
            $general_mail=$resultat->value;
            break;
        default:
    }
}

$_POST["mail"] = (isset($_POST["mail"])) ? $_POST["mail"] : $general_mail;
$title = (isset($title)) ? $title : "";
$content = (isset($content)) ? $content : "";

$html="<html>";
$html.="<head>";
$html.="<title>Domokine</title>";
$html.="<style type=\"text/css\">";
$html.="
            body {
            direction: ltr;
            width:100% !important;
            min-width: 100%;
            color:#4d4d4d;
            -webkit-text-size-adjust:100%;
            -ms-text-size-adjust:100%;
            margin:0;
            padding:0;
            }   
            a:hover {
            text-decoration: underline;
            }
            h1 {font-size: 34px;}
            h2 {font-size: 30px;}
            h3 {font-size: 26px;}
            h4 {font-size: 22px;}
            h5 {font-size: 18px;}
            h6 {font-size: 16px;}
            h4, h3, h2, h1 {
            display: block;
            margin: 5px 0 15px 0;
            }
            h7, h6, h5 {
            display: block;
            margin: 5px 0 5px 0 !important;
            }
            .template-label {
            color: #ffffff;
            font-weight: bold;
            font-size: 11px;
            }
            .note .panel {
            padding: 10px !important;
            background: #ECF8FF;
            border: 0;
            }
            .page-header { 
            width: 100%;
            }
            .fondu {
                border: 0;
                height: 1px;
                background-image: linear-gradient(to right, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.75), rgba(0, 0, 0, 0));
            }
            .social-icons {
            float: right;
            }
            .social-icons td {
            padding: 0 2px !important;
            width: auto !important;
            }
            .social-icons td:last-child {
            padding-right: 0 !important;
            }
            .social-icons td img {
            max-width: none !important; 
            }
            table.container.content > tbody > tr > td{
            background: #fff;  
            padding: 15px !important;
            }
            .page-footer  {
            width: 100%;
            background: #2f2f2f;
            }
            @media only screen and (max-width: 600px) {
            body {
            background: #fff; 
            color:#4d4d4d; 
            }
            table.container.content > tbody > tr > td{
            padding: 0px !important;
            }
            table[class='body'] table.columns .social-icons td {
            width: auto !important;
            }
            .page-header {
            padding: 10px !important;
            }
            .devider {
            margin: 15px 0;
            }
            .media-item {
            border-bottom: 1px solid #eee;
            padding: 15px 0 !important;
            }
            .fondu {
                border: 0;
                height: 1px;
                background-image: linear-gradient(to right, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.75), rgba(0, 0, 0, 0));
            }
            .hidden-mobile {
            display: none;
            }
            .visible-mobile {
            display: block;
            }
            }
        ";
$html.="</style>";
$html.="</head>";
$html.="<body>";
$html.='
<div style="width:100%;">
      <div style="float:left;width:35%;text-align: center;height:60px;">
            <img src="http://maleksultan1.free.fr/logo_horizontal.png" style="max-height:55px;width: 100%;max-width: 233px;" />
      </div>
      <div style="float:left;width:35%;text-align: center;height:60px;">
            <h4 style="margin-top:30px;">'.utf8_decode($title).'</h4>
      </div>
      <div style="clear:left;width:100%;">
            <hr class="fondu" />
      </div>
      <div style="clear:left;width:100%;clear:left;width:100%;">
            '.utf8_decode($content).'
      </div>
      <div style="clear:left;width:100%;">
            <hr class="fondu" />
      </div>
      <div style="clear:left:width:100%">
            The END
      </div>
</div>
        ';
/*
<table class="body">
      <tbody>
      <tr>
            <td class="center" valign="top" align="center">
                  <!-- BEGIN: Header -->
                  <img src="http://maleksultan1.free.fr/logo.png" />
            </td>
            <td>
                  <h4>'.$title.'</h4>
            </td>
                <!-- END: Header -->
            <!-- BEGIN: Content -->
                <table class="container content" align="center">
            <tr>
                  <td>
                        <table class="row">
                        <tr>
                              <td class="wrapper last">
                                    <!-- BEGIN: Heading Content -->
                                    <table class="twelve columns">
                                    <tr>
                                          <td>
                                                <h4>'.$title.'</h4>
                                                <p>
                                                       '.$content.'
                                                </p>
                                                <table class="btn green" align="right">
                                                <tr>
                                                      <td>
                                                            <a href="email_newsletter.html#">
                                                            En savoir plus... </a>
                                                      </td>
                                                </tr>
                                                </table>
                                          </td>
                                          <td class="expander">
                                          </td>
                                    </tr>
                                    </table>
                                    <!-- END: Heading Content -->
                              </td>
                        </tr>
                        </table>
                        </td>
                </tr>
                </table>
                <!-- END: Content -->
        </td>
    </tr>
    </tbody>
</table>
*/
$html.="</body>";
$html.="</html>";

$from =     $login_gmail;
$to =       $_POST["mail"];
$subject = (!isset($subject)) ? "Test Mail Domokine" : $subject;
$html = (!isset($html)) ? "\n\nIl s'agit d'un test d'envoi de mail" : $html;
$host =     "ssl://smtp.gmail.com";
$port =     "465";
$username = $login_gmail;
$password = $password_gmail;

$headers = array (
         'From' => $from,
         'To' => $to,
         'Subject' => $subject
         );

$mime = new Mail_mime();
// TXT
$text = 'Text version of email';
$mime->setTXTBody($text);
//HTML
$mime->setHTMLBody($html);

if(isset($filename)){
      $mime->addAttachment($filename, "application/pdf");
}

$headers = $mime->headers($headers);
$body = $mime->get();

$message =& Mail::factory('smtp',
      array (
            'host' => $host,
            'port' => $port,
            'auth' => true,
            'username' => $username,
            'password' => $password));
$message->send($to, $headers, $body);

/*$smtp = Mail::factory('smtp',
      array (
            'host' => $host,
            'port' => $port,
            'auth' => true,
            'username' => $username,
            'password' => $password));

$message = new Mail_mime();
$message->setTXTBody("Text message only");
$message->setHTMLBody($html);

$headers = $mime->headers($headers);

$mail =& Mail::factory('smtp',
      array (
            'host' => $host,
            'port' => $port,
            'auth' => true,
            'username' => $username,
            'password' => $password));


$result = $mail->send($to, $message->headers($headers), $message->get());

if (PEAR::isError($mail)) die($mail->getMessage());*/
?>
