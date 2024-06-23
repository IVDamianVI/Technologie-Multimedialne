<?php

declare(strict_types=1);
session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: logIn.php');
    exit();
}
$_SESSION['page'] = basename($_SERVER['PHP_SELF']);
$isMobile = true;
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
    <link rel="stylesheet" href="css/styleIndex.css">
    <link rel="stylesheet" href="css/lightModeColors.css">
    <link rel="stylesheet" href="css/darkModeColors.css">
    <!-- Icon -->
    <link rel="icon" href="media/favicon/favicon-orange.png">
    <!-- Scripts Internal -->
    <script src="script/loadHeader.js"></script>
    <!-- GeoIP2 -->
    <script src="//geoip-js.com/js/apis/geoip2/v2.1/geoip2.js"></script>
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

<body onload="myLoadHeader(); autoSubmitForm()">
    <div id='myHeader'></div>
    <main>
        <section class="sekcja1">
            <div class="container-fluid">
                <div class="container">
                    <div class="row">
                        <div class="col col-12 col-md flex-column">
                            <div class="col card text-center m-1 p-2">
                                <h1 class="text-center shine" style="color: #ff9000;">
                                    Zalogowany użytkownik
                                </h1>
                                <span style="font-weight:bold;">
                                <?php
                                echo exec('whoami');
                                ?>
                                </span>
                            </div>
                            <div class="col card m-1 p-2">
                                <h1 class="text-center shine" style="color: #ff9000;">
                                    DNS Domeny
                                </h1>
                                <div class="container" style="padding-left: 4em;">
                                <?php
                                $result = dns_get_record("ivdamianvi.smallhost.pl");
                                $tag = '<span style="font-weight:bold;">';
                                $tagEnd = '</span>';
                                foreach ($result as $record) {
                                    echo '<span style="font-weight:bold;">Host: <span style="font-weight:normal;">' . $record['host'] . "<br>";
                                    echo '<span style="font-weight:bold;">Class: <span style="font-weight:normal;">' . $record['class'] . "<br>";
                                    echo '<span style="font-weight:bold;">TTL: <span style="font-weight:normal;">' . $record['ttl'] . "<br>";
                                    echo '<span style="font-weight:bold;">Type: <span style="font-weight:normal;">' . $record['type'] . "<br>";

                                    if ($record['type'] === 'MX') {
                                        echo '<span style="font-weight:bold;">Priority: <span style="font-weight:normal;">' . $record['pri'] . "<br>";
                                        echo '<span style="font-weight:bold;">Target: <span style="font-weight:normal;">' . $record['target'] . "<br>";
                                    } elseif ($record['type'] === 'TXT') {
                                        echo '<span style="font-weight:bold;">TXT Data: <span style="font-weight:normal;">' . $record['txt'] . "<br>";
                                    } elseif ($record['type'] === 'A') {
                                        echo '<span style="font-weight:bold;">IP Address: <span style="font-weight:normal;">' . $record['ip'] . "<br>";
                                    }
                                    echo "<br>";
                                }
                                ?>
                                </div>
                            </div>
                            <div class="col card text-center m-1 p-2">
                                <h1 class="text-center shine" style="color: #ff9000;">
                                    CPU, RAM, Procesy
                                </h1>
                                <?php
                                exec('TERM=xterm /usr/bin/top -n 1 -b -i', $top, $error);
                                echo nl2br(implode("\n", $top));
                                if ($error) {
                                    exec('TERM=xterm /usr/bin/top -n 1 -b 2>&1', $error);
                                    echo "Error: ";
                                    exit($error[0]);
                                }
                                ?>
                            </div>
                        </div>
                        <div class="col col-12 col-md flex-column">
                            <div class="col card text-center m-1 p-2">
                                <h1 class="text-center shine" style="color: #ff9000;">
                                    IP i nazwy domenowe
                                </h1>
                                <?php
                                echo '<span style="font-weight:bold;">IP serwera domeny</span>';
                                $ip = gethostbyname('ivdamianvi.smallhost.pl');
                                echo $ip . '<BR />';
                                echo '<span style="font-weight:bold;">IP gościa portalu</span>';
                                $ip = $_SERVER["REMOTE_ADDR"];
                                echo $ip . '<BR />';
                                echo '<span style="font-weight:bold;">Nazwa domenowa skojarzona z IP</span>';
                                $hostname = gethostbyaddr("8.8.8.8");
                                echo $hostname . '<BR />';
                                echo '<span style="font-weight:bold;">Nazwa domenowa hosta gościa portalu</span>';
                                $hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
                                echo $hostname;
                                ?>
                            </div>
                            <div class="col card m-1 p-2">
                                <h1 class="text-center shine" style="color: #ff9000;">
                                    Lista plików w katalogu
                                </h1>
                                <?php
                                $output = shell_exec('ls -al');
                                echo "<pre>$output</pre>";
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Dark/Light Button START -->
    <button class="btn btn-outline-warning bg-dark position-fixed end-0 translate-middle-y" id="btnSwitch" style="z-index: 999; margin-right: 2px; border-color: #ff9000;">
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
            xhr.onload = function() {
                if (xhr.status === 200) {}
            };
            xhr.send(formData);
        }
    </script>
    <!-- Get Info END -->
</body>

</html>