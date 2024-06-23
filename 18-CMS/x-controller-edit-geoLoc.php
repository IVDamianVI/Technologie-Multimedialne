<?php
session_start();
require_once 'access.php';
$dbConnection = new mysqli($dbHost, $dbUsername, $dbPassword, $dbDatabase);
if ($dbConnection->connect_error) {
    die("Connection failed: " . $dbConnection->connect_error);
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_cms = $_POST['id_cms'];
    $new_geoLoc = $_POST['geoLoc'];
    $query = "UPDATE `cms` SET `geo_loc` = '$new_geoLoc' WHERE `id_cms` = $id_cms";
    if ($dbConnection->query($query) === TRUE) {
        $_SESSION['success_message'] = "Zaktualizowano pomyślnie!";
    } else {
        $_SESSION['error_message'] = "Błąd: " . $query . "<br>" . $dbConnection->error;
    }
    $dbConnection->close();
    header('Location: jak-dotrzec.php');
    exit();
}

?>