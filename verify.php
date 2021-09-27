<?php


$error = "";

session_start();

$success_message = "";

if (isset($_GET['code'])) {
    require_once 'database/ChatUser.php';

    $user_object = new ChatUser();
    $user_object->setUserVerificationCode($_GET['code']);

    if ($user_object->is_valid_email_verification_code()) {
        $user_object->setUserStatus('Enable');

        if ($user_object->enable_user_account()) {
            $_SESSION['success_message'] = "Seu email foi verificado com sucesso, agora você pode fazer login";
            header("Location:index.php");
        } else {
            $error = "Algo deu errado";
        }
    } else {
        $error = "Algo deu errado";
    }
}

?>
