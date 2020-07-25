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

    ws = new ASocket({
        url: 'ws://'+window.location.hostname+':10000'
    });
    
    if (! chosenRoom) {
        $blockRoom.show();
    } else {
        $blockChat.show();
    }

    // var convertTime = function(timestpa)

    chatEvent.on('chat.public.joined', function() {
        //alert('You joined joined');
    });

    chatEvent.on('chat.public.ujoined', function(response) {
        var template = Handlebars.compile($('#template-chat-list').html());
        $('#people-list').append(template({
            name: response.message.name
        }));
    });

    chatEvent.on('chat.public.send', function(response) {
        var template = Handlebars.compile($('#template-inbox-incoming').html());
        var time = moment(response.time * 1000).format('h:mm:ss');

        $('#messages').append(template({
            name: response.message.user,
            message: response.message.message,
            time: time
        }));
    });

    chatEvent.on('conn.connected', function() {
        $('#conn-status')
        .attr('class', 'badge badge-success')
        .html('connected')
    });


    chatEvent.on('conn.connecting', function() {
        $('#conn-status')
        .attr('class', 'badge badge-info')
        .html('connecting')
    });


    chatEvent.on('conn.reconnecting', function(number) {
        $('#conn-status')
        .attr('class', 'badge badge-warning')
        .html('reconnecting '+number)
    });


    chatEvent.on('conn.closer', function(error) {
        $('#conn-status')
        .attr('class', 'badge badge-error')
        .html(JSON.stringify(error))
    });


    chatEvent.on('conn.error', function(error) {
        $('#conn-status')
        .attr('class', 'badge badge-error')
        .html(JSON.stringify(error))
    });

    var initChatBlock = function() {
        $blockRoom.hide();
        $blockChat.show();

        var textTimesInterval;
        var runTextIntervalTimeout;

        var textInterval = function() {
            textTimesInterval = setInterval(function() {
                $inputMessage.val('Hello('+textTimes+')');
                textTimes++;
            }, 1000);
        }

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
        
        var template = Handlebars.compile($('#template-inbox-outgoing').html());
        var time = moment((new Date()).getTime()).format('h:mm:ss');

        $('#messages').append(template({
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