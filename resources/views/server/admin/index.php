<?php require(view_path('layout/header.php')); ?>
<link rel="stylesheet" href="/assets/codemirror/codemirror.css">
<link rel="stylesheet" href="/assets/codemirror/darcula.css">
<div class="container">
    <div class="card shadow-2">
        <div class="card-header">
            Welcome To Admin Panel
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md" id="execution-result"></div>
                <div class="col-md">
                    <form action="" id="form-send-command">
                        <div class="my-2">
                            <textarea cols="30" rows="4" name="command" id="command" class="form-control border-primary"
                                      placeholder="server.admin.config.env">
{
    "command": "server.admin.config.env",
    "action": "list-commands"
}</textarea>
                        </div>
                        <div class="text-right">
                            <button id="btn-send" type="submit" class="m-0 btn btn-md btn-blue z-depth-0 ml-1">
                                <i class="fa fa-paper-plane"></i>
                                Send
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<?php require(view_path('layout/footer.php')); ?>

<script>
    const adminSocketPrefix = '<?= $_ENV['ADMIN_SOCKET_URL_PREFIX'] ?>';
</script>
<script src="/assets/codemirror/codemirror.js"></script>
<script src="/assets/codemirror/addon/edit/matchbrackets.js"></script>
<script src="/assets/codemirror/addon/edit/closebrackets.js"></script>
<script src="/assets/codemirror/addon/comment/continuecomment.js"></script>
<script src="/assets/codemirror/addon/comment/comment.js"></script>
<script src="/assets/codemirror/javascript.js"></script>
<script>
    var editor = CodeMirror.fromTextArea(document.getElementById("command"), {
        matchBrackets: true,
        autoCloseBrackets: true,
        mode: "application/ld+json",
        lineWrapping: true,
        tabSize: 4,
        tabStyle: 'space',
        theme: 'darcula'
    });
</script>

<script src="/assets/js/site/admin.js?t=<?= time() ?>"></script>