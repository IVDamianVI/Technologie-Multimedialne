<?php
session_start();

if (!isset($_SESSION['loggedin'])) {
    header('Location: logIn.php');
    exit();
}

require('access.php');
include('loginCheckScript.php');

if (isset($_POST['directoryName'])) {
    $directoryName = $_POST['directoryName'];
    $ftpCloudDir = '/domains/ivdamianvi.smallhost.pl/public_html/z4/media/cloud/' . $_SESSION['user'];
    $fileType = 'directory';
    $ftpConn = ftp_connect($ftpServer);
    $loginResult = ftp_login($ftpConn, $ftpUsername, $ftpPassword);

    if ($ftpConn && $loginResult) {
        ftp_pasv($ftpConn, true);

        $newDirectoryPath = $ftpCloudDir . '/' . $directoryName;

        if (!ftp_mkdir($ftpConn, $newDirectoryPath)) {
            $_SESSION['error_message'] = 'Błąd tworzenia folderu.';
            header('Location: cloud.php');
            exit();
        }

        $dbConn = mysqli_connect($dbHost, $dbUsername, $dbPassword, $dbDatabase);

        if ($dbConn) {
            mysqli_query($dbConn, "SET NAMES 'utf8'");

            $stmt = mysqli_prepare($dbConn, "INSERT INTO cloud (username, path, fileName, fileType) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, 'ssss', $_SESSION['user'], $_SESSION['user'], $directoryName, $fileType);
            $stmtResult = mysqli_stmt_execute($stmt);

            if ($stmtResult) {
                $_SESSION['success_message'] = 'Pomyślnie utworzono folder.';
                header('Location: cloud.php');
                exit();
            } else {
                $_SESSION['error_message'] = 'Błąd bazy danych.';
                header('Location: cloud.php');
                exit();
            }
        } else {
            $_SESSION['error_message'] = 'Błąd bazy danych.';
            header('Location: cloud.php');
            exit();
        }
    } else {
        $_SESSION['error_message'] = 'Błąd serwera.';
        header('Location: cloud.php');
        exit();
    }
} else {
    $_SESSION['error_message'] = 'Błąd tworzenia folderu.';
    header('Location: cloud.php');
    exit();
}
?>