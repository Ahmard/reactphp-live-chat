var log = console.log;
var chatEvent = new EventEmitter();

var ASocket = (function(log, chatEvent) {
    var connConfig;

    var ws = {
        readyState: 100
    };

    var time;

    var callbacksToCall = [];

    var isConnecting = false;

    var isFirstConn = true;
    
    var reconnectPayload;
    

    function executeCallbacks(callbacks, param, then){
        if(callbacks.length){
            for (var i = callbacks.length - 1; i >= 0; i--) {
                callbacks[i](param);
            }
        }
        
        if(then) then();
    }

    function constructTime() {
        var d = new Date();
        return d.getTime();
    }

    function connect(callback) {
        //Let's see if we have connection already
        if(ws)
        if (ws.readyState == 1) {
            if (callback) callback();
            return;
        } else if (isConnecting) {
            if(callback){
                callbacksToCall.push(callback);
            }
            return;
        }
        
        setTimeout(function() {
            chatEvent.emit('conn.change', 'connecting');
            chatEvent.emit('conn.connecting');
        }, 250);
        
        isConnecting = true;
        var retryNum = 1;
        var retryInterval = setInterval(function() {
            //if we are not connected or connecting
            if (ws.readyState != 1 && ws.readyState != 0) {
                //Log message if its not first
                if (! isFirstConn){
                    log('Retrying connection('+retryNum+')...');
                    
                    //Call connection status callbacks
                    chatEvent.emit('conn.change', 'reconnecting', retryNum);
                    chatEvent.emit('conn.reconnecting', retryNum);
                }
                try {
                    ws = new WebSocket(connConfig.url);
                } catch (e) {
                    handleError(e);
                }
                var stateCheckInterval = setInterval(function() {
                   //Continue checking until our socket is not in 'connecting state'
                   if(ws.readyState != 0){
                       clearInterval(stateCheckInterval);
                   }
                    //Init
                    if (ws.readyState == 1) {
                        clearInterval(retryInterval);

                        p.init();

                        isConnecting = false;
                        
                        retryNum = 1;
                        
                        //If its reconnecting, then we will send reconnectPayload
                        if(! isFirstConn && reconnectPayload){
                            p.send(reconnectPayload);
                        }

                        //Execute
                        if (callback) callback();
                        
                        executeCallbacks(callbacksToCall, null, function(){
                            callbacksToCall = [];
                        });
                        
                        log("Connection established.");
                        //Call connection status callbacks
                        chatEvent.emit('conn.change', 'connected');
                        chatEvent.emit('conn.connected');
                    
                    }
                    
                }, 100);
                retryNum++;
                isFirstConn = false;
            }
        }, 1500);
    }

    function TheSocket(config) {
        connConfig = config;
        connect();
    }

    var p = TheSocket.prototype;

    p.showDebug = false;

    p.getSocket = function() {
        return ws;
    };

    p.init = function() {
        var hm = this.handleMessage;
        ws.onmessage = (message) => this.handleMessage(message);

        //ws.onopen = () => this.ping();

        ws.onclose = () => this.handleClose();

        ws.onerror = () => this.handleError();
        
    };

    p.send = function(data, callback) {
        if (ws.readyState == 1) {
            ws.send(JSON.stringify(data));
            //exec callback
            if(callback) callback();
        } else {
            connect(function() {
                p.send(data);
                //exec callback
                if(callback) callback();
            });
        }
    };

    p.ping = function() {
        this.send({
            command: 'system.ping',
            time: constructTime()
        });
    };

    p.pong = function() {
        this.send({
            command: 'system.pong',
            time: constructTime()
        });
    };

    p.handleMessage = function(message) {
        if(this.showDebug){
            var dt = new Date();
            var now = dt.getHours()+':'+dt.getMinutes()+':'+dt.getSeconds();
            log(now+': '+message.data);
        }
        var recvMessage = JSON.parse(message.data);
        var command = recvMessage.command;
        
        //Emit events bound to this message command
        chatEvent.emit(command, recvMessage);
    };
    
    p.handleError = function(e) {
        chatEvent.emit('conn.error', e);
        log('Error: ');
        log(JSON.stringify(e));
    };

    p.handleClose = function(e) {
        chatEvent.emit('conn.closed', e);
        
        connect();
    };
    
    p.setReconnectPayload = function(payload){
        reconnectPayload = payload;
    };
    
    //When ping message is received
    chatEvent.on('system.ping', function(){
        p.pong();
    });
    
    chatEvent.on('system.pong', function(){
        log('Pong message received');
    });
    
    return TheSocket;
})(log, chatEvent);