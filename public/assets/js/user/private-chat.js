let $modal = $('#modal_general');

let startConversation;
let convUsers = [];
let findUser;
let scrollMessages;

function showToolTip(element) {
    $(element).tooltip({
        placement: 'top',
        title: $(element).data('full-name')
    })
}

$(function () {
    let convWith;
    let lastSearchedUser = JSON.parse('{"id":2,"username":"Ahmard"}');
    let htmlNewConversation = $('#template-new-conversation').html();
    let templateConvItem = Handlebars.compile($('#template-conv-list-item').html());
    let templateOutgoingMessage = Handlebars.compile($('#template-outgoing-message').html());
    let templateIncomingMessage = Handlebars.compile($('#template-incoming-message').html());

    let $divConvList = $('#conv-list');
    let $colConv = $('#col-conv');
    let $divMessages = $('#messages');
    let elMessages = document.getElementById('messages');

    setTimeout(() => fetchConversations(), 250);

    siteEvent.on('chat.private.send', function (response) {
        let message = response.message;

        displayMessage({
            sender_id: message.sender_id,
            message: message.message,
            time: response.time
        });
    });

    let fetchConversations = function () {
        $.ajax({
            url: '/api/chat/private/fetch-conversations/' + TOKEN,
            error: function (error) {
                alert('Error occurred');
                console.log(error);
                0
            }
        }).then(function (response) {
            convUsers = response.data.conversations;

            $divConvList.html('');

            response.data.conversations.forEach(function (user) {

                //Determine conversant id
                if (user.sender_id === USER.id) {
                    user.id = user.receiver_id;
                    user.username = user.receiver_uname;
                } else {
                    user.id = user.sender_id;
                    user.username = user.sender_uname;
                }

                $divConvList.append(templateConvItem({
                    user: user
                }));
                getConversationStatus(user.id);
            });
        });

    };

    let getConversationStatus = function (userId) {

        $.ajax({
            url: '/api/chat/private/get-conversation-status/' + userId + '/' + TOKEN
        }).then(function (response) {
            let convStatus;
            if (response.data.total_unread > 0) {
                convStatus = '<i class="text-info">' + response.data.total_unread + ' messages</i>';
            } else {
                convStatus = '<i class="mdb-color">no new messages</i>';
            }

            let presence;
            if (response.data.presence) {
                presence = '<i class="text-success">Online</i>';
            } else {
                presence = '<i class="text-danger">Offline</i>';
            }

            let $elConvListItem = $('#conv-list-item-' + userId);

            $elConvListItem.find('.user_info .conv-status')
                .append(convStatus);

            $elConvListItem
                .find('.user_info .presence')
                .append(presence);
        });
    };

    let displayMessage = function (message) {
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

    scrollMessages = function () {
        elMessages.scrollTo(0, elMessages.scrollHeight);
    };

    startConversation = function (userId, isFresh = false) {
        $modal.modal('hide');

        //Handle active chats
        if (convWith) {
            $('#conv-list-item-' + convWith.id).removeClass('m-active');
        }
        convWith = findUser(userId).user;
        $('#conv-list-item-' + convWith.id).addClass('m-active');

        $('#conv-with-username').text(convWith.username);

        //If we are start new conversation
        if (isFresh) {
            $divConvList.append(templateConvItem({
                user: convWith
            }));
        }

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

        $('#form-send-message').off('submit').on('submit', function (event) {
            event.preventDefault();

            let $textareaMessage = $(event.target).find('textarea[name="message"]');
            websocket.send({
                command: 'chat.private.send',
                receiver_id: convWith.id,
                message: $textareaMessage.val()
            }, function () {
                if (!findUser(convWith.id)) {
                    convUsers.push(convWith);
                }

                $divMessages.append(templateOutgoingMessage({
                    message: {
                        message: $textareaMessage.val(),
                        time: (new Date()).getTime()
                    },
                    user: USER
                }));

                //Clear textarea
                $textareaMessage.val('');

                scrollMessages();
            });
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

                            $('#div-user-lookup-result').html(templateSearchUserItem({
                                user: response.data
                            }))
                        }
                    }
                });
            });
        });
        $modal.modal('show');
    });

});