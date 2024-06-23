<?php
session_start();
require_once 'access.php';

$dbConnection = new mysqli($dbHost, $dbUsername, $dbPassword, $dbDatabase);

if ($dbConnection->connect_error) {
    die("Connection failed: " . $dbConnection->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_cms = $_POST['id_cms'];

    // Arrays received from the form
    $names = $_POST['name'];
    $urls = $_POST['url'];

    // Delete all current entries to add fresh ones
    $deleteQuery = "DELETE FROM `cms_headers` WHERE `id_cms` = ?";
    $stmt = $dbConnection->prepare($deleteQuery);
    $stmt->bind_param("i", $id_cms);
    $stmt->execute();
    $stmt->close();

    // Prepare the insert statement
    $insertQuery = "INSERT INTO `cms_headers` (id_cms, name, url) VALUES (?, ?, ?)";
    $stmt = $dbConnection->prepare($insertQuery);
    if (!$stmt) {
        $_SESSION['error_message'] = "Błąd: Nie można przygotować zapytania";
        header('Location: edit-header.php');
        exit();
    }

    // Insert each name and url pair
    for ($i = 0; $i < count($names); $i++) {
        if (!empty($names[$i]) && !empty($urls[$i])) { // Only insert if both fields are filled
            $stmt->bind_param("iss", $id_cms, $names[$i], $urls[$i]);
            $stmt->execute();
        }
    }

    if ($stmt->affected_rows > 0) {
        $_SESSION['success_message'] = "Zaktualizowano pomyślnie!";
    } else {
        $_SESSION['error_message'] = "Brak zmian do zapisania lub błąd zapytania";
    }

    $stmt->close();
    $dbConnection->close();
    header('Location: edit-header.php');
    exit();
}
?>