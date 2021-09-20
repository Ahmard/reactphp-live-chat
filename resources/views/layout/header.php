<?php

use Server\Http\Request;
use Server\Http\Url;

/**
 * @var Request $request
 */

$homeUrl = '/';

$auth = $request->auth();
if ($auth->check()) {
    $homeUrl = '/home/' . $auth->token();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta http-equiv="Content-Language" content="en">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <title><?= $title ?? $_ENV['APP_TITLE'] ?></title>

    <meta name="viewport"
          content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no"/>
    <meta name="description" content="This is an example dashboard created using build-in elements and components.">
    <meta name="msapplication-tap-highlight" content="no">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="/assets/css/fontawesome-all.min.css">
    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
    <!-- Material Design Bootstrap -->
    <link rel="stylesheet" href="/assets/css/mdb.min.css">

    <script>
        const USER = JSON.parse('<?=json_encode($auth->user())?>');
        const TOKEN = '<?=$auth->token()?>';
    </script>
    <!-- Your custom styles (optional) -->
</head>

<!--Big blue-->
<body class="fixed-sn animated mdb-color lighten-3">
<!-- Navbar -->
<nav class="navbar mdb-color fixed-top navbar-expand-lg scrolling-navbar double-nav">
    <!-- Breadcrumb -->
    <div class="breadcrumb-dn mr-auto">
        <a href="<?= $homeUrl ?>" class="text-white pt-4">
            <b><?= $_ENV['APP_NAME']; ?></b>
        </a>
    </div>

    <div class="d-flex change-mode">
        <!-- Navbar links -->
        <ul class="nav navbar-nav nav-flex-icons ml-auto">
            <!-- Dropdown -->
            <?php if ($auth->check()): ?>
                <li class="nav-item">
                    <a href="/chat/private/<?= Url::getToken() ?>" class="nav-link btn btn-primary btn-sm waves-effect"
                       id="nav-link-message">
                        <span class="badge red"></span>
                        <i class="fa fa-envelope"></i>
                        <span class="d-none d-md-inline-block">Message</span>
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link btn btn-sm btn-primary dropdown-toggle waves-effect" href="#"
                       data-toggle="dropdown"
                       aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-user"></i>
                        <span class="clearfix d-none d-sm-inline-block">
                            <?= $auth->user()['username'] ?>
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right z-depth-1" aria-labelledby="userDropdown">
                        <a class="dropdown-item" href="<?= $request->authRoute('user/profile') ?>">Profile</a>
                        <a class="dropdown-item" href="/">Log Out</a>
                    </div>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>
<!-- Navbar -->

<!-- Main layout -->
<main style="padding-top:80px">