<?php
include_once "{$_SERVER['DOCUMENT_ROOT']}/lib/constants.php";
include_once mysqlAuthFilePath;
include_once mysqlUserFilePath;
include_once mysqlPhotoFilePath;
include_once modelsFilePath;

class Auth {
    public mysqli $mysqli;
    public MySQLAuth $mysqlAuth;
    public MySQLUser $mysqlUser;
    public MySQLPhoto $mysqlPhoto;
    public int $user_id;

    function __construct() {
        $this->mysqli = new mysqli(
            "192.168.1.32",
            "student",
            "12345678#aA",
            "studing");
        $this->mysqlAuth = new MySQLAuth($this->mysqli);
        $this->mysqlUser = new MySQLUser($this->mysqli);
        $this->mysqlPhoto = new MySQLPhoto($this->mysqli);
    }

    function getUserToken(string $password): string {
        $passwordHash = hash(SHA_ALGO, $password, true);
        $dateTimeHash = hash(SHA_ALGO, (new DateTime())->getTimestamp(), true);
        $browserHash = hash(SHA_ALGO, $_SERVER['HTTP_USER_AGENT'], true);
        $fullHash = hash(
            SHA_ALGO,
            $passwordHash . $dateTimeHash . $browserHash . $dateTimeHash,
            true);
        return $fullHash;
    }

    /**
     * Tests USER_AGENT
     *
     * @return int
     * >=0 - user id
     * -1 - сессия с таким хэшем не найдена
     * -2 - хэш HTTP_USER_AGENT не совпадает
     */
    function testUserToken(): int {
        return $this->mysqlAuth->testSession($_COOKIE['usertoken']);
    }

    function getCurrentUser(): UserModel|bool {
        $user_id = -2;
        if (($user_id = $this->testUserToken()) < 0) {
            return false;
        } else {
            $this->user_id = $user_id;
            return $this->getUser($user_id);
        }
    }

    function getUser(int $user_id): UserModel| bool {
        return $this->mysqlUser->getUser($user_id);
    }

    /**
     * @param string $username
     * @param string $password
     * @return string|bool
     * true, если авторизация прошла успешно
     * текст ошибки, если произошла ошибка
     */
    function login(string $username, string $password): string|bool {
        $errorCode = 0;
        if (($errorCode = $this->mysqlAuth->testCredentials($username, $password)) == 0) {
            $session_hash = $this->getUserToken($password);
            $user_id = $this->mysqlUser->getUserID($username);
            if (!is_bool($user_id)) {
                if (($errorCode = $this->mysqlAuth->createSession($user_id, $session_hash)) == 0) {
                    $this->user_id = $user_id;
                    setcookie(
                        'usertoken',
                        $session_hash,
                        time() + 30 * 24 * 60 * 60,
                        '/',
                        httponly: true);
                } else {
                    return "Не удалось создать сессию. Код ошибки: $errorCode";
                }
            } else {
                return "Пользователь с именем $username не найден";
            }
        } else {
            switch($errorCode) {
                case 1:
                    return "Пользователь с таким именем не найден";
                case 2:
                    return "Неверный пароль";
            }
        }
        return true;
    }

    function logout() {
        if (isset($_COOKIE['usertoken'])) {
            $this->user_id = -1;
            $this->mysqlAuth->removeSession($_COOKIE['usertoken']);
            setcookie('usertoken', null, -1, '/');
        }
    }

    function addPhoto(string $photoBlob, string $mimeType): string|bool {
        $user = $this->getCurrentUser();
        $result = $this->mysqlPhoto->addPhoto($user->id, $photoBlob, $mimeType);
        if ($result != 0) {
            switch ($result) {
                case 1406:
                    return 'Файл слишком большой';
            }
            return 'Неизвестная ошибка. Код ошибки: ' . $result;
        }
        return true;
    }

    function getCurrentUserPhotos(): array|string {
        if ($this->user_id != -1) {
            $user_id = $this->getCurrentUser()->id;
        } else {
            $user_id = $this->user_id;
        }
        return $this->getUserPhotos($user_id);
    }

    function getUserPhotos(int $user_id): array|string {
        $result = $this->mysqlPhoto->getUserPhotos($user_id);
        if (is_bool($result)) {
            return "Не удалось получить фотографии пользователя с id $user_id";
        }
        return $result;
    }

    function addFriend(int $friend_id): int {
        if ($this->user_id != -1) {
            $user_id = $this->getCurrentUser()->id;
        } else {
            $user_id = $this->user_id;
        }
        return $this->mysqlUser->addFriend($user_id, $friend_id);
    }
}
?>
