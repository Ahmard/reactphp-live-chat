<?php require(view_path('layout/header.php')); ?>
    <div class="container">
        <div class="card shadow-2">
            <div class="card-header font-weight-bold">Choose chat mode</div>
            <div class="card-body">
                <div class="text-center">
                    <a href="/chat/public" class="btn btn-large btn-info mr-4">
                        <i class="fa fa-comment fa-3x"></i>
                        <br/>Public Chat
                    </a>
                    <a href="/chat/private" class="btn btn-large btn-info ml-4">
                        <i class="fa fa-comment-alt fa-3x"></i>
                        <br/>Private Chat
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php require(view_path('layout/footer.php')); ?>