//Initialize socket wrapper
let chatSocketUrl = 'ws://' + window.location.host + privateChatSocketPrefix;

let $elNavMessageBadge = $('#nav-link-message').find('.badge');

const websocket = new SocketWrapper({
    url: chatSocketUrl
}, function () {

    //Tone to be played when new message is received
    let toneMessage = new Howl({
        src: ['/assets/mp3/juntos.mp3'],
        volume: 1
    });

    //Message that will be sent to server when the browser got reconnected
    let payload = {
        command: 'user.iam-online'
    };

    websocket.setReconnectPayload(payload);

    websocket.send(payload, function () {
        //If connected
    });


    siteEvent.on('chat.private.send', function (response) {
        toneMessage.play();
        let totalMessage = parseInt($elNavMessageBadge.text()) || 0;
        $elNavMessageBadge.text(totalMessage + 1);
    });

});
