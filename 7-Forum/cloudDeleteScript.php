<?php

session_start();

if (!isset($_SESSION['loggedin'])) {
    header('Location: logIn.php');
    exit();
}

require('access.php');
include('loginCheckScript.php');

if (isset($_POST['fileName']) && isset($_POST['id']) && isset($_POST['fileType'])) {
    $fileName = $_POST['fileName'];
    $id = $_POST['id'];
    $fileType = $_POST['fileType'];

    $ftpCloudDir = '/domains/ivdamianvi.smallhost.pl/public_html/z4/media/cloud/' . $_SESSION['user'] . '/' . $fileName;

    $ftpConn = ftp_connect($ftpServer);
    $loginResult = ftp_login($ftpConn, $ftpUsername, $ftpPassword);

    if ($ftpConn && $loginResult) {
        ftp_pasv($ftpConn, true);

        if ($fileType == 'directory') {
            if (ftp_rmdir($ftpConn, $ftpCloudDir)) {
                deleteFromDatabase($id);
            } else {
                $_SESSION['error_message'] = 'Błąd usuwania folderu z chmury.';
                header('Location: cloud.php');
                exit();
            }
        } else {
            if (ftp_delete($ftpConn, $ftpCloudDir)) {
                deleteFromDatabase($id);
            } else {
                $_SESSION['error_message'] = 'Błąd usuwania pliku z chmury.';
                header('Location: cloud.php');
                exit();
            }
        }
    } else {
        $_SESSION['error_message'] = 'Błąd logowania do chmury.';
        header('Location: cloud.php');
        exit();
    }
} else {
    $_SESSION['error_message'] = 'Błąd przesyłania danych.';
    header('Location: cloud.php');
    exit();
}

function deleteFromDatabase($id)
{
    require('access.php');
    $dbConn = mysqli_connect($dbHost, $dbUsername, $dbPassword, $dbDatabase);

    if ($dbConn) {
        mysqli_query($dbConn, "SET NAMES 'utf8'");

        $stmt = mysqli_prepare($dbConn, "DELETE FROM cloud WHERE id = ? AND username = ?");
        mysqli_stmt_bind_param($stmt, 'is', $id, $_SESSION['user']);
        $stmtResult = mysqli_stmt_execute($stmt);

        if ($stmtResult) {
            $_SESSION['success_message'] = 'Plik/folder został pomyślnie usunięty.';
            header('Location: cloud.php');
            exit();
        } else {
            $_SESSION['error_message'] = 'Błąd chmury podczas usuwania pliku/folderu.';
            header('Location: cloud.php');
            exit();
        }
    } else {
        $_SESSION['error_message'] = 'Błąd połączenia z chmurą.';
        header('Location: cloud.php');
        exit();
    }
}


?>