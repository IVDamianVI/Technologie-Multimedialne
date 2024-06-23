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
require_once 'access.php';
$dbConnection = new mysqli($dbHost, $dbUsername, $dbPassword, $dbDatabase);
if ($dbConnection->connect_error) {
    die("Connection failed: " . $dbConnection->connect_error);
}
$query = "SELECT * FROM `cms` WHERE `id_cms` = 1";
$result = $dbConnection->query($query);
$row = $result->fetch_assoc();
$oFirmieText = $row['about_company'];
$dbConnection->close();
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
    <script src="https://cdn.tiny.cloud/1/nvwkm0y2yx2qgxmizw1dw69ipyk8o1ersyezk4jf87l5r7wj/tinymce/7/tinymce.min.js"
        referrerpolicy="origin"></script>
    <!-- Title -->
    <title>Grubecki</title>
</head>

<body onload="myLoadHeader(); autoSubmitForm()">
    <div id='myHeader'></div>
    <main>
        <section class="sekcja1">
            <div class="container-fluid container">
                <h1 class="text-center shine" style="color: #ff9000; padding-top: 1em;">
                    O firmie
                </h1>
                <?php if ($userGroup == 'admin') { ?>
                    <div class="container text-center">
                        <?php include ('alerts.php'); ?>
                    </div>
                    <br />
                    <form method="post" action="x-controller-edit-about.php">
                        <input type="hidden" name="id_cms" value="1" />
                        <textarea id="editor" name="content"><?= htmlspecialchars($oFirmieText) ?></textarea>
                        <button type="submit" class="btn btn-warning"><i class="bi bi-floppy-fill"></i> Zapisz
                            zmiany</button>
                    </form>
                    <script>
                        tinymce.init({
                            selector: '#editor',
                            plugins: 'a11ychecker advcode casechange formatpainter linkchecker autolink lists checklist media mediaembed pageembed permanentpen powerpaste table advtable tinymcespellchecker',
                            toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat',
                            setup: function (editor) {
                                editor.on('change', function () {
                                    editor.save();
                                });
                            }
                        });
                    </script>
                <?php } else {
                    echo htmlspecialchars_decode($oFirmieText);
                } ?>
            </div>
        </section>
    </main>
    <script type="text/javascript" src="script/buttonTheme.js"></script>
    <!-- Dark/Light Button END -->
    <?php require_once 'footer.php'; ?>
</body>

</html>