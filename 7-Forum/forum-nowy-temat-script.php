<?php

declare(strict_types=1);
session_start();

if (!isset($_SESSION['loggedin'])) {
    header('Location: logIn.php');
    exit();
}
require('access.php');

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    $_SESSION['error_message'] = 'Nieprawidłowe żądanie.';
    header('Location: forum-nowy-temat.php');
    exit();
}

$title = $_POST['title'];
$text = $_POST['text'];
$creator = $_SESSION['user'];

$badWords = ['chuj', 'chuju', 'huj', 'huju', 'cholera', 'cholery', 'kurwa', 'kurwy', 'kurew', 'jebać', 'jebac', 'jebanie', 'pierdolić', 'pierdolic', 'pierdolenie', 'pierdolisz']; // Wypełnij listę rzeczywistymi przekleństwami

foreach ($badWords as $badWord) {
    if (stripos($title, $badWord) !== false || stripos($text, $badWord) !== false) {
        $_SESSION['error_message'] = 'Tytuł lub treść zawiera niedozwolone słowa.';
        $_SESSION['form_data'] = ['title' => $title, 'text' => $text];
        header('Location: forum-nowy-temat.php');
        exit();
    }
}

$dbConn = mysqli_connect($dbHost, $dbUsername, $dbPassword, $dbDatabase);
if (!$dbConn) {
    $_SESSION['error_message'] = 'Błąd połączenia z bazą danych.';
    header('Location: forum-nowy-temat.php');
    exit();
}

mysqli_query($dbConn, "SET NAMES 'utf8'");
$stmt = mysqli_prepare($dbConn, "INSERT INTO topic (title, text, creator) VALUES (?, ?, ?)");
mysqli_stmt_bind_param($stmt, 'sss', $title, $text, $creator);
$stmtResult = mysqli_stmt_execute($stmt);

if (!$stmtResult) {
    $_SESSION['error_message'] = 'Błąd połączenia z bazą danych.';
    header('Location: forum-nowy-temat.php');
    exit();
}

$_SESSION['success_message'] = 'Temat został dodany pomyślnie.';
header('Location: forum.php');
exit();
?>
