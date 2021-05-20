<?php 
    # TODO: сделать список пользователей
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
    <title>Главная</title>
<?php
if (isset($user_id) && $currentUser->id != $user_id && !$auth->isFriend($user_id)) {
?>
<script>
window.addEventListener("load", function() {
    e = document.getElementById('addFriend');
    e.onclick = function () {
        var xhr = new XMLHttpRequest()
        <?php 
        echo "xhr.open('GET', '/api/add_friend.php?id=$user_id');";
        ?>
        xhr.send()
        e.hidden = true
    }
});
<?php } ?>
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
            if (isset($user_id)) {
        ?>
                <span>Имя пользователя: <b><? echo $user->username; ?></b></span>
                <br><span>Дата регистрации: <b><? echo $user->registrationDate->format('Y-m-d H:i:s'); ?></b></span>
        <?php 
            if ($currentUser->id != $user_id && !$auth->isFriend($user_id)) {
                echo '<br><button id="addFriend">Добавить в друзья</button>';
            }
        ?>
                <br><a href="/gallery/?id=<? echo $user->id; ?>"><button>Галерея</button></a>
        <?php
            } else {
                echo 'Список пользователей: ';
                function buildUser(UserModel $user) {
                    return "<li><a href='/users/?id=$user->id'>$user->username</a></li>";
                }
                $users = (new MySQLUser())->getUsers();
                echo '<ul>';
                foreach($users as $user) {
                    echo buildUser($user);
                }
                echo '</ul>';
            }
        ?>
        <br>
        </div>
    </center>
</body>
</html>

