<?php


class ChatRooms
{
    private $chat_id;
    private $userId;
    private $message;
    private $created_on;
    protected $connect;

    public function __construct()
    {
        require_once 'Database_connection.php';

        $database_object = new Database_connection();
        $this->connect = $database_object->connect();
    }

    /**
     * @return mixed
     */
    public function getChatId()
    {
        return $this->chat_id;
    }

    /**
     * @param mixed $chat_id
     */
    public function setChatId($chat_id)
    {
        $this->chat_id = $chat_id;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param mixed $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return mixed
     */
    public function getCreatedOn()
    {
        return $this->created_on;
    }

    /**
     * @param mixed $created_on
     */
    public function setCreatedOn($created_on)
    {
        $this->created_on = $created_on;
    }

    public function saveChat()
    {
        $query = "INSERT INTO chatrooms SET user_id = :user_id, msg = :msg, created_on = :created_on";

        $statement = $this->connect->prepare($query);
        $statement->bindParam(":user_id", $this->userId);
        $statement->bindParam(":msg", $this->message);
        $statement->bindParam(":created_on", $this->created_on);
        $statement->execute();
    }

    public function get_all_chat_data()
    {
        $query = "SELECT * FROM chatrooms cr "
            . "INNER JOIN chat_user_table cut ON cr.user_id = cut.user_id "
            . "ORDER BY cr.id ASC";

        $statement = $this->connect->prepare($query);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);

    }


}