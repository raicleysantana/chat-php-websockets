<?php
session_start();

if (!isset($_SESSION['user_data'])) {
    header("location: index.php");
}

require 'database/ChatRooms.php';
require 'database/ChatUser.php';

$chat_object = new ChatRooms();

$chat_data = $chat_object->get_all_chat_data();

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
<div>

    <div class="row">

        <div class="col-lg-4 col-md-4 col-sm-5" style="background-color: #f1f1f1; height: 100vh">

            <?php
            $login_user_id = '';

            $token = '';

            foreach ($_SESSION['user_data'] as $key => $value) :
                $login_user_id = $value['id'];
                $token = $value['token'];
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

            <input type="hidden" name="login_user_id" id="login_user_id" value="<?= $login_user_id; ?>">

            <input type="hidden" name="is_active_chat" id="is_active_chat" value="No">

            <?php
            $user_object = new ChatUser();

            $user_object->setUserId($login_user_id);
            $user_data = $user_object->get_user_all_with_status_count();
            ?>
            <div class="card mt-3">
                <div class="card-header">
                    Lista de Usuários
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <?php
                        foreach ($user_data as $key => $user) {
                            $icon = '<i class="fa fa-circle text-danger"></i>';

                            if ($user['user_login_status'] === 'Login') {
                                $icon = '<i class="fa fa-circle text-success"></i>';
                            }

                            if ($user['user_id'] != $login_user_id) {
                                if ($user['count_status'] > 0) {
                                    $total_unread_message = '<span class="badge badge-danger badge-pill">' . $user['count_status'] . '</span>';
                                } else {
                                    $total_unread_message = '';
                                }

                                echo '<a class="list-group-item list-group-item-action select_user" style="cursor: pointer" data-userid="' . $user['user_id'] . '">
                                            <img src="' . $user['user_profile'] . '" class="img-fluid rounded-circle img-thumbnail" width="50">
                                            <span class="ml-1">
                                            <strong>
                                            <span id="list_user_name_' . $user['user_id'] . '">' . $user['user_name'] . '
                                            </span>
                                            <span id="userid_' . $user['user_id'] . '">' . $total_unread_message . '</span>
                                            </strong>
                                            </span>
                                            <span class="mt-2 float-right" id="userstatus_' . $user['user_id'] . '">' . $icon . '</span>
                                        </a>';
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8 col-md-8 col-sm-7">

            <h2>Chat Privado</h2>
            <hr>
            <br>
            <div id="chat_area"></div>
        </div>
    </div>
</div>
<script src="js/jquery-3.5.1.min.js"></script>
<script src="js/bootstrap.bundle.js"></script>
<script src="js/parsley.min.js"></script>
<script>
    $(document).ready(function () {

        var receiver_userid = '';

        var conn = new WebSocket('ws://localhost:8080/dsv/chat/?token=<?= $token; ?>');
        conn.onopen = function (e) {
            console.log("Connection established!");
        };

        conn.onmessage = function (e) {
            var data = JSON.parse(e.data);

            var row_class = '';
            var background_class = '';

            if (data.from == 'Eu') {
                row_class = 'row justify-content-start';
                background_class = 'alert-primary';
            } else {
                row_class = 'row justify-content-end';
                background_class = 'alert-success';
            }

            if (receiver_userid == data.userId || data.from == 'Eu') {
                if ($('#is_active_chat').val() == 'Yes') {
                    var html_data = `<div class="${row_class}">`;
                    html_data += `<div class="col-sm-10">`;
                    html_data += `<div class="shadow-sm alert ${background_class}">`;
                    html_data += `<b>${data.from} - </b>${data.msg}<br>`;
                    html_data += `<div class="text-right">`;
                    html_data += `<small><i>${data.datetime}</i></small>`;
                    html_data += `</div>`;
                    html_data += `</div>`;
                    html_data += `</div>`;
                    html_data += `</div>`;

                    $('#messages_area').append(html_data);
                    $('#messages_area').scrollTop($('#messages_area')[0].scrollHeight);
                    $('#chat_message').val('');
                }
            }
        }


        conn.onclose = function (e) {
            console.log('Connection close');
        }

        function make_chat_area(user_name) {
            var html = `
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col col-sm-6">
                                    <b>Chat com <span class="text-danger" id="chat_user_name">${user_name}</span></b>
                                </div>
                                 <div class="col col-sm-6 text-right">
                                    <a href="chatroom.php" class="btn btn-success btn-sm" style="margin-right: 5px">Chat em grupo</a>
                                    <button type="button" class="close" id="close_chat_area" data-dismiss="alert" aria-label="Close">
                                          <span aria-hidden="true">&times;</span>
                                    </button>
                                 </div>
                            </div>
                        </div>
                        <div class="card-body" id="messages_area">

                        </div>
                    </div>
                <form id="chat_form" method="POST" data-parsley-erros-container="#validation_error">
                   <div class="input-group mb-3" style="height: 7vh">
                        <textarea
                            class="form-control"
                            id="chat_message"
                            name="chat_message"
                            placeholder="Digite aqui sua messagem"
                            data-parsley-maxlength="1000"
                            data-parsley-pattern="/^[a-zA-Z0-9]+$/"
                            required
                        ></textarea>

                        <div class="input-group-append">
                            <button type="submit" name="send" id="send" class="btn btn-success">
                                <i class="fa fa-paper-plane"></i>
                            </button>
                        </div>
                    </div>
                    <div id="validation_error"></div>
                    <br>
                </form>
                `;

            $('#chat_area').html(html);

            $('#chat_form').parsley();
        }

        $(document).on('click', '.select_user', function () {
            receiver_userid = $(this).data('userid');

            var from_user_id = $('#login_user_id').val();

            var receiver_user_name = $(`#list_user_name_${receiver_userid}`).text();

            $('.select_user.active').removeClass('active');

            $(this).addClass('active');

            make_chat_area(receiver_user_name);

            $('#is_active_chat').val('Yes');

            $.ajax({
                url: 'action.php',
                method: 'POST',
                data: {
                    action: 'fetch_chat',
                    to_user_id: receiver_userid,
                    from_user_id: from_user_id
                },
                dataType: 'JSON',
                success: function (data) {
                    console.log(data);
                    if (data.length > 0) {
                        var html_data = '';

                        for (var count = 0; count < data.length; count++) {
                            var row_class = '';
                            var background_class = '';
                            var user_name = '';

                            if (data[count].from_user_id == from_user_id) {
                                row_class = 'row justify-content-start';
                                background_class = 'alert-primary';
                                user_name = 'Eu';
                            } else {
                                row_class = 'row justify-content-end';
                                background_class = 'alert-success';
                                user_name = data[count].from_user_name;
                            }

                            html_data += `<div class="${row_class}">`;
                            html_data += `<div class="col-sm-10">`;
                            html_data += `<div class="shadow alert ${background_class}">`;
                            html_data += `<b>${user_name} - </b>`;
                            html_data += `${data[count].chat_message} <br/>`;
                            html_data += `<div class="text-right">`;
                            html_data += `<small><i>${data[count].timestamp}</i></small>`
                            //html_data += ``
                            html_data += `</div>`;
                            html_data += `</div>`;
                            html_data += `</div>`;
                            html_data += `</div>`;
                        }

                        $('#userid_' + receiver_userid).html('');

                        $('#messages_area').html(html_data);

                        $('#messages_area').scrollTo($('#messages_area')[0].scrollHeight);
                    }
                }
            });
        });

        $(document).on('click', '#close_chat_area', function () {
            $('#chat_area').html('');
            $('.select_user.active').removeClass('active');
        });

        $(document).on('submit', '#chat_form', function (e) {
            e.preventDefault();

            if ($('#chat_form').parsley().isValid()) {
                var user_id = $('#login_user_id').val();
                var message = $('#chat_message').val();
                var data = {
                    userId: user_id,
                    msg: message,
                    receiver_userid: receiver_userid,
                    command: 'Private'
                }

                conn.send(JSON.stringify(data));

            }
        });
    });
</script>
</body>
</html>
