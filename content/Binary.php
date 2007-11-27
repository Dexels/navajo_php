<?php
require_once "navajo/client/NavajoClient.php";
switch ($_REQUEST['extension']) {
    case "jpg" :
        header("Content-type: image/jpeg");
        break;
    case "gif" :
        header("Content-type: image/gif");
        break;
    case "png" :
        header("Content-type: image/png");
        break;
    case "pdf" :
        header("Content-type: application/pdf");
        header("Content-Disposition: attachment; filename=my_binary.pdf");
        break;
    default :
        header("Content-type: application/unknown");
}

$service = $_REQUEST['service'];
$path = $_REQUEST['path'];
$joomla = $_REQUEST['joomlaSession'];

session_name($joomla);
session_start();

echo base64_decode($_SESSION["myBinary[$service][$path]"]);
unset ($_SESSION["myBinary[$service][$path]"]);
?>
