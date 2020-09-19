<?php require(view_path('layout/header.php')); ?>
<div class="container">
    <div class="card shadow-2">
        <div class="card-header">
            Welcome
        </div>
        <div class="card-body">
            This is multi-purpose server that handles <b>Http Requests</b> and <b>Socket Connections</b>, built on top of
            <a href="https://reactphp.org">ReactPHP</a> and <a href="https://socketo.me">Ratchet PHP</a>.<br />
            Please know that this is entirely experimental, so production usage is discouraged.
            <hr />
            <b><i>This is built to show a little of what <a href="https://reactphp.org">ReactPHP</a> can do.</i></b>
            <hr/>
            <div id="total-users-chatting" class="font-weight-bold">
                There are x users having public conversation
            </div>
            <p class="font-italic mt-2">
                Let's start :)
            </p>
            <div class="d-flex justify-content-between">
                <div>
                    <a href="/chat/public" class="btn btn-md btn-primary">
                        <i class="fa fa-comment"></i>
                        Public Chat
                    </a>
                    <a href="/chat/private" class="btn btn-md btn-primary">
                        <i class="fa fa-comment-dots"></i>
                        Private Chat
                    </a>
                </div>
                <div>
                    <a href="/login" class="btn btn-md btn-primary">
                        <i class="fa fa-sign-in-alt"></i>
                        Login
                    </a>
                    <a href="/register" class="btn btn-md btn-primary">
                        <i class="fa fa-plus"></i>
                        Register
                    </a>
                </div>
            </div>
        </div>
        <div class="card-footer">
            This is the beginning of my journey to the AsyncPHP land.
        </div>
    </div>
</div>
<?php require(view_path('layout/footer.php')); ?>