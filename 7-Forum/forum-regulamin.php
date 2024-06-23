<?php

declare(strict_types=1);
session_start();
// if (!isset($_SESSION['loggedin'])) {
//     header('Location: logIn.php');
//     exit();
// }
$_SESSION['page'] = basename($_SERVER['PHP_SELF']);
// include('loginCheckScript.php');
// include('logOutAutoScript.php');
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
    <link rel="stylesheet" href="css/styleIndex.css">
    <link rel="stylesheet" href="css/lightModeColors.css">
    <link rel="stylesheet" href="css/darkModeColors.css">
    <link rel="stylesheet" href="css/styleForum.css">
    <link rel="stylesheet" href="css/styleChat.css">
    <!-- Icon -->
    <link rel="icon" href="media/favicon/favicon-orange.png">
    <!-- Scripts Internal -->
    <script src="script/loadHeader.js"></script>
    <!-- GeoIP2 -->
    <script src="//geoip-js.com/js/apis/geoip2/v2.1/geoip2.js"></script>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN"
        crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap5.min.js" type="text/javascript"
        language="javascript"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.js" type="text/javascript" language="javascript"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js" type="text/javascript"
        language="javascript"></script>
    <!-- Title -->
    <title>Grubecki</title>
</head>

<body onload="myLoadHeader(); autoSubmitForm()">
    <div id='myHeader'></div>
    <main style="position: relative; top: 35px;">
        <div class="menu-left">
            <br />
            <a href="forum.php" class="d-flex align-items-center">
                <p class="mb-0">Strona główna</p>
            </a>
            <a href="forum-regulamin.php" class="d-flex align-items-center">
                <p class="mb-0">Regulamin</p>
            </a>
            <br />
            <p class="d-flex align-items-center mb-1"><strong>Twoje tematy</strong></p>
            <?php
            if (isset($_SESSION['loggedin'])) {
                require('access.php');
                $username = $_SESSION['user'];
                $dbConn = mysqli_connect($dbHost, $dbUsername, $dbPassword, $dbDatabase);
                $result = mysqli_query($dbConn, "SELECT * FROM topic WHERE creator = '$username' ORDER BY id DESC");
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<a href="forum.php?temat=' . $row['id'] . '" class="d-flex align-items-center">';
                    echo '<p class="mb-0">' . $row['title'] . '</p>';
                    echo '</a>';
                }
                mysqli_close($dbConn);
            }
            ?>
            <a href="forum-nowy-temat.php" class="d-flex align-items-center">
                <p class="mb-0">+ Nowy temat</p>
            </a>
            <br /><br />
            <?php
            if (!isset($_SESSION['loggedin'])) {
                echo '<a href="logIn.php" class="d-flex align-items-center">';
                echo '<p class="mb-0">Zaloguj się</p></a>';
            }
            ?>
        </div>
        <div class="content padding scrollable" style="">
            <h1 class="shine" style="color: #ff9000;">Regulamin Forum Internetowego</h1>

            <h2>Wstęp</h2>
            <p><b>1.</b> Niniejszy regulamin określa zasady korzystania z Forum Internetowego <i>[nazwa forum]</i>.</p>
            <p><b>2.</b> Użytkownik, rejestrując się na forum, akceptuje postanowienia niniejszego regulaminu.</p>
            <br />
            <h2>Postanowienia Ogólne</h2>
            <p><b>1.</b> Każdy użytkownik zobowiązany jest do przestrzegania zasad kultury osobistej i netykiety.</p>
            <p><b>2.</b> Zakazuje się publikowania treści niezgodnych z prawem, obraźliwych, dyskryminujących, czy
                zawierających mowę nienawiści.</p>
            <br />
            <h2>Treści Wulgarne i Obraźliwe</h2>
            <p><b>1.</b> Użytkownicy zobowiązani są do powstrzymywania się od publikowania wulgarnych, obscenicznych lub
                obraźliwych treści.</p>
            <p><b>2.</b> W przypadku naruszenia tej zasady, moderator forum ma prawo do edycji lub usunięcia postu.</p>
            <p><b>3.</b> Powtarzające się publikowanie treści wulgarnych skutkować będzie czasową lub stałą blokadą
                konta użytkownika.</p>
            <p><b>4.</b> W szczególnie rażących przypadkach, administracja forum zastrzega sobie prawo do zgłoszenia
                sprawy do odpowiednich organów.</p>
            <br />
            <h2>Postępowanie Moderatorów</h2>
            <p><b>1.</b> Moderatorzy są odpowiedzialni za przestrzeganie regulaminu na forum.</p>
            <p><b>2.</b> Moderator ma prawo do edycji, przenoszenia lub usuwania postów, które naruszają regulamin.</p>
            <p><b>3.</b> Decyzje moderatorów są ostateczne i nie podlegają dyskusji na forum publicznym.</p>
            <br />
            <h2>Prawa i Obowiązki Użytkowników</h2>
            <p><b>1.</b> Użytkownik ma prawo do wyrażania swoich opinii, o ile nie naruszają one zasad regulaminu.</p>
            <p><b>2.</b> Użytkownik zobowiązany jest do szanowania opinii innych użytkowników oraz do konstruktywnej
                dyskusji.</p>
            <p><b>3.</b> Użytkownik ma obowiązek zgłaszania administracji wszelkich przypadków naruszeń regulaminu.</p>
            <br />
            <h2>Postanowienia Końcowe</h2>
            <p><b>1.</b> Administracja forum zastrzega sobie prawo do zmiany regulaminu. O wszelkich zmianach
                użytkownicy będą informowani z odpowiednim wyprzedzeniem.</p>
            <p><b>2.</b> Naruszenie regulaminu może skutkować różnymi formami sankcji, od ostrzeżenia po stałą blokadę
                konta.</p>
            <p><b>3.</b> Regulamin wchodzi w życie z dniem jego opublikowania na forum.</p>
            <br /><br /><br />
            <p style="color: grey; opacity: .2;">Regulamin wygenerowany przez ChatGPT.</p>
        </div>
    </main>
    <!-- Dark/Light Button START -->
    <button class="btn btn-outline-warning bg-dark position-fixed end-0 translate-middle-y" id="btnSwitch"
        style="z-index: 999; margin-right: 2px; border-color: #ff9000;">
        <i class="bi bi-sun-fill" style="color: #ff9000"></i>
    </button>
    <script type="text/javascript" src="script/buttonTheme.js"></script>
    <!-- Dark/Light Button END -->
    <?php require_once 'footer.php'; ?>
    <!-- Get Info START -->
    <form method="POST" id="getInfo" name="getInfo">
        <input type="hidden" value="" id="display" name="display" />
        <input type="hidden" value="" id="viewport" name="viewport" />
        <input type="hidden" value="" id="colors" name="colors" />
        <input type="hidden" value="" id="cookies" name="cookies" />
        <input type="hidden" value="" id="java" name="java" />
        <input type="hidden" value="" id="page" name="page" />
        <input type="hidden" value="" id="city" name="city" />
        <input type="hidden" value="" id="coords" name="coords" />
    </form>
    <script type="text/javascript" src="script/getInfo.js"></script>
    <script>
        function autoSubmitForm() {
            var formData = new FormData(document.getElementById("getInfo"));
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "getInfoScript.php", true);
            xhr.onload = function () {
                if (xhr.status === 200) { }
            };
            xhr.send(formData);
        }
    </script>
    <!-- Get Info END -->
</body>

</html>