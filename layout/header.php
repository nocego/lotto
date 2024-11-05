<?php
//    error_reporting(E_ALL);
//    ini_set('display_errors', 1);
//?>
<?php
session_start();
?>
<!doctype html>
<html lang="de">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <!--- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <title><?= isset($PageTitle) ? $PageTitle : "Lotto"?></title>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-5">
        <div class="container">
            <a class="navbar-brand" href="http://lotto.konsum-staldenried.ch/">Lotto</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="http://lotto.konsum-staldenried.ch/">Home</a>
                    </li>
<!--                    <li class="nav-item dropdown">-->
<!--                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">-->
<!--                            Dropdown-->
<!--                        </a>-->
<!--                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">-->
<!--                            <li><a class="dropdown-item" href="#">Action</a></li>-->
<!--                            <li><a class="dropdown-item" href="#">Another action</a></li>-->
<!--                            <li><hr class="dropdown-divider"></li>-->
<!--                            <li><a class="dropdown-item" href="#">Something else here</a></li>-->
<!--                        </ul>-->
<!--                    </li>-->
                </ul>
                <ul class="navbar-nav d-flex">
                    <li class="nav-item">
                        <?php
                        if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != "") {
                            ?>
                                <a class="nav-link active" aria-current="page" href="/login/logout.php">Logout (<?= $_SESSION['username']?>)</a>
                            <?php
                        } else {
                            ?>
                            <a class="nav-link active" aria-current="page" href="/login/login.php">Login</a>
                            <?php
                        }
                        ?>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

