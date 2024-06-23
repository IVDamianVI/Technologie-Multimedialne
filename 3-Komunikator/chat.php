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
    <link rel="stylesheet" href="css/styleChat.css">
    <link rel="stylesheet" href="css/lightModeColors.css">
    <link rel="stylesheet" href="css/darkModeColors.css">
    <!-- Icon -->
    <link rel="icon" href="media/favicon/favicon-orange.png">
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
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" type="text/javascript" language="javascript"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js" type="text/javascript"
        language="javascript"></script>
    <!-- Scripts Internal -->
    <script src="script/liveChat.js"></script>
    <!-- Title -->
    <title>Grubecki</title>
</head>

<body>
    <?php include('header.php'); ?>
    <main>
        <section class="section">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 offset-md-3 chatHeight">
                        <div class="chat-container chatHeight" id="chatContainer"
                            style='background-size: cover; background-repeat: no-repeat; background-position: center center;'>
                            <div class="chat-box" id="chatBox"
                                style="background-color: rgba(255, 255, 255, 0) !important;">
                                <ul class="list-group list-group-flush" id="data-container">
                                </ul>
                            </div>
                        </div>
                        <form class="message-form" method="POST" action="sendScript.php" enctype="multipart/form-data"
                            autocomplete="off">
                            <?php
                            if (isset($_SESSION['error_message'])) {
                                echo '<div class="alert alert-danger" role="alert">';
                                echo '<i class="bi bi-exclamation-triangle-fill"></i> '.$_SESSION['error_message'];
                                echo '</div>';
                                unset($_SESSION['error_message']);
                            }
                            ?>
                            <input type="hidden" class="hide" name="fromUser" value="<?php echo $_SESSION['user']; ?>"><br>
                            <div class="form-group">
                                <label for="toUser">Odbiorca:</label>
                                <input type="text" id="toUser" name="toUser" maxlength="20" size="20">
                                <!-- <select id="toUser" name="toUser"> -->
                                <?php
                                // require('access.php');
                                // $dbConn = mysqli_connect($dbHost, $dbUsername, $dbPassword, $dbDatabase);
                                // $result = mysqli_query($dbConn, 'SELECT * FROM users ORDER BY username ASC');
                                // while ($row = mysqli_fetch_assoc($result)) {
                                //     echo '<option value="' . $row['username'] . '">' . $row['username'] . '</option>';
                                // }
                                // mysqli_close($dbConn);
                                ?>
                                <!-- </select> -->
                            </div>
                            <div class="form-group">
                                <!--<div class="pc-only">-->
                                    <i class="bi bi-image"></i> |
                                    <i class="bi bi-music-note-beamed"></i> |
                                    <i class="bi bi-film"></i> |
                                    <i class="bi bi-file-earmark"></i>
                                    <input type="file" id="attachment" name="attachment"><br>
                               <!-- </div>
                                <div class="mobile-only">
                                    <i class="bi bi-file-plus-fill"></i>
                                </div>-->
                            </div>
                            <div class="form-group d-flex align-items-center">
                                <textarea id="expandingTextarea" rows="1" name="message" maxlength="9999"></textarea>
                                <button class="btn btn-primary" type="submit" disabled><i
                                        class="bi bi-send-fill"></i></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <!-- Dark/Light Button START -->
    <button class="btn btn-outline-warning bg-dark position-fixed end-0 translate-middle-y" id="btnSwitch"
        style="z-index: 999; margin-right: 2px; border-color: #ff9000;">
        <i class="bi bi-sun-fill" style="color: #ff9000"></i>
    </button>
    <script type="text/javascript" src="script/buttonTheme.js"></script>
    <script type="text/javascript" src="script/buttonSendMessage.js"></script>
    <!-- Dark/Light Button END -->
    <div class="pc-only">
        <?php //require_once 'footer.php'; ?>
    </div>

    <script>
        // Function to scroll the chat container to the bottom
        function scrollToBottom() {
            var chatContainer = document.getElementById('chatContainer');
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }

        // Scroll to the bottom when the page has finished loading
        window.addEventListener('load', function () {
            scrollToBottom();
        });
    </script>

</body>

</html>