<?php

include_once "{$_SERVER['DOCUMENT_ROOT']}/lib/constants.php";
include_once headersFilePath;
include_once authFilePath;
include_once mysqlAuthFilePath;
if (isset($_POST['username']) && isset($_POST['password'])) {
    $result = (new MySQLAuth())->register($_POST['username'], $_POST['password']);
    if ($result != 0) {
        switch($result) {
            case 1062:
                $error = 'Пользователь с таким именем уже зарегестрирован';
                break;
            case 1406:
                $error = 'Слишком длинное имя пользователя или пароль';
                break;
            default:
                $error = "Неизвестная ошибка. Код ошибки: $result";
        }
    } else {
        header('Location: /');
    }
}
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Зарегестрироваться</title>
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
                <td colspan="2"><center>Регистрация</center></td>
            </tr>
            <tr>
                <td>Имя пользователя: </td>
                <td><input type="text" name="username" id="username" style="width:100%"></td>
            </tr>
            <tr>
                <td>Пароль: </td>
                <td><input type="password" name="password" id="password" style="width: 100%;"></td>
            </tr>
            <tr>
                <td colspan=2>
                    <div style="padding-top: 5px;">
                        <input type="submit" value="Зарегестрироваться" name="Submit" style="width:100%;">
                    </div>
                </td>
            </tr>
            <?  if (isset($error)) { ?>
            <tr>
                <td colspan=2>
                    <span class="error">
                        <? echo $error; ?>
                    </span>
                </td>
            </tr>
            <?  } ?>
            <tr>
                <td colspan="2">
                    <div style="position: absolute; right: 0;">
                        Уже зарегестрирован?
                        <a href="/login/">Войти</a>
                    </div>
                </td>
            </tr>
        </table>
    </form>
</div>
</body>
</html>
