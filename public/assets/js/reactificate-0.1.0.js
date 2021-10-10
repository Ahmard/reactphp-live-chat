window.Reactificate = window.Reactificate || {};

window.Reactificate.EventEmitter = (function () {
    function EventEmitter() {
        let events = {
            'on': {},
            'once': {}
        };

        this.on = function (name, listener) {
            if (!events['on'][name]) {
                events['on'][name] = [];
            }

            events['on'][name].push(listener);
        };

        this.once = function (name, listener) {
            if (!events['once'][name]) {
                events['once'][name] = [];
            }

            events['once'][name].push(listener);
        };

        this.dispatch = function (name, data = []) {
            let regularEvent = events['on'];
            if (regularEvent.hasOwnProperty(name)) {
                regularEvent[name].forEach(function (listener) {
                    listener(...data)
                });
            }

            let onceEvent = events['once'];
            if (onceEvent.hasOwnProperty(name)) {
                onceEvent[name].forEach(function (listener) {
                    listener(data);
                });

                delete onceEvent[name];
            }
        }
    }

    return EventEmitter;
})();

window.Reactificate.Notification = (function () {
    function RNotification() {
        let _this = this;
        let _notification;

        this.request = function () {
            if (_this.isDeclined()) {
                return new Promise(function (resolve, reject) {
                    reject();
                });
            }

            return Notification.requestPermission();
        };

        this.send = function (object) {
            _notification = new Notification(object.title, object);

            if (object.hasOwnProperty('redirect')) {
                _notification.addEventListener('click', () => {
                    window.location = object.redirect;
                });
            }
        }

        this.getNotification = () => _notification;

        this.isDefault = function () {
            return 'default' === Notification.permission;
        };

        this.isGranted = function () {
            return 'granted' === Notification.permission;
        };

        this.isDeclined = function () {
            return 'declined' === Notification.permission;
        };


        setTimeout(() => _this.request(), 50);
    }


    return RNotification;
})();

window.Reactificate.Websocket = (function () {
    function Websocket(wsUri, options = []) {

        let _this = this;
        let _event = new Reactificate.EventEmitter();
        /**@returns WebSocket**/
        let websocket;
        let reconnectionInterval = 1000;
        let connectionState = 'standby';
        let willReconnect = true;

        let defaultAuthToken = null;
        let reconnectionTimeout = null;

        /**
         * Log message to console
         * @param message
         */
        let log = function (message) {
            console.log(message);
        };

        let createSocket = function (isReconnecting = false) {
            if (true === isReconnecting) {
                connectionState = 'reconnecting';
                _event.dispatch('reconnecting');
            } else {
                connectionState = 'connecting';
                _event.dispatch('connecting');

            }

            if (wsUri.indexOf('ws://') === -1 && wsUri.indexOf('wss://') === -1) {
                wsUri = 'ws://' + window.location.host + wsUri;
            }

            websocket = new WebSocket(wsUri, options);

            websocket.addEventListener('open', function (...arguments) {
                if ('reconnecting' === connectionState) {
                    _event.dispatch('reconnect');
                }

                changeState('open', arguments);
            });

            websocket.addEventListener('message', function (...arguments) {
                _event.dispatch('message', arguments);
            });

            websocket.addEventListener('close', function (...arguments) {
                changeState('close', arguments);
            });

            websocket.addEventListener('error', function (...arguments) {
                changeState('error', arguments);
            });
        };

        let changeState = function (stateName, event) {
            connectionState = stateName;

            if ('close' === stateName && willReconnect) {
                _this.reconnect();
            }

            _event.dispatch(stateName, [event]);
        };

        let close = function (reconnect = false) {
            if (reconnect) {
                willReconnect = true;
                connectionState = 'internal_reconnection';
            }

            websocket.close();
        };

        /**
         * Check if connection is opened
         * @returns {boolean}
         */
        this.isOpened = function () {
            return 'open' === connectionState;
        };

        /**
         * Gets server connection state
         * @returns {string}
         */
        this.getState = function () {
            return connectionState;
        };

        /**
         * Get browser implementation of WebSocket object
         * @return {WebSocket}
         */
        this.getWebSocket = () => websocket;

        /**
         * This event fires when a connection is opened/created
         * @param listener
         */
        this.onOpen = (listener) => _event.on('open', listener);

        /**
         * This event fires when message is received
         * @param listener
         */
        this.onMessage = (listener) => _event.on('message', (payload) => {
            if ('string' === typeof payload.data) {
                listener(JSON.parse(payload.data), payload)
            } else {
                listener(payload, payload);
            }
        });

        /**
         * Listens to filtered websocket command message
         * @param command {string}
         * @param listener {callback}
         */
        this.onCommand = (command, listener) => _event.on('command.' + command, listener);

        /**
         * Listens to Reactificate socket command
         * @param listener
         */
        this.onAnyCommand = (listener) => _event.on('command', listener);

        /**
         * This event fires when this connection is closed
         * @param listener
         */
        this.onClose = (listener) => _event.on('close', listener);

        /**
         * This event fires when client is disconnecting this connection
         * @param listener
         */
        this.onDisconnect = (listener) => _event.on('custom.disconnect', listener);

        /**
         * This event fires when an error occurred
         * @param listener
         */
        this.onError = (listener) => _event.on('error', listener);

        /**
         * This event fires when this connection is in connecting state
         * @param listener
         */
        this.onConnecting = (listener) => _event.on('connecting', listener);

        /**
         * This event fires when this reconnection is in connecting state
         * @param listener
         */
        this.onReconnecting = (listener) => _event.on('reconnecting', listener);

        /**
         * This event fires when this reconnection has been reconnected
         * @param listener
         */
        this.onReconnect = (listener) => _event.on('reconnect', listener);


        this.onReady = function (listener) {
            window.addEventListener('DOMContentLoaded', listener)
        };

        /**
         * Set reconnection interval
         * @param interval
         */
        this.setReconnectionInterval = function (interval) {
            reconnectionInterval = interval;
        };

        /**
         * Set an authentication token that will be included in each outgoing message
         *
         * @param token {string} authentication token
         */
        this.setAuthToken = function (token) {
            defaultAuthToken = token;
        };

        /**
         * Send message to websocket server
         * @param command {any} command name
         * @param message {array|object|int|float|string} message
         * @return Promise
         */
        this.send = function (command, message = {}) {
            command = JSON.stringify({
                command: command,
                message: message,
                time: new Date().getTime(),
                token: defaultAuthToken
            });

            //Send message
            return new Promise((resolve, reject) => {
                //Only send message when client is connected
                if (this.isOpened()) {
                    try {
                        websocket.send(command);
                        resolve(_this);
                    } catch (error) {
                        reject(error);
                    }

                    //Send message when connection is recovered
                } else {
                    log('Your message will be sent when server connection is recovered!');
                    _event.once('open', () => {
                        try {
                            websocket.send(command);
                            resolve(_this);
                        } catch (error) {
                            reject(error);
                        }
                    });
                }
            })
        };

        /**
         * Manually reconnect this connection
         */
        this.reconnect = function () {
            close(true);

            if (false !== reconnectionInterval) {
                reconnectionTimeout = setTimeout(
                    () => createSocket(true),
                    reconnectionInterval
                );
            }
        };

        /**
         * Connect to websocket server
         *
         * @returns {Websocket}
         */
        this.connect = function () {
            // Create websocket connection
            createSocket();

            //Notification handler
            _this.onMessage(function (payload) {
                if (payload.command) {
                    //Dispatch command events
                    _event.dispatch('command', [payload]);

                    _event.dispatch()

                    if ('Reactificate.Notification' === payload.command) {
                        (new Reactificate.Notification()).send(payload.data);
                    }
                }
            });

            return _this;
        };

        /**
         * Close this connection, the connection will not be reconnected.
         */
        this.close = function () {
            willReconnect = false;
            close(false)
            clearTimeout(reconnectionTimeout);
            _event.dispatch('custom.disconnect');
        };
    }

    /**
     * Create and connect websocket object
     *
     * @param wsUri {string} An address to websocket server.
     * @param options {string|string[]} Additional websocket options
     * @returns {Websocket}
     */
    Websocket.connect = function (wsUri, options = []) {
        return new Websocket(wsUri, options).connect();
    };

    return Websocket;
})();