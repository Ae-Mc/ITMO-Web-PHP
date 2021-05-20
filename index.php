<?php 
    include_once "{$_SERVER['DOCUMENT_ROOT']}/lib/constants.php";
    include_once authFilePath;
    include_once modelsFilePath;
    header('Content-Type: text/html; encoding=utf-8');
    $host  = $_SERVER['HTTP_HOST'];
    $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    $extra = 'login/';
    if(!isset($_COOKIE["usertoken"]))
        header("Location: http://$host$uri/$extra");
    $auth = new Auth();
    $user = $auth->getCurrentUser();
    if (is_bool($user)) {
        header("Location: http://$host$uri/$extra");
    }
?>

<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Главная</title>
</head>
<body>
    <center>
        <span>Имя пользователя: <b><? echo $user->username; ?></b></span>
        <br><span>Дата регистрации: <b><? echo $user->registrationDate->format('Y-m-d H:i:s'); ?></b></span>
        <br><a href="/gallery/"><button>Галерея</button></a>
        <br><a href="/users/"><button>Список пользователей</button></a>
        <br><a href="/logout/"><button>Выйти</button></a>
    </center>
</body>
</html>
