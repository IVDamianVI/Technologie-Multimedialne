<?php

declare(strict_types=1);
session_start();
$current_page = basename($_SERVER['PHP_SELF']);
if (!isset($_SESSION['loggedin'])) {
    header('Location: logIn.php');
    exit();
}
$_SESSION['page'] = basename($_SERVER['PHP_SELF']);
$user = $_SESSION['user'];
$userGroup = $_SESSION['userGroup'];
include('loginCheckScript.php');
include('logOutAutoScript.php');
require('access.php');
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
    <link rel="stylesheet" href="css/styleGeo.css">
    <link rel="stylesheet" href="css/lightModeColors.css">
    <link rel="stylesheet" href="css/darkModeColors.css">
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
    <!-- Font Awesome Kit -->
    <script src="https://kit.fontawesome.com/bc52b20c9d.js" crossorigin="anonymous"></script>
    <!-- Title -->
    <title>Grubecki</title>
</head>

<body onload="myLoadHeader(); autoSubmitForm()">
    <div id='myHeader'></div>
    <main style="margin-bottom: 4em;">
        <section class="sekcja1" data-bs-theme="dark">
            <div class="container-fluid">
                <?php
                if ($userGroup === "admin") {
                    echo '<h1 class="text-center shine" style="color: #ff9000; padding-top: 1em; padding-bottom: 1em;">
                    Informacje o użytkownikach
                    </h1>';
                } else {
                    echo '<h1 class="text-center shine" style="color: #ff9000; padding-top: 1em; padding-bottom: 1em;">
                    Historia logowania
                    </h1>';
                }
                ?>
                <div class="row justify-content-center">
                    <div class="col-auto">
                        <table class="table table-responsive text-center table-striped">
                            <tr class="align-middle">
                                <th scope="col">Data</th>
                                <th scope="col">IP</th>
                                <th scope="col">Wizyty</th>
                                <th scope="col">Lokalizacja</th>
                                <th scope="col" class="pc-only">Współrzędne</th>
                                <th scope="col">Google<br>Maps</th>
                                <th scope="col">Przeglądarka<br> i System</th>
                                <th scope="col" class="pc-only">Ekran</th>
                                <th scope="col" class="pc-only">Okno</th>
                                <th scope="col" class="pc-only">Kolory</th>
                                <th scope="col" class="pc-only">Cookies</th>
                                <th scope="col" class="pc-only">Java</th>
                                <th scope="col" class="pc-only">Język</th>
                            </tr>
                            <?php
                            function replaceBrowserAndOSWithIcons($text)
                            {
                                $browserIcons = [
                                    "Chrome" => '<i class="bi bi-browser-chrome" style="color: #4c8bf5;"></i>',
                                    "Firefox" => '<i class="bi bi-browser-firefox" style="color: #E66000;"></i>',
                                    "Edge" => '<i class="bi bi-browser-edge" style="color: #0078d7;"></i>',
                                    "Safari" => '<i class="bi bi-browser-safari" style="color: #00D3F9;"></i>',
                                    "Opera" => '<i class="fa fa-opera" style="color: #ff1b2d;"></i>',
                                    'Internet Explorer' => '<i class="fa-brands fa-internet-explorer" style="color: #1EBBEE;"></i>',
                                ];

                                $osIcons = [
                                    "Windows" => '<i class="bi bi-windows" style="color: #357ec7;"></i>',
                                    "Mac OS" => '<i class="bi bi-apple" style="color: #ffffff;"></i>',
                                    "iOS" => '<i class="bi bi-apple" style="color: #ffffff;"></i>',
                                    "Android" => '<i class="bi bi-android2" style="color: #a4c639;"></i>',
                                ];

                                foreach ($browserIcons as $browserName => $browserIcon) {
                                    $text = str_replace($browserName, $browserIcon, $text);
                                }

                                foreach ($osIcons as $osName => $osIcon) {
                                    $replacement = '<br>' . $osIcon;
                                    $text = str_replace($osName, $replacement, $text);
                                }

                                $text = preg_replace('/(\d+\.\d+)[.\d]*/', '$1', $text);

                                return $text;
                            }

                            $true = '<i style="color: #45e46d; font-size: 1.5em;" class="bi bi-check-circle-fill"></i>';
                            $false = '<i style="color: #e44545; font-size: 1.5em;" class="bi bi-x-circle-fill"></i>';
                            $dbConn = mysqli_connect($dbHost, $dbUsername, $dbPassword, $dbDatabase);

                            if ($dbConn) { //* Połączono z bazą danych
                                if ($userGroup === "admin") {
                                    $goscieQuery = "SELECT * FROM goscieportalu JOIN visits ON goscieportalu.ip = visits.visitor_ip GROUP BY ip ORDER BY datetime DESC ";
                                } else {
                                    $goscieQuery = "SELECT * FROM goscieportalu JOIN visits ON goscieportalu.ip = visits.visitor_ip WHERE username = '$user' GROUP BY ip ORDER BY datetime DESC ";
                                }
                                $goscieResult = mysqli_query($dbConn, $goscieQuery);

                                if (mysqli_num_rows($goscieResult) > 0) {
                                    if (mysqli_num_rows($goscieResult) > 5) {
                                        $isMobile = true;
                                    }
                                    while ($row = mysqli_fetch_assoc($goscieResult)) {
                                        $browserAndOS = replaceBrowserAndOSWithIcons($row["browser"]);
                                        echo '<tr class="align-middle">';
                                        echo "<td>" . $row["datetime"] . "</td>";
                                        echo "<td>" . $row["ip"] . "</td>";
                                        echo '<td><i class="bi bi-eye-fill"></i> ' . $row["views"] . '</td>';
                                        echo "<td>" . $row["localization"] . "</td>";
                                        echo '<td class="pc-only">' . $row["coord"] . "</td>";
                                        echo '<td><a style="color: #ea4335; font-size: 1.5em;" href="https://www.google.pl/maps/place/' . $row["coord"] . '"><i class="bi bi-geo-alt-fill"></i></a></td>';
                                        echo "<td>" . $browserAndOS . "</td>";
                                        echo '<td class="pc-only">' . $row["display"] . "</td>";
                                        echo '<td class="pc-only">' . $row["viewport"] . "</td>";
                                        echo '<td class="pc-only">' . $row["colors"] . "</td>";
                                        echo '<td class="pc-only">' . (($row["cookies"] == '1') ? $true : $false) . "</td>";
                                        echo '<td class="pc-only">' . (($row["java"] == '1') ? $true : $false) . "</td>";
                                        echo '<td class="pc-only">' . $row["lang"] . "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "Brak rekordów w bazie danych.";
                                }
                            } else { //! Nie można połączyć się z bazą danych
                                $_SESSION['error_message'] = mysqli_connect_errno() . ' ' . mysqli_connect_error();
                                header('Location: index.php');
                                exit();
                            }

                            $dbConn->close();
                            ?>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <!-- Dark/Light Button -->
    <button class="btn btn-outline-warning bg-dark position-fixed end-0 translate-middle-y" id="btnSwitch"
        style="z-index: 999; margin-right: 2px; border-color: #ff9000;">
        <i class="bi bi-sun-fill" style="color: #ff9000"></i>
    </button>
    <script type="text/javascript" src="script/buttonTheme.js"></script>
    <!-- Dark/Light Button -->

    <?php
    require_once 'footer.php'; ?>
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