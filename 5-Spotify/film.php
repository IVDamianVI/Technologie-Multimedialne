<?php

declare(strict_types=1);
session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: logIn.php');
    exit();
}

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
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js" type="text/javascript" language="javascript"></script>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap5.min.js" type="text/javascript" language="javascript"></script>
    <!-- Title -->
    <title>Grubecki</title>
</head>

<body onload="myLoadHeader(); autoSubmitForm()">
    <div id='myHeader'></div>
    <main>
        <div class="d-flex flex-column flex-shrink-0 menu">
            <div class="panel-1">
                <a href="films.php" class="nav-link">
                    <h1><i class="bi bi-house-door"></i> Home</h1>
                </a>
            </div>
            <div class="panel">
                <h1>
                    <a href="films-all.php"><i class="bi bi-collection-fill"></i> Biblioteka</a>
                    <button class="playlist-add" data-bs-toggle="dropdown" data-toggle="tooltip" title="Utwórz playlistę">
                        <i class="bi bi-plus-lg"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="filmNewPlaylistScript.php">
                                <i class="bi bi-plus-square-fill"></i>
                                Utwórz nową playlistę
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="film-upload.php">
                                <i class="bi bi-plus-square-fill"></i>
                                Dodaj nowy utwór
                            </a>
                        </li>
                    </ul>
                </h1>
                <script>
                    $(function() {
                        $('[data-toggle="tooltip"]').tooltip()
                    })
                </script>
                <ul class="nav nav-pills flex-column mb-auto playlist-list">
                    <li class="playlist">
                        <a href="films-all.php" class="nav-link">
                            <div class="playlist-img">
                                <img src="media/playlist/filmplaylist.svg" alt="playlist">
                            </div>
                            <div class="playlist-text">
                                <span class="playlist-title">Wszystkie filmy</span></br>
                                <span class="playlist-info">Playlista &centerdot; Netflix
                                </span>
                            </div>
                        </a>
                    </li>
                    <?php
                    require('access.php');
                    $dbConn = mysqli_connect($dbHost, $dbUsername, $dbPassword, $dbDatabase);
                    $result = mysqli_query($dbConn, 'SELECT * FROM filmplaylistname ORDER BY id ASC');
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo '<li class="playlist"><a href="film-playlist.php?id=' . $row['id'] . '" class="nav-link">';
                        echo '<div class="playlist-img">';
                        echo '<img src="media/playlist/filmplaylist.svg" alt="playlist">';
                        echo '</div><div class="playlist-text">';
                        echo '<span class="playlist-title">' . $row['name'] . '</span></br>
                        <span class="playlist-info">Playlista &centerdot;
                            ' . $row['creator'] . '
                        </span>';
                        echo '</div></a></li>';
                    }
                    mysqli_close($dbConn);
                    ?>
                </ul>

            </div>
        </div>
        <div class="content-film">
            <div class="container text-center">
                <?php
                if (isset($_SESSION['error_message'])) {
                    echo '<br/><div class="alert alert-danger" role="alert">';
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
            </div>
            <div class="video-player text-left align-items-center">
            <video width="70%" controls autoplay>
                <?php
                $filmID = $_GET['id'];
                require('access.php');
                $dbConn = mysqli_connect($dbHost, $dbUsername, $dbPassword, $dbDatabase);
                $result = mysqli_query($dbConn, "SELECT * FROM film WHERE id = $filmID;");
                while ($row = mysqli_fetch_assoc($result)) {
                    $filmTitle = $row['title'];
                    $fileName = $row['filename'];
                    echo '<source src="media/film/'.$fileName.'" type="video/mp4">';
                }
                mysqli_close($dbConn);
                ?>
                    Your browser does not support HTML video.
                </video>
            </div>
            <h3><?php echo $filmTitle; ?></h3>
        </div>
    </main>

    <!-- Dark/Light Button START -->
    <button class="btn btn-outline-warning bg-dark position-fixed end-0 translate-middle-y" id="btnSwitch" style="z-index: 999; margin-right: 2px; border-color: #ff9000;">
        <i class="bi bi-sun-fill" style="color: #ff9000"></i>
    </button>
    <script type="text/javascript" src="script/buttonTheme.js"></script>
    <!-- Dark/Light Button END -->
    <?php //require_once 'footer.php'; 
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
            xhr.onload = function() {
                if (xhr.status === 200) {}
            };
            xhr.send(formData);
        }
    </script>
    <!-- Get Info END -->
</body>

</html>