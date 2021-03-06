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
    $user = $auth->getCurrentUser();
    if (is_bool($user)) {
        header("Location: http://$host$uri/$extra");
    }
    if (!isset($_GET['id'])) {
        $user_id = $user->id;
    } else {
        $user_id = $_GET['id'];
    }
?>

<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Главная</title>
</head>
<body>
    <a href="/" style="position: fixed; top: 2em; left: 2em;">
        <button>На главную</button>
    </a>
    <center>
        <br>
        <?php
            function buildImage(PhotoModel $model) {
                $photo = base64_encode($model->photo_blob);
                return "<img src=\"data:$model->mime_type;base64, $photo\">";
            }
            $photos = $auth->getUserPhotos($user_id);
            foreach($photos as $photo) {
                echo buildImage($photo);
                echo "<br>$photo->title<br><br>";
            }
            if (!isset($_GET['id'])) {
                echo '<br><a href="/add_image/"><button>Добавить фото</button></a>';
            }
        ?>
        <br>
    </center>
</body>
</html>

