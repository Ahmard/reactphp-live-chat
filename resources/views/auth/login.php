<?php

use Server\Http\Request;

/**
 * @var Request $request
 */


require(view_path('layout/header.php')); ?>

    <div class="container">
        <div class="card shadow-2">
            <div class="card-header">Login</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md">
                        Login to your account
                        <?php
                        if (!empty($error)) {
                            echo '<div class="alert alert-danger">'
                                . "<b>{$error}:</b>"
                                . '</div>';
                        }

                        if (!empty($errors)) {
                            foreach ($errors as $inputName => $validation) {
                                $inputName = ucfirst($inputName);
                                foreach ($validation as $error) {
                                    echo '<div class="alert alert-danger">'
                                        . "<b>{$inputName}:</b> {$error->getMessage()}"
                                        . '</div>';
                                }
                            }
                        }
                        ?>
                    </div>
                    <div class="col-md">
                        <form method="post" action="/login">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input name="email" id="email" placeholder="Email(anonymous@chat.test)"
                                       class="form-control" value="<?= $request->post('email') ?>">
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" name="password" id="password" placeholder="Password(1234)"
                                       class="form-control" value="<?= $request->post('password') ?>">
                            </div>

                            <button type="submit" class="my-2 btn btn-md z-depth-0 btn-block btn-primary">
                                <i class="fa fa-sign-in-alt"></i>
                                Login
                            </button>
                        </form>

                        <div class="mt-3">
                            New here? <a href="/register">Register</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php require(view_path('layout/footer.php')); ?>