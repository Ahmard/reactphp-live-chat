//Initialize socket wrapper
let chatSocketUrl = 'ws://' + window.location.host + privateChatSocketPrefix;

let $elNavMessageBadge = $('#nav-link-message').find('.badge');

const websocket = Reactificate.Websocket.connect(chatSocketUrl);
websocket.setAuthToken(TOKEN);

websocket.onOpen(function () {
    //Tone to be played when new message is received
    let toneMessage = new Howl({
        src: ['/assets/mp3/juntos.mp3'],
        volume: 0.5
    });

    websocket.onOpen(function () {
        websocket.send('user.iam-online', []);
    });

    websocket.onCommand('chat.private.send', function () {
        toneMessage.play();
        let totalMessage = parseInt($elNavMessageBadge.text()) || 0;
        $elNavMessageBadge.text(totalMessage + 1);
    });

});