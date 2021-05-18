<?php
class UserModel {
    public int $id;
    public string $username;
    public DateTime $registrationDate;

    function __construct(
        int $id, string $username, DateTime $registrationDate) {
        $this->id = $id;
        $this->username = $username;
        $this->registrationDate = $registrationDate;
    }
}

class PhotoModel {
    public int $id;
    public int $user_id;
    public string $photo_blob;
    public string $mime_type;
    public DateTime $publicationDate;

    function __construct(
            int $id,
            int $user_id,
            string $photo_blob,
            string $mime_type,
            DateTime $publicationDate) {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->mime_type = $mime_type;
        $this->photo_blob = $photo_blob;
        $this->publicationDate = $publicationDate;
    }
}
?>
