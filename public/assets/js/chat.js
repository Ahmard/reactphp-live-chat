var chosenRoom;
var chosenName;
var ws;

$(function () {
    var $blockRoom = $('#block-room');
    var $blockChat = $('#block-chat');
    var $inputMessage = $('#input-message');
    var $inputRoom = $('#input-room');
    var $inputName = $('#input-name');
    var textTimes = 0;

    var templateOutgoingMessage = Handlebars.compile($('#template-inbox-outgoing').html());
    var templateIncomingMessage = Handlebars.compile($('#template-inbox-incoming').html());
    var templateUserJoined = Handlebars.compile($('#template-chat-list').html());
    var templateUserJoinedMessage = Handlebars.compile($('#template-user-joined').html());
    var templateUserLeftMessage = Handlebars.compile($('#template-user-left').html());

    var textInterval;
    var textTimesInterval;
    var runTextIntervalTimeout;
    
    let reconnectionInsurrance = function () {
        $('#people-list').html('');
    };

    //When current user joined a group successfully
    chatEvent.on('chat.public.joined', function () {
        //alert('You joined joined');
    });

    //When user joined the room
    chatEvent.on('chat.public.ujoined', function (response) {
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
    });

    //When user left the group
    chatEvent.on('chat.public.left', function (response) {
        $('#client-' + response.message.client_id).remove();
        //Show message that user left
        $('#messages').append(templateUserLeftMessage({
            name: response.message.name
        }));
    });

    //When new message is received
    chatEvent.on('chat.public.send', function (response) {
        var time = moment(response.time * 1000).format('h:mm:ss');

        $('#messages').append(templateIncomingMessage({
            name: response.message.user,
            message: response.message.message,
            time: time
        }));
    });

    //When browser is connected to server successfully
    chatEvent.on('conn.connected', function () {
        $('#conn-status')
            .attr('class', 'badge badge-success')
            .html('connected');
    });

    //When browser is connected to server successfully
    chatEvent.on('conn.disconnected', function () {
        destructChatBlock();
    });

    //when browser is connecting
    chatEvent.on('conn.connecting', function () {
        $('#conn-status')
            .attr('class', 'badge badge-info')
            .html('connecting');
    });

    //when browser is retring lost connecting
    chatEvent.on('conn.reconnecting', function (number) {
        $('#conn-status')
            .attr('class', 'badge badge-warning')
            .html('reconnecting ' + number);
    });

    //When the connection is closed
    chatEvent.on('conn.closed', function (error) {
        $('#conn-status')
            .attr('class', 'badge badge-danger')
            .html(JSON.stringify(error));

        reconnectionInsurrance();
    });

    //When we got an error with the connection
    chatEvent.on('conn.error', function (error) {
        $('#conn-status')
            .attr('class', 'badge badge-danger')
            .html(JSON.stringify(error));

        reconnectionInsurrance();
    });

    var constructChatBlock = function () {
        $blockRoom.hide();
        $blockChat.removeClass('d-none');

        textInterval = function () {
            textTimesInterval = setInterval(function () {
                $inputMessage.val('Hello(' + textTimes + ')');
                textTimes++;
            }, 1000);
        };

        $inputMessage.on('input', function () {
            if ($inputMessage.val() === '') {
                clearTimeout(runTextIntervalTimeout);
                runTextIntervalTimeout = setTimeout(textInterval, 5000);
                return;
            }

            clearTimeout(runTextIntervalTimeout);
            clearInterval(textTimesInterval);
        });

        textInterval();
    };
    
    var destructChatBlock = function(){
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

        ws = new ASocket({
            url: 'ws://' + window.location.hostname + ':10000'
        }, function () {
            var payload = {
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
    });

    //Send message form
    $('#form-send-message').submit(function (event) {
        event.preventDefault();
        var payload = {
            command: 'chat.public.send',
            message: $inputMessage.val(),
            time: (new Date()).getTime()
        };
        var time = moment((new Date()).getTime()).format('h:mm:ss');

        $('#messages').append(templateOutgoingMessage({
            name: chosenName,
            message: $inputMessage.val(),
            time: time
        }));

        ws.send(payload, function () {
            $inputMessage.val('');
            textTimes = 0;
        });
    });

    //Leave room
    $('#btn-leave-room').click(function () {
        ws.send({
            command: 'chat.public.leave',
            time: (new Date()).getTime()
        }, () => ws.disconnect());
    });
});