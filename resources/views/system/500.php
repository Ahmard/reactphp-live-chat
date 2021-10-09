<?php

/**
 * @var Throwable|null $exception
 * @var Throwable|null $error
 */
$error = ($error ?? $exception) ?? null;

require(view_path('layout/header.php'));

?>
    <div class="container">
        <div class="card card-danger">
            <div class="card-header bg-danger text-white">
                500(Internal Server Error)
            </div>
            <div class="card-body text-danger font-weight-bolder">
                <?php
                if (isset($error)) {
                    if ($error instanceof Exception) {
                        echo $error->getMessage();
                    } else {
                        echo $error;
                    }
                } else {
                    echo 'Server ran in to an error while processing your request.';
                }
                ?>
                <br/>
                <a href="/">Let's go home</a>
            </div>
        </div>
    </div>
<?php require(view_path('layout/footer.php')); ?>