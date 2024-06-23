<?php

declare(strict_types=1);
session_start();
$_SESSION['page'] = basename($_SERVER['PHP_SELF']);
require_once 'access.php';
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
    <link rel="stylesheet" href="css/face.css">
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
    <style>
        #bot-avatar {
            position: absolute;
            top: 130px;
            left: 15%;
            margin: 0 auto;
            max-width: 50px;
            height: 100px;
            justify-content: center;
            transform: scale(0.5);
        }
    </style>
</head>

<body onload="myLoadHeader(); autoSubmitForm()">
    <div id='myHeader'></div>
    <main>
        <br />
        <section class="section">
            <div class="container">
                <h1 class="text-center shine" style="color: #ff9000; padding-top: 1em;">
                    Chatbot
                </h1>
                <div id="bot-avatar">
                    <?php echo file_get_contents("media/svg/avatar.svg"); ?>
                </div>
                <div class="row">
                    <div class="col-md-6 offset-md-3 chatHeight">
                        <div class="chat-container chatHeight" id="chatContainer"
                            style='background-size: cover; background-repeat: no-repeat; background-position: center center;'>
                            <div class="chat-box" id="chatBox"
                                style="background-color: rgba(255, 255, 255, 0) !important;">
                                <ul class="list-group list-group-flush" id="data-container">
                                    <?php
                                    $ip = $_SERVER['REMOTE_ADDR'];
                                    require_once 'access.php';
                                    $dbConnection = new mysqli($dbHost, $dbUsername, $dbPassword, $dbDatabase);
                                    if ($dbConnection->connect_error) {
                                        die("Connection failed: " . $dbConnection->connect_error);
                                    }
                                    $query = "SELECT * FROM `chatbot` WHERE question_ip = '$ip' ORDER BY `id` ASC;";
                                    $result = $dbConnection->query($query);
                                    while ($row = $result->fetch_assoc()) {
                                        echo '<div class="list-group-item sent-message"><p class="message-text">' . $row['question'] . '</p></div>';
                                        echo '<div class="list-group-item received-message"><p class="message-text">' . $row['answer'] . '</p></div>';
                                    }
                                    $dbConnection->close();
                                    ?>
                                </ul>
                            </div>
                        </div>
                        <script>
                            window.addEventListener('load', function () {
                                var chatContainer = document.querySelector('.chat-container');
                                chatContainer.scrollTop = chatContainer.scrollHeight;
                            });
                        </script>
                        <form class="message-form" method="POST" action="x-controller-chatbot.php"
                            enctype="multipart/form-data" autocomplete="off">
                            <input type="hidden" name="id_cms" value="1" />
                            <div class="form-group d-flex align-items-center">
                                <textarea id="expandingTextarea" rows="1" name="userQuery" maxlength="9999"
                                    placeholder="Zadaj pytanie..." required></textarea>
                                <button class="btn btn-primary ready" type="submit"><i
                                        class="bi bi-send-fill"></i></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <script type="text/javascript" src="script/buttonTheme.js"></script>
    <!-- Dark/Light Button END -->
    <?php require_once 'footer.php'; ?>
</body>

</html>