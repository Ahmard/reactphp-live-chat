<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta http-equiv="Content-Language" content="en">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <title><?=$title ?? $_ENV['APP_TITLE']?></title>

    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no" />
    <meta name="description" content="This is an example dashboard created using build-in elements and components.">
    <meta name="msapplication-tap-highlight" content="no">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="/assets/css/fontawesome-all.min.css">
    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
    <!-- Material Design Bootstrap -->
    <link rel="stylesheet" href="/assets/css/mdb.min.css">

    <!-- Your custom styles (optional) -->
</head>

<!--Big blue-->
<body class="fixed-sn animated mdb-color lighten-3">
    <!-- Navbar -->
    <nav class="navbar mdb-color fixed-top navbar-expand-lg scrolling-navbar double-nav">
        <!-- Breadcrumb -->
        <div class="breadcrumb-dn mr-auto">
            <a href="/" class="text-white pt-4">
                <b><?=$_ENV['APP_NAME'];?></b>
            </a>
        </div>

        <div class="d-flex change-mode">
            <!-- Navbar links -->
            <ul class="nav navbar-nav nav-flex-icons ml-auto">
                <!-- Dropdown -->
<!--
                <li class="nav-item">
                    <a href="/" class="nav-link btn btn-warning btn-sm waves-effect">
                        <span class="badge red">1</span>
                        <i class="fa fa-bell"></i>
                        <span class="d-none d-md-inline-block">Notifications</span>
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link btn btn-sm btn-warning dropdown-toggle waves-effect" href="#"
                        data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-user"></i>
                        <span class="clearfix d-none d-sm-inline-block">Profile</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right z-depth-1" aria-labelledby="userDropdown">
                        <a class="dropdown-item" href="#" onclick="event.preventDefault();
                            document.getElementById('logout-form').submit();">Log Out</a>
                        <a class="dropdown-item" href="/">My account</a>
                    </div>
                </li>
-->
            </ul>
        </div>
    </nav>
    <!-- Navbar -->

    <!-- Main layout -->
    <main style="padding-top:80px">