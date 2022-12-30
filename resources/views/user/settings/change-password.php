<?php

/**
 * @var Request $request
 */

use Server\Http\Request;

require(view_path('layout/header.php'));

?>

<div class="row justify-content-center mt-4">
    <div class="col-md-7">
        <div class="card">
            <div class="card-header">Change Password</div>
            <div class="card-body">
                <div id="request-response"></div>
                <form id="form-change-password">
                    <div class="input-group mb-2">
                        <input type="password" name="password" class="form-control" placeholder="Old Password">
                    </div>

                    <div class="row mb-2">
                        <div class="col-md">
                            <input type="password" name="new_password" class="form-control" placeholder="New Password">
                        </div>
                        <div class="col-md">
                            <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-sm btn-primary btn-block">
                        <i class="fa fa-sync-alt"></i> Change Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require(view_path('layout/footer.php')); ?>
<script>
    $('#form-change-password').submit(function (event) {
        event.preventDefault();

        let $btnChangePassword = $(event.currentTarget).find('button[type="submit"]');

        $btnChangePassword.addClass('disabled')
            .html('<i class="fa fa-spinner fa-spin"></i> Changing password');

        //remove prev error
        $('#request-response').html('');

        $.ajax({
            url: '<?= $request->authRoute('api/user/settings/change-password') ?>',
            method: 'POST',
            error: ajaxErrorHandler,
            data: {
                old_password: $('input[name="password"]').val(),
                new_password: $('input[name="new_password"]').val(),
                confirm_password: $('input[name="confirm_password"]').val(),
            }
        }).then(function (response) {
            if (response.success){
                $('#request-response').html('<div class="alert alert-success"><i class="fa fa-check"></i> '+response.data+'</div>');

                document.getElementById('form-change-password').reset();
            }else {
                //show error
                $('#request-response').html('<div class="alert alert-danger">'+response.error+'</div>');
            }

            $btnChangePassword.removeClass('disabled')
                .html('<i class="fa fa-sync-alt"></i> Change Password')
        });
    });
</script>
