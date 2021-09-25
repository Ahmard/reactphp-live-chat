const siteEvent = new EventEmitter();

const SocketWrapper = (function (event) {

    let time = Math.random();

    function SocketWrapper(credentials) {
        let _this = this;

        _this.getTime = function () {
            return time;
        }

        this.connCredentials = credentials;

        this.showDebug = false;

        this.callbacksToCall = [];

        this.isConnecting = false;

        this.isFirstConnection = true;

        this.isDisconnected = false;

        this.reconnectPayloads = [];

        this.ws = {
            readyState: 100
        };


        this.Events = {
            CONNECTING: 'SOCKET.CONNECTING',
            CONNECTED: 'SOCKET.CONNECTED',
            RECONNECTING: 'SOCKET.RECONNECTING',
            RECONNECTED: 'SOCKET.RECONNECTED',
            DISCONNECTED: 'SOCKET.DISCONNECTED'
        };


        this.executeCallbacks = function (callbacks, params, then) {
            if (callbacks.length) {
                for (let i = callbacks.length - 1; i >= 0; i--) {
                    callbacks[i](...params);
                }
            }

            if (then) then();
        };

        this.constructTime = function () {
            return (new Date()).getTime();
        };

        this.connect = function (callback) {
            let retryInterval;
            //Stop if user disconnected manually(leave room)
            if (this.isDisconnected && retryInterval) {
                clearInterval(retryInterval);
                return;
            }
            //Let's see if we have connection already
            if (_this.ws.readyState === 1) {
                if (callback) callback();
                return;
            } else if (_this.isConnecting) {
                if (callback) {
                    _this.callbacksToCall.push(callback);
                }
                return;
            }

            setTimeout(function () {
                event.emit('conn.change', 'connecting');
                event.emit('conn.connecting');
            }, 250);

            _this.isConnecting = true;
            let retryNum = 1;
            retryInterval = setInterval(function () {

                //if we are not connected or connecting
                if (_this.ws.readyState !== 1 && _this.ws.readyState !== 0) {
                    //Log message if its not first
                    if (!_this.isFirstConnection) {
                        console.log('Retrying connection(' + retryNum + ')...');

                        //Call connection status callbacks
                        event.emit('conn.change', 'reconnecting', retryNum);
                        event.emit('conn.reconnecting', retryNum);
                    }

                    try {
                        if (_this.connCredentials.url){
                            _this.ws = new WebSocket(_this.connCredentials.url);
                        }else {
                            throw new Error('Socket connection uri is not defined.')
                        }
                    } catch (e) {
                        _this.handleError(e);
                    }


                    let stateCheckInterval = setInterval(function () {
                        //Continue checking until our socket is not in 'connecting state'
                        if (_this.ws.readyState !== 0) {
                            clearInterval(stateCheckInterval);
                        }
                        //Init
                        if (_this.ws.readyState === 1) {
                            clearInterval(retryInterval);

                            _this.init();

                            _this.isConnecting = false;

                            retryNum = 1;

                            //If its reconnecting, then we will send this.reconnectPayloada
                            if (!_this.isFirstConnection) {
                                _this.reconnectPayloads.forEach(function (payload) {
                                    _this.send(payload);
                                })
                            }

                            //Execute
                            if (callback) callback();

                            _this.executeCallbacks(_this.callbacksToCall, [_this.ws], function () {
                                _this.callbacksToCall = [];
                            });

                            console.log(`Connection established: ${_this.connCredentials.url}.`);

                            //Call connection status callbacks
                            event.emit('conn.change', 'connected');
                            event.emit('conn.connected');

                            //Emit reconnected event
                            if (!_this.isFirstConnection){
                                //Call connection status callbacks
                                event.emit('conn.change', 'reconnecting', retryNum);
                                event.emit('conn.reconnected', retryNum);
                            }
                        }

                    }, 100);
                    retryNum++;
                    _this.isFirstConnection = false;
                }
            }, 1500);
        };

        this.isConnected = function(){
            return this.ws.readyState === 1;
        }

        this.getSocket = function () {
            return this.ws;
        };

        this.init = function () {
            this.ws.onmessage = this.handleMessage;

            //this.ws.onopen = () => this.ping();

            this.ws.onclose = this.handleClose;

            this.ws.onerror = this.handleError;

        };

        this.send = function (data, callback) {
            if (!data.time) {
                data.time = (new Date()).getTime();
            }

            if (TOKEN) {
                data.token = TOKEN;
            }


            if (this.ws.readyState === 1) {
                this.ws.send(JSON.stringify(data));
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

        this.ping = function () {
            this.send({
                command: 'system.ping',
                time: this.constructTime()
            });
        };

        this.pong = function () {
            this.send({
                command: 'system.pong',
                time: this.constructTime()
            });
        };

        this.handleMessage = function (message) {
            if (this.showDebug) {
                let dt = new Date();
                let now = dt.getHours() + ':' + dt.getMinutes() + ':' + dt.getSeconds();
                console.log(now + ': ' + message.data);
            }
            let recvMessage = JSON.parse(message.data);
            let command = recvMessage.command;

            //Emit events bound to this message command
            event.emit(command, recvMessage);
        };

        this.handleError = function (e) {
            //event.emit('conn.error', e);
            console.log('Error: ');
            console.log(e);
        };

        this.handleClose = function (e) {
            if (!this.isDisconnected) {
                event.emit('conn.closed', e);
            }

            _this.connect();
        };

        this.setReconnectPayload = function (payload) {
            this.reconnectPayloads.push(payload);
        };

        this.addReconnectPayload = function (payload) {
            this.reconnectPayloads.push(payload);
        };

        this.disconnect = function () {
            //Mark connection as disconnected
            this.isDisconnected = true;
            //Disconnect
            this.ws.close();
            //Emit event that connection is disconnected
            event.emit('conn.disconnected')
        };

        this.onReady = function (callback){
            if(this.isConnected()){
                callback(this.ws);
            }else {
                this.callbacksToCall.push(callback);
            }
        };

        //When ping message is received
        event.on('system.ping', function () {
            _this.pong();
            this.lastPingReceivedAt = (new Date()).getTime();
        });

        event.on('system.pong', function () {
            console.log('Pong message received');
        });

        event.on('system.ping.interval', function (message) {
            let interval = message.message;
        });


        event.on('system.response.404', function (response) {
            console.log(response.message);
            alert('Socket colis not found, please check console for more info.');
        });

        event.on('system.response.403', function (response) {
            if (response.message.action === 'redirect'){
                window.location = response.message.uri;
            }else {
                console.log(response.message);
                alert('You just made unauthorised request, please check console for more info.');
            }
        });

        event.on('system.response.500', function (response) {
            console.log(response.message);
            alert('Internal server error occurred, please check console for more info.');
        });
    }

    return SocketWrapper;
})(siteEvent);