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
require 'access.php';

$conn = mysqli_connect($dbHost, $dbUsername, $dbPassword, $dbDatabase);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_task'])) {
    $nazwa_zadania = $conn->real_escape_string($_POST['nazwa_zadania']);
    $idp = $_SESSION['userid'];

    $sql = "INSERT INTO zadanie (idp, nazwa_zadania) VALUES ('$idp', '$nazwa_zadania')";
    if ($conn->query($sql) === TRUE) {
        $idz = $conn->insert_id;
        if (isset($_POST['podzadania']) && is_array($_POST['podzadania'])) {
            foreach ($_POST['podzadania'] as $podzadanie) {
                $nazwa_podzadania = $conn->real_escape_string($podzadanie['nazwa']);
                $wykonawca_id = $conn->real_escape_string($podzadanie['wykonawca']);
                $stan = 0;
                $sql = "INSERT INTO podzadanie (idz, idp, nazwa_podzadania, stan) VALUES ('$idz', '$wykonawca_id', '$nazwa_podzadania', '$stan')";
                $conn->query($sql);
            }
        }

        echo "Zadanie zostało dodane.";
    } else {
        echo "Błąd: " . $sql . "<br>" . $conn->error;
    }
}

$login = $_SESSION['user'];
$sql = "SELECT * FROM users WHERE username='$login'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();
$_SESSION['userid'] = $user['id'];
$_SESSION['page'] = 'index.php';
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
</head>

<body onload="myLoadHeader(); autoSubmitForm()">
    <div id='myHeader'></div>
    <main class="container mt-5">
        <section>
            <!-- <h1 class="text-center mb-4" style="color: #ff9000;">Witaj na stronie głównej!</h1> -->
        </section>
        <div class="container text-center">
    <?php
    if (isset($_SESSION['error_message'])) {
        echo '<div class="alert alert-danger" role="alert">';
        echo '<b><i class="bi bi-exclamation-triangle-fill"></i> Wystąpił problem</b><hr/>';
        echo $_SESSION['error_message'];
        echo '</div>';
        unset($_SESSION['error_message']);
    } else if (isset($_SESSION['success_message'])) {
        echo '<div class="alert alert-success" role="alert">';
        echo '<b><i class="bi bi-check-circle-fill"></i> Sukces</b><hr/>';
        echo $_SESSION['success_message'];
        echo '</div>';
        unset($_SESSION['success_message']);
    }
    if (isset($_SESSION['warning_message'])) {
        echo '<div class="alert alert-warning" role="alert">';
        echo '<b><i class="bi bi-info-circle-fill"></i> Uwaga</b><hr/>';
        echo $_SESSION['warning_message'];
        echo '</div>';
        unset($_SESSION['warning_message']);
    }
    ?>
</div>
        <section>
            <h2 class="text-center mb-4">Twoje zadania</h2>
            <div class="table-responsive">
                <table id="tasksTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Zadanie</th>
                            <th>Średni Stan</th>
                            <th>Twórca</th>
                            <th>Wykonawcy</th>
                            <th>Akcje</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        function getStatusColor($percent) {
                            if ($percent >= 100) {
                                return '#39ff14';
                            } elseif ($percent >= 66) {
                                return '#ffcc00';
                            } elseif ($percent >= 33) {
                                return '#ff9933';
                            } else {
                                return '#ff0000';
                            }
                        }

                        $userGroup = $_SESSION['userGroup'];
                        $userId = $_SESSION['userid'];

                        if ($userGroup == 'admin') {
                            $sql = "SELECT * FROM zadanie";
                        } else {
                            $sql = "SELECT DISTINCT zadanie.* FROM zadanie 
                                    LEFT JOIN podzadanie ON zadanie.idz = podzadanie.idz 
                                    WHERE zadanie.idp = $userId OR podzadanie.idp = $userId";
                        }

                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $idz = $row['idz'];
                                $sql_podzadania = "SELECT AVG(stan) as sredni_stan FROM podzadanie WHERE idz='$idz'";
                                $result_podzadania = $conn->query($sql_podzadania);
                                $sredni_stan = $result_podzadania->fetch_assoc()['sredni_stan'];
                                $sredni_stan = $sredni_stan !== null ? round((float)$sredni_stan, 2) : 0;
                                $status_color = getStatusColor($sredni_stan);

                                $sql_creator = "SELECT username FROM users WHERE id=" . $row['idp'];
                                $result_creator = $conn->query($sql_creator);
                                $creator = $result_creator->fetch_assoc()['username'];

                                $sql_wykonawcy = "SELECT DISTINCT users.username FROM podzadanie JOIN users ON podzadanie.idp = users.id WHERE podzadanie.idz='$idz'";
                                $result_wykonawcy = $conn->query($sql_wykonawcy);
                                $wykonawcy = [];
                                while ($wykonawca = $result_wykonawcy->fetch_assoc()) {
                                    $wykonawcy[] = $wykonawca['username'];
                                }
                                $wykonawcy_list = implode(', ', $wykonawcy);

                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['nazwa_zadania']) . "</td>";
                                echo "<td style='color: $status_color'>" . $sredni_stan . "%</td>";
                                echo "<td>" . htmlspecialchars($creator) . "</td>";
                                echo "<td>" . htmlspecialchars($wykonawcy_list) . "</td>";
                                echo "<td><a href='edit-task.php?idz=" . $row['idz'] . "' class='btn btn-primary'><i class='bi bi-pencil-fill'></i></a> <a href='delete-task.php?idz=" . $row['idz'] . "' class='btn btn-danger'><i class='bi bi-trash3-fill'></i></a></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center'>Brak zadań</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </section>
                        <hr/>
        <section>
            <h2 class="text-center mb-4">Dodaj nowe zadanie</h2>
            <form method="POST" action="index.php" id="new-task">
                <div class="mb-3">
                    <label for="nazwa_zadania" class="form-label">Nazwa zadania</label>
                    <input type="text" class="form-control" id="nazwa_zadania" name="nazwa_zadania" required>
                </div>
                <div id="podzadaniaContainer">
                    <div class="mb-3 podzadanie-group">
                        <label for="nazwa_podzadania" class="form-label"><i class="bi bi-list-task"></i> Treść podzadania</label>
                        <input type="text" class="form-control" name="podzadania[0][nazwa]" required>
                        <label for="wykonawca" class="form-label"><i class="bi bi-person-fill"></i> Wykonawca</label>
                        <select class="form-select" name="podzadania[0][wykonawca]" required>
                            <option value="" selected disabled hidden>Wybierz wykonawcę</option>
                            <?php
                            $sql_users = "SELECT id, username FROM users";
                            $result_users = $conn->query($sql_users);
                            while ($user = $result_users->fetch_assoc()) {
                                echo "<option value='" . $user['id'] . "'>" . $user['username'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <button type="button" class="btn btn-secondary" onclick="addPodzadanie()"><i class="bi bi-plus-circle-dotted"></i> Dodaj podzadanie</button>
                <div class="text-end">
                    <button type="submit" class="btn btn-primary" name="add_task"><i class="bi bi-plus-square-fill"></i> Publikuj zadanie</button>
                </div>
            </form>
        </section>
    </main>
    <br/><br/><br/><br/>
    <script>
        let podzadanieCount = 1;
        function addPodzadanie() {
            const container = document.getElementById('podzadaniaContainer');
            const div = document.createElement('div');
            div.classList.add('mb-3', 'podzadanie-group');
            div.innerHTML = `
                <label for="nazwa_podzadania_${podzadanieCount}" class="form-label"><i class="bi bi-list-task"></i> Treść podzadania</label>
                <input type="text" class="form-control" name="podzadania[${podzadanieCount}][nazwa]" required>
                <label for="wykonawca" class="form-label"><i class="bi bi-person-fill"></i> Wykonawca</label>
                <select class="form-select" name="podzadania[${podzadanieCount}][wykonawca]" required>
                <option value="" selected disabled hidden>Wybierz wykonawcę</option>
                    <?php
                    $sql_users = "SELECT id, username FROM users";
                    $result_users = $conn->query($sql_users);
                    while ($user = $result_users->fetch_assoc()) {
                        echo "<option value='" . $user['id'] . "'>" . $user['username'] . "</option>";
                    }
                    ?>
                </select>`;
            container.appendChild(div);
            podzadanieCount++;
        }
    </script>
    <?php require_once 'footer.php'; ?>
</body>

</html>
