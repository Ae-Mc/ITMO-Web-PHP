<?php
include_once "{$_SERVER['DOCUMENT_ROOT']}/lib/constants.php";
include_once mysqlBaseFilePath;
include_once modelsFilePath;

class MySQLPhoto extends MySQLBase {
    public mysqli $mysql;
    public mysqli_stmt $addPhotoQuery;
    public mysqli_stmt $getUserPhotosQuery;

    function _initQueries() {
        $this->addPhotoQuery = $this->_prepareQuery(
            "INSERT INTO insta_photo (`user_id`, `title`, `photo`, `mime_type`)"
            . " VALUES((?), (?), (?), (?))");

        $this->getUserPhotosQuery = $this->_prepareQuery(
            "SELECT photo, title, mime_type, publication_date FROM insta_photo"
            . " WHERE user_id=(?)");
    }

    function addPhoto(int $user_id, string $title, string $photo_blob, string $mime_type) {
        $this->addPhotoQuery->bind_param(
            'isbs',
            $user_id,
            $title,
            $user_id,
            $mime_type
        );

        $this->addPhotoQuery->send_long_data(2, $photo_blob);
        $this->addPhotoQuery->execute();
        return $this->addPhotoQuery->errno;
    }

    function getUserPhotos(int $user_id): array|bool {
        $this->getUserPhotosQuery->bind_param('i', $user_id);
        $this->getUserPhotosQuery->execute();
        $photos = [];
        $result = $this->getUserPhotosQuery->get_result();
        if (is_bool($result)) {
            return false;
        }
        for($i = 0; $i < $result->num_rows; $i++) {
            $result->data_seek($i);
            $row = $result->fetch_assoc();
            $photos[] = new PhotoModel(
                0,
                $user_id,
                $row['title'],
                $row['photo'],
                $row['mime_type'],
                new DateTime($row['publication_date']));
        }
        return $photos;
    }

    function _prepareQuery(string $query): mysqli_stmt|false {
        $preparedQuery = $this->mysql->prepare($query);
        if($preparedQuery == false) {
            echo "Prepare of query \"$query\"<br>failed with error: {$this->mysql->error}";
        }
        return $preparedQuery;
    }
}
?>
