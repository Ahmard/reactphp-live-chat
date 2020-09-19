let ws;

$(function (){
    let adminSocketUrl = 'ws://' + window.location.host + adminSocketPrefix;

    let $btnSend = $('#btn-send');
    let $inputCommand = $('#command');
    let $fieldExecutionResult = $('#execution-result');

    ws = new SocketWrapper({
        url: adminSocketUrl
    }, () => adminBlock());

    siteEvent.on('conn.connected', function () {
        console.log('CONNECTED')
    });

    siteEvent.on('server.admin.config.env.result', function (response) {
        $fieldExecutionResult.html(JSON.stringify(response.message, null, 4));
    });

    $('#form-send-command').submit(function (event) {
        event.preventDefault();

        let payload = JSON.parse($inputCommand.val());
        payload.time = (new Date()).getTime();

        ws.send(payload);
    });

    let adminBlock = function () {
        ws.send({
            command: 'server.admin.config.env',
            action: 'list-commands'
        });
    };
});
