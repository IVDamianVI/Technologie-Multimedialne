<?php

session_start();

if (!isset($_SESSION['loggedin'])) {
    header('Location: logIn.php');
    exit();
}

require('access.php');
include('loginCheckScript.php');

if (isset($_POST['newFileName']) && isset($_POST['fileId'])) {
    $newFileName = $_POST['newFileName'];
    $fileId = $_POST['fileId'];

    $dbConn = mysqli_connect($dbHost, $dbUsername, $dbPassword, $dbDatabase);

    if ($dbConn) {
        mysqli_query($dbConn, "SET NAMES 'utf8'");

        $stmtSelect = mysqli_prepare($dbConn, "SELECT fileName FROM cloud WHERE id = ? AND username = ?");
        mysqli_stmt_bind_param($stmtSelect, 'is', $fileId, $_SESSION['user']);
        mysqli_stmt_execute($stmtSelect);
        $resultSelect = mysqli_stmt_get_result($stmtSelect);
        $rowSelect = mysqli_fetch_assoc($resultSelect);
        $currentFileName = $rowSelect['fileName'];

        $ftpCloudDir = '/domains/ivdamianvi.smallhost.pl/public_html/z4/media/cloud/' . $_SESSION['user'];
        $newFilePath = $ftpCloudDir . '/' . $newFileName;

        $ftpConn = ftp_connect($ftpServer);
        $loginResult = ftp_login($ftpConn, $ftpUsername, $ftpPassword);

        if ($ftpConn && $loginResult) {
            ftp_pasv($ftpConn, true);

            $isFileNameTaken = ftp_size($ftpConn, $newFilePath) != -1;

            if ($isFileNameTaken) {
                $_SESSION['error_message'] = 'Nazwa pliku już istnieje w folderze FTP.';
                header('Location: cloud.php');
                exit();
            }

            $stmtUpdate = mysqli_prepare($dbConn, "UPDATE cloud SET fileName = ? WHERE id = ? AND username = ?");
            mysqli_stmt_bind_param($stmtUpdate, 'sis', $newFileName, $fileId, $_SESSION['user']);
            $stmtResultUpdate = mysqli_stmt_execute($stmtUpdate);

            if ($stmtResultUpdate) {
                $currentFilePath = $ftpCloudDir . '/' . $currentFileName;

                if (ftp_rename($ftpConn, $currentFilePath, $newFilePath)) {
                    $_SESSION['success_message'] = 'Nazwa pliku została pomyślnie zmieniona.';
                } else {
                    $_SESSION['error_message'] = 'Błąd podczas zmiany nazwy pliku na serwerze FTP.';
                }

                ftp_close($ftpConn);
            } else {
                $_SESSION['error_message'] = 'Błąd bazy danych podczas zmiany nazwy pliku.';
            }

            header('Location: cloud.php');
            exit();
        } else {
            $_SESSION['error_message'] = 'Błąd logowania do serwera FTP.';
        }

        header('Location: cloud.php');
        exit();
    } else {
        $_SESSION['error_message'] = 'Błąd połączenia z bazą danych.';
    }

    header('Location: cloud.php');
    exit();
} else {
    $_SESSION['error_message'] = 'Brak nowej nazwy pliku lub identyfikatora pliku w żądaniu POST.';
    header('Location: cloud.php');
    exit();
}