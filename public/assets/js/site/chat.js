let chosenRoom;
let chosenName;

const ws = Reactificate.Websocket.connect('ws://' + window.location.host + chatSocketPrefix);
const siteEvent = new Reactificate.EventEmitter();

ws.setAuthToken(TOKEN);
ws.onMessage(message => console.log(message));

$(function () {
    let $blockRoom = $('#block-room');
    let $blockChat = $('#block-chat');
    let $textareaMessage = $('#textarea-send-message');
    let $inputRoom = $('#input-room');
    let $inputName = $('#input-name');
    let $elMessages = $('#messages');
    let elMessages = document.getElementById('messages');
    let $elTypingStatus = $('#div-typing-status');
    let textTimes = 0;

    let templateOutgoingMessage = Handlebars.compile($('#template-inbox-outgoing').html());
    let templateIncomingMessage = Handlebars.compile($('#template-inbox-incoming').html());
    let templateUserJoined = Handlebars.compile($('#template-chat-list').html());
    let templateUserJoinedMessage = Handlebars.compile($('#template-user-joined').html());
    let templateUserLeftMessage = Handlebars.compile($('#template-user-left').html());
    let templateTypingStatus = Handlebars.compile($('#template-typing-status').html());

    let typingStatus;

    const reconnectionInsurance = function () {
        $('#people-list').html('');
    };

    const sendMessage = function () {
        const message = $textareaMessage.val();

        if ('' === message.trim()) {
            $textareaMessage.val(null);
        } else {
            $elMessages.append(templateOutgoingMessage({
                name: chosenName,
                message: message.linkify()
            }));

            //Scroll to last messages
            scrollMessage();

            ws.send('chat.public.send', message).then(function () {
                $textareaMessage.val('');
                textTimes = 0;
            });
        }
    };

    //Tone to be played when new message is received
    const toneMessage = new Howl({
        src: ['/assets/mp3/juntos.mp3'],
        volume: 0.5
    });

    //Tone to be played when user join group
    const toneJoined = new Howl({
        src: ['/assets/mp3/done-for-you.mp3'],
        volume: 0.5
    });

    let scrollMessage = function () {
        if (elMessages.scroll) {
            //Scroll to last messages
            elMessages.scroll(0, elMessages.scrollHeight);
        }
    };

    // Message that will be sent to server when the browser got reconnected
    const joinRoom = async function () {
        if (ws.isOpened()) {
            ws.send('chat.public.join', {
                name: chosenName,
                room: chosenRoom
            }).then(function () {
                chosenRoom = $inputRoom.val();
                chosenName = $inputName.val();
                // Initialize chat block
                constructChatBlock();
                //Display room name
                $('#room-name').html(chosenName + ' @ <i>' + chosenRoom + '</i>');
            });
        } else {
            ws.connect();
        }
    };

    ws.onCommand('system.ping', function () {
        ws.send('system.pong', []);
    });

    //When current user joined a group successfully
    ws.onCommand('chat.public.joined', function () {
        //alert('You joined joined');
    });

    //When user joined the room
    ws.onCommand('chat.public.user-joined', function (response) {
        let addToJoined = function (clientData) {
            $('#people-list').append(templateUserJoined({
                id: clientData['client_id'],
                name: clientData.name
            }));
            //Show message that user joined
            $('#messages').append(templateUserJoinedMessage({
                name: clientData.name
            }));
        };

        if (typeof response.message === 'object') {
            for (let i = 0; i < response.message.length; i++) {
                addToJoined(response.message[i]);
            }
        }

        //Play tone
        toneJoined.play();

        //Scroll message bar
        scrollMessage();
    });

    //When user left the group
    ws.onCommand('chat.public.left', function (response) {
        $('#client-' + response.message['client_id']).remove();

        //Show message that user left
        $('#messages').append(templateUserLeftMessage({
            name: response.message.name
        }));

        //Scroll message bar
        scrollMessage();
    });

    //When new message is received
    ws.onCommand('chat.public.send', function (response) {
        let time = moment(response.time * 1000).format('h:mm:ss');
        let clientId = response.message['client_id'];

        $elMessages.append(templateIncomingMessage({
            name: response.message.user,
            message: response.message.message.linkify(),
            time: time
        }));

        typingStatus.remove(clientId);

        //Play tone
        toneMessage.play();

        //Scroll message bar
        scrollMessage();
    });

    // when browser is connected to server successfully
    ws.onOpen(function () {
        $('#conn-status')
            .attr('class', 'badge badge-success')
            .html('connected');

        if (chosenRoom) joinRoom().then(r => console.log('Room joined'));
    });

    // when server connection is disconnected
    ws.onDisconnect(function () {
        destructChatBlock();
    });

    // when browser is connecting
    ws.onConnecting(function () {
        $('#conn-status')
            .attr('class', 'badge badge-info')
            .html('connecting');
    });

    //when browser is retrying lost connecting
    ws.onReconnecting(function (number) {
        $('#conn-status')
            .attr('class', 'badge badge-warning')
            .html('reconnecting ' + number);
    });

    //When the connection is closed
    ws.onClose(function (error) {
        $('#conn-status')
            .attr('class', 'badge badge-danger')
            .html('closed');

        reconnectionInsurance();
    });

    //When we got an error with the connection
    ws.onError(function (error) {
        $('#conn-status')
            .attr('class', 'badge badge-danger')
            .html(JSON.stringify(error));

        reconnectionInsurance();
    });

    const constructChatBlock = function () {
        $blockRoom.hide();
        $blockChat.removeClass('d-none');

        $textareaMessage.on('input', function () {
            if ($textareaMessage.val() === '') {
            }
        });

        //Leave room
        $('#btn-leave-room').off('click').click(function () {
            ws.send('chat.public.leave', []);
            // Destroy chat block
            destructChatBlock();
        });
    };

    const destructChatBlock = function () {
        $('#people-list').html('');

        $('#messages').html('');

        //Make the button available
        $('#form-choose-room')
            .find('button').eq(0)
            .removeAttr('disabled')
            .html('Join');

        //Change connection status to disconnected
        $('#conn-status')
            .attr('class', 'badge badge-primary')
            .html('ready');

        // Display room chooser
        $blockRoom.show();

        //Hide block chat
        $blockChat.addClass('d-none');
    };

    //Choose room form
    $('#form-choose-room').submit(function (event) {
        event.preventDefault();

        $(event.target).find('button')
            .eq(0)
            .attr('disabled', 'disabled')
            .html('Joining...');

        chosenName = $inputName.val();
        chosenRoom = $inputRoom.val();

        if (chosenRoom.length < 1) {
            $inputRoom.addClass('is-invalid').focus();
            return;
        }

        if (chosenName.length < 1) {
            $inputName.addClass('is-invalid').focus();
            return;
        }

        // Initialize websocket
        joinRoom().then(r => console.log('Room Joined'));

        typingStatus = new TypingStatus();

        typingStatus.init({
            ws: ws,
            command: 'chat.public.typing'
        });

        typingStatus.listen({
            $elTypingStatus: $elTypingStatus,
            templateTypingStatus: templateTypingStatus
        });
    });

    //Listen to textarea typing or deletion
    $textareaMessage.on('keydown', function () {
        typingStatus.send();
    });

    //Listen to textarea enter key press
    $textareaMessage.on('keypress', function (event) {
        if (event.keyCode === 13) {
            sendMessage();
        }
    });

    //Send message form
    $('#form-send-message').submit(function (event) {
        event.preventDefault();
        sendMessage();
    });
});