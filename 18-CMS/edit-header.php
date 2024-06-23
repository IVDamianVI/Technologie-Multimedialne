<?php

declare(strict_types=1);
session_start();
$_SESSION['page'] = basename($_SERVER['PHP_SELF']);
if (isset($_SESSION['loggedin'])) {
    $user = $_SESSION['user'];
    $userGroup = $_SESSION['userGroup'];
} else {
    $userGroup = 'guest';
    $user = 'Gość';
}

if ($userGroup != 'admin') {
    header('Location: /z18/');
    exit();
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
    <!-- Title -->
    <title>Grubecki</title>
    <style>
        .row {
            margin-top: .5em;
        }
    </style>
</head>

<body onload="myLoadHeader(); autoSubmitForm()">
    <div id='myHeader'></div>
    <main>
        <section class="sekcja1">
            <div class="container-fluid">
                <h1 class="text-center shine" style="color: #ff9000; padding-top: 6em;">
                    Edytuj menu
                </h1>
                <div class="container text-center">
                    <?php include ('alerts.php'); ?>
                </div>
                <div class="container text-center">
                    <form action="x-controller-edit-header.php" method="post">
                        <input type="hidden" name="id_cms" value="1" />
                        <div id="inputContainer">
                            <div class="row gx-1">
                                <div class="col">
                                    <b>Nazwa w menu</b>
                                </div>
                                <div class="col">
                                    <b>URL</b>
                                </div>
                            </div>
                            <?php
                            require_once 'access.php';
                            $dbConnection = new mysqli($dbHost, $dbUsername, $dbPassword, $dbDatabase);
                            if ($dbConnection->connect_error) {
                                die("Connection failed: " . $dbConnection->connect_error);
                            }
                            $query = "SELECT * FROM `cms_headers` WHERE `id_cms` = 1";
                            $result = $dbConnection->query($query);
                            while ($row = $result->fetch_assoc()) {
                                echo '<div class="row gx-1">';
                                echo '<div class="col">';
                                echo '<input type="text" class="form-control" name="name[]" placeholder="Wprowadź nazwę" value="' . htmlspecialchars($row['name']) . '" autocomplete="off">';
                                echo '</div>';
                                echo '<div class="col">';
                                echo '<input type="text" class="form-control" name="url[]" placeholder="Wprowadź URL" value="' . htmlspecialchars($row['url']) . '" autocomplete="off">';
                                echo '</div>';
                                echo '</div>';
                            }
                            $dbConnection->close();
                            ?>
                            <div class="row gx-1">
                                <div class="col">
                                    <input type="text" class="form-control" name="name[]" placeholder="Wprowadź nazwę"
                                        autocomplete="off">
                                </div>
                                <div class="col">
                                    <input type="text" class="form-control" name="url[]" placeholder="Wprowadź URL"
                                        autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <br />
                        <button type="submit" class="btn btn-warning"><i class="bi bi-floppy-fill"></i> Zapisz
                            zmiany</button>
                    </form>
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const inputContainer = document.getElementById('inputContainer');
                            function addInputFields() {
                                const lastInputs = inputContainer.querySelectorAll('.row:last-child input');
                                if (lastInputs.length !== 0 && lastInputs[0].value.trim() !== '' && lastInputs[1].value.trim() !== '') {
                                    const newRow = document.createElement('div');
                                    newRow.className = 'row gx-1';
                                    newRow.innerHTML = `
                                    <div class="col">
                                        <input type="text" class="form-control" name="name[]" placeholder="Wprowadź nazwę" autocomplete="off">
                                    </div>
                                    <div class="col">
                                        <input type="text" class="form-control" name="url[]" placeholder="Wprowadź URL" autocomplete="off">
                                    </div>`;
                                    inputContainer.appendChild(newRow);
                                }
                            }
                            inputContainer.addEventListener('input', function (event) {
                                if (event.target.name === 'url[]') {
                                    event.target.value = event.target.value
                                        .replace(/ą/g, 'a').replace(/ć/g, 'c').replace(/ę/g, 'e')
                                        .replace(/ł/g, 'l').replace(/ń/g, 'n').replace(/ó/g, 'o')
                                        .replace(/ś/g, 's').replace(/ż/g, 'z').replace(/ź/g, 'z')
                                        .replace(/[^a-z0-9\-]/gi, '')
                                        .replace(/\s+/g, '-');
                                    addInputFields();
                                }
                            });
                        });
                    </script>
                </div>
            </div>
        </section>
    </main>
    <script type="text/javascript" src="script/buttonTheme.js"></script>
    <!-- Dark/Light Button END -->
    <?php require_once 'footer.php'; ?>
</body>

</html>