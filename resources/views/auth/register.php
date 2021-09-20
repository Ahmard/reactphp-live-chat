<?php

use Server\Http\Request;

/**
 * @var Request $request
 */


require(view_path('layout/header.php')); ?>

    <div class="container">
        <div class="card shadow-2">
            <div class="card-header">Create Account</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md">
                        Create your account
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
                        <form method="post" action="/register">
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input name="username" id="username" placeholder="Username" class="form-control"
                                       value="<?= $request->getParsedBody()['username'] ?>">
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input name="email" id="email" placeholder="Email" class="form-control"
                                       value="<?= $request->getParsedBody()['email'] ?>">
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input name="password" id="password" placeholder="Password" class="form-control"
                                       value="<?= $request->getParsedBody()['password'] ?>">
                            </div>

                            <button type="submit" class="my-2 btn btn-md z-depth-0 btn-block btn-primary">
                                <i class="fa fa-plus"></i>
                                Create
                            </button>
                        </form>

                        <div class="mt-3">
                            Already have an account? <a href="/login">Login</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php require(view_path('layout/footer.php')); ?>