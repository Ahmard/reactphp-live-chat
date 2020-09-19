<?php require(view_path('layout/header.php')); ?>

<div class="container">
    <div class="card shadow-2">
        <div class="card-header">Create Account</div>
        <div class="card-body">
            <div class="alert alert-success rounded">
                Account created successfully.
            </div>
            <div class="text-right mt-2">
                <a href="/login" class="btn btn-md btn-brown rounded">
                    <i class="fa fa-sign-in-alt"></i>
                    Login
                </a>
            </div>
        </div>
    </div>
</div>

<?php require(view_path('layout/footer.php')); ?>