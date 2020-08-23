var log = console.log;
var chatEvent = new EventEmitter();

var ASocket = (function (log, chatEvent) {
    var ws = {
        readyState: 100
    };

    var _this;

    function TheSocket(credentials, callback) {
        _this = this;

        _this.connCredentials = credentials;
        
        this.connect(callback);
    }

    TheSocket.prototype.connCredentials;

    TheSocket.prototype.showDebug = false;

    TheSocket.prototype.callbacksToCall = [];

    TheSocket.prototype.isConnecting = false;

    TheSocket.prototype.isFirstConnection = true;

    TheSocket.prototype.isDisconnected = false;

    TheSocket.prototype.reconnectPayload;
    


    TheSocket.prototype.executeCallbacks = function (callbacks, param, then) {
        if (callbacks.length) {
            for (var i = callbacks.length - 1; i >= 0; i--) {
                callbacks[i](param);
            }
        }

        if (then) then();
    };

    TheSocket.prototype.constructTime = function () {
        var d = new Date();
        return d.getTime();
    };

    TheSocket.prototype.connect = function (callback) {
        //Stop if user disconnected manually(leave room)
        if (this.isDisconnected) {
            clearInterval(retryInterval);
            return;
        }
        //Let's see if we have connection already
        if (ws)
            if (ws.readyState == 1) {
                if (callback) callback();
                return;
            } else if (_this.isConnecting) {
                if (callback) {
                    _this.callbacksToCall.push(callback);
                }
                return;
            }

        setTimeout(function () {
            chatEvent.emit('conn.change', 'connecting');
            chatEvent.emit('conn.connecting');
        }, 250);

        _this.isConnecting = true;
        var retryNum = 1;
        var retryInterval = setInterval(function () {

            //if we are not connected or connecting
            if (ws.readyState != 1 && ws.readyState != 0) {
                //Log message if its not first
                if (!_this.isFirstConnection) {
                    log('Retrying connection(' + retryNum + ')...');

                    //Call connection status callbacks
                    chatEvent.emit('conn.change', 'reconnecting', retryNum);
                    chatEvent.emit('conn.reconnecting', retryNum);
                }
                try {
                    ws = new WebSocket(_this.connCredentials.url);
                } catch (e) {
                    _this.handleError(e);
                }


                var stateCheckInterval = setInterval(function () {
                    //Continue checking until our socket is not in 'connecting state'
                    if (ws.readyState != 0) {
                        clearInterval(stateCheckInterval);
                    }
                    //Init
                    if (ws.readyState == 1) {
                        clearInterval(retryInterval);

                        _this.init();

                        _this.isConnecting = false;

                        retryNum = 1;

                        //If its reconnecting, then we will send TheSocket.prototype.reconnectPayload
                        if (!_this.isFirstConnection && _this.reconnectPayload) {
                            _this.send(_this.reconnectPayload);
                        }

                        //Execute
                        if (callback) callback();

                        _this.executeCallbacks(_this.callbacksToCall, null, function () {
                            _this.callbacksToCall = [];
                        });

                        log("Connection established.");

                        //Call connection status callbacks
                        chatEvent.emit('conn.change', 'connected');
                        chatEvent.emit('conn.connected');
                    }

                }, 100);
                retryNum++;
                _this.isFirstConnection = false;
            }
        }, 1500);
    };

    TheSocket.prototype.getSocket = function () {
        return ws;
    };

    TheSocket.prototype.init = function () {
        ws.onmessage = (message) => this.handleMessage(message);

        //ws.onopen = () => this.ping();

        ws.onclose = () => this.handleClose();

        ws.onerror = () => this.handleError();

    };

    TheSocket.prototype.send = function (data, callback) {
        if (ws.readyState == 1) {
            ws.send(JSON.stringify(data));
            //exec callback
            if (callback) callback();
        } else {
            this.connect(function () {
                _this.send(data);
                //exec callback
                if (callback) callback();
            });
        }
    };

    TheSocket.prototype.ping = function () {
        this.send({
            command: 'system.ping',
            time: _this.constructTime()
        });
    };

    TheSocket.prototype.pong = function () {
        this.send({
            command: 'system.pong',
            time: _this.constructTime()
        });
    };

    TheSocket.prototype.handleMessage = function (message) {
        if (this.showDebug) {
            var dt = new Date();
            var now = dt.getHours() + ':' + dt.getMinutes() + ':' + dt.getSeconds();
            log(now + ': ' + message.data);
        }
        var recvMessage = JSON.parse(message.data);
        var command = recvMessage.command;
        
        //Emit events bound to this message command
        chatEvent.emit(command, recvMessage);
    };

    TheSocket.prototype.handleError = function (e) {
        chatEvent.emit('conn.error', e);
        log('Error: ');
        log(JSON.stringify(e));
    };

    TheSocket.prototype.handleClose = function (e) {
        if (!_this.isDisconnected) {
            chatEvent.emit('conn.closed', e);
        }
        this.connect();
    };

    TheSocket.prototype.setReconnectPayload = function (payload) {
        _this.reconnectPayload = payload;
    };

    TheSocket.prototype.disconnect = function () {
        //Mark connection as disconnected
        _this.isDisconnected = true;
        //Disconnect
        ws.close();
        //Emit event that connection is disconnected
        chatEvent.emit('conn.disconnected')
    };

    //When ping message is received
    chatEvent.on('system.ping', function () {
        _this.pong();
        
        _this.lastPingReceivedAt = (new Date()).getTime();
    });

    chatEvent.on('system.pong', function () {
        log('Pong message received');
    });
    
    chatEvent.on('system.ping.interval', function(message){
        let interval = message.message;
    });

    return TheSocket;
})(log, chatEvent);