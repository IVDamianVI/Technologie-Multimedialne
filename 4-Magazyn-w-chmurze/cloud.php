<?php

declare(strict_types=1);
session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: logIn.php');
    exit();
}else {
    // header('Location: logIn.php');
    // exit();
}
$_SESSION['page'] = basename($_SERVER['PHP_SELF']);
require('access.php');
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
    <link rel="stylesheet" href="css/styleCloud.css">
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

    <script src="https://kit.fontawesome.com/bc52b20c9d.js" crossorigin="anonymous"></script>
    <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.js" type="text/javascript" language="javascript"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js" type="text/javascript" language="javascript"></script>
    <!-- Bootstrap Tabel -->
    <link href="https://unpkg.com/bootstrap-table@1.22.1/dist/bootstrap-table.min.css" rel="stylesheet">
    <script src="https://unpkg.com/tableexport.jquery.plugin/tableExport.min.js"></script>
    <script src="https://unpkg.com/bootstrap-table@1.22.1/dist/bootstrap-table.min.js"></script>
    <script src="https://unpkg.com/bootstrap-table@1.22.1/dist/bootstrap-table-locale-all.min.js"></script>
    <script src="https://unpkg.com/bootstrap-table@1.22.1/dist/extensions/export/bootstrap-table-export.min.js"></script>
    <!-- Bootstrap Switch -->
    <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>
    <!-- Title -->
    <title>Grubecki</title>
</head>

<body onload="myLoadHeader(); autoSubmitForm()">
    <div id='myHeader'></div>
    <main>
        <section class="sekcja1">
            <div class="container-fluid">
                <div class="container">
                    <div id="drop-area" ondrop="dropHandler(event);" ondragover="dragOverHandler(event);">
                        <form method="POST" action="cloudUploadScript.php" enctype="multipart/form-data" autocomplete="off">
                            <div class="pc-only">
                                <h3>Przeciągnij i upuść pliki tutaj</h3>
                                <span>lub</span>
                            </div>
                            <div class="form-group">
                                <input type="file" id="file-input" name="files[]" multiple onchange="handleFiles(this.files)">
                                <button class="btn btn-primary" type="submit" disabled>
                                    <i class="bi bi-cloud-arrow-up-fill"></i>
                                </button>
                            </div>
                        </form>
                        <div id="scroll-window">
                            <ul id="file-list"></ul>
                        </div>
                    </div>
                </div>
                <script type="text/javascript" src="script/dragAndDrop.js"></script>
                <div class="container text-center">
                    <?php
                    if (isset($_SESSION['error_message'])) {
                        echo '<div class="alert alert-danger" role="alert">';
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
                    <div class="modal fade" id="createDirectoryModal" tabindex="-1" role="dialog" aria-labelledby="createDirectoryModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="createDirectoryModalLabel">Utwórz nowy folder</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="createDirectoryForm">
                                        <div class="mb-3">
                                            <label for="directoryName" class="form-label">Nazwa folderu:</label>
                                            <input type="text" class="form-control" id="directoryName" name="directoryName" required>
                                        </div>
                                        <button type="button" class="btn btn-primary" onclick="createDirectory()">Utwórz</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <br />
                <h1 class="text-center shine" style="color: #ff9000;">
                    My Cloud <i class="bi bi-cloud-fill"></i>
                </h1>
                <div class="container">
                    <input type="checkbox" class="switch" id="toggleSwitch" data-toggle="toggle" data-on="<i class='bi bi-grid'></i>" data-onstyle="toggle-list" data-off="<i class='bi bi-list-task'></i>" data-offstyle="toggle-item">
                    <button class="btn btn-primary ready createDir" id="createDirectoryBtn" data-bs-toggle="modal" data-bs-target="#createDirectoryModal">
                        <i class="bi bi-folder-plus"></i>
                    </button>
                </div>
                <script>
                    $(document).ready(function() {
                        $('#toggleSwitch').change(function() {
                            if ($(this).prop('checked')) {
                                // If checked, show cloud-item and hide cloud-list
                                $('.cloud-item').css('display', 'block');
                                $('.cloud-list').css('display', 'none');
                            } else {
                                // If unchecked, hide cloud-item and show cloud-list
                                $('.cloud-item').css('display', 'none');
                                $('.cloud-list').css('display', 'block');
                            }
                        });
                    });
                </script>
            </div>
            <?php include('cloudDir.php'); ?>
        </section>
    </main>
    <script type="text/javascript" src="script/cloud.js"></script>
    <script type="text/javascript" src="script/fileIconCloud.js"></script>
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