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
    $auth = new Auth();
    $currentUser = $auth->getCurrentUser();
    if (isset($_GET['id'])) {
        $user_id = $_GET['id'];
        $user = (new MySQLUser())->getUser($user_id);
    } 
?>

<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Друзья</title>
</script>
</head>
<body>
    <a href="/" style="position: fixed; top: 2em; left: 2em;">
        <button>На главную</button>
    </a>
    <center>
        <div style="display: inline-block; text-align: left;">
        <br>
        <?php
            echo 'Список друзей: ';
            function buildUser(UserModel $user) {
                return "<li><a href='/users/?id=$user->id'>$user->username</a></li>";
            }
            $friends = $auth->getFriends();
            echo '<ul>';
            foreach($friends as $user) {
                echo buildUser($user);
            }
            echo '</ul>';
        ?>
        <br>
        </div>
    </center>
</body>
</html>

