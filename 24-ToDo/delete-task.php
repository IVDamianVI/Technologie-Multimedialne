<?php

declare(strict_types=1);
session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: logIn.php');
    exit();
}
require 'access.php';

// Połączenie z bazą danych
$conn = mysqli_connect($dbHost, $dbUsername, $dbPassword, $dbDatabase);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$idz = $_GET['idz'];

// Usunięcie podzadań
$sql = "DELETE FROM podzadanie WHERE idz='$idz'";
if ($conn->query($sql) === TRUE) {
    // Usunięcie zadania
    $sql = "DELETE FROM zadanie WHERE idz='$idz'";
    if ($conn->query($sql) === TRUE) {
        echo "Zadanie zostało usunięte.";
    } else {
        echo "Błąd: " . $sql . "<br>" . $conn->error;
    }
} else {
    echo "Błąd: " . $sql . "<br>" . $conn->error;
}

$conn->close();
header('Location: index.php');
exit();

?>