const log = console.log;
const siteEvent = new EventEmitter();

const SocketWrapper = (function (log, siteEvent) {
    let ws = {
        readyState: 100
    };

    function SocketWrapper(credentials, callback) {
        this.connCredentials = credentials;
        
        this.connect(callback);
    }

    SocketWrapper.Events = {
        CONNECTING: 'SOCKET.CONNECTING',
        CONNECTED: 'SOCKET.CONNECTED',
        RECONNECTING: 'SOCKET.RECONNECTING',
        DISCONNECTED: 'SOCKET.DISCONNECTED'
    };

    SocketWrapper.prototype.connCredentials = {};

    SocketWrapper.prototype.showDebug = false;

    SocketWrapper.prototype.callbacksToCall = [];

    SocketWrapper.prototype.isConnecting = false;

    SocketWrapper.prototype.isFirstConnection = true;

    SocketWrapper.prototype.isDisconnected = false;

    SocketWrapper.prototype.reconnectPayload = {};
    


    SocketWrapper.prototype.executeCallbacks = function (callbacks, param, then) {
        if (callbacks.length) {
            for (let i = callbacks.length - 1; i >= 0; i--) {
                callbacks[i](param);
            }
        }

        if (then) then();
    };

    SocketWrapper.prototype.constructTime = function () {
        return (new Date()).getTime();
    };

    SocketWrapper.prototype.connect = function (callback) {
        let _this = this;
        //Stop if user disconnected manually(leave room)
        if (this.isDisconnected) {
            clearInterval(retryInterval);
            return;
        }
        //Let's see if we have connection already
        if (ws)
            if (ws.readyState === 1) {
                if (callback) callback();
                return;
            } else if (_this.isConnecting) {
                if (callback) {
                    _this.callbacksToCall.push(callback);
                }
                return;
            }

        setTimeout(function () {
            siteEvent.emit('conn.change', 'connecting');
            siteEvent.emit('conn.connecting');
        }, 250);

        _this.isConnecting = true;
        let retryNum = 1;
        let retryInterval = setInterval(function () {

            //if we are not connected or connecting
            if (ws.readyState !== 1 && ws.readyState !== 0) {
                //Log message if its not first
                if (!_this.isFirstConnection) {
                    log('Retrying connection(' + retryNum + ')...');

                    //Call connection status callbacks
                    siteEvent.emit('conn.change', 'reconnecting', retryNum);
                    siteEvent.emit('conn.reconnecting', retryNum);
                }
                try {
                    ws = new WebSocket(_this.connCredentials.url);
                } catch (e) {
                    _this.handleError(e);
                }


                let stateCheckInterval = setInterval(function () {
                    //Continue checking until our socket is not in 'connecting state'
                    if (ws.readyState !== 0) {
                        clearInterval(stateCheckInterval);
                    }
                    //Init
                    if (ws.readyState === 1) {
                        clearInterval(retryInterval);

                        _this.init();

                        _this.isConnecting = false;

                        retryNum = 1;

                        //If its reconnecting, then we will send SocketWrapper.prototype.reconnectPayload
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
                        siteEvent.emit('conn.change', 'connected');
                        siteEvent.emit('conn.connected');
                    }

                }, 100);
                retryNum++;
                _this.isFirstConnection = false;
            }
        }, 1500);
    };

    SocketWrapper.prototype.getSocket = function () {
        return ws;
    };

    SocketWrapper.prototype.init = function () {
        ws.onmessage = (message) => this.handleMessage(message);

        //ws.onopen = () => this.ping();

        ws.onclose = () => this.handleClose();

        ws.onerror = () => this.handleError();

    };

    SocketWrapper.prototype.send = function (data, callback) {
        if(!data.time){
            data.time = (new Date()).getTime();
        }

        if (ws.readyState === 1) {
            ws.send(JSON.stringify(data));
            //exec callback
            if (callback) callback();
        } else {
            this.connect(function () {
                this.send(data);
                //exec callback
                if (callback) callback();
            });
        }
    };

    SocketWrapper.prototype.ping = function () {
        this.send({
            command: 'system.ping',
            time: this.constructTime()
        });
    };

    SocketWrapper.prototype.pong = function () {
        this.send({
            command: 'system.pong',
            time: this.constructTime()
        });
    };

    SocketWrapper.prototype.handleMessage = function (message) {
        if (this.showDebug) {
            let dt = new Date();
            let now = dt.getHours() + ':' + dt.getMinutes() + ':' + dt.getSeconds();
            log(now + ': ' + message.data);
        }
        let recvMessage = JSON.parse(message.data);
        let command = recvMessage.command;
        
        //Emit events bound to this message command
        siteEvent.emit(command, recvMessage);
    };

    SocketWrapper.prototype.handleError = function (e) {
        siteEvent.emit('conn.error', e);
        log('Error: ');
        log(JSON.stringify(e));
    };

    SocketWrapper.prototype.handleClose = function (e) {
        if (!this.isDisconnected) {
            siteEvent.emit('conn.closed', e);
        }
        this.connect();
    };

    SocketWrapper.prototype.setReconnectPayload = function (payload) {
        this.reconnectPayload = payload;
    };

    SocketWrapper.prototype.disconnect = function () {
        //Mark connection as disconnected
        this.isDisconnected = true;
        //Disconnect
        ws.close();
        //Emit event that connection is disconnected
        siteEvent.emit('conn.disconnected')
    };

    //When ping message is received
    siteEvent.on('system.ping', function () {
        SocketWrapper.prototype.pong();
        SocketWrapper.prototype.lastPingReceivedAt = (new Date()).getTime();
    });

    siteEvent.on('system.pong', function () {
        log('Pong message received');
    });
    
    siteEvent.on('system.ping.interval', function(message){
        let interval = message.message;
    });

    return SocketWrapper;
})(log, siteEvent);