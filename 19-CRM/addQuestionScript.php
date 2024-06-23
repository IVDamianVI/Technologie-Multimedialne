<?php
declare(strict_types=1);
session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: logIn.php');
    exit();
}
require ('access.php');

$conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbDatabase);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $category = $_POST['category'];
    $question = $_POST['question'];

    if (empty($user_id) || empty($category) || empty($question)) {
        echo "Wszystkie pola są wymagane!";
        exit;
    }

    $sql = "INSERT INTO zapytania (user_id, kategoria, pytanie, created) VALUES (?, ?, ?, NOW())";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("iss", $user_id, $category, $question);

        if ($stmt->execute()) {
            header("Location: index.php");
            exit;
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