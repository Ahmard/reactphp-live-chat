<!doctype html>
<html>
<head>
    <title>Live Chat</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link href="assets/css/style.css" rel="stylesheet" id="bootstrap-css">
    <style>
        .conn-status {
            margin: 15px 25px;
        }
    </style>
</head>
<body>
    <div class="conn-status font-weight-bold list-group-item">
        Connectivity: <i id="conn-status" class="badge badge-primary">initialising</i>
    </div>
    <div class="container mt-3" id="block-room" style="display:none">
        <div class="card">
            <div class="card-header">
                Choose Room
            </div>
            <div class="card-body">
                <form id="form-choose-room">
                    <input type="text" id="input-name" name="name" value="ahmard" class="form-control mb-2" placeholder="Enter your name">
                    <input type="text" id="input-room" name="room" value="dayi" class="form-control" placeholder="Enter room name">

                    <button type="submit" class="btn btn-block btn-md btn-primary mt-2">Choose</button>
                </form>
            </div>
        </div>
    </div>

    <div class="container mt-5" id="block-chat" style="display:none">
        <div class="card">
            <div class="card-header">
                Live Chat
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

                    <button type="submit" class="btn btn-block btn-md btn-primary mt-2">Send Message</button>
                </form>
            </div>
        </div>
    </div>

    <template id="template-chat-list">
        <div class="chat_list active_chat">
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
                        <b>{{name}}</b><br/>
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

    <script src="assets/js/jquery-3.4.1.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/moment.min.js"></script>
    <script src="assets/js/handlebars.js"></script>
    <script src="assets/js/eventEmitter.min.js"></script>
    <script src="assets/js/socket.js?t=<?=time() ?>"></script>
    <script src="assets/js/chat.js?t=<?=time() ?>"></script>
</body>
</html>