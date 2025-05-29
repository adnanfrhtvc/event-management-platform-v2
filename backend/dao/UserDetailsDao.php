<?php
require_once 'BaseDao.php';

class UserDetailsDao extends BaseDao {
    public function __construct() {
        parent::__construct("user_details");
    }

    public function getByUserId($user_id) {
        $stmt = $this->connection->prepare("SELECT * FROM user_details WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);;
    }

    public function insert($details) {
        return parent::insert($details);
    }

    public function updateByUserId($user_id, $data) {
        $fields = "";
        foreach ($data as $key => $value) {
            $fields .= "$key = :$key, ";
        }
        $fields = rtrim($fields, ", ");

        $sql = "UPDATE user_details SET $fields WHERE user_id = :user_id";
        $stmt = $this->connection->prepare($sql);
        $data['user_id'] = $user_id;

        return $stmt->execute($data);
    }
}
?>
