<!doctype html>
<html>
<head>
    <title>Live Chat</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="<?=url('assets/css/bootstrap.min.css')?>">
    <link href="<?=url('assets/css/style.css')?>" rel="stylesheet" id="bootstrap-css">
    <style>
        .conn-status {
            margin: 15px 25px;
        }
    </style>
</head>
<body>
    <div class="card m-5">
        <div class="card-header">Welcome</div>
        <div class="card-body">
            This multi-purpose server that handles <b>Http Requests</b> and <b>Sockets Connection</b> built on top of
            <a href="https://reactphp.org">ReactPHP</a> and <a href="https://socketo.me">Ratchet PHP</a>.<br/>
            Please know that this is entirely experimental, so production usage is discouraged.<hr/>
            <b><i>This is built to show a little of what <a href="https://reactphp.org">ReactPHP</a> can do.</i></b>
            <p>
                <a href="<?=url('chat')?>">Let's start :)</a>
            </p>
        </div>
        <div class="card-footer">
            This is the beginning of my journey to the AsyncPHP land.
        </div>
    </div>
</body>
</html>