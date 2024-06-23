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

$savedTitle = $_SESSION['form_data']['title'] ?? '';
$savedText = $_SESSION['form_data']['text'] ?? '';
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
    <link rel="stylesheet" href="css/styleForum.css">
    <link rel="stylesheet" href="css/styleChat.css">
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
    <main style="position: relative; top: 40px;">
        <div class="content">
            <div class="container">
                <br />
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
                <form method="POST" action="forum-nowy-temat-script.php" enctype="multipart/form-data"
                    autocomplete="off">
                    <div class="form-group">
                        <h3><label for="title">Temat</label></h3>
                        <?php echo '<input type="text" class="form-control" id="title" name="title" placeholder="Tytuł postu" required value="' . htmlspecialchars($savedTitle) . '">'; ?>
                        <!-- <input type="text" class="form-control" id="title" name="title" placeholder="Tytuł postu" required> -->
                    </div>
                    <br />
                    <div class="form-group">
                        <h3><label for="lyrics">Opis</label></h3>
                        <?php echo '<textarea class="form-control" id="text" name="text" placeholder="Opisz temat" rows="18" style="max-height: 500px" required>' . htmlspecialchars($savedText) . '</textarea>'; ?>
                        <!-- <textarea class="form-control" id="text" name="text" placeholder="Opisz temat" rows="18" style="max-height: 500px" required></textarea> -->
                    </div>
                    <br />
                    <div class="form-group d-flex flex-row-reverse">
                        <button class="btn btn-primary ready" type="submit" style="width: auto;">
                            Opublikuj temat
                        </button>
                    </div>
                </form>
                <?php unset($_SESSION['form_data']); ?>
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        // Sprawdź, czy istnieje textarea o id 'text'
                        var textarea = document.getElementById('text');
                        if (textarea) {
                            // Zamień <br/> na nowe linie
                            textarea.value = textarea.value.replace(/<br\s*\/?>/gi, '\n');
                        }

                        var form = document.querySelector('form');
                        form.addEventListener('submit', function (event) {
                            // Zamień nowe linie na <br/> przed wysłaniem formularza
                            textarea.value = textarea.value.replace(/\n/g, '<br/>');
                        });
                    });
                </script>
            </div>
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