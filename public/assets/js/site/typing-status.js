const TypingStatus = (function () {

    function TypingStatus() {
        const _this = this;

        this.typingStatuses = {};
        let isInitialised = false;
        let invokeWhenInitialised = [];

        this.init = function (data) {
            isInitialised = true;

            this.ws = data.ws;
            this.command = data.command;

            invokeWhenInitialised.forEach((func) => {
                func[0](...func[1]);
            });
        };


        /**
         * Check if id is registered in typing statuses
         * @param clientId
         * @returns {boolean}
         */
        this.has = function (clientId) {
            return !!this.typingStatuses[clientId];
        };

        /**
         * Send typing status to server
         * @param status
         * @param withData
         */
        this.send = function (status = 'typing', withData = {}) {
            if ({} !== withData) {
                withData.status = status;
                this.ws.send(this.command, withData);
            } else {
                this.ws.send(this.command, {status: status});
            }
        };

        /**
         * Remove typing status
         * @param clientId
         */
        this.remove = function (clientId) {
            if (this.has(clientId)) {
                clearTimeout(this.typingStatuses[clientId]);
                $('#typing-status-' + clientId).remove();
                delete this.typingStatuses[clientId];
            }
        };

        /**
         * Listen to typing status and act on it
         * @param config
         */
        this.listen = function (config) {
            if (!isInitialised) {
                invokeWhenInitialised.push([
                    _this.listen, [config]
                ]);
                return;
            }

            let $elTypingStatus = config.$elTypingStatus;
            let templateTypingStatus = config.templateTypingStatus;

            _this.ws.onCommand(_this.command, function (response) {
                let message = response.message;

                let clientId = message.client_id;
                let tStatusInterval = _this.typingStatuses[clientId];

                if (message.status !== 'typing') {
                    _this.remove(clientId);
                    return;
                }

                //Create a typing message which will disappear in x seconds
                clearTimeout(tStatusInterval);
                _this.typingStatuses[clientId] = setTimeout(function () {
                    _this.remove(clientId);
                }, message.timeout);

                //If message is already displayed
                if (tStatusInterval) {
                    return;
                }

                $elTypingStatus.append(templateTypingStatus({
                    name: message.user,
                    id: clientId
                }))
            });

        };

    }

    return TypingStatus;
})();