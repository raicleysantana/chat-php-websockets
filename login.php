<?php


$error = "";
$success_message = "";

if (isset($_POST['register'])) {
    session_start();

    if (isset($_SESSION['user_data'])) {
        header("Location:chatroon.php");
    }

    require_once 'database/ChatUser.php';

    $user_object = new ChatUser();

    $user_data = $user_object->get_user_data_by_email();

    if (is_array($user_data) && count($user_data) > 0) {
        $error = "Esse email já registrado";
    } else {

        $user_object->setUserName($_POST['user_name']);
        $user_object->setUserEmail($_POST['user_email']);
        $user_object->setUserPassword($_POST['user_password']);
        $user_object->setUserProfile($user_object->make_avatar(strtoupper($_POST['user_name'][0])));
        $user_object->setUserStatus('Disabled');
        $user_object->setUserCreatedOn(date('Y-m-d H:i:s'));
        $user_object->setUserVerificationCode(md5(uniqid()));

        if ($user_object->save_data()) {
            if ($user_object->send_email_verification()) {
                $success_message = "Registrado com sucesso. Um email de verificação foi enviado para seu email";
            } else {
                $error = "Ocorreu um error ao registrar";
            }
        } else {
            $error = "Ocorreu um error ao registrar";
        }

    }


}
?>

<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Registrar</title>
    <link
            rel="stylesheet"
            href="css/bootstrap.min.css"
    >

    <!-- Custom styles for this template -->
    <link href="css/global.css" rel="stylesheet">
    <link href="css/signin.css" rel="stylesheet">
</head>
<body>

<style>
    .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    @media (min-width: 768px) {
        .bd-placeholder-img-lg {
            font-size: 3.5rem;
        }
    }
</style>

<div class="row justify-content-center">
    <div class="register-alert">
        <?php if ($error != ''): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <?= $error; ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <?php if ($success_message != ''): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $success_message; ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="row">
    <div class="col-md-12">

    </div>
</div>


<script src="js/jquery-3.5.1.min.js"></script>
<script src="js/bootstrap.bundle.js"></script>
<script src="js/parsley.min.js"></script>
<script>
    $(document).ready(function () {
        $("#form-register").parsley();
    });
</script>
</body>
</html>
