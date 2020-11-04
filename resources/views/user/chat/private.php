<?php require(view_path('layout/header.php')); ?>
<link rel="stylesheet" href="/assets/css/site.message.css">

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-sm-4 col-md-4 col-xl-3 chat">
            <div class="card mb-sm-3 mb-md-0 contacts_card" style="height:530px">
                <div class="card-header bg-default">
                    <div class="input-group">
                        <input type="text" placeholder="Search..." name="" class="form-control search text-white">
                        <div class="input-group-prepend">
                            <span class="input-group-text search_btn"><i class="fas fa-search"></i></span>
                        </div>
                    </div>
                </div>
                <div class="card-body contacts_body">
                    <ul class="contacts" id="conv-list">
                        <div class="text-center">
                            <i class="fa fa-spinner fa-pulse fa-3x mt-5 text-default"></i>
                            <div class="text-default mt-2">Loading conversations...</div>
                        </div>
                    </ul>
                </div>
                <div class="card-footer justify-content-end">
                    <button id="btn-new-conversation" class="btn btn-md btn-primary text-white">
                        <i class="fa fa-plus"></i> New Conversation
                    </button>
                </div>
            </div>
        </div>
        <div id="col-conv" class="col-sm-8 col-md-7 col-xl-6 chat" style="display: none">
            <div class="card" style="height:530px">
                <div class="card-header msg_head bg-default" style="">
                    <div class="d-flex justify-content-between bd-highlight">
                        <div class="d-flex justify-content-between">
                            <div class="img_cont">
                                <img class="rounded-circle user_img">
                                <span class="online_icon"></span>
                            </div>
                            <div class="user_info">
                                <span class="text-dark" id="conv-with-username">{{user.username}}</span>
                                <p>
                                    1767 Messages
                                </p>
                            </div>
                        </div>
                        <div>
                            <a class="btn btn-sm p-2 m-0 btn-info"><i class="fas fa-video text-white fa-2x"></i></a>
                            <a class="btn btn-sm p-2 m-0 btn-info"><i class="fas fa-phone text-white fa-2x"></i></a>
                            <a class="btn btn-sm p-2 m-0 btn-info" href="#" id="userDropdown"
                               data-toggle="dropdown"
                               aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-ellipsis-v text-white fa-2x"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right z-depth-1"
                                 aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#">Profile</a>
                                <a class="dropdown-item" href="#">Archive</a>
                            </div>
                        </div>
                    </div>

                    <div class="action_menu">
                        <ul>
                            <li><i class="fas fa-user-circle"></i> View profile</li>
                            <li><i class="fas fa-users"></i> Add to close friends</li>
                            <li><i class="fas fa-plus"></i> Add to group</li>
                            <li><i class="fas fa-ban"></i> Block</li>
                        </ul>
                    </div>
                </div>
                <div class="card-card-body msg_card_body m-3" id="messages">

                    <div>
                        <!--Pagination-->
                    </div>
                </div>
                <div class="card-footer">
                    <form id="form-send-message"
                          method="post">
                        <div class="input-group w-auto">
                            <div class="input-group-append">
                                        <span class="input-group-text attach_btn"><i
                                                    class="fas fa-paperclip"></i></span>
                            </div>
                            <textarea id="textarea-message" name="message" rows="4" class="form-control"
                                      placeholder="Type your message..."></textarea>
                            <div class="input-group-append">
                                <button id="btn-send-message" type="submit"
                                        class="btn btn-md btn_send btn-info p-3 m-0"><i
                                            class="fas fa-2x fa-location-arrow"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<template id="template-conv-list-item">
    <a onmouseover="showToolTip(this)"
       onclick="startConversation({{user.id}})"
       id="person-{{user.id}}"
       data-full-name="{{user.username}}">
        <li class="border-success" id="conv-list-item-{{user.id}}">
            <div class="d-flex bd-highlight">
                <div class="img_cont">
                    <img src="/images/gender/avatar-unknown.png"
                         class="rounded-circle user_img">
                    <span class="online_icon"></span>
                </div>
                <div class="user_info">
                    <div>{{user.username}}</div>
                    <div class="presence"></div>
                    <div class="conv-status"></div>
                </div>
            </div>
        </li>
    </a>
</template>

<template id="template-conv">
    <div id="messages"></div>
    <div>
        <form id="form-send-message">
            <div>
                <textarea name="message" class="form-control" rows="3" placeholder="Message..."></textarea>
            </div>
            <div>
                <button class="btn btn-md btn-block btn-grey mt-1 z-depth-0">
                    <i class="fa fa-paper-plane"></i> Send Message
                </button>
            </div>
        </form>
    </div>
</template>

<template id="template-outgoing-message">
    <div class="d-flex justify-content-end mb-4">
        <div class="msg_container_send">
            <div class="text-wrap">{{message.message}}</div>
            <span class="msg_time_send">{{message.time}}</span>
        </div>
        <div class="img_cont_msg">
            <img src="/images/gender/avatar-unknown.png" class="rounded-circle user_img_msg">
        </div>
    </div>
</template>

<template id="template-incoming-message">
    <div class="d-flex justify-content-start mb-4">
        <div class="img_cont_msg">
            <img src="/images/gender/avatar-unknown.png" class="rounded-circle user_img_msg">
        </div>
        <div class="msg_container">
            <div class="text-wrap">{{message.message}}</div>
            <span class="msg_time">{{message.time}}</span>
        </div>
    </div>
</template>

<template id="template-new-conversation">
    <div class="shadow-2" id="block-search-user">
        <div class="card-body">
            <div class="mb-2">In order to start new conversation you must search the user first.</div>
            <form id="form-check-user">
                <div class="input-group">
                    <input type="text" id="input-username" placeholder="Enter username" class="form-control">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-md btn-primary m-0">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
            <div class="list-group my-2" id="div-user-lookup-result"></div>
        </div>
    </div>
</template>

<template id="template-user-lookup-item">
    <a class="list-group-item list-group-item-action d-flex justify-content-between"
       onclick="startConversation({{user.id}}, true)">
        <span><i class="fa fa-user-alt"></i> {{user.username}}</span>
        <span><i class="fa fa-chevron-right"></i></span>
    </a>
</template>

<?php require(view_path('layout/footer.php')); ?>
<script>
    const chatSocketPrefix = '<?= $socket_prefix ?>';
</script>
<script src="/assets/js/user/private-chat.js"></script>