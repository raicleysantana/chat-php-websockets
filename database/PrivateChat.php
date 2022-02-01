<?php

class PrivateChat
{
    private $chat_message_id;
    private $to_user_id;
    private $from_user_id;
    private $chat_message;
    private $timestamp;
    private $status;
    protected $connect;


    public function __construct()
    {
        require_once 'Database_connection.php';

        $db = new Database_connection();
        $this->connect = $db->connect();
    }

    /**
     * @return mixed
     */
    public function getChatMessageId()
    {
        return $this->chat_message_id;
    }

    /**
     * @param mixed $chat_message_id
     */
    public function setChatMessageId($chat_message_id)
    {
        $this->chat_message_id = $chat_message_id;
    }

    /**
     * @return mixed
     */
    public function getToUserId()
    {
        return $this->to_user_id;
    }

    /**
     * @param mixed $to_user_id
     */
    public function setToUserId($to_user_id)
    {
        $this->to_user_id = $to_user_id;
    }

    /**
     * @return mixed
     */
    public function getFromUserId()
    {
        return $this->from_user_id;
    }

    /**
     * @param mixed $from_user_id
     */
    public function setFromUserId($from_user_id)
    {
        $this->from_user_id = $from_user_id;
    }

    /**
     * @return mixed
     */
    public function getChatMessage()
    {
        return $this->chat_message;
    }

    /**
     * @param mixed $chat_message
     */
    public function setChatMessage($chat_message)
    {
        $this->chat_message = $chat_message;
    }

    /**
     * @return mixed
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param mixed $timestamp
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    function get_all_chat_data()
    {
        $colunas = "a.user_name AS from_user_name, b.user_name AS to_user_name, chat_message, timestamp, status, "
            . "to_user_id, from_user_id";

        $query = "SELECT {$colunas} FROM chat_message "
            . "INNER JOIN chat_user_table a ON chat_message.from_user_id = a.user_id "
            . "INNER JOIN chat_user_table b ON chat_message.to_user_id = b.user_id "
            . "WHERE (chat_message.from_user_id = :from_user_id AND chat_message.to_user_id = :to_user_id) OR "
            . "(chat_message.from_user_id = :to_user_id AND chat_message.to_user_id = :from_user_id)";

        $statement = $this->connect->prepare($query);
        $statement->bindParam(':from_user_id', $this->from_user_id);
        $statement->bindParam(':to_user_id', $this->to_user_id);

        if (!$statement->execute()) {
            file_put_contents('debux.txt', $statement->errorInfo());
        }

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function save_chat()
    {
        $query = "INSERT INTO chat_message SET to_user_id = :to_user_id, from_user_id = :from_user_id, "
            . "chat_message = :chat_message, timestamp = :timestamp, status =:status";

        $statement = $this->connect->prepare($query);

        $statement->bindParam(':from_user_id', $this->from_user_id);
        $statement->bindParam(':to_user_id', $this->to_user_id);
        $statement->bindParam(':chat_message', $this->chat_message);
        $statement->bindParam(':status', $this->status);
        $statement->bindParam(':timestamp', $this->timestamp);

        $statement->execute();

        return $this->connect->lastInsertId();
    }

    public function update_chat_status()
    {
        $query = "UPDATE chat_message SET status = :status WHERE chat_message_id = :chat_message_id";

        $statement = $this->connect->prepare($query);
        $statement->bindParam(':chat_message_id', $this->chat_message_id);
        $statement->bindParam(':status', $this->status);
        $statement->execute();
    }

    public function change_chat_status()
    {
        $query = "UPDATE chat_message SET status = 'Yes' WHERE from_user_id = :from_user_id AND to_user_id = :to_user_id "
            . "status = 'No'";

        $statement = $this->connect->prepare($query);
        $statement->bindParam(':from_user_id', $this->from_user_id);
        $statement->bindParam(':to_user_id', $this->to_user_id);
        $statement->execute();
    }


}