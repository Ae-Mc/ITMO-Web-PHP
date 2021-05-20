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

    if (isset($_FILES['image'])) {
        if (in_array($_FILES['image']['type'], acceptedImageTypes)) {
            if(is_string($result = $auth->addPhoto(
                $_POST['title'],
                file_get_contents($_FILES['image']['tmp_name']),
                $_FILES['image']['type'],
            ))) {
                $error = $result;
            }
            if(!isset($error)) {
                //header('Location: /');
            }
        } else {
            echo "Type {$_FILES['image']['type']} is not supported";
        }
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
        <form  method="post" enctype="multipart/form-data">
            <input type="file" name="image">
            <br><input type="text" name="title" placeholder="Название">
            <?php if (isset($error)) {?>
            <b><span style="color: red;"><?php echo $error; ?></span></b>
            <?php } ?>
            <br><input type="submit">
        </form>
        <a href="/"><button>На главную</button></a>
    </center>
</body>
</html>

