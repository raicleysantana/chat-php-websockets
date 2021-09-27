<?php
session_start();

if (!isset($_SESSION['user_data'])) {
    header("location: index.php");
}

require 'database/ChatRooms.php';
require 'database/ChatUser.php';

$chat_object = new ChatRooms();

$chat_data = $chat_object->get_all_chat_data();

$user_object = new ChatUser();
$user_data = $user_object->get_user_all_data();

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
    <script src="https://kit.fontawesome.com/dae326310d.js" crossorigin="anonymous"></script>
</head>
<body>
<style>
    #messages {
        height: 200px;
        background: whitesmoke;
        overflow: auto;
    }

    #messages_area {
        height: 300px;
        overflow-y: auto;
        background: #e6e6e6;
    }
</style>
<div class="container">
    <br>
    <br>
    <h3 class="text-center">PHP Chat aplicação usando websockets</h3>
    <br>
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    Chat
                </div>
                <div class="card-body" id="messages_area">
                    <?php
                    foreach ($chat_data as $chat):
                        if (isset($_SESSION['user_data'][$chat['user_id']])):
                            $from = "Me";
                            $row_class = "row justify-content-end";
                            $background_class = "alert-success";
                        else:
                            $from = $chat['user_name'];
                            $row_class = 'row justify-content-start';
                            $background_class = 'text-dark alert-light';
                        endif;

                        ?>
                        <div class="<?= $row_class ?>">
                            <div class="col-sm-10">
                                <div class="shadow-sm alert <?= $background_class ?>">
                                    <b><?= $from . ' - ' . $chat['msg'] ?></b>
                                    <br>
                                    <div class="text-right">
                                        <small><i><?= $chat['created_on'] ?></i></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="card-footer" style="padding: 2px">
                    <form action="" method="post" id="form-chat_room">
                        <div class="input-group mb-3">
                        <textarea
                                class="form-control"
                                rows="1"
                                id="chat_message"
                                name="chat_message"
                                placeholder="Digite sua mensagem aqui"
                                data-parsley-maxlength="1000"
                                required
                        ></textarea>
                            <div class="input-group-append">
                                <button
                                        type="submit"
                                        name="send"
                                        id="send"
                                        class="btn btn-primary">
                                    Enviar
                                </button>
                            </div>
                        </div>

                        <div id="validation_error">

                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <?php
            $login_user_id = '';

            foreach ($_SESSION['user_data'] as $key => $value) :
                $login_user_id = $value['id'];

                ?>
                <input type="hidden" name="login_user_id" id="login_user_id" value="<?= $login_user_id ?>">
                <div class="mt-3 mb-3 text-center">
                    <img
                            class="img-fluid img-thumbnail rounded-circle"
                            src="<?= $value['profile'] ?>"
                            style="width: 150px"
                    >
                    <h3 class="mt-2"><?= $value['name'] ?></h3>
                    <a class="btn btn-secondary mt-2 mb-2" href="profile.php">Editar</a>
                    <input
                            class="btn btn-primary mt-2 mb-2"
                            type="button"
                            name="logout"
                            id="logout"
                            value="Logout"
                    >
                </div>
            <?php endforeach; ?>

            <div class="card mt-3">
                <div class="card-header">
                    Lista de Usuários
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <?php
                        if (count($user_data) > 0):
                            foreach ($user_data as $key => $user):
                                $icon = '<i class="fa fa-circle text-danger"></i>';
                                if ($user['user_login_status'] == 'Login') {
                                    $icon = '<i class="fa fa-circle text-success"></i>';
                                }

                                if ($user['user_id'] != $login_user_id) {
                                    echo '<a class="list-group-item list-group-item-action">
                                            <img class="img-fluid rounded-circle img-thumbnail"  style="width: 50px" src="' . $user['user_profile'] . '">
                                            <span class="ml-1">
                                            <strong>' . $user['user_name'] . '</strong>
                                            </span>
                                            <span class="mt-2 float-right">' . $icon . ' </span>
                                    </a > ';
                                }
                            endforeach;
                        endif;
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="js/jquery-3.5.1.min.js"></script>
<script src="js/bootstrap.bundle.js"></script>
<script src="js/parsley.min.js"></script>
<script>
    $(document).ready(function () {

        var conn = new WebSocket('ws://localhost:8080/dsv/chat');
        conn.onopen = function (e) {
            console.log("Connection established!");
        };

        conn.onmessage = function (e) {
            console.log(e.data);

            var data = JSON.parse(e.data);
            var row_class = '';
            var background_class = ''

            if (data.from == 'Me') {
                row_class = 'row justify-content-end';
                background_class = 'alert-success';
            } else {
                row_class = 'row justify-content-start';
                background_class = 'text-dark alert-light';
            }

            var html_data = `<div class="${row_class}">
                                <div class="col-sm-10">
                                    <div class="shadow-sm alert ${background_class}"><b>${data.from} - ${data.msg}<br></b>
                                    <div class="text-right"><small><i>${data.dt}</i></small></div>
                                </div>
                                </div>
                            </div>`;

            $("#messages_area").append(html_data);
            $("#chat_message").val('');
        };

        conn.onclose = function (e) {
            alert('tetes');
        }

        $("#form-chat_room").parsley();

        $("#messages_area").scrollTop($("#messages_area")[0].scrollHeight);

        $("#form-chat_room").on("submit", function (e) {
            e.preventDefault();

            if ($("#form-chat_room").parsley().isValid()) {
                var user_id = $("#login_user_id").val();
                var message = $("#chat_message").val();
                var data = {
                    userId:
                    user_id,
                    msg: message
                };

                conn.send(JSON.stringify(data));
                $("#chat_message").val('');

                $("#messages_area").scrollTop($("#messages_area")[0].scrollHeight);
            }
        });

        $("#logout").click(() => {
            user_id = $("#login_user_id").val();

            $.ajax({
                url: "action.php",
                method: "POST",
                data: {
                    user_id,
                    action: "leave"
                },
                success: function (data) {
                    var response = JSON.parse(data);

                    if (response.status == 1) {
                        location = 'index.php';
                    }
                }
            });
        });
    });
</script>
</body>
</html>
