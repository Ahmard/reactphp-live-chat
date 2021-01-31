<?php require(view_path('layout/header.php')); ?>

<div class="container mt-5">
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="mt-2 font-weight-bold">Profile</div>
            <a href="<?=authRoute('user/settings')?>" class="btn btn-sm px-2 btn-outline-primary">
                <i class="fa fa-cog"></i> Settings
            </a>
        </div>
        <div class="card-body">
            <div class="list-group">

            </div>
        </div>
    </div>
</div>

<?php require(view_path('layout/footer.php')); ?>
<script>

</script>
