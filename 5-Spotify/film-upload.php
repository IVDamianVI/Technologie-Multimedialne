<?php

declare(strict_types=1);
session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: logIn.php');
    exit();
}
// else if ($_SESSION['userGroup'] !== "admin") {
//     header('Location: index.php');
//     exit();
// }

$_SESSION['page'] = basename($_SERVER['PHP_SELF']);
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
    <link rel="stylesheet" href="css/styleYouTube.css">
    <link rel="stylesheet" href="css/lightModeColors.css">
    <link rel="stylesheet" href="css/darkModeColors.css">
    <!-- Icon -->
    <link rel="icon" href="media/favicon/favicon-orange.png">
    <!-- Scripts Internal -->
    <script src="script/loadHeader.js"></script>
    <!-- GeoIP2 -->
    <script src="//geoip-js.com/js/apis/geoip2/v2.1/geoip2.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.js" type="text/javascript" language="javascript"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js" type="text/javascript"
        language="javascript"></script>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN"
        crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap5.min.js" type="text/javascript"
        language="javascript"></script>
    <!-- Title -->
    <title>Grubecki</title>
</head>

<body onload="myLoadHeader(); autoSubmitForm()">
    <div id='myHeader'></div>
    <main>
        <div class="content">
            <div class="container">
                <form method="POST" action="filmUploadScript.php" enctype="multipart/form-data" autocomplete="off">
                    <div id="drop-area" ondrop="dropHandler(event);" ondragover="dragOverHandler(event);">
                        <div class="pc-only">
                            <h3>Przeciągnij i upuść plik tutaj</h3>
                            <span>lub</span>
                        </div>
                        <div class="form-group">
                            <input type="file" id="file-input" name="file-input" onchange="handleFiles(file)">
                        </div>
                        <div id="scroll-window" style="display: none;">
                            <ul id="file-list"></ul>
                        </div>
                    </div>
                    <br/>
                    <div class="form-group">
                        <label for="title">Tytuł</label>
                        <input type="text" class="form-control" id="title" name="title" placeholder="Tytuł" required>
                    </div>
                    <br/>
                    <div class="form-group">
                        <label for="musician">Reżyser</label>
                        <input type="text" class="form-control" id="musician" name="musician" placeholder="Reżyser"
                        required>
                    </div>
                    <br/>
                    <div class="form-group">
                        <label for="lyrics">Napisy</label>
                        <textarea class="form-control" id="lyrics" name="lyrics" placeholder="Napisy do filmu"
                            rows="3"></textarea>
                    </div>
                    <br/>
                    <div class="form-group">
                        <label for="musictypeid">Gatunek</label>
                        <select name="musictypeid" required>
                            <!-- <option value="" name="musictypeid" disabled selected hidden>Wybierz gatunek</option> -->
                            <?php
                            require('access.php');
                            $dbConn = mysqli_connect($dbHost, $dbUsername, $dbPassword, $dbDatabase);
                            $result = mysqli_query($dbConn, 'SELECT * FROM filmtype ORDER BY id ASC');
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo '<option value="' . $row['id'] . '" name="musictypeid">' . $row['name'] . '</option>';
                            }
                            mysqli_close($dbConn);
                            ?>
                        </select>
                        |
                        <label for="musictypeid">Dodaj do playlisty</label>
                        <select name="playlistid">
                            <option value="null" name="musictypeid" selected>Wybierz...</option>
                            <?php
                            require('access.php');
                            $dbConn = mysqli_connect($dbHost, $dbUsername, $dbPassword, $dbDatabase);
                            $result = mysqli_query($dbConn, 'SELECT * FROM filmplaylistname ORDER BY id ASC');
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo '<option value="' . $row['id'] . '" name="playlistid">' . $row['name'] . '</option>';
                            }
                            mysqli_close($dbConn);
                            ?>
                        </select>
                    </div>
                    <br/>
                    <div class="form-group d-flex flex-row-reverse">
                        <button class="btn btn-primary ready" type="submit" disabled style="width: auto;">
                            +<i class="bi bi-music-note"></i> Dodaj utwór
                        </button>
                    </div>
                </form>
            </div>
            <script type="text/javascript" src="script/dragAndDrop.js"></script>

        </div>
    </main>
    <!-- Dark/Light Button START -->
    <button class="btn btn-outline-warning bg-dark position-fixed end-0 translate-middle-y" id="btnSwitch"
        style="z-index: 999; margin-right: 2px; border-color: #ff9000;">
        <i class="bi bi-sun-fill" style="color: #ff9000"></i>
    </button>
    <script type="text/javascript" src="script/buttonTheme.js"></script>
    <!-- Dark/Light Button END -->
    <?php require_once 'footer.php';
    ?>
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