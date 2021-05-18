<?php

include_once "{$_SERVER['DOCUMENT_ROOT']}/lib/constants.php";
include_once $modelsFilePath;

class MySQL {
    private static mysqli_stmt $mysql;
    private static mysqli_stmt $registrationQuery;
    private static mysqli_stmt $loginQuery;
    private static mysqli_stmt $createSessionQuery;
    private static mysqli_stmt $testSessionQuery;
    private static mysqli_stmt $getUserByIDQuery;
    private static mysqli_stmt $getUserIDByUsernameQuery;

    function __construct() {
        $this->mysql = new mysqli(
            "192.168.1.32",
            "student",
            "12345678#aA",
            "studing");
        if ($this->mysql->connect_errno) {
            echo "Error! Can't connect to database";
            exit;
        }

        $this->_initQueries();
    }

    function getUser(int $user_id): UserModel|bool {
        $this->getUserByIDQuery->bind_param('i', $user_id);
        $this->getUserByIDQuery->execute();
        $this->getUserByIDQuery->store_result();
        $user = new UserModel(0, '', new DateTime());
        $dateTimeString = '';
        $this->getUserByIDQuery->bind_result(
            $user->id, $user->username, $dateTimeString);
        $this->getUserByIDQuery->fetch();
        if ($user->id == null) {
            return false;
        }
        $user->registrationDate->setTimestamp(strtotime($dateTimeString));
        return $user;
    }
    /**
     * Получает id пользователя по имени
     *
     * @param string $username имя пользователя
     * @return int|bool
     * возвращает id пользователя, если он существует, иначе false
     */
    function getUserID(string $username): int|bool {
        $this->getUserIDByUsernameQuery->bind_param('s', $username);
        $this->getUserIDByUsernameQuery->execute();
        $this->getUserIDByUsernameQuery->store_result();
        $user_id = null;
        $this->getUserIDByUsernameQuery->bind_result($user_id);
        $this->getUserIDByUsernameQuery->fetch();
        if ($user_id == null) {
            return false;
        }
        return $user_id;
    }

    function register(string $username, string $password): int {
        $passHash = hash(SHA_ALGO, $password, true);
        $this->registrationQuery->bind_param('sb', $username, $passHash);
        $this->registrationQuery->send_long_data(1, $passHash);
        $this->registrationQuery->execute();
        return $this->registrationQuery->errno;
    }

    /**
     * Проверяет, существует ли пользователь с таким именем и паролем
     *
     * @param string $username имя пользователя
     * @param string $password пароль
     * @return int
     * 0 - всё хорошо
     * 1 - пользователь с таким именем не найден
     * 2 - неверный пароль
    */
    function testCredentials(string $username, string $password): int {
        $passHash = hash(SHA_ALGO, $password, true);
        $this->loginQuery->bind_param('s', $username);
        $this->loginQuery->execute();
        $this->loginQuery->store_result();
        $passwordHash = null;
        $this->loginQuery->bind_result($passwordHash);
        $this->loginQuery->fetch();

        if($passwordHash == null) {
            return 1;
        } elseif($passwordHash != $passHash) {
            return 2;
        }

        return 0;
    }

    /**
     * @return int error code
     */
    function createSession(int $userID, string $session_hash): int {
        $this->createSessionQuery->bind_param(
            'ibb',
            $userID,
            $session_hash,
            hash(SHA_ALGO, $_SERVER['HTTP_USER_AGENT'], true));
        $this->createSessionQuery->send_long_data(1, $session_hash);
        $this->createSessionQuery->send_long_data(
            2, hash(SHA_ALGO, $_SERVER['HTTP_USER_AGENT'], true));
        $this->createSessionQuery->execute();
        return $this->createSessionQuery->errno;
    }

    /**
     * @return int
     * любое положительное число - это user_id сессии
     * -1 - сессия с таким хэшем не найдена
     * -2 - USER_AGENT изменился
     */
    function testSession(string $session_hash): int {
        $this->testSessionQuery->bind_param('b', $session_hash);
        $this->testSessionQuery->send_long_data(0, $session_hash);
        $this->testSessionQuery->execute();
        $this->testSessionQuery->store_result();
        $user_id = null;
        $user_agent_hash = null;
        $this->testSessionQuery->bind_result($user_id, $user_agent_hash);
        $this->testSessionQuery->fetch();
        if ($user_agent_hash == null) {
            return -1;
        } elseif ($user_agent_hash != hash(SHA_ALGO, $_SERVER['HTTP_USER_AGENT'], true)) {
            return -2;
        } else {
            return $user_id;
        }
    }

    function _initQueries() {
        $this->registrationQuery =
            $this->mysql->prepare(
                "INSERT INTO insta_user (`username`, `password_hash`) VALUES((?), (?))");
        if($this->registrationQuery == false) {
            echo "Prepare of registration query failed with error: {$this->mysql->error}";
        }

        $this->loginQuery = $this->mysql->prepare(
            "SELECT password_hash FROM insta_user WHERE username=(?) LIMIT 1");
        if($this->loginQuery == false) {
            echo "Prepare of login query failed with error: {$this->mysql->error}";
        }

        $this->createSessionQuery = $this->mysql->prepare(
            "INSERT INTO insta_session"
            . " (`user_id`, `session_hash`, `user_agent_hash`)"
            . " VALUES((?), (?), (?))");
        if($this->createSessionQuery == false) {
            echo "Prepare of create session query failed with error: {$this->mysql->error}";
        }

        $this->testSessionQuery = $this->mysql->prepare(
            "SELECT user_id, user_agent_hash FROM insta_session WHERE session_hash=(?) LIMIT 1");
        if($this->testSessionQuery == false) {
            echo "Prepare of test session query failed with error: {$this->mysql->error}";
        }

        $this->getUserByIDQuery = $this->mysql->prepare(
            "SELECT id, username, registration_date FROM insta_user WHERE id=(?) LIMIT 1");
        if($this->getUserByIDQuery == false) {
            echo "Prepare of get user query failed with error: {$this->mysql->error}";
        }

        $this->getUserIDByUsernameQuery = $this->mysql->prepare(
            "SELECT id FROM insta_user WHERE username=(?) LIMIT 1");
        if($this->getUserIDByUsernameQuery == false) {
            echo "Prepare of get user id query failed with error: {$this->mysql->error}";
        }
    }
}
?>
