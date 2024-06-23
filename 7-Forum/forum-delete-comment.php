<?php
session_start();
require('access.php');

$dbConn = mysqli_connect($dbHost, $dbUsername, $dbPassword, $dbDatabase);

$commentId = intval($_GET['id']);
$topicId = intval($_GET['temat']);
$loggedInUser = $_SESSION['user'] ?? '';
$userGroup = $_SESSION['userGroup'];

if ($commentId) {
    // Usuń komentarz, jeśli należy do zalogowanego użytkownika
    $sql = "DELETE FROM comments WHERE id = ?";
    if ($stmt = mysqli_prepare($dbConn, $sql)) {
        mysqli_stmt_bind_param($stmt, 'i', $commentId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

mysqli_close($dbConn);

// Przekierowanie z powrotem do forum
header("Location: forum.php?temat=$topicId");
exit();
?>
