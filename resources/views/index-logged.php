<?php use App\Core\Http\Url;

require(view_path('layout/header.php')); ?>
<div class="container">
    <div class="card">
        <div class="card-header">Welcome <?= request()->auth()->user()['username'] ?></div>
        <div class="card-body">
            <div class="card-title">Here is a list of what you can do</div>
            <div class="list-group">
                <a href="/chat/public" class="list-group-item list-group-item-action">
                    <div class="d-flex justify-content-between">
                            <span>
                                <i class="fa fa-comment"></i>
                                Public Chat
                            </span>
                        <i class="fa fa-chevron-right"></i>
                    </div>
                </a>
                <a href="/chat/private/<?= Url::getToken() ?>" class="list-group-item list-group-item-action">
                    <div class="d-flex justify-content-between">
                            <span>
                                <i class="fa fa-comment-alt"></i>
                                Private Chat
                            </span>
                        <i class="fa fa-chevron-right"></i>
                    </div>
                </a>
                <a href="/note/<?= Url::getToken() ?>" class="list-group-item list-group-item-action">
                    <div class="d-flex justify-content-between">
                            <span>
                                <i class="fa fa-list-alt"></i>
                                Note Taking
                            </span>
                        <i class="fa fa-chevron-right"></i>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<?php require(view_path('layout/footer.php')); ?>