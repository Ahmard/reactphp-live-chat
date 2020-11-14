let chosenRoom;
let chosenName;
let ws = new SocketWrapper({
    url: 'ws://' + window.location.host + chatSocketPrefix,
});

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

    let textInterval;
    let textTimesInterval;
    let runTextIntervalTimeout;

    let typingStatus;

    let reconnectionInsurance = function () {
        $('#people-list').html('');
    };

    let sendMessage = function () {
        let payload = {
            command: 'chat.public.send',
            message: $textareaMessage.val()
        };

        if ('' === payload.message.trim()) {
            $textareaMessage.val(null);
        } else {
            $elMessages.append(templateOutgoingMessage({
                name: chosenName,
                message: $textareaMessage.val()
            }));

            //Scroll to last messages
            scrollMessage();

            ws.send(payload, function () {
                $textareaMessage.val('');
                textTimes = 0;
            });
        }
    };

    //Tone to be played when new message is received
    let toneMessage = new Howl({
        src: ['/assets/mp3/juntos.mp3'],
        volume: 1
    });

    //Tone to be played when user join group
    let toneJoined = new Howl({
        src: ['/assets/mp3/done-for-you.mp3'],
        volume: 1
    });

    let scrollMessage = function () {
        //Scroll to last messages
        elMessages.scroll(0, elMessages.scrollHeight);
    };

    //When current user joined a group successfully
    siteEvent.on('chat.public.joined', function () {
        //alert('You joined joined');
    });

    //When user joined the room
    siteEvent.on('chat.public.user-joined', function (response) {
        let addToJoined = function (clientData) {
            $('#people-list').append(templateUserJoined({
                id: clientData.client_id,
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
    siteEvent.on('chat.public.left', function (response) {
        $('#client-' + response.message.client_id).remove();
        //Show message that user left
        $('#messages').append(templateUserLeftMessage({
            name: response.message.name
        }));

        //Scroll message bar
        scrollMessage();
    });

    //When new message is received
    siteEvent.on('chat.public.send', function (response) {
        let time = moment(response.time * 1000).format('h:mm:ss');
        let clientId = response.message.client_id;

        $elMessages.append(templateIncomingMessage({
            name: response.message.user,
            message: response.message.message,
            time: time
        }));

        typingStatus.remove(clientId);

        //Play tone
        toneMessage.play();

        //Scroll message bar
        scrollMessage();
    });

    //When browser is connected to server successfully
    siteEvent.on('conn.connected', function () {
        $('#conn-status')
            .attr('class', 'badge badge-success')
            .html('connected');
    });

    //When browser is connected to server successfully
    siteEvent.on('conn.disconnected', function () {
        destructChatBlock();
    });

    //when browser is connecting
    siteEvent.on('conn.connecting', function () {
        $('#conn-status')
            .attr('class', 'badge badge-info')
            .html('connecting');
    });

    //when browser is retring lost connecting
    siteEvent.on('conn.reconnecting', function (number) {
        $('#conn-status')
            .attr('class', 'badge badge-warning')
            .html('reconnecting ' + number);
    });

    //When the connection is closed
    siteEvent.on('conn.closed', function (error) {
        $('#conn-status')
            .attr('class', 'badge badge-danger')
            .html(JSON.stringify(error));

        reconnectionInsurance();
    });

    //When we got an error with the connection
    siteEvent.on('conn.error', function (error) {
        $('#conn-status')
            .attr('class', 'badge badge-danger')
            .html(JSON.stringify(error));

        reconnectionInsurance();
    });

    let constructChatBlock = function () {
        $blockRoom.hide();
        $blockChat.removeClass('d-none');

        textInterval = function () {
            textTimesInterval = setInterval(function () {
                $textareaMessage.val('Hello(' + textTimes + ')');
                textTimes++;
            }, 1000);
        };

        $textareaMessage.on('input', function () {
            if ($textareaMessage.val() === '') {
                clearTimeout(runTextIntervalTimeout);
                runTextIntervalTimeout = setTimeout(textInterval, 5000);
                return;
            }

            clearTimeout(runTextIntervalTimeout);
            clearInterval(textTimesInterval);
        });

        textInterval();
    };

    let destructChatBlock = function () {
        clearInterval(textInterval);

        clearInterval(textTimesInterval);

        clearTimeout(runTextIntervalTimeout);

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
        //Show room chooser
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

        //Initialize socket wrapper
        ws.connect( function () {
            let payload = {
                command: 'chat.public.join',
                name: chosenName,
                room: chosenRoom
            };

            //Message that will be sent to server when the browser got reconnected
            ws.setReconnectPayload(payload);

            ws.send(payload, function () {
                chosenRoom = $inputRoom.val();
                chosenName = $inputName.val();
                constructChatBlock();
                //Display room name
                $('#room-name').html(chosenName + ' @ <i>' + chosenRoom + '</i>');
            });
        });

        typingStatus = new TypingStatus();

        typingStatus.init({
            ws: ws,
            command: 'chat.public.typing'
        });

        typingStatus.listen({
            siteEvent: siteEvent,
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

    //Leave room
    $('#btn-leave-room').click(function () {
        ws.send({
            command: 'chat.public.leave',
        }, () => ws.disconnect());
    });
});