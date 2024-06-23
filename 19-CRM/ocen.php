<?php

declare(strict_types=1);
session_start();
require ('access.php');
if (!isset($_SESSION['loggedin'])) {
    header('Location: logIn.php');
    exit();
}
$_SESSION['page'] = basename($_SERVER['PHP_SELF']);
include ('loginCheckScript.php');
include ('logOutAutoScript.php');

$conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbDatabase);

$question_id = $_GET['id'] ?? null;
$question_text = '';
$answer_text = '';

if ($question_id) {
    $sql = "SELECT pytanie, odpowiedz FROM zapytania WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $question_id);
        $stmt->execute();
        $stmt->bind_result($question_text, $answer_text);
        $stmt->fetch();
        $stmt->close();
    }
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
        .form-group,
        input,
        select {
            color: #fff !important;
        }

        table,
        tr,
        td {
            border: 3px solid #ff9000 !important;
            border-radius: 10px !important;
        }

        th {
            background-color: #ff9000 !important;
            color: #000 !important;
            border: 3px solid #000 !important;
        }
    </style>
</head>

<body onload="myLoadHeader()">
    <div id='myHeader'></div>
    <main>
        <section class="sekcja1">
            <div class="container">
                <h2 class="text-center shine" style="color: #ff9000; padding-top: 1em;">
                    Oceń odpowiedź na pytanie
                </h2>
                <form method="POST" action="ocenScript.php">
                    <input type="hidden" name="question_id" value="<?php echo htmlspecialchars($question_id); ?>">
                    <div class="form-group">
                        <label for="question_text" style="color: #ff9000;">Treść pytania</label>
                        <textarea class="form-control" id="question_text" rows="5"
                            readonly><?php echo htmlspecialchars($question_text); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="answer_text" style="color: #ff9000;">Treść odpowiedzi</label>
                        <textarea class="form-control" id="answer_text" rows="5"
                            readonly><?php echo htmlspecialchars($answer_text); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="rating" style="color: #ff9000;">Ocena (1-5)</label>
                        <select class="form-select" name="rating" id="rating" required>
                            <option value="" selected disabled hidden>Wybierz ocenę</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-warning btn-block"><i class="bi bi-send-fill"></i>
                            Wyślij ocenę</button>
                    </div>
                </form>
            </div>
        </section>
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
</body>

</html>