<?php require(view_path('layout/header.php')); ?>

<div class="row justify-content-center mt-4">
    <div class="col-md-7">
        <div class="card">
            <div class="card-header font-weight-bold">Settings</div>

            <div class="card-body">
                <div class="list-group">
                    <a class="list-group-item-action list-group-item shadow"
                       href="<?= authRoute('user/settings/change-password') ?>">
                        <i class="fa fa-cogs"></i> Change Password
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require(view_path('layout/footer.php')); ?>
<script>

</script>
