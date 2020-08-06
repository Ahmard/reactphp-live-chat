var chosenRoom;
var chosenName;
var ws;

$(function() {
    var $blockRoom = $('#block-room');
    var $blockChat = $('#block-chat');
    var $inputMessage = $('#input-message');
    var $inputRoom = $('#input-room');
    var $inputName = $('#input-name');
    var textTimes = 0;
    
    var templateOutgoingMessage = Handlebars.compile($('#template-inbox-outgoing').html());
    var templateIncomingMessage = Handlebars.compile($('#template-inbox-incoming').html());
    var templateUserJoined = Handlebars.compile($('#template-chat-list').html());

    var ws = new ASocket({
        url: 'ws://'+window.location.hostname+':10000'
    });

    //When current joined a group successfully
    chatEvent.on('chat.public.joined', function() {
        //alert('You joined joined');
    });

    //When user joined the room
    chatEvent.on('chat.public.ujoined', function(response) {
        $('#people-list').append(templateUserJoined({
            name: response.message.name
        }));
    });
    //When new message is received
    chatEvent.on('chat.public.send', function(response) {
        var time = moment(response.time * 1000).format('h:mm:ss');

        $('#messages').append(templateIncomingMessage({
            name: response.message.user,
            message: response.message.message,
            time: time
        }));
    });
    //When browser is connected to server successfully
    chatEvent.on('conn.connected', function() {
        $('#conn-status')
        .attr('class', 'badge badge-success')
        .html('connected')
    });
    //when browser is connecting
    chatEvent.on('conn.connecting', function() {
        $('#conn-status')
        .attr('class', 'badge badge-info')
        .html('connecting')
    });
    //when browser is retring lost connecting
    chatEvent.on('conn.reconnecting', function(number) {
        $('#conn-status')
        .attr('class', 'badge badge-warning')
        .html('reconnecting '+number)
    });
    //When the connection is closed
    chatEvent.on('conn.closed', function(error) {
        $('#conn-status')
        .attr('class', 'badge badge-error')
        .html(JSON.stringify(error))
    });
    //When we got an error with the connection
    chatEvent.on('conn.error', function(error) {
        $('#conn-status')
        .attr('class', 'badge badge-error')
        .html(JSON.stringify(error))
    });

    var initChatBlock = function() {
        $blockRoom.hide();
        $blockChat.removeClass('d-none');

        var textTimesInterval;
        var runTextIntervalTimeout;

        var textInterval = function() {
            textTimesInterval = setInterval(function() {
                $inputMessage.val('Hello('+textTimes+')');
                textTimes++;
            }, 1000);
        };

        $inputMessage.on('input', function() {
            if ($inputMessage.val() === ''){
                clearTimeout(runTextIntervalTimeout)
                runTextIntervalTimeout = setTimeout(textInterval, 5000);
                return;
            }

            clearTimeout(runTextIntervalTimeout)
            clearInterval(textTimesInterval);
        });

        textInterval();
    }

    //Choose room form
    $('#form-choose-room').submit(function(event) {
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

        var payload = {
            command: 'chat.public.join',
            name: chosenName,
            room: chosenRoom
        };

        //Message that will be sent to server when the browser got reconnected
        ws.setReconnectPayload(payload);
    
        ws.send(payload, function() {
            chosenRoom = $inputRoom.val();
            chosenName = $inputName.val();
            initChatBlock();
        });

    });

    //Send message form
    $('#form-send-message').submit(function(event) {
        event.preventDefault();
        var payload = {
            command: 'chat.public.send',
            message: $inputMessage.val(),
            time: (new Date()).getTime()
        }
        var time = moment((new Date()).getTime()).format('h:mm:ss');

        $('#messages').append(templateOutgoingMessage({
            name: chosenName,
            message: $inputMessage.val(),
            time: time
        }));
        
        ws.send(payload, function() {
            $inputMessage.val('')
            textTimes = 0;
        })
    });
});