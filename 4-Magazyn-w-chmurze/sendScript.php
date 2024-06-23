<?php

declare(strict_types=1);
session_start();
require('access.php');
$time = date('H:i:s', time());
$fromUser = $_POST['fromUser'];
$toUser = $_POST['toUser'];
$message = $_POST['message'];
$ftpChatDir = '/domains/ivdamianvi.smallhost.pl/public_html/z4/media/chat/' . $_SESSION['user'] . '';
$attachment = $_FILES["attachment"]["tmp_name"];
$fileName = $_FILES["attachment"]["name"];
$ftpConn = ftp_connect($ftpServer);

if (isset($_POST['message']) || isset($_FILES["attachment"]["name"])) {
    $dbConn = mysqli_connect($dbHost, $dbUsername, $dbPassword, $dbDatabase);
    mysqli_query($dbConn, "SET NAMES 'utf8'");
    $checkUserQuery = mysqli_query($dbConn, "SELECT * FROM users WHERE username = '$toUser'");
    if (mysqli_num_rows($checkUserQuery) === 0) {
        $_SESSION['error_message'] = 'Nieprawidłowa nazwa użytkownika.';
        mysqli_close($dbConn);
        header('Location: chat.php');
        exit();
    }
    $sql = "INSERT INTO messages (message, fromUser, toUser, attachment) 
            VALUES ('$message', '$fromUser', '$toUser', '$fileName')";
    $sendQuery = mysqli_query($dbConn, $sql);
    if ($_FILES["attachment"]["name"] !== '') {
        if ($ftpConn) {
            $ftpLogin = ftp_login($ftpConn, $ftpUsername, $ftpPassword);
            if ($ftpLogin) {
                if (ftp_chdir($ftpConn, $ftpChatDir)) {
                    if (ftp_put($ftpConn, $fileName, $attachment, FTP_BINARY)) {
                        if ($sendQuery) {
                        } else {
                            ftp_close($ftpConn);
                            mysqli_close($dbConn);
                            header('Location: chat.php');
                            exit();
                        }
                    }
                }
            }
        }
    } else {
        ftp_close($ftpConn);
        mysqli_close($dbConn);
        header('Location: chat.php');
        exit();
    }

    mysqli_close($dbConn);
}
header('Location: chat.php');
