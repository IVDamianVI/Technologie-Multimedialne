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

if (isset($_GET['songID'])) {
    require('access.php');
    $dbConn = mysqli_connect($dbHost, $dbUsername, $dbPassword, $dbDatabase);
    $result = mysqli_query($dbConn, 'SELECT * FROM song WHERE id = ' . $_GET['songID']);
    $row = mysqli_fetch_assoc($result);
    $_SESSION['currentSong']  = $row['filename'];
    $_SESSION['currentSongTitle']  = $row['title'];
    $_SESSION['currentSongMusician']  = $row['musician'];
} else if (!isset($_SESSION['currentSong'])) {
    $_SESSION['currentSong']  = 'placeholder.mp3';
    $_SESSION['currentSongTitle']  = 'Tytuł';
    $_SESSION['currentSongMusician']  = 'Autor';
}

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
    <link rel="stylesheet" href="css/styleSpotify.css">
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
                <a href="spotify.php" class="nav-link">
                    <h1><i class="bi bi-house-door"></i> Home</h1>
                </a>
            </div>
            <div class="panel">
                <h1>
                    <a href="spotify-utwory.php"><i class="bi bi-collection-fill"></i> Biblioteka</a>
                    <button class="playlist-add" data-bs-toggle="dropdown" data-toggle="tooltip" title="Utwórz playlistę">
                        <i class="bi bi-plus-lg"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="spotifyNewPlaylistScript.php">
                                <i class="bi bi-plus-square-fill"></i>
                                Utwórz nową playlistę
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="spotify-upload.php">
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
                        <a href="spotify-utwory.php" class="nav-link">
                            <div class="playlist-img">
                                <img src="media/playlist/playlist.svg" alt="playlist">
                            </div>
                            <div class="playlist-text">
                                <span class="playlist-title">Wszystkie utwory</span></br>
                                <span class="playlist-info">Playlista &centerdot; Spotify
                                </span>
                            </div>
                        </a>
                    </li>
                    <?php
                    require('access.php');
                    $dbConn = mysqli_connect($dbHost, $dbUsername, $dbPassword, $dbDatabase);
                    $result = mysqli_query($dbConn, 'SELECT * FROM playlistname ORDER BY id ASC');
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo '<li class="playlist"><a href="spotify-playlist.php?id='.$row['id'].'" class="nav-link">';
                        echo '<div class="playlist-img">';
                        echo '<img src="media/playlist/playlist.svg" alt="playlist">';
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
        <div class="content song-list">
            <div class="d-flex flex-row-reverse">
                <a href="spotify-upload.php">
                    <button class="btn btn-primary ready" type="submit" style="width: auto;">
                        +<i class="bi bi-music-note"></i> Dodaj utwór
                    </button>
                </a>
            </div>
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
            <h3>
                Lista utworów
            </h3>
            <div class="d-flex flex-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="miniature" scope="col"></th>
                            <th scope="col">Tytuł</th>
                            <th scope="col">Wykonawca</th>
                            <th scope="col">Gatunek</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        require('access.php');
                        $dbConn = mysqli_connect($dbHost, $dbUsername, $dbPassword, $dbDatabase);
                        $result = mysqli_query($dbConn, 'SELECT song.id AS ids, song.title AS title, song.musician AS musician, musictype.name AS genre
                        FROM song
                        LEFT JOIN musictype ON song.musictypeid = musictype.id
                        ORDER BY song.title ASC');
                        if (mysqli_num_rows($result) == 0) {
                            echo '<p>Brak utworów w bibliotece...</p>';
                        } else {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo '<tr>';
                                echo '<td class="miniature"><a href="spotify-utwory.php?songID=' . $row['ids'] . '"><img src="media/playlist/playlist.svg" alt="playlist"></a></td>';
                                echo '<td><a href="spotify-utwory.php?songID=' . $row['ids'] . '">' . $row['title'] . '</a></td>';
                                echo '<td><a href="spotify-utwory.php?songID=' . $row['ids'] . '">' . $row['musician'] . '</a></td>';
                                echo '<td><a href="spotify-utwory.php?songID=' . $row['ids'] . '">' . $row['genre'] . '</a></td>';
                                echo '</tr>';
                            }
                        }
                        mysqli_close($dbConn);
                        ?>
                    </tbody>
                </table>
            </div>
            <br /><br />
        </div>
    </main>
    <footer class="fixed-bottom d-flex flex-wrap justify-content-between align-items-center">
        <div class="col-md-4 mb-0 view-track d-flex">
            <div class="playlist-view-img">
                <img src="media/playlist/playlist.svg" alt="track">
            </div>
            <div class="playlist-view-text">
                <p class="playlist-view-title"><?php echo $_SESSION['currentSongTitle']; ?></p>
                <p class="playlist-view-info"><?php echo $_SESSION['currentSongMusician']; ?></p>
            </div>
        </div>
        <div class="col-md-4 d-flex align-items-center justify-content-center controls">
            <!-- <i class="bi bi-shuffle option"></i>
            <i class="bi bi-skip-start-fill skip"></i>
            <i class="bi bi-pause-circle-fill play-pause"></i>
            <i class="bi bi-skip-end-fill skip"></i>
            <i class="bi bi-repeat option"></i>
            <br /> -->
            <!-- <audio controls hidden>
                <source src="https://ia800905.us.archive.org/19/items/FREE_background_music_dhalius/backsound.mp3"
                    type="audio/mp3">
            </audio>
            <div class="audio-player">
                <div class="timeline">
                    <div class="progress-bar">
                        <div class="progress"></div>
                    </div>
                </div>
                <div class="controls">
                    <div class="play-container">
                        <div class="toggle-play play">
                        </div>
                    </div>
                    <div class="time">
                        <div class="current">0:00</div>
                        <div class="length"></div>
                    </div>
                    <div class="volume-container">
                        <div class="volume-button">
                            <div class="volume icono-volumeMedium"></div>
                        </div>
                        <div class="volume-slider">
                            <div class="volume-percentage"></div>
                        </div>
                    </div>
                </div>
            </div> -->

            <audio controls>
                <source src="media/song/<?php echo $_SESSION['currentSong']; ?>" type="audio/mp3">
            </audio>
        </div>
        <script src="script/spotify.js"></script>
        <div class="nav col-md-4 justify-content-end button">
            <i class="bi bi-file-music"></i>
            <i class="bi bi-list"></i>
            <i class="bi bi-pc-display"></i>
            <i class="bi bi-volume-up-fill"></i>
        </div>
    </footer>

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