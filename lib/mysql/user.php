<?php
include_once "{$_SERVER['DOCUMENT_ROOT']}/lib/constants.php";
include_once mysqlBaseFilePath;
include_once modelsFilePath;

class MySQLUser extends MySQLBase {
    public mysqli_stmt $getUserByIDQuery;
    public mysqli_stmt $getUserIDByUsernameQuery;

    function _initQueries() {
        $this->getUserByIDQuery = $this->_prepareQuery(
            "SELECT id, username, registration_date FROM insta_user WHERE id=(?) LIMIT 1");
        $this->getUserIDByUsernameQuery = $this->_prepareQuery(
            "SELECT id FROM insta_user WHERE username=(?) LIMIT 1");
    }

    function getUser(int $user_id): UserModel|false {
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
     * @return int|false
     * возвращает id пользователя, если он существует, иначе false
     */
    function getUserID(string $username): int|false {
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
}
?>
