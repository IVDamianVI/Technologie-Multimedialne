<?php

declare(strict_types=1);

session_start();

if (!isset($_SESSION['loggedin'])) {
    header('Location: logIn.php');
    exit();
}

require('access.php');
include('loginCheckScript.php');

if (isset($_POST['fileName']) && isset($_POST['fileId'])) {
    $fileName = $_POST['fileName'];
    $fileId = $_POST['fileId'];
    $username = $_SESSION['user'];

    $dbConn = mysqli_connect($dbHost, $dbUsername, $dbPassword, $dbDatabase);

    if ($dbConn) {
        mysqli_query($dbConn, "SET NAMES 'utf8'");

        $stmtSelect = mysqli_prepare($dbConn, "SELECT fileName, fileType, fileSize FROM cloud WHERE id = ? AND username = ?");
        mysqli_stmt_bind_param($stmtSelect, 'is', $fileId, $username);
        mysqli_stmt_execute($stmtSelect);
        $resultSelect = mysqli_stmt_get_result($stmtSelect);
        $rowSelect = mysqli_fetch_assoc($resultSelect);

        if ($rowSelect) {
            $originalFileName = $rowSelect['fileName'];
            $fileType = $rowSelect['fileType'];
            $fileSize = $rowSelect['fileSize'];

            $duplicateFileName = 'Kopia ' . $originalFileName;
            $duplicateFilePath = 'media/cloud/' . $username . '/' . $duplicateFileName;

            $stmtInsert = mysqli_prepare($dbConn, "INSERT INTO cloud (username, fileName, fileType, fileSize) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmtInsert, 'sssi', $username, $duplicateFileName, $fileType, $fileSize);
            $stmtResultInsert = mysqli_stmt_execute($stmtInsert);

            if ($stmtResultInsert) {
                copy('media/cloud/' . $username . '/' . $originalFileName, $duplicateFilePath);

                $_SESSION['success_message'] = 'Plik został pomyślnie skopiowany.';
            } else {
                $_SESSION['error_message'] = 'Błąd bazy danych podczas kopiowania pliku.';
            }
        } else {
            $_SESSION['error_message'] = 'Nie znaleziono pliku o podanym identyfikatorze.';
        }

        mysqli_close($dbConn);
    } else {
        $_SESSION['error_message'] = 'Błąd połączenia z bazą danych.';
    }

    header('Location: cloud.php');
    exit();
} else {
    $_SESSION['error_message'] = 'Nieprawidłowe żądanie POST.';
    header('Location: cloud.php');
    exit();
}
?>
