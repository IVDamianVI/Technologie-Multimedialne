<?php

declare(strict_types=1);
session_start();
require('access.php');

$dbConn = mysqli_connect($dbHost, $dbUsername, $dbPassword, $dbDatabase);
$user = htmlentities($_POST['user'], ENT_QUOTES, "UTF-8");
$pass = $_POST['pass'];
$pass1 = $_POST['pass1'];
$passHash = password_hash($pass, PASSWORD_BCRYPT);
$loginButton = '<br><br><a id="buttonLogin" href="logIn.php">Zaloguj się</a>';
$ftpChatDir = '/domains/ivdamianvi.smallhost.pl/public_html/z3/media/chat';
$ftpAvatarDir = '/domains/ivdamianvi.smallhost.pl/public_html/z3/media/avatar';
$ftpBannerDir = '/domains/ivdamianvi.smallhost.pl/public_html/z3/media/banner';

if ($dbConn) {
    mysqli_query($dbConn, "SET NAMES 'utf8'");

    if (isset($user) && isset($pass) && isset($pass1)) {
        $stmt = mysqli_prepare($dbConn, "SELECT * FROM users WHERE BINARY username=?");
        mysqli_stmt_bind_param($stmt, 's', $user);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) == 0) {
            $ftpConn = ftp_connect($ftpServer);

            if ($ftpConn) {
                $ftpLogin = ftp_login($ftpConn, $ftpUsername, $ftpPassword);

                if ($ftpLogin) {
                    $ftpChdirChat = ftp_chdir($ftpConn, $ftpChatDir);
                    
                    if ($ftpChdirChat && ftp_mkdir($ftpConn, $user)) {
                        ftp_close($ftpConn);

                        if (isset($_FILES["avatar"]) && $_FILES["avatar"]["error"] === 0) {
                            $user = htmlentities($_POST['user'], ENT_QUOTES, "UTF-8");
                            $avatar = $_FILES["avatar"]["tmp_name"];
                            $fileName = $_FILES["avatar"]["name"];

                            $ftpConn = ftp_connect($ftpServer);

                            if ($ftpConn) {
                                $ftpLogin = ftp_login($ftpConn, $ftpUsername, $ftpPassword);

                                if ($ftpLogin) {
                                    $ftpChdirAvatar = ftp_chdir($ftpConn, $ftpAvatarDir);

                                    if ($ftpChdirAvatar && ftp_put($ftpConn, $fileName, $avatar, FTP_BINARY)) { // Uploaded avatar to FTP
                                        $stmt = mysqli_prepare($dbConn, "INSERT INTO users (username, password, avatar) VALUES (?, ?, ?)");
                                        mysqli_stmt_bind_param($stmt, 'sss', $user, $passHash, $fileName);
                                        mysqli_stmt_execute($stmt);

                                        if ($stmt) {
                                            $_SESSION['success_message'] = 'Pomyślnie utworzono konto.';
                                            mysqli_close($dbConn);
                                            header('Location: logIn.php');
                                            exit();
                                        } else {
                                            $_SESSION['error_message'] = 'Błąd bazy danych.';
                                            mysqli_close($dbConn);
                                            header('Location: register.php');
                                            exit();
                                        }
                                    } else { 
                                        $_SESSION['error_message'] = 'Błąd przesyłania pliku na serwer FTP.';
                                        ftp_close($ftpConn);
                                        mysqli_close($dbConn);
                                        header('Location: register.php');
                                        exit();
                                    }
                                } else {
                                    $_SESSION['error_message'] = 'Błąd logowania do serwera FTP.';
                                    ftp_close($ftpConn);
                                    mysqli_close($dbConn);
                                    header('Location: register.php');
                                    exit();
                                }
                            } else {
                                $_SESSION['error_message'] = 'Błąd połączenia z serwerem FTP.';
                                mysqli_close($dbConn);
                                header('Location: register.php');
                                exit();
                            }
                        } else {
                            $stmt = mysqli_prepare($dbConn, "INSERT INTO users (username, password) VALUES (?, ?)");
                            mysqli_stmt_bind_param($stmt, 'ss', $user, $passHash);
                            mysqli_stmt_execute($stmt);

                            if ($stmt) {
                                $_SESSION['success_message'] = 'Pomyślnie utworzono konto.';
                                mysqli_close($dbConn);
                                header('Location: logIn.php');
                                exit();
                            } else {
                                $_SESSION['error_message'] = mysqli_error($dbConn);
                                mysqli_close($dbConn);
                                header('Location: register.php');
                                exit();
                            }
                        }
                    } else {
                        $_SESSION['error_message'] = 'Błąd utworzenia folderu na serwerze FTP.';
                        ftp_close($ftpConn);
                        mysqli_close($dbConn);
                        header('Location: register.php');
                        exit();
                    }
                } else {
                    $_SESSION['error_message'] = 'Błąd logowania do serwera FTP.';
                    ftp_close($ftpConn);
                    mysqli_close($dbConn);
                    header('Location: register.php');
                    exit();
                }
            } else {
                $_SESSION['error_message'] = 'Błąd połączenia z serwerem FTP.';
                mysqli_close($dbConn);
                header('Location: register.php');
                exit();
            }
        } else {
            $_SESSION['error_message'] = 'Wprowadź inną nazwę użytkownika.';
            mysqli_close($dbConn);
            header('Location: register.php');
            exit();
        }
    } else {
        $_SESSION['error_message'] = 'Wszystkie pola muszą być wypełnione.';
        mysqli_close($dbConn);
        header('Location: register.php');
        exit();
    }
} else {
    $_SESSION['error_message'] = 'Błąd połączenia z bazą danych.';
    header('Location: register.php');
    exit();
}
?>
