<?php
include_once "{$_SERVER['DOCUMENT_ROOT']}/lib/constants.php";
include_once mysqlBaseFilePath;

class MySQLAuth extends MySQLBase {
    public mysqli_stmt $registrationQuery;
    public mysqli_stmt $loginQuery;
    public mysqli_stmt $createSessionQuery;
    public mysqli_stmt $testSessionQuery;
    public mysqli_stmt $removeSessionQuery;

    function _initQueries() {
        $this->registrationQuery =
            $this->_prepareQuery(
                "INSERT INTO insta_user (`username`, `password_hash`) VALUES((?), (?))");
        $this->loginQuery = $this->_prepareQuery(
            "SELECT password_hash FROM insta_user WHERE username=(?) LIMIT 1");

        $this->createSessionQuery = $this->_prepareQuery(
            "INSERT INTO insta_session"
            . " (`user_id`, `session_hash`, `user_agent_hash`)"
            . " VALUES((?), (?), (?))");
        $this->testSessionQuery = $this->_prepareQuery(
            "SELECT user_id, user_agent_hash FROM insta_session WHERE session_hash=(?) LIMIT 1");
        $this->removeSessionQuery = $this->_prepareQuery(
            "DELETE FROM insta_session WHERE session_hash=(?)");
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

    /**
     * @return int error code
     */
    function removeSession(string $session_hash): int {
        $this->removeSessionQuery->bind_param('b', $session_hash);
        $this->removeSessionQuery->send_long_data(0, $session_hash);
        $this->removeSessionQuery->execute();
        return $this->createSessionQuery->errno;
    }
}
?>
