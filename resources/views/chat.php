<!doctype html>
<html>

<head>
    <title>Live Chat</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="<?= url('assets/css/bootstrap.min.css') ?>">
    <link href="<?= url('assets/css/style.css') ?>" rel="stylesheet" id="bootstrap-css">
    <link href="<?= url('assets/css/fontawesome-all.min.css') ?>" rel="stylesheet" id="bootstrap-css">
    <style>
        .conn-status {
            margin: 15px 25px;
        }

        .small-text {
            font-size: 11px;
        }
    </style>
</head>

<body>
    <div class="conn-status font-weight-bold list-group-item">
        <i class="fa fa-info-circle"></i> Connectivity: <i id="conn-status" class="badge badge-primary">ready</i>
    </div>
    <div class="container" id="block-room">
        <div class="card">
            <div class="card-header">
                Choose Room
            </div>
            <div class="card-body">
                <form id="form-choose-room">
                    <div class="form-group">
                        <label for="input-name">User Name:</label><br>
                        <input type="text" id="input-name" name="name" value="<?= $room['user'] ?>" class="form-control mb-2" placeholder="Enter your name">
                    </div>
                    <div class="form-group">
                        <label for="input-room">Room/Group/Channel Name:</label>
                        <input type="text" id="input-room" name="room" value="<?= $room['name'] ?>" class="form-control" placeholder="Enter room name">
                    </div>

                    <button type="submit" class="btn btn-block btn-md btn-primary mt-2">
                        Join
                        <i class="fa fa-hiking"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="container mt-3 d-none" id="block-chat">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <div class="badge badge-primary p-2">
                        <i class="fa fa-home"></i>
                        <b id="room-name"></b>
                    </div>
                    <div>
                        <button id="btn-leave-room" class="btn btn-sm btn-danger"><i class="fa fa-sign-out-alt"></i> Leave Room</button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="messaging">
                        <div class="inbox_msg">
                            <div id="inbox-people" class="inbox_people">
                                <div class="inbox_chat" id="people-list">
                                </div>
                            </div>
                            <div class="mesgs">
                                <div class="msg_history" id="messages">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <form id="form-send-message">
                    <textarea rows="3" id="input-message" name="message" class="form-control" placeholder="Type your message..."></textarea>

                    <button type="submit" class="btn btn-block btn-md btn-primary mt-2">
                        Send Message
                        <i class="fa fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <template id="template-chat-list">
        <div class="chat_list active_chat" id="client-{{id}}">
            <div class="chat_people">
                <div class="chat_ib">
                    <h6>{{name}}</h6>
                </div>
            </div>
        </div>
    </template>

    <template id="template-inbox-incoming">
        <div class="incoming_msg mt-3">
            <div class="received_msg">
                <div class="received_withd_msg">
                    <p>
                        <b>{{name}}</b><br />
                        <span class="ml-3">{{message}}</span>
                    </p>
                    <span class="time_date">{{time}}</span>
                </div>
            </div>
        </div>
    </template>
    <template id="template-inbox-outgoing">
        <div class="outgoing_msg mt-3">
            <div class="sent_msg">
                <p class="p-2">
                    {{message}}
                </p>
                <span class="time_date">{{time}}</span>
            </div>
        </div>
    </template>
    <template id="template-user-joined">
        <div class="text-center"> 
            <span class="bg-info shadow-1 p-1 small-text">
                <i class="fa fa-user"></i> 
                {{name}} joined
            </span>
        </div>
    </template>
    <template id="template-user-left">
        <div class="text-center"> 
            <span class="bg-warning shadow-1 p-1 small-text">
                <i class="fa fa-user"></i> 
                {{name}} left
            </span>
        </div>
    </template>

    <script>
        var socket_url = '<?= $socket_url ?>';
    </script>
    <script src="<?= url('assets/js/jquery-3.5.1.min.js') ?>"></script>
    <script src="<?= url('assets/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= url('assets/js/moment.min.js') ?>"></script>
    <script src="<?= url('assets/js/handlebars.min-v4.7.6.js') ?>"></script>
    <script src="<?= url('assets/js/EventEmitter.min.js') ?>"></script>
    <script src="<?= url('assets/js/howler.min.js') ?>"></script>
    <script src="<?= url('assets/js/socket.js') ?>"></script>
    <script src="<?= url('assets/js/chat.js') ?>"></script>
</body>

</html>