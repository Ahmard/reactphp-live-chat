<?php require(view_path('layout/header.php')); ?>
    <link rel="stylesheet" href="/assets/css/style.css">

    <div class="mx-3 mb-3 z-depth-2 conn-status font-weight-bold list-group-item">
        <i class="fa fa-info-circle"></i> Connectivity: <i id="conn-status" class="badge badge-primary">ready</i>
    </div>
    <div class="container" id="block-room">
        <div class="card z-depth-3">
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
        <div class="text-center mt-1">
            <span class="bg-info shadow-1 p-1 small-text">
                <i class="fa fa-user"></i> 
                {{name}} joined
            </span>
        </div>
    </template>
    <template id="template-user-left">
        <div class="text-center mt-1">
            <span class="bg-warning shadow-1 p-1 small-text">
                <i class="fa fa-user"></i> 
                {{name}} left
            </span>
        </div>
    </template>

<?php require(view_path('layout/footer.php')); ?>

<script>
    const chatSocketPrefix = '<?= $socket_prefix ?>';
</script>
<script src="/assets/js/site/chat.js"></script>