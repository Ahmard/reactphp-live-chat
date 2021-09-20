<?php

use Server\Http\Request;

/**
 * @var Request $request
 */

require(view_path('layout/header.php'));

?>
<div class="container">
    <div class="card">
        <div class="card-header">Welcome <?= $request->auth()->user()['username'] ?></div>
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
                <a href="<?= $request->authRoute('chat/private') ?>" class="list-group-item list-group-item-action">
                    <div class="d-flex justify-content-between">
                            <span>
                                <i class="fa fa-comment-alt"></i>
                                Private Chat
                            </span>
                        <i class="fa fa-chevron-right"></i>
                    </div>
                </a>
                <a href="<?= $request->authRoute('note') ?>" class="list-group-item list-group-item-action">
                    <div class="d-flex justify-content-between">
                            <span>
                                <i class="fa fa-book-open"></i>
                                Note Taking
                            </span>
                        <i class="fa fa-chevron-right"></i>
                    </div>
                </a>
                <a href="<?= $request->authRoute('list') ?>" class="list-group-item list-group-item-action">
                    <div class="d-flex justify-content-between">
                            <span>
                                <i class="fa fa-list"></i>
                                List Taking
                            </span>
                        <i class="fa fa-chevron-right"></i>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<?php require(view_path('layout/footer.php')); ?>