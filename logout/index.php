<?php
include_once "{$_SERVER['DOCUMENT_ROOT']}/lib/constants.php";
include_once authFilePath;

$auth = new Auth();
$auth->logout();
header('Location: /');
?>
