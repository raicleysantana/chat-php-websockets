<?php
session_start();

if (!isset($_SESSION['user_data'])) {
    header("location: index.php");
}

require 'database/ChatUser.php';

$user_object = new ChatUser();

$user_id = '';

foreach ($_SESSION['user_data'] as $key => $value) {
    $user_id = $value['id'];
}

$user_object->setUserId($user_id);
$user_data = $user_object->get_user_data_by_id();

$message = '';

if (isset($_POST['edit'])) {
    $user_profile = $_POST['hidden_user_profile'];

    if ($_FILES['user_profile']['name'] != '') {
        $user_profile = $user_object->upload_imagem($_FILES['user_profile']);
        $_SESSION['user_data'][$user_id]['profile'] = $user_profile;
    }

    $user_object->setUserName($_POST['user_name']);
    $user_object->setUserEmail($_POST['user_email']);
    $user_object->setUserPassword($_POST['user_password']);
    $user_object->setUserProfile($user_profile);
    $user_object->setUserId($user_id);

    if ($user_object->update_data()) {
        $message = '<div class="alert alert-success">Perfil atualizado com sucesso</div>';
    } else {

    }
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
<div class="container">
    <br>
    <br>
    <h3 class="text-center">PHP Chat aplicação usando websockets</h3>
    <br>
    <?php echo $message; ?>
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">Perfil</div>
                <div class="col-md-6 text-right">
                    <a class="btn btn-warning btn-sm" href="chatroom.php">
                        Ir para o Chat
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form action="" method="post" id="form-profile" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="user_name">Nome</label>
                    <input
                            type="text"
                            id="user_name"
                            name="user_name"
                            class="form-control"
                            placeholder="Entre com seu nome"
                            required
                            autofocus
                            value="<?= $user_data['user_name'] ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="user_email">Email</label>
                    <input
                            type="text"
                            id="user_email"
                            name="user_email"
                            class="form-control"
                            placeholder="Entre com seu email"
                            required
                            autofocus
                            readonly
                            value="<?= $user_data['user_email'] ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="user_password">Senha</label>
                    <input
                            type="password"
                            id="user_password"
                            name="user_password"
                            class="form-control"
                            placeholder="Entre com sua senha"
                            required
                            data-parsley-minlength="6"
                            data-parsley-maxlength="12"
                            value="<?= $user_data['user_password'] ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="user_password">Perfil</label>
                    <br>
                    <input type="file" name="user_profile" id="user_profile">
                    <br>
                    <img
                            class="img-fluid img-thumbnail mt-3 rounded-circle"
                            src="<?= $user_data['user_profile'] ?>"
                            style="width: 100px"
                    >
                    <input type="hidden" name="hidden_user_profile" value="<?= $user_data['user_profile'] ?>">
                    <div class="form-group text-center">
                        <input type="submit" name="edit" class="btn btn-primary" value="Editar">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="js/jquery-3.5.1.min.js"></script>
<script src="js/bootstrap.bundle.js"></script>
<script src="js/parsley.min.js"></script>
<script>
    $(document).ready(function () {
        $("#form-profile").parsley();
    });
</script>
</body>
</html>
