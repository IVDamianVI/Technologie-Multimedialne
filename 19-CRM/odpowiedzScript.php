<?php

declare(strict_types=1);
session_start();
require ('access.php');

if (!isset($_SESSION['loggedin']) || $_SESSION['userGroup'] != 'pracownik') {
    header('Location: logIn.php');
    exit();
}
$conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbDatabase);
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $question_id = $_POST['question_id'];
    $answer = $_POST['answer'];
    $pracownik_id = $_POST['pracownik_id'];

    if (empty($question_id) || empty($answer)) {
        echo "Wszystkie pola są wymagane!";
        exit;
    }

    $sql = "UPDATE zapytania SET odpowiedz = ?, pracownik_id = ?, data_odp = NOW(), modified = NOW() WHERE id = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sii", $answer, $pracownik_id, $question_id);

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