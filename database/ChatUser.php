<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require dirname(__DIR__) . '/vendor/autoload.php';

class ChatUser
{

    private $user_id;
    private $user_name;
    private $user_email;
    private $user_password;
    private $user_profile;
    private $user_status;
    private $user_created_on;
    private $user_verification_code;
    private $user_login_status;
    private $user_token;
    private $user_connection_id;
    public $connect;


    public function __construct()
    {
        require_once 'Database_connection.php';
        $database_object = new Database_connection();

        $this->connect = $database_object->connect();
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @param mixed $user_id
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     * @return mixed
     */
    public function getUserName()
    {
        return $this->user_name;
    }

    /**
     * @param mixed $user_name
     */
    public function setUserName($user_name)
    {
        $this->user_name = $user_name;
    }

    /**
     * @return mixed
     */
    public function getUserEmail()
    {
        return $this->user_email;
    }

    /**
     * @return mixed
     */
    public function getUserToken()
    {
        return $this->user_token;
    }

    /**
     * @param mixed $user_token
     */
    public function setUserToken($user_token)
    {
        $this->user_token = $user_token;
    }

    /**
     * @return mixed
     */
    public function getUserConnectionId()
    {
        return $this->user_connection_id;
    }

    /**
     * @param mixed $user_connection_id
     */
    public function setUserConnectionId($user_connection_id)
    {
        $this->user_connection_id = $user_connection_id;
    }

    /**
     * @param mixed $user_email
     */
    public function setUserEmail($user_email)
    {
        $this->user_email = $user_email;
    }

    /**
     * @return mixed
     */
    public function getUserPassword()
    {
        return $this->user_password;
    }

    /**
     * @param mixed $user_password
     */
    public function setUserPassword($user_password)
    {
        $this->user_password = $user_password;
    }

    /**
     * @return mixed
     */
    public function getUserProfile()
    {
        return $this->user_profile;
    }

    /**
     * @param mixed $user_profile
     */
    public function setUserProfile($user_profile)
    {
        $this->user_profile = $user_profile;
    }

    /**
     * @return mixed
     */
    public function getUserStatus()
    {
        return $this->user_status;
    }

    /**
     * @param mixed $user_status
     */
    public function setUserStatus($user_status)
    {
        $this->user_status = $user_status;
    }

    /**
     * @return mixed
     */
    public function getUserCreatedOn()
    {
        return $this->user_created_on;
    }

    /**
     * @param mixed $user_created_on
     */
    public function setUserCreatedOn($user_created_on)
    {
        $this->user_created_on = $user_created_on;
    }

    /**
     * @return mixed
     */
    public function getUserVerificationCode()
    {
        return $this->user_verification_code;
    }

    /**
     * @param mixed $user_verification_code
     */
    public function setUserVerificationCode($user_verification_code)
    {
        $this->user_verification_code = $user_verification_code;
    }

    /**
     * @return mixed
     */
    public function getUserLoginStatus()
    {
        return $this->user_login_status;
    }

    /**
     * @param mixed $user_login_status
     */
    public function setUserLoginStatus($user_login_status)
    {
        $this->user_login_status = $user_login_status;
    }

    function make_avatar($character)
    {
        $path = "images/" . time() . ".png";
        $image = imagecreate(200, 200);
        $red = rand(0, 255);
        $green = rand(0, 255);
        $blue = rand(0, 255);
        imagecolorallocate($image, $red, $green, $blue);
        $textcolor = imagecolorallocate($image, 255, 255, 255);

        $font = dirname(__FILE__) . "/font/arial.ttf";
        imagettftext($image, 100, 0, 55, 150, $textcolor, $font, $character);
        imagepng($image, $path);
        imagedestroy($image);

        return $path;
    }

    function get_user_data_by_email()
    {
        $user_data = '';

        $query = "SELECT * FROM chat_user_table "
            . "WHERE user_email = :user_email";

        $statement = $this->connect->prepare($query);
        $statement->bindParam(":user_email", $this->user_email);

        if ($statement->execute()) {
            $user_data = $statement->fetch(PDO::FETCH_ASSOC);
        }

        return $user_data;
    }

    function save_data()
    {
        $query = "INSERT INTO chat_user_table SET user_name = :user_name, user_email = :user_email, "
            . " user_password = :user_password, user_profile = :user_profile, user_status = :user_status, "
            . "user_created_on = :user_created_on, user_verification_code = :user_verification_code";

        $statement = $this->connect->prepare($query);
        $statement->bindParam(":user_name", $this->user_name);
        $statement->bindParam(":user_email", $this->user_email);
        $statement->bindParam(":user_password", $this->user_password);
        $statement->bindParam(":user_profile", $this->user_profile);
        $statement->bindParam(":user_status", $this->user_status);
        $statement->bindParam(":user_created_on", $this->user_created_on);
        $statement->bindParam(":user_verification_code", $this->user_verification_code);

        if ($statement->execute()) {
            return true;
        } else {
            var_dump($statement->errorInfo());
            die;
            return false;
        }
    }

    function send_email_verification()
    {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->CharSet = PHPMailer::CHARSET_UTF8;
        $mail->SMTPAuth = true;
        $mail->Host = "smtp.gmail.com";
        $mail->Username = "*******";
        $mail->Password = "*******";
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;
        $mail->setFrom('raicleysantana1@gmail.com', 'RAICLEY');
        $mail->addAddress($this->user_email);
        $mail->isHTML(true);
        $mail->Subject = "Verificação de email para acessar o chat";

        $mensagem = <<< HTML
<p>Oi, {$this->user_name}!</p>
<p>Sua conta no(a) chat está quase pronta. Para ativá-la, por favor confirme o seu endereço de email clicando no link abaixo.</p>
<p><a href="http://localhost/dsv/chat/verify.php?code={$this->user_verification_code}">Ativar minha conta/Confirmar meu email</a></p>
<p>Sua conta não será ativada até que seu email seja confirmado.</p>
<p>Se você não se cadastrou no(a) chat recentemente, por favor ignore este email.</p>
HTML;

        $mail->Body = $mensagem;
        if ($mail->send()) {
            return true;
        } else {
            var_dump($mail->isError());
            die;
        }
    }

    public function is_valid_email_verification_code()
    {
        $query = "SELECT * FROM chat_user_table WHERE user_verification_code = :user_verification_code";

        $statement = $this->connect->prepare($query);
        $statement->bindParam(":user_verification_code", $this->user_verification_code);
        $statement->execute();

        if ($statement->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function enable_user_account()
    {
        $query = "UPDATE chat_user_table SET user_status = :user_status WHERE user_verification_code = :user_verification_code";
        $statement = $this->connect->prepare($query);
        $statement->bindParam(':user_status', $this->user_status);
        $statement->bindParam(':user_verification_code', $this->user_verification_code);

        if ($statement->execute()) {
            return true;
        } else {
            return false;
        }

    }

    public function update_user_login_data()
    {
        $query = "UPDATE chat_user_table SET user_login_status = :user_login_status, user_token = :user_token "
            . "WHERE user_id = :user_id";

        $statement = $this->connect->prepare($query);

        $statement->bindParam(":user_id", $this->user_id);
        $statement->bindParam(":user_login_status", $this->user_login_status);
        $statement->bindParam(":user_token", $this->user_token);

        if ($statement->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function get_user_data_by_id()
    {
        $user_data = array();
        $query = "SELECT * FROM chat_user_table WHERE user_id = :user_id";

        $statement = $this->connect->prepare($query);
        $statement->bindParam(":user_id", $this->user_id);

        try {
            if ($statement->execute()) {
                $user_data = $statement->fetch(PDO::FETCH_ASSOC);
            } else {
                $user_data = array();
            }
        } catch (\Exception $error) {
            echo $error->getMessage();
        }

        return $user_data;
    }

    public function upload_imagem($user_profile)
    {
        $extension = explode('.', $user_profile['name']);
        $new_name = rand() . '.' . $extension[1];
        $destination = "images/{$new_name}";
        move_uploaded_file($user_profile['tmp_name'], $destination);

        return $destination;
    }

    public function update_data()
    {
        $query = "UPDATE chat_user_table "
            . "SET user_name = :user_name, user_email = :user_email, "
            . "user_password = :user_password, user_profile = :user_profile "
            . "WHERE user_id = :user_id ";

        $statement = $this->connect->prepare($query);
        $statement->bindParam(":user_id", $this->user_id);
        $statement->bindParam(":user_name", $this->user_name);
        $statement->bindParam(":user_email", $this->user_email);
        $statement->bindParam(":user_profile", $this->user_profile);
        $statement->bindParam(":user_password", $this->user_password);


        if ($statement->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function get_user_all_data()
    {
        $query = "SELECT * FROM chat_user_table";

        $statement = $this->connect->prepare($query);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    function get_user_all_with_status_count()
    {
        $query = "SELECT user_id, user_name, user_profile, user_login_status, "
            . "(SELECT COUNT(*) FROM chat_message WHERE to_user_id = :user_id AND from_user_id = chat_user_table.user_id AND status = 'No') "
            . "AS count_status FROM chat_user_table";

        $statement = $this->connect->prepare($query);
        $statement->bindParam(":user_id", $this->user_id);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update_user_connection_id()
    {
        $query = "UPDATE chat_user_table SET user_connection_id = :user_connection_id " .
            "WHERE user_token = :user_token";

        $statement = $this->connect->prepare($query);
        $statement->bindParam(':user_connection_id', $this->user_connection_id);
        $statement->bindParam(':user_token', $this->user_token);
        $statement->execute();
    }

    public function get_user_id_from_token()
    {
        $query = "SELECT user_id FROM chat_user_table WHERE user_token = :user_token";

        $statement = $this->connect->prepare($query);
        $statement->bindParam(':user_token', $this->user_token);
        $statement->execute();
        $user_id = $statement->fetch(PDO::FETCH_ASSOC);

        return $user_id;
    }


}