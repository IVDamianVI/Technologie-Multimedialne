<?php

declare(strict_types=1);
session_start();
if (isset($_SESSION['loggedin'])) {
    $userGroup = $_SESSION['userGroup'];
}else {
    $userGroup = 'guest';
}
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
        <div class="content padding" style="<?php if (isset($_GET['temat'])) {
            echo 'display: none;';
        } ?>">
            <h1 class="shine" style="color: #ff9000;">Forum</h1>
            <br />
            <h2>Nowe tematy:</h2>
            <hr />
            <?php
            require('access.php');
            $dbConn = mysqli_connect($dbHost, $dbUsername, $dbPassword, $dbDatabase);
            $result = mysqli_query($dbConn, "SELECT * FROM topic ORDER BY id DESC");
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<div class="topic">';
                echo '<a href="forum.php?temat=' . $row['id'] . '">';
                echo '<h3 class="topic-title">' . $row['title'] . '</h3>';
                echo '</a>';
                echo '<p class="topic-info">Dodane przez <a href="profile.php?user=' . $row['creator'] . '"><span class="topic-username">' . $row['creator'] . '</span></a> <span class="topic-date">' . $row['datetime'] . '</span></p>';
                echo '</div>';
                // mysqli_close($dbConn);
            }
            ?>
        </div>
        <div class="content scrollable" style="<?php if (!isset($_GET['temat'])) {
            echo 'display: none;';
        } ?>">
            <?php
            $topicTitle = 'null';
            $topicText = 'null';
            $topicCreator = 'null';
            $topicDatetime = 'null';
            if (isset($_GET['temat'])) {
                $topicID = $_GET['temat'];
                require('access.php');
                // $username = $_SESSION['user'];
                $dbConn = mysqli_connect($dbHost, $dbUsername, $dbPassword, $dbDatabase);
                $result = mysqli_query($dbConn, "SELECT * FROM topic WHERE id = $topicID");
                while ($row = mysqli_fetch_assoc($result)) {
                    $topicTitle = $row['title'];
                    $topicText = $row['text'];
                    $topicCreator = $row['creator'];
                    $topicDatetime = $row['datetime'];
                }
                mysqli_close($dbConn);
            }
            ?>
            <?php if (isset($_GET['temat'])): ?>
                <div class="topic-details">
                    <h2 class="topic-title">
                        <?php echo htmlspecialchars($topicTitle); ?>
                    </h2>
                    <p class="topic-description">
                        <?php echo $topicText; ?>
                    </p>
                    <p class="topic-meta">Utworzone <span class="topic-date">
                            <?php echo htmlspecialchars($topicDatetime); ?>
                        </span> przez <a href="profile.php?user=<?php echo $topicCreator; ?>"><span class="topic-username">
                                <?php echo htmlspecialchars($topicCreator); ?>
                            </span></a></p>
                </div>
            <?php endif; ?>
            <script>
                function confirmDeletion() {
                    return confirm("Czy na pewno chcesz usunąć komentarz?");
                }
            </script>
            <!-- Comments Section -->
            <div class="comments-section">
                <h3>Komentarze:</h3>
                <?php
                if (isset($_GET['temat'])) {
                    $topic = intval($_GET['temat']);
                    require('access.php');
                    $dbConn = mysqli_connect($dbHost, $dbUsername, $dbPassword, $dbDatabase);

                    $loggedInUser = $_SESSION['user'] ?? '';
                    $sql = "SELECT c.* FROM comments c 
                        LEFT JOIN blacklist b ON c.creator = b.username AND c.creator <> ?
                        WHERE c.topic = ? AND (b.username IS NULL OR c.creator = ?)
                        ORDER BY c.id ASC";

                    $commentsFound = false;
                
                    if ($stmt = mysqli_prepare($dbConn, $sql)) {
                        mysqli_stmt_bind_param($stmt, 'sis', $loggedInUser, $topic, $loggedInUser);
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);

                        while ($row = mysqli_fetch_assoc($result)) {
                            $commentsFound = true;
                            echo '<div class="comment">';
                            echo '<p class="comment-text">' . $row['text'] . '</p>';
                            echo '<div class="comment-meta"><span class="comment-date"><i class="bi bi-clock"></i> ' . htmlspecialchars($row['datetime']) . '</span>  <a href="profile.php?user=' . $row['creator'] . '"><span class="comment-author"><i class="bi bi-person-fill"></i> ' . htmlspecialchars($row['creator']) . '</span></a>';

                            if ($row['creator'] === $loggedInUser || $userGroup === "admin") {
                                echo ' <a href="forum-delete-comment.php?id=' . $row['id'] . '&temat=' . $topic . '" class="delete-comment-button" onclick="return confirmDeletion();"><i class="bi bi-trash"></i></a>';
                            }

                            echo '</div></div>';
                        }

                        mysqli_stmt_close($stmt);
                    }

                    mysqli_close($dbConn);

                    if (!$commentsFound) {
                        echo '<p>Brak komentarzy dla tego tematu.</p>';
                    }
                }
                ?>
            </div>

            <form class="message-form" method="POST" action="forum-send-script.php" enctype="multipart/form-data"
                autocomplete="off">
                <?php
                if (isset($_SESSION['error_message'])) {
                    echo '<div class="alert alert-danger" role="alert">';
                    echo '<i class="bi bi-exclamation-triangle-fill"></i> ' . $_SESSION['error_message'];
                    echo '</div>';
                    unset($_SESSION['error_message']);
                }
                ?>
                <?php if (isset($_GET['temat']) && isset($_SESSION['user'])): ?>
                    <input type="hidden" class="hide" name="creator" value="<?php echo $_SESSION['user']; ?>">
                    <input type="hidden" class="hide" name="topic" value="<?php echo $_GET['temat']; ?>">
                    <br />
                    <div class="form-group d-flex align-items-center">
                        <textarea id="expandingTextarea" rows="2" name="message" maxlength="9999"
                            placeholder="Napisz komentarz. Pamiętaj, że wszelkie wulgarne słowa są surowo zabornione..."
                            required></textarea>
                        <button class="btn btn-primary ready" type="submit"><i class="bi bi-send-fill"></i></button>
                    </div>
                <?php endif; ?>
            </form>

            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    var form = document.querySelector('.message-form');
                    form.addEventListener('submit', function (event) {
                        var textarea = document.getElementById('expandingTextarea');
                        textarea.value = textarea.value.replace(/\n/g, '<br/>');
                    });
                });
            </script>
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