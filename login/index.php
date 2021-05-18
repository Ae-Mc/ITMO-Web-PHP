<?php
include_once "{$_SERVER['DOCUMENT_ROOT']}/lib/constants.php";
include_once headersFilePath;
include_once authFilePath;

if (isset($_POST['password']) && isset($_POST['username'])) {
    $result = (new Auth())->login($_POST['username'], $_POST['password']);
    if (!is_bool($result)) {
        $error = $result;
    } else {
        header('Location: /');
    }
}
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход</title>
    <style type="text/css">
        .error {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div style="position: absolute; left: 50%; top: 50%; transform: translate3d(-50%, -50%, 0);">
    <form method="post">
        <table>
            <tr>
                <td colspan="2"><center>Войти</center></td>
            </tr>
            <tr>
                <td>Имя пользователя: </td>
                <td><input type="text" name="username" id="username"></td>
            </tr>
            <tr>
                <td>Пароль: </td>
                <td><input type="password" name="password" id="password"></td>
            </tr>
            <tr>
                <td colspan=2>
                    <div style="padding-top: 5px;">
                        <input type="submit" value="Войти" name="Submit" style="width:100%;">
                    </div>
                </td>
            </tr>
            <?
                if (isset($error)) {
            ?>
            <tr>
                <td colspan=2>
                    <span class="error">
                        <? echo $error; ?>
                    </span>
                </td>
            </tr>
            <?
                }
            ?>
            <tr>
                <td colspan="2">
                    <div style="position: absolute; right: 0;">
Нет аккаунта?
                        <a href="/register/">Зарегестрироваться</a>
                    </div>
                </td>
            </tr>
        </table>
    </form>
</div>
</body>
</html>
