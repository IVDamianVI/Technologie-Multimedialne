<?php

declare(strict_types=1);
session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: logIn.php');
    exit();
}
$_SESSION['page'] = basename($_SERVER['PHP_SELF']);
include ('loginCheckScript.php');
include ('logOutAutoScript.php');
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

        .card-header {
            background-color: #ff9000 !important;
            color: #000 !important;
            font-weight: bold;
        }

        hr {
            border: 3px solid #ff9000 !important;
            border-radius: 10px !important;
        }

        .category-icon {
            font-size: 2em;
            color: #ff9000;
            text-align: right;
            width: 50px !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        div.form-group.col-2 {
            padding: 0 !important;
            margin: 0 !important;
        }

        .form-group {
            margin-bottom: 10px !important;
        }
    </style>
</head>

<body onload="myLoadHeader(); autoSubmitForm()">
    <div id='myHeader'></div>
    <main>
        <section class="sekcja1">
            <div class="container-fluid">
                <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true): ?>
                    <h1 class="text-center shine" style="color: #ff9000; padding-top: 1em;">
                        Witaj, <?php echo $_SESSION['user']; ?>!
                    </h1>
                <?php endif; ?>
                <?php if (isset($_SESSION['userGroup']) && $_SESSION['userGroup'] == 'user'): ?>
                    <div class="container">
                        <h2 class="text-center shine" style="color: #ff9000; padding-top: 1em;">
                            Prześlij zapytanie
                        </h2>
                        <hr />
                        <form method="POST" action="addQuestionScript.php">
                            <input type="hidden" name="user_id" value="<?php echo $_SESSION['userid']; ?>">
                            <div class="row">
                                <div class="col-1 category-icon"><i class="bi bi-bookmark-fill"></i></div>
                                <div class="form-group col-2">
                                    <select class="form-select" name="category" id="category" required>
                                        <option value="" selected disabled hidden>Wybierz zagadnienie</option>
                                        <option value="Sprzedaż nowych usług">Sprzedaż nowych usług</option>
                                        <option value="Pomoc techniczna">Pomoc techniczna</option>
                                        <option value="Rezygnacja z usługi">Rezygnacja z usługi</option>
                                        <option value="Inne">Inne</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <textarea class="form-control" name="question" id="question" rows="3"
                                    placeholder="Wprowadź treść pytania" required></textarea>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-warning btn-block"><i class="bi bi-send-fill"></i>
                                    Wyślij</button>
                            </div>
                        </form>
                        <h2 class="text-center shine" style="color: #ff9000;">
                            Twoje zapytania
                        </h2>
                        <hr />
                        <table id="table" class="table table-striped table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col"><i class="bi bi-bookmark-fill"></i> Kategoria</th>
                                    <th scope="col"><i class="bi bi-question-square-fill"></i> Pytanie</th>
                                    <th scope="col"><i class="bi bi-patch-question-fill"></i> Odpowiedź</th>
                                    <th scope="col"><i class="bi bi-star-fill"></i> Ocena</th>
                                    <th scope="col"><i class="bi bi-calendar2-plus-fill"></i> Data wysłania</th>
                                    <th scope="col"><i class="bi bi-calendar2-check-fill"></i> Data odpowiedzi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbDatabase);
                                $sql = "SELECT * FROM zapytania WHERE user_id = ? ORDER BY created DESC";
                                if ($stmt = $conn->prepare($sql)) {
                                    $stmt->bind_param("i", $_SESSION['userid']);
                                    $stmt->execute();
                                    $result = $stmt->get_result();
                                    while ($row = $result->fetch_assoc()) {
                                        $ocena = $row['ocena'];
                                        $ocenaIkona = "";
                                        $id = $row['id'];
                                        for ($i = 1; $i <= 5; $i++) {
                                            if ($i <= $ocena) {
                                                $ocenaIkona = $ocenaIkona . "<i class='bi bi-star-fill'></i>";
                                            } else {
                                                $ocenaIkona = $ocenaIkona . "<i class='bi bi-star'></i>";
                                            }
                                        }

                                        if (empty($ocena) && !empty($row['odpowiedz'])) {
                                            $ocenaIkona = "<a href='ocen.php?id=$id'><button class='btn btn-warning btn-block'><i class='bi bi-star-fill'></i> Oceń</button></a>";
                                        } else if (empty($ocena) && empty($row['odpowiedz'])) {
                                            $ocenaIkona = "-";
                                        }
                                        $odpowiedz = $row['odpowiedz'];
                                        if (empty($odpowiedz)) {
                                            $odpowiedz = "Brak odpowiedzi, proszę czekać.";
                                        }
                                        $dataOdp = $row['data_odp'];
                                        if (empty($dataOdp)) {
                                            $dataOdp = "-";
                                        }
                                        echo "<tr>";
                                        echo "<td class='col-1'>" . $row['kategoria'] . "</td>";
                                        echo "<td>" . $row['pytanie'] . "</td>";
                                        echo "<td>" . $odpowiedz . "</td>";
                                        echo "<td class='col-1'>" . $ocenaIkona . "</td>";
                                        echo "<td class='col-1'>" . $row['created'] . "</td>";
                                        echo "<td class='col-1'>" . $dataOdp . "</td>";
                                        echo "</tr>";
                                    }
                                    if ($result->num_rows == 0) {
                                        echo "<tr><td colspan='6'>Brak zapytań...</td></tr>";
                                    }
                                    if ($result->num_rows > 1) {
                                        $isMobile = true;
                                    }
                                    $stmt->close();
                                }
                                $conn->close();
                                ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
                <?php if (isset($_SESSION['userGroup']) && $_SESSION['userGroup'] == 'pracownik'): ?>
                    <div class="container">
                        <h2 class="text-center shine" style="color: #ff9000; padding-top: 1em;">
                            Twoje Statystki
                        </h2>
                        <hr />
                        <div class="row">
                            <div class="col-2">
                                <div class="card text-white card-avg-ocena mb-3">
                                    <div class="card-header"><i class="bi bi-star-fill"></i> Średnia ocena</div>
                                    <div class="card-body">
                                        <h5 class="card-title text-center">
                                            <?php
                                            $conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbDatabase);
                                            $sql = "SELECT AVG(ocena) AS total FROM zapytania WHERE ocena IS NOT NULL AND pracownik_id = ?";
                                            if ($stmt = $conn->prepare($sql)) {
                                                $stmt->bind_param("i", $_SESSION['userid']);
                                                $stmt->execute();
                                                $result = $stmt->get_result();
                                                $row = $result->fetch_assoc();
                                                $ocena = $row['total'];
                                                if (empty($ocena)) {
                                                    $ocena = "-";
                                                }
                                                $ocena = number_format((float) $ocena, 2, '.', '');
                                                echo $ocena;
                                                $stmt->close();
                                            }
                                            $conn->close();
                                            ?>
                                        </h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="card text-white card-odpowiedzi mb-3">
                                    <div class="card-header"><i class="bi bi-patch-question-fill"></i> Odpowiedzi</div>
                                    <div class="card-body">
                                        <h5 class="card-title text-center">
                                            <?php
                                            $conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbDatabase);
                                            $sql = "SELECT COUNT(*) AS total FROM zapytania WHERE pracownik_id = ?";
                                            if ($stmt = $conn->prepare($sql)) {
                                                $stmt->bind_param("i", $_SESSION['userid']);
                                                $stmt->execute();
                                                $result = $stmt->get_result();
                                                $row = $result->fetch_assoc();
                                                echo $row['total'];
                                                $stmt->close();
                                            }
                                            $conn->close();
                                            ?>
                                        </h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br />
                        <h2 class="text-center shine" style="color: #ff9000;">
                            Nowe zapytania klientów
                        </h2>
                        <hr />
                        <table id="table" class="table table-striped table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col"><i class="bi bi-person-fill"></i> Klient</th>
                                    <th scope="col"><i class="bi bi-bookmark-fill"></i> Kategoria</th>
                                    <th scope="col"><i class="bi bi-question-square-fill"></i> Pytanie</th>
                                    <th scope="col"><i class="bi bi-calendar2-plus-fill"></i> Data zapytania</th>
                                    <th scope="col"><i class="bi bi-patch-question-fill"></i> Odpowiedź</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbDatabase);
                                $sql = "SELECT zapytania.id, zapytania.ocena, zapytania.created, zapytania.pytanie, zapytania.odpowiedz, zapytania.data_odp, zapytania.kategoria, users.username  
                                FROM zapytania JOIN users ON users.id = zapytania.user_id WHERE zapytania.odpowiedz IS NULL ORDER BY created DESC";
                                if ($stmt = $conn->prepare($sql)) {
                                    $stmt->execute();
                                    $result = $stmt->get_result();
                                    while ($row = $result->fetch_assoc()) {
                                        $ocena = $row['ocena'];
                                        $ocenaIkona = "";
                                        for ($i = 1; $i <= 5; $i++) {
                                            if ($i <= $ocena) {
                                                $ocenaIkona = $ocenaIkona . "<i class='bi bi-star-fill'></i>";
                                            } else {
                                                $ocenaIkona = $ocenaIkona . "<i class='bi bi-star'></i>";
                                            }
                                        }
                                        $id = $row['id'];
                                        $odpowiedz = $row['odpowiedz'];
                                        if (empty($odpowiedz)) {
                                            $odpowiedz = "<a href='odpowiedz.php?id=$id'><button class='btn btn-warning btn-block'>
                                                <i class='bi bi-caret-right-fill'></i> Odpowiedz</button></a>";
                                        }
                                        $dataOdp = $row['data_odp'];
                                        if (empty($dataOdp)) {
                                            $dataOdp = "-";
                                        }
                                        echo "<tr>";
                                        echo "<td>" . $row['username'] . "</td>";
                                        echo "<td class='col-2'>" . $row['kategoria'] . "</td>";
                                        echo "<td>" . $row['pytanie'] . "</td>";
                                        echo "<td class='col-2'>" . $row['created'] . "</td>";
                                        echo "<td class='text-center col-2'>" . $odpowiedz . "</td>";
                                        echo "</tr>";
                                    }
                                    if ($result->num_rows > 2) {
                                        $isMobile = true;
                                    }
                                    $stmt->close();
                                }
                                $conn->close();
                                ?>
                            </tbody>
                        </table>
                        <br />
                        <h2 class="text-center shine" style="color: #ff9000;">
                            Twoje odpowiedzi
                        </h2>
                        <hr />
                        <table id="table" class="table table-striped table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col"><i class="bi bi-person-fill"></i> Klient</th>
                                    <th scope="col"><i class="bi bi-bookmark-fill"></i> Kategoria</th>
                                    <th scope="col"><i class="bi bi-question-square-fill"></i> Pytanie</th>
                                    <th scope="col"><i class="bi bi-patch-question-fill"></i> Odpowiedź</th>
                                    <th scope="col"><i class="bi bi-star-fill"></i> Ocena</th>
                                    <th scope="col"><i class="bi bi-calendar2-check-fill"></i> Data odpowiedzi</th>
                                    <th scope="col"><i class="bi bi-calendar2-plus-fill"></i> Data zapytania</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbDatabase);
                                $sql = "SELECT zapytania.id, zapytania.ocena, zapytania.created, zapytania.pytanie, zapytania.odpowiedz, zapytania.data_odp, zapytania.kategoria, users.username  
                                FROM zapytania JOIN users ON users.id = zapytania.user_id WHERE pracownik_id = ? ORDER BY created DESC";
                                if ($stmt = $conn->prepare($sql)) {
                                    $stmt->bind_param("i", $_SESSION['userid']);
                                    $stmt->execute();
                                    $result = $stmt->get_result();
                                    while ($row = $result->fetch_assoc()) {
                                        $ocena = $row['ocena'];
                                        $ocenaIkona = "";
                                        for ($i = 1; $i <= 5; $i++) {
                                            if ($i <= $ocena) {
                                                $ocenaIkona = $ocenaIkona . "<i class='bi bi-star-fill'></i>";
                                            } else {
                                                $ocenaIkona = $ocenaIkona . "<i class='bi bi-star'></i>";
                                            }
                                        }
                                        $id = $row['id'];
                                        $odpowiedz = $row['odpowiedz'];
                                        if (empty($odpowiedz)) {
                                            $odpowiedz = "<a href='odpowiedz.php?id=$id'><button class='btn btn-warning btn-block'>
                                    Odpowiedz teraz</button></a>";
                                        }
                                        $dataOdp = $row['data_odp'];
                                        if (empty($dataOdp)) {
                                            $dataOdp = "-";
                                        }
                                        echo "<tr>";
                                        echo "<td>" . $row['username'] . "</td>";
                                        echo "<td class='col-2'>" . $row['kategoria'] . "</td>";
                                        echo "<td>" . $row['pytanie'] . "</td>";
                                        echo "<td>" . $odpowiedz . "</td>";
                                        echo "<td class='col-1'>" . $ocenaIkona . "</td>";
                                        echo "<td class='col-2'>" . $dataOdp . "</td>";
                                        echo "<td class='col-2'>" . $row['created'] . "</td>";
                                        echo "</tr>";
                                    }
                                    $isMobile = true;
                                    $stmt->close();
                                }
                                $conn->close();
                                ?>
                            </tbody>
                        </table>
                        <br />
                    </div>
                <?php endif; ?>
                <?php if (isset($_SESSION['userGroup']) && $_SESSION['userGroup'] == 'admin'): ?>
                    <?php $isMobile = true; ?>
                    <div class="container">
                        <a href="register.php"><button type="submit" class="btn btn-warning btn-block"><i
                                    class="bi bi-person-fill-add"></i> Stwórz
                                nowe konto</button></a>
                        <br />
                        <h2 class="text-center shine" style="color: #ff9000; padding-top: 1em;">
                            Statystki portalu
                        </h2>
                        <hr />
                        <div class="row">
                            <div class="col-2">
                                <div class="card text-white card-klienci mb-3">
                                    <div class="card-header"><i class="bi bi-person-fill"></i> Klienci</div>
                                    <div class="card-body">
                                        <h5 class="card-title text-center">
                                            <?php
                                            $conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbDatabase);
                                            $sql = "SELECT COUNT(*) AS total FROM users WHERE userGroup = 'user'";
                                            if ($stmt = $conn->prepare($sql)) {
                                                $stmt->execute();
                                                $result = $stmt->get_result();
                                                $row = $result->fetch_assoc();
                                                echo $row['total'];
                                                $stmt->close();
                                            }
                                            $conn->close();
                                            ?>
                                        </h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="card text-white card-pracownicy mb-3">
                                    <div class="card-header"><i class="bi bi-person-fill"></i> Pracownicy</div>
                                    <div class="card-body">
                                        <h5 class="card-title text-center">
                                            <?php
                                            $conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbDatabase);
                                            $sql = "SELECT COUNT(*) AS total FROM users WHERE userGroup = 'pracownik'";
                                            if ($stmt = $conn->prepare($sql)) {
                                                $stmt->execute();
                                                $result = $stmt->get_result();
                                                $row = $result->fetch_assoc();
                                                echo $row['total'];
                                                $stmt->close();
                                            }
                                            $conn->close();
                                            ?>
                                        </h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="card text-white card-zapytania mb-3">
                                    <div class="card-header"><i class="bi bi-question-square-fill"></i> Zapytania</div>
                                    <div class="card-body">
                                        <h5 class="card-title text-center">
                                            <?php
                                            $conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbDatabase);
                                            $sql = "SELECT COUNT(*) AS total FROM zapytania";
                                            if ($stmt = $conn->prepare($sql)) {
                                                $stmt->execute();
                                                $result = $stmt->get_result();
                                                $row = $result->fetch_assoc();
                                                echo $row['total'];
                                                $stmt->close();
                                            }
                                            $conn->close();
                                            ?>
                                        </h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="card text-white card-odpowiedzi mb-3">
                                    <div class="card-header"><i class="bi bi-patch-question-fill"></i> Odpowiedzi</div>
                                    <div class="card-body">
                                        <h5 class="card-title text-center">
                                            <?php
                                            $conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbDatabase);
                                            $sql = "SELECT COUNT(*) AS total FROM zapytania WHERE odpowiedz IS NOT NULL";
                                            if ($stmt = $conn->prepare($sql)) {
                                                $stmt->execute();
                                                $result = $stmt->get_result();
                                                $row = $result->fetch_assoc();
                                                echo $row['total'];
                                                $stmt->close();
                                            }
                                            $conn->close();
                                            ?>
                                        </h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="card text-white card-avg-ocena mb-3">
                                    <div class="card-header"><i class="bi bi-star-fill"></i> Średnia ocena</div>
                                    <div class="card-body">
                                        <h5 class="card-title text-center">
                                            <?php
                                            $conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbDatabase);
                                            $sql = "SELECT AVG(ocena) AS total FROM zapytania WHERE ocena IS NOT NULL";
                                            if ($stmt = $conn->prepare($sql)) {
                                                $stmt->execute();
                                                $result = $stmt->get_result();
                                                $row = $result->fetch_assoc();
                                                $ocena = $row['total'];
                                                if (empty($ocena)) {
                                                    $ocena = "-";
                                                }
                                                $ocena = number_format((float) $ocena, 2, '.', '');
                                                echo $ocena;
                                                $stmt->close();
                                            }
                                            $conn->close();
                                            ?>
                                        </h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="card text-white card-aktywnosc mb-3">
                                    <div class="card-header"><i class="bi bi-clipboard-data-fill"></i> Aktywność dzisiaj
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title text-center">
                                            <?php
                                            $conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbDatabase);
                                            $sql = "SELECT COUNT(*) AS total FROM goscieportalu WHERE DATE(datetime) = CURDATE()";
                                            if ($stmt = $conn->prepare($sql)) {
                                                $stmt->execute();
                                                $result = $stmt->get_result();
                                                $row = $result->fetch_assoc();
                                                echo $row['total'];
                                                $stmt->close();
                                            }
                                            $conn->close();
                                            ?>
                                        </h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br />
                        <h2 class="text-center shine" style="color: #ff9000;">
                            Lista pracowników
                        </h2>
                        <hr />
                        <table id="table" class="table table-striped table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col"><i class="bi bi-person-fill"></i> Użytkownik</th>
                                    <th scope="col"><i class="bi bi-person-badge-fill"></i> Rola</th>
                                    <th scope="col"><i class="bi bi-patch-question-fill"></i> Odpowiedzi</th>
                                    <th scope="col"><i class="bi bi-star-fill"></i> Ocena</th>
                                    <th scope="col"><i class="bi bi-speedometer"></i> Czas odpowiedzi</th>
                                    <th scope="col"><i class="bi bi-calendar2-check-fill"></i> Ostatnia aktywność
                                    </th>
                                    <th scope="col"><i class="bi bi-calendar2-plus-fill"></i> Konto od</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbDatabase);
                                $sql = "SELECT users.username, users.id, users.userGroup, users.created, goscieportalu.browser, goscieportalu.localization, goscieportalu.datetime
                                FROM users 
                                LEFT JOIN goscieportalu ON users.username = goscieportalu.username WHERE users.userGroup = 'pracownik'
                                GROUP BY users.username ORDER BY users.created DESC";
                                if ($stmt = $conn->prepare($sql)) {
                                    $stmt->execute();
                                    $result = $stmt->get_result();
                                    while ($row = $result->fetch_assoc()) {
                                        $id = $row['id'];
                                        $ocenaIkona = "";

                                        $conn2 = new mysqli($dbHost, $dbUsername, $dbPassword, $dbDatabase);
                                        $sql2 = "SELECT COUNT(*) AS total FROM zapytania WHERE pracownik_id = ?";
                                        if ($stmt2 = $conn2->prepare($sql2)) {
                                            $stmt2->bind_param("i", $id);
                                            $stmt2->execute();
                                            $result2 = $stmt2->get_result();
                                            $row2 = $result2->fetch_assoc();
                                            $odpowiedzi = $row2['total'];
                                            $stmt2->close();
                                        }
                                        $conn2->close();

                                        $conn1 = new mysqli($dbHost, $dbUsername, $dbPassword, $dbDatabase);
                                        $sql1 = "SELECT AVG(ocena) AS total FROM zapytania WHERE ocena IS NOT NULL AND pracownik_id = ?";
                                        if ($stmt1 = $conn1->prepare($sql1)) {
                                            $stmt1->bind_param("i", $id);
                                            $stmt1->execute();
                                            $result1 = $stmt1->get_result();
                                            $row1 = $result1->fetch_assoc();
                                            $ocena = $row1['total'];
                                            if (empty($ocena)) {
                                                $ocena = "-";
                                            }
                                            $ocena = number_format((float) $ocena, 2, '.', '');
                                            for ($i = 1; $i <= 5; $i++) {
                                                if ($i <= $ocena) {
                                                    $ocenaIkona = $ocenaIkona . "<i class='bi bi-star-fill'></i>";
                                                } else {
                                                    $ocenaIkona = $ocenaIkona . "<i class='bi bi-star'></i>";
                                                }
                                            }
                                            if ($odpowiedzi == 0 || empty($ocena)) {
                                                $ocenaIkona = "Brak ocen";
                                            }
                                            $stmt1->close();
                                        }
                                        $conn1->close();

                                        $conn3 = new mysqli($dbHost, $dbUsername, $dbPassword, $dbDatabase);
                                        $sql3 = "SELECT created, data_odp FROM zapytania WHERE pracownik_id = ?";
                                        if ($stmt3 = $conn3->prepare($sql3)) {
                                            $stmt3->bind_param("i", $id);
                                            $stmt3->execute();
                                            $result3 = $stmt3->get_result();

                                            $totalTime = 0;
                                            $count = 0;

                                            while ($row3 = $result3->fetch_assoc()) {
                                                $czasOdpSekundy = strtotime($row3['data_odp']) - strtotime($row3['created']);
                                                $totalTime += $czasOdpSekundy;
                                                $count++;
                                            }

                                            if ($count > 0) {
                                                $averageTime = $totalTime / $count;
                                                $averageTimeFormatted = gmdate("H:i:s", (int) $averageTime);
                                            } else {
                                                $averageTimeFormatted = "00:00:00";
                                            }

                                            if ($odpowiedzi == 0) {
                                                $averageTimeFormatted = "Brak odpowiedzi";
                                            }

                                            $stmt3->close();
                                        }
                                        $conn3->close();

                                        $role = $row['userGroup'];
                                        if ($role == 'pracownik') {
                                            $role = "<i class='bi bi-person-badge-fill'></i> Pracownik";
                                        } else {
                                            $role = "<i class='bi bi-person-fill'></i> Klient";
                                        }
                                        echo "<tr>";
                                        echo "<td>" . $row['username'] . "</td>";
                                        echo "<td>" . $role . "</td>";
                                        echo "<td class='text-center col-2'>" . $odpowiedzi . "</td>";
                                        echo "<td class='text-center col-1'>" . $ocenaIkona . "</td>";
                                        echo "<td class='text-center col-2'>$averageTimeFormatted</td>";
                                        echo "<td class='text-center col-2'>" . $row['datetime'] . "</td>";
                                        echo "<td class='text-center col-2'>" . $row['created'] . "</td>";
                                        echo "</tr>";
                                    }
                                    $stmt->close();
                                }
                                $conn->close();
                                ?>
                            </tbody>
                        </table>
                        <br />
                        <h2 class="text-center shine" style="color: #ff9000;">
                            Zarejestrowani klienci
                        </h2>
                        <hr />
                        <table id="table" class="table table-striped table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col"><i class="bi bi-person-fill"></i> Użytkownik</th>
                                    <th scope="col"><i class="bi bi-person-badge-fill"></i> Rola</th>
                                    <th scope="col"><i class="bi bi-calendar2-plus-fill"></i> Konto od</th>
                                    <th scope="col"><i class="bi bi-calendar2-check-fill"></i> Ostatnia aktywność</th>
                                    <th scope="col"><i class="bi bi-laptop-fill"></i> Przeglądarka</th>
                                    <th scope="col"><i class="bi bi-geo-alt-fill"></i> Lokalizacja</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbDatabase);
                                $sql = "SELECT users.username, users.userGroup, users.created, goscieportalu.browser, goscieportalu.localization, goscieportalu.datetime
                                FROM users 
                                LEFT JOIN goscieportalu ON users.username = goscieportalu.username WHERE users.userGroup != 'pracownik' AND users.userGroup != 'admin'
                                GROUP BY users.username ORDER BY users.created DESC";
                                if ($stmt = $conn->prepare($sql)) {
                                    $stmt->execute();
                                    $result = $stmt->get_result();
                                    while ($row = $result->fetch_assoc()) {
                                        $role = $row['userGroup'];
                                        if ($role == 'admin') {
                                            $role = "<i class='bi bi-person-badge-fill'></i> Pracownik";
                                        } else {
                                            $role = "<i class='bi bi-person-fill'></i> Klient";
                                        }
                                        echo "<tr>";
                                        echo "<td>" . $row['username'] . "</td>";
                                        echo "<td>" . $role . "</td>";
                                        echo "<td>" . $row['created'] . "</td>";
                                        echo "<td>" . $row['datetime'] . "</td>";
                                        echo "<td>" . $row['browser'] . "</td>";
                                        echo "<td>" . $row['localization'] . "</td>";
                                        echo "</tr>";
                                    }
                                    $stmt->close();
                                }
                                $conn->close();
                                ?>
                            </tbody>
                        </table>
                        <br />
                    </div>
                <?php endif; ?>
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