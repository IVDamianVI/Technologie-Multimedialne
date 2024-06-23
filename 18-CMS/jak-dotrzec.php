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
$geoLoc = $row['geo_loc'];
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
    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <!-- Title -->
    <title>Grubecki</title>
    <style>
        .leaflet-control-attribution {
            display: none;
        }
    </style>
</head>

<body onload="myLoadHeader(); autoSubmitForm()">
    <div id='myHeader'></div>
    <main>
        <section class="sekcja1">
            <div class="container-fluid container">
                <h1 class="text-center shine" style="color: #ff9000; padding-top: 1em;">
                    Jak do nas dotrzeć?
                </h1>
                <?php if ($userGroup == 'admin') { ?>
                    <div class="container text-center">
                        <?php include ('alerts.php'); ?>
                    </div>
                    <br />
                    <form action="x-controller-edit-geoLoc.php" method="post" onsubmit="return validateGeoLoc()">
                        <input type="hidden" name="id_cms" value="1" />
                        <div class="form-group">
                            <label for="geoLoc">Lokalizacja geograficzna:</label>
                            <input type="text" class="form-control" id="geoLoc" name="geoLoc" value="<?php echo $geoLoc; ?>"
                                required oninput="sanitizeInput(this)">
                        </div>
                        <button type="submit" class="btn btn-warning"><i class="bi bi-floppy-fill"></i> Zapisz
                            zmiany</button>
                    </form>
                    <script>
                        function sanitizeInput(input) {
                            input.value = input.value.replace(/[^0-9,\-\.\s,]/g, '');
                        }
                        function validateGeoLoc() {
                            var geoLocInput = document.getElementById("geoLoc").value;
                            var regex = /^[0-9,\-\.\s]+$/;
                            if (!regex.test(geoLocInput)) {
                                alert("Wprowadź poprawną lokalizację geograficzną. Dopuszczalne znaki to: cyfry, myślniki, kropki, przecinki i spacje.");
                                return false;
                            }
                            return true;
                        }
                    </script>
                <?php } ?>
                <br />
                <div id="map" style="width: 100%; height: 400px;"></div>
                <script>
                    var map = L.map('map').setView([<?php echo $geoLoc; ?>], 13);

                    L.tileLayer('https://{s}.google.com/vt/lyrs=m&hl=pl&x={x}&y={y}&z={z}', {
                        maxZoom: 20,
                        subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
                        attribution: '<a href="https://www.google.pl/maps/">&copy; Google Maps</a>'
                    }).addTo(map);

                    var marker = L.marker([<?php echo $geoLoc; ?>]).addTo(map);
                </script>
            </div>
        </section>
    </main>
    <script type="text/javascript" src="script/buttonTheme.js"></script>
    <!-- Dark/Light Button END -->
    <?php require_once 'footer.php'; ?>
</body>

</html>