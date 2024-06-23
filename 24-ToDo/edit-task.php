<?php

declare(strict_types=1);
session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: logIn.php');
    exit();
}
require 'access.php';

$conn = mysqli_connect($dbHost, $dbUsername, $dbPassword, $dbDatabase);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$idz = $_GET['idz'];
$userId = $_SESSION['userid'];
$_SESSION['page'] = 'edit-task.php';

$sql = "SELECT * FROM zadanie WHERE idz='$idz'";
$result = $conn->query($sql);
$zadanie = $result->fetch_assoc();

if (!$zadanie) {
    die("Zadanie nie znalezione");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_task'])) {
    if (isset($_POST['nazwa_zadania']) && $zadanie['idp'] == $userId) {
        $nazwa_zadania = $conn->real_escape_string($_POST['nazwa_zadania']);
        $sql = "UPDATE zadanie SET nazwa_zadania='$nazwa_zadania' WHERE idz='$idz'";
        $conn->query($sql);
    }
    foreach ($_POST['podzadania'] as $idpz => $podzadanie) {
        if ($zadanie['idp'] == $userId) {
            if (isset($podzadanie['nazwa'])) {
                $nazwa_podzadania = $conn->real_escape_string($podzadanie['nazwa']);
                $sql = "UPDATE podzadanie SET nazwa_podzadania='$nazwa_podzadania' WHERE idpz='$idpz'";
                $conn->query($sql);
            }
            if (isset($podzadanie['wykonawca'])) {
                $wykonawca_id = $conn->real_escape_string($podzadanie['wykonawca']);
                $sql = "UPDATE podzadanie SET idp='$wykonawca_id' WHERE idpz='$idpz'";
                $conn->query($sql);
            }
        }
        if (isset($podzadanie['stan'])) {
            $stan = (int) $podzadanie['stan'];
            $sql = "UPDATE podzadanie SET stan='$stan' WHERE idpz='$idpz' AND (idp='$userId' OR idz='$idz')";
            $conn->query($sql);
        }
    }
    $_SESSION['success_message'] = "Zadanie zostało zaktualizowane.";
    header("Location: /z24/");
    exit();
}

$sql = "SELECT * FROM podzadanie WHERE idz='$idz'";
$result_podzadania = $conn->query($sql);

$sql_users = "SELECT id, username FROM users";
$result_users = $conn->query($sql_users);
$users = [];
while ($user = $result_users->fetch_assoc()) {
    $users[] = $user;
}

?>
<!DOCTYPE html>
<html lang="pl" data-bs-theme="dark">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Damian Grubecki">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <link rel="stylesheet" href="css/styleIndex.css">
    <link rel="stylesheet" href="css/lightModeColors.css">
    <link rel="stylesheet" href="css/darkModeColors.css">
    <link rel="icon" href="media/favicon/favicon-orange.png">
    <script src="script/loadHeader.js"></script>
    <script src="//geoip-js.com/js/apis/geoip2/v2.1/geoip2.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN"
        crossorigin="anonymous"></script>
    <title>Edytuj Zadanie</title>
</head>

<body onload="myLoadHeader();">
    <?php include 'header.php'; ?>
    <main class="container mt-5">
        <section>
            <h1 class="text-center mb-4" style="color: #ff9000;"><i class='bi bi-pencil-fill'></i> Edytuj Zadanie</h1>
        </section>

        <section>
            <form method="POST" action="edit-task.php?idz=<?php echo $idz; ?>">
                <div class="mb-3">
                    <label for="nazwa_zadania" class="form-label"><i class="bi bi-pencil-fill"></i> Nazwa
                        zadania</label>
                    <input type="text" class="form-control" id="nazwa_zadania" name="nazwa_zadania"
                        value="<?php echo htmlspecialchars($zadanie['nazwa_zadania']); ?>" required <?php if ($zadanie['idp'] != $userId)
                               echo 'disabled'; ?>>
                </div>
                <div id="podzadaniaContainer">
                    <?php while ($podzadanie = $result_podzadania->fetch_assoc()) {
                        $editable = ($zadanie['idp'] == $userId);
                        $editableState = (isset($podzadanie['idp']) && ($podzadanie['idp'] == $userId || $zadanie['idp'] == $userId));
                        $disabled = $editable ? '' : 'disabled';
                        $disabledState = $editableState ? '' : 'disabled';
                        $style = $editableState ? '' : 'style="background-color: black;"';
                        ?>
                        <div class="mb-3 podzadanie-group" <?php echo $style; ?>>
                            <label for="nazwa_podzadania_<?php echo $podzadanie['idpz']; ?>" class="form-label"><i
                                    class="bi bi-list-task"></i> Treść podzadania</label>
                            <input type="text" class="form-control"
                                name="podzadania[<?php echo $podzadanie['idpz']; ?>][nazwa]"
                                value="<?php echo htmlspecialchars($podzadanie['nazwa_podzadania']); ?>" required <?php echo $disabled; ?>>
                            <label for="stan_<?php echo $podzadanie['idpz']; ?>" class="form-label"><i
                                    class="bi bi-bar-chart-line-fill"></i> Stan</label>
                            <div class="range-container">
                                <input type="range" class="form-range" min="0" max="100"
                                    name="podzadania[<?php echo $podzadanie['idpz']; ?>][stan]"
                                    value="<?php echo $podzadanie['stan']; ?>" <?php echo $disabledState; ?>>
                            </div>
                            <label for="wykonawca_<?php echo $podzadanie['idpz']; ?>" class="form-label"><i
                                    class="bi bi-person-fill"></i> Wykonawca</label>
                            <select class="form-select" name="podzadania[<?php echo $podzadanie['idpz']; ?>][wykonawca]"
                                <?php echo $disabled; ?>>
                                <?php foreach ($users as $user) { ?>
                                    <option value="<?php echo $user['id']; ?>" <?php if ($user['id'] == $podzadanie['idp'])
                                           echo 'selected'; ?>>
                                        <?php echo htmlspecialchars($user['username']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    <?php } ?>
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-primary" name="update_task"><i
                            class="bi bi-check-circle-fill"></i> Zaktualizuj zadanie</button>
                </div>
            </form>
        </section>
    </main>
    <br /><br /><br /><br />
    <?php require_once 'footer.php'; ?>
</body>

</html>