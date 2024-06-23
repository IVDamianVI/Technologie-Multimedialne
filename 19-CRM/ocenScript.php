<?php

declare(strict_types=1);
session_start();
require ('access.php');

if (!isset($_SESSION['loggedin'])) {
    header('Location: logIn.php');
    exit();
}
$conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbDatabase);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $question_id = $_POST['question_id'];
    $rating = $_POST['rating'];

    // Walidacja danych
    if (empty($question_id) || empty($rating) || !is_numeric($rating) || $rating < 1 || $rating > 5) {
        echo "Wszystkie pola są wymagane i ocena musi być liczbą od 1 do 5!";
        exit;
    }

    // Przygotowanie zapytania SQL
    $sql = "UPDATE zapytania SET ocena = ?, modified = NOW() WHERE id = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ii", $rating, $question_id);

        if ($stmt->execute()) {
            header("Location: index.php");
        } else {
            echo "Błąd: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Błąd przygotowania zapytania: " . $conn->error;
    }

    $conn->close();
} else {
    echo "Nieprawidłowe żądanie.";
}
?>