<?php

declare(strict_types=1);
session_start();
$current_page = basename($_SERVER['PHP_SELF']);
if (!isset($_SESSION['loggedin'])) {
    header('Location: logIn.php');
    exit();
}
include('loginCheckScript.php');
include('logOutAutoScript.php');
?>
<!DOCTYPE html>
<html lang="pl" data-bs-theme="dark">

<head>
    <!-- Meta -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Meta Info -->
    <meta name="author" content="Damian Grubecki">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <!-- Style Sheets Internal -->
    <link rel="stylesheet" href="css/styleLogin.css">
    <!-- Icon -->
    <link rel="icon" href="media/favicon/favicon-orange.png">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap5.min.js" type="text/javascript" language="javascript"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.js" type="text/javascript" language="javascript"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js" type="text/javascript" language="javascript"></script>
    <!-- Title -->
    <title>Grubecki</title>
</head>

<body class="text-center background-first">
    <div class="form-register background-second">
        <div class="logo">
            <img height="60px" src="media/favicon/favicon-orange.png" alt="Logo">
        </div>
        <h1 id="title">Zmień tło</h1>
        <span id="subtitle">Wybierz nowy baner dla swojego profilu</span>
        <form class="form-signin form-register" action="changeBannerScript.php" method="post" enctype="multipart/form-data">
            <?php
            // Obsługa powiadomień o błędach i sukcesach
            session_start();
            if (isset($_SESSION['error_message'])) {
                echo '<div class="alert alert-danger" role="alert">';
                echo '<i class="bi bi-exclamation-triangle-fill"></i> Wystąpił problem<hr>';
                echo $_SESSION['error_message'];
                echo '</div>';
                unset($_SESSION['error_message']);
            } else if (isset($_SESSION['success_message'])) {
                echo '<div class="alert alert-success" role="alert">';
                echo '<i class="bi bi-check-circle-fill"></i> Sukces<hr>';
                echo $_SESSION['success_message'];
                echo '</div>';
                unset($_SESSION['success_message']);
            }
            ?>
            <!-- Wyświetlenie obecnego baneru -->
            <div class="banner-container" style="margin-top: 1em">
                <img src="media/banner/<?php echo $_SESSION['banner']; ?>" />
            </div>
            <p id="label-avatar" style="margin-top: 12px">Aktualny baner</p>
            <!-- Wyświetlenie obecnego baneru -->
            <label for="banner">Wybierz nową grafikę z komputera</label>
            <input type="file" id="banner" name="banner">
            <label for="banner">Dopuszczalne formaty: JPG, PNG, SVG, GIF</label>
            <button class="btn btn-lg btn-primary" type="submit" disabled>Zmień</button>
        </form>
        <div class="mb-3">
            <!-- <a id="link" href="resetBannerScript.php" style="font-weight: unset;">Zresetuj baner do domyślnego</a> -->
            <br>
            <a id="link" href="profile.php" style="font-weight: unset;">Powrót na stronę profilu</a>
        </div>
    </div>
    <script src="script/buttonChangeAvatar.js" type="text/javascript"></script>
</body>

</HTML>