<?php
include_once "{$_SERVER['DOCUMENT_ROOT']}/lib/constants.php";
include_once mysqlBaseFilePath;
include_once modelsFilePath;

class MySQLUser extends MySQLBase {
    public mysqli_stmt $getUsersQuery;
    public mysqli_stmt $getUserByIDQuery;
    public mysqli_stmt $getUserIDByUsernameQuery;
    public mysqli_stmt $addFriendQuery;
    public mysqli_stmt $isFriendQuery;

    function _initQueries() {
        $this->getUserByIDQuery = $this->_prepareQuery(
            "SELECT id, username, registration_date FROM insta_user WHERE id=(?) LIMIT 1");
        $this->getUserIDByUsernameQuery = $this->_prepareQuery(
            "SELECT id FROM insta_user WHERE username=(?) LIMIT 1");
        $this->getUsersQuery = $this->_prepareQuery(
            "SELECT id, username, registration_date FROM insta_user");
        $this->addFriendQuery = $this->_prepareQuery(
            "INSERT INTO insta_friend (user_id, friend_id) VALUES ((?), (?))");
        $this->isFriendQuery = $this->_prepareQuery(
            "SELECT id FROM insta_friend WHERE user_id=(?) AND friend_id=(?) LIMIT 1");
    }

    function getUsers(): array {
        $this->getUsersQuery->execute();
        $result = $this->getUsersQuery->get_result();
        $users = [];
        if (is_bool($result)) {
            return false;
        }
        for($i = 0; $i < $result->num_rows; $i++) {
            $result->data_seek($i);
            $row = $result->fetch_assoc();
            $users[] = new UserModel(
                $row['id'],
                $row['username'],
                new DateTime($row['registration_date']));
        }
        return $users;
    }

    function getUser(int $user_id): UserModel|false {
        $this->getUserByIDQuery->bind_param('i', $user_id);
        $this->getUserByIDQuery->execute();
        $this->getUserByIDQuery->store_result();
        $user = new UserModel($user_id, '', new DateTime());
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

    function addFriend(int $user_id, int $friend_id): int {
        $this->addFriendQuery->bind_param('ii', $user_id, $friend_id);
        # $this->addFriendQuery->execute();
        # $this->addFriendQuery->bind_param('ii', $friend_id, $user_id);
        $this->addFriendQuery->execute();
        return $this->addFriendQuery->errno;
    }

    function isFriend(int $user_id, int $friend_id): bool {
        $this->isFriendQuery->bind_param('ii', $user_id, $friend_id);
        $this->isFriendQuery->execute();
        if ($this->isFriendQuery->get_result()->num_rows > 0) {
            return true;
        }
        return false;
    }
}
?>
