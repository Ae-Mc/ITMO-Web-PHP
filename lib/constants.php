<?php
const headersFilePath = __DIR__ . '/headers.php';
const authFilePath = __DIR__ . '/auth_functions.php';
const mysqlBaseFilePath = __DIR__ . '/mysql/base.php';
const mysqlAuthFilePath = __DIR__ . '/mysql/auth.php';
const mysqlUserFilePath = __DIR__ . '/mysql/user.php';
const mysqlPhotoFilePath = __DIR__ . '/mysql/photo.php';
const modelsFilePath = __DIR__ . '/models.php';
const acceptedImageTypes = ['image/png', 'image/jpeg', 'image/gif', 'image/webp'];

const SHA_ALGO = "sha3-512";

$authentificated = isset($_COOKIE['usertoken']);
?>
