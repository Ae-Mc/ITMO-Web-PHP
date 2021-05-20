<?php
    include_once "{$_SERVER['DOCUMENT_ROOT']}/lib/constants.php";
    include_once authFilePath;
    include_once modelsFilePath;
    header('Content-Type: text/html; encoding=utf-8');

    $host  = $_SERVER['HTTP_HOST'];
    $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    $extra = 'login/';
    if(!$authentificated)
        header("Location: http://$host$uri/$extra");
    if (isset($_GET['id'])) {
        $auth = new Auth();
        $user = $auth->getCurrentUser();
        if (is_bool($user)) {
            header("Location: http://$host$uri/$extra");
        }
        $friend_id = $_GET['id'];
        $result = $auth->addFriend($friend_id);
        if ($result != 0) {
            echo $result;
        }
    }
?>
