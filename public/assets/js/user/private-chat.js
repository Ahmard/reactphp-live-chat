let $modal = $('#modal_general');

let startConversation;
let convUsers = [];
let findUser;
let getUser;
let scrollMessages;

function showToolTip(element) {
    $(element).tooltip({
        placement: 'top',
        title: $(element).data('full-name')
    })
}

$(function () {
    let conversant;
    let lastSearchedUser = JSON.parse('{"id":2,"username":"Ahmard"}');
    let htmlNewConversation = $('#template-new-conversation').html();
    let templateConvItem = Handlebars.compile($('#template-conv-list-item').html());
    let templateOutgoingMessage = Handlebars.compile($('#template-outgoing-message').html());
    let templateIncomingMessage = Handlebars.compile($('#template-incoming-message').html());
    let templateTypingStatus = Handlebars.compile($('#template-typing-status').html());

    const htmlOnlinePresence = '<i class="text-success user-presence">Online</i>';
    const htmlOfflinePresence = '<i class="text-danger user-presence">Offline</i>';

    const htmlMessageCounter = function (numberOfMessages) {
        switch (numberOfMessages) {
            case 0:
                return '<i class="mdb-color message-counter">no new messages</i>'
            case 1:
                return '<i class="text-info message-counter">' + numberOfMessages + ' message</i>';
            default:
                return '<i class="text-info message-counter">' + numberOfMessages + ' messages</i>';

        }
    };

    let $divConvList = $('#conv-list');
    let $colConv = $('#col-conv');
    let $divMessages = $('#messages');
    let $elTypingStatus = $('#div-typing-status');
    let elMessages = document.getElementById('messages');

    let typingStatus = new TypingStatus();

    setTimeout(() => fetchConversations(), 250);

    websocket.onReady(function () {
        typingStatus.init({
            ws: websocket,
            command: 'chat.private.typing'
        });
    })

    siteEvent.on('conn.reconnected', function () {
        if (convUsers) {
            monitorUsersPresence();
        }
    });

    siteEvent.on('chat.private.send', function (response) {
        let message = response.message;

        typingStatus.remove(message.client_id);

        handleMessage({
            sender_id: message.sender_id,
            message: message.message,
            time: response.time
        });

        if (conversant){
            if (conversant.id === message.sender_id) {
                markMessageAsRead(message);
            }
        }
    });

    siteEvent.on('chat.private.online', function (response) {
        let userId = response.message.user_id;
        $(`#conv-list-item-${userId} .presence`).html(htmlOnlinePresence);
    })

    siteEvent.on('chat.private.offline', function (response) {
        let userId = response.message.user_id;
        $(`#conv-list-item-${userId} .presence`).html(htmlOfflinePresence);
    })

    let fetchConversations = function () {
        $.ajax({
            url: '/api/chat/private/fetch-conversations/' + TOKEN,
            error: function (error) {
                alert('Error occurred');
                console.log(error);
            }
        }).then(function (response) {
            let conversations = convUsers = response.data.conversations;

            $divConvList.html('');

            for (let i = 0; i < conversations.length; i++) {
                let user = conversations[i];

                //Determine conversant id
                if (user.sender_id === USER.id) {
                    user.id = user.receiver_id;
                    user.username = user.receiver_uname;
                } else {
                    user.id = user.sender_id;
                    user.username = user.sender_uname;
                }

                displayConversationItem(user, false);

                //if it's last loop, we monitor users presence
                if (i === (conversations.length - 1)) {
                    monitorUsersPresence();
                }
            }
        });
    };

    let displayConversationItem = function (user, willMonitorPresence = true) {

        $divConvList.append(templateConvItem({
            user: user
        }));

        getConversationStatus(user.id);

        //if it's last loop, we monitor users presence
        if (willMonitorPresence) {
            monitorUsersPresence([user]);
        }
    }

    /**
     * Monitor users presence
     * @param user - if we want to listen to single user presence
     */
    let monitorUsersPresence = function (user = null) {
        let usersToMonitor = user || convUsers;
        websocket.send({
            command: 'chat.private.monitor-users-presence',
            message: {
                users: usersToMonitor.map(function (user) {
                    return {
                        user_id: (user.sender_id === USER.id ? user.receiver_id : user.sender_id)
                    };
                })
            }
        })
    };

    let getConversationStatus = function (userId) {

        $.ajax({
            url: '/api/chat/private/get-conversation-status/' + userId + '/' + TOKEN
        }).then(function (response) {
            let convStatus = htmlMessageCounter(response.data.total_unread);

            let presence = htmlOfflinePresence;
            if (response.data.presence) {
                presence = htmlOnlinePresence;
            }

            let $elConvListItem = $('#conv-list-item-' + userId);

            $elConvListItem.find('.user_info .conv-status')
                .append(convStatus);

            $elConvListItem
                .find('.user_info .presence')
                .append(presence);
        });
    };

    let markMessageAsRead = function (message, callback) {
        $.ajax({
            url: '/api/chat/private/' + message.id + '/mark-as-read/' + TOKEN,
            method: 'PATCH',
            error: ajaxErrorHandler
        }).then(function (response) {
            if (callback) callback(response);
        });
    }

    let handleMessage = function (message) {
        let $elConvListItem;

        //We add users to conversation list if they are not already in
        if (!$divConvList.has(`#person-${message.sender_id}`).length) {
            getUser(message.sender_id, function (user) {
                //Add user to conversant list
                convUsers.push(user);

                displayConversationItem(user);
            });
        }

        if (!conversant || conversant.id !== message.sender_id) {
            $elConvListItem = $(`#person-${message.sender_id}`);

            let text = $elConvListItem.find('.message-counter').text();

            let convStatus = htmlMessageCounter((parseInt(text) || 0) + 1);

            $elConvListItem.find('.conv-status').html(convStatus);

            //We don't need to display message
            //since users are not actively having conversation
            return;
        }

        displayMessage(message);
    }

    let displayMessage = function (message) {
        message.time = moment(message.time * 1000).format('h:mm:ss');
        message.message = message.message.linkify();

        if (message.sender_id === USER.id) {
            $divMessages.append(templateOutgoingMessage({
                message: message,
                user: {
                    user_id: message.sender_id,
                    username: message.sender_uname
                }
            }));
            scrollMessages();
        } else {
            if (message.status === 0) {
                markMessageAsRead(message, function (ajaxRequestResponse) {
                    let $elConvListItem = $('#conv-list-item-' + message.sender_id);

                    let text = $elConvListItem.find('.user_info .conv-status .message-counter').text();

                    let convStatus = htmlMessageCounter(parseInt(text) - 1);

                    $elConvListItem
                        .find('.user_info .conv-status .message-counter')
                        .html(convStatus);

                });
            }

            $divMessages.append(templateIncomingMessage({
                message: message,
                user: {
                    user_id: message.receiver_id,
                    username: message.receiver_uname
                }
            }));
            scrollMessages();
        }
    }

    findUser = function (userId) {
        for (let i = 0; i < convUsers.length; i++) {
            if (convUsers[i].id === userId) {
                return {
                    key: i,
                    user: convUsers[i],
                };
            }
        }

        return null;
    };

    getUser = function(userId, callback){
        let user = findUser(userId);
        if(!user){
            $.ajax({
                url: `/api/user/${userId}/${TOKEN}`,
                error: ajaxErrorHandler
            }).then(function (response) {
                callback(response.data);
            });
        }
    }

    scrollMessages = function () {
        elMessages.scrollTo(0, elMessages.scrollHeight);
    };

    /**
     * Start new conversation
     * @param userId
     * @param isFresh
     */
    startConversation = function (userId, isFresh = false) {
        $modal.modal('hide');

        let $formSendMessage = $('#form-send-message');
        let $textareaMessage = $formSendMessage.find('textarea[name="message"]');

        //Handle previous active chats
        if (conversant) {
            $('#conv-list-item-' + conversant.id).removeClass('m-active');
        }
        conversant = findUser(userId).user;

        $('#conv-with-username').text(conversant.username);

        //If we are start new conversation
        if (isFresh) {
            displayConversationItem(conversant);
        }

        //Mark this chat as active
        $('#conv-list-item-' + conversant.id).addClass('m-active');

        $colConv.show();

        //Fetch conversation
        $.ajax({
            url: '/api/chat/private/' + userId + '/' + TOKEN,
            method: 'GET',
            error: function (error) {
                alert('Error occurred');
                console.log(error);
            }
        }).then(function (response) {
            //Clear previous messages
            $divMessages.html('');

            response.data.forEach(function (message) {
                displayMessage(message);
            })
        });

        //Send typing status
        $textareaMessage.on('keydown', function (event) {
            if (event.keyCode === 13) {
                $formSendMessage.submit();
            } else {
                typingStatus.send('typing', {
                    receiver_id: conversant.id
                });
            }
        });

        //Listen typing status
        typingStatus.listen({
            siteEvent: siteEvent,
            $elTypingStatus: $elTypingStatus,
            templateTypingStatus: templateTypingStatus
        });

        $formSendMessage.off('submit').on('submit', function (event) {
            event.preventDefault();

            if ('' !== $textareaMessage.val().trim()) {
                websocket.send({
                    command: 'chat.private.send',
                    receiver_id: conversant.id,
                    message: $textareaMessage.val()
                }, function () {
                    if (!findUser(conversant.id)) {
                        convUsers.push(conversant);
                    }

                    $divMessages.append(templateOutgoingMessage({
                        message: {
                            message: $textareaMessage.val().linkify(),
                            time: (new Date()).getTime()
                        },
                        user: USER
                    }));

                    //Clear textarea
                    $textareaMessage.val('');

                    scrollMessages();
                });
            }
        });
    };

    $('#btn-new-conversation').click(function () {
        let templateSearchUserItem = Handlebars.compile($('#template-user-lookup-item').html());
        $modal.find('.modal-title').text('Start new conversation');
        $modal.find('.modal-body').html(htmlNewConversation);
        $modal.one('shown.bs.modal', function () {
            let $inputUsername = $('#input-username').focus();

            $('#form-check-user').off('submit').submit(function (event) {
                event.preventDefault();

                let $divUserLookupResult = $('#div-user-lookup-result');

                $divUserLookupResult.html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i> </div>')

                $.ajax({
                    url: '/api/chat/private/check-user/' + TOKEN,
                    data: {
                        username: $inputUsername.val()
                    },
                    error: function (error) {
                        alert('Error occurred');
                        console.log(error);
                    }
                }).then(function (response) {
                    if (response.status) {
                        if (response.exists) {
                            lastSearchedUser = response.data;

                            if (!findUser(response.data.id)) {
                                convUsers.push(response.data);
                            }

                            $divUserLookupResult.html(templateSearchUserItem({
                                user: response.data
                            }))
                        } else {
                            $divUserLookupResult.html('<div class="alert alert-danger"><i class="fa fa-info"></i> No results found.</div>')
                        }
                    } else {
                        $divUserLookupResult.html('<div class="alert alert-danger"><i class="fa fa-info"></i> No results found.</div>')
                    }
                });
            });
        });
        $modal.modal('show');
    });

});