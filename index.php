<?php
session_start();

$error = '';

if (isset($_SESSION['user_data'])) {
    header("location: chatroom.php");
}


if (isset($_POST['login'])) {
    require_once 'database/ChatUser.php';

    $user_object = new ChatUser();
    $user_object->setUserEmail($_POST['user_email']);

    $user_data = $user_object->get_user_data_by_email();

    if (is_array($user_data) && count($user_data) > 0) {
        if ($user_data['user_status'] == "Enable") {
            if ($user_data['user_password'] == $_POST['user_password']) {
                $user_object->setUserId($user_data['user_id']);
                $user_object->setUserLoginStatus('Login');

                if ($user_object->update_user_login_data()) {
                    $_SESSION['user_data'][$user_data['user_id']] = [
                        'id' => $user_data['user_id'],
                        'name' => $user_data['user_name'],
                        'profile' => $user_data['user_profile'],
                    ];

                    header("location: chatroom.php");
                }
            } else {
                $error = "Senha incorreta";
            }
        } else {
            $error = "Por favor, verifique seu endereço de email";
        }
    } else {
        $error = "Endereço de email errado";
    }

    $user_object->setUserPassword($_POST['user_password']);
}
?>

<html>
<head>
    <link
            rel="stylesheet"
            href="css/bootstrap.min.css"
    >

    <!-- Custom styles for this template -->
    <link href="css/global.css" rel="stylesheet">
    <link href="css/signin.css" rel="stylesheet">
    <link href="css/parsley.css" rel="stylesheet">
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
<br>
<br>
<h3 class="text-center">PHP Chat aplicação usando websockets</h3>
<div class="row justify-content-md-center">

    <div class="col-md-4">
        <?php
        if (isset($_SESSION['success_message'])) :?>

            <div class="alert alert-success">
                <?= $_SESSION['success_message']; ?>
            </div>
            <?php
            unset($_SESSION['success_message']);
        endif;

        if ($error != ''): ?>
            <div class="alert alert-danger">
                <?= $error; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<div class="row col-md-12">
    <form method="post" action="index.php" class="form-signin" id="form-login" data-parsley-validate="">
        <h1 class="h3 mb-3 font-weight-normal text-center">Login</h1>

        <label for="inputEmail" class="sr-only">Email</label>
        <input
                type="email"
                name="user_email"
                id="user_email"
                class="form-control"
                placeholder="Entre com seu Email"
                required
                autofocus
        >

        <label for="inputPassword" class="sr-only">Senha</label>
        <input
                type="password"
                name="user_password"
                id="user_password"
                class="form-control"
                placeholder="Entre com sua senha"
                required
                data-parsely-minlength="6"
                data-parsely-maxlength="12"

        >

        <input name="login" class="btn btn-primary btn-block" type="submit" value="Entrar">
        <button class="btn btn-block btn-link">Registrar</button>
        <p class="mt-5 mb-3 text-muted">&copy; 2017-2020</p>
    </form>
</div>
</div>
<script src="js/jquery-3.5.1.min.js"></script>
<script src="js/bootstrap.bundle.js"></script>
<script src="js/parsley.min.js"></script>
<script>
    $(document).ready(function () {
        $("#form-login").parsley();
    });
</script>
</body>
</html>
