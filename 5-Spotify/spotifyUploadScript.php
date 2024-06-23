<?php

declare(strict_types=1);
session_start();
$current_page = basename($_SERVER['PHP_SELF']);
if (!isset($_SESSION['loggedin'])) {
    header('Location: logIn.php');
    exit();
}
require('access.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $song = $_FILES["file-input"]["tmp_name"];
    $fileName = $_FILES["file-input"]["name"];
    $title = $_POST['title'];
    $musician = $_POST['musician'];
    $lyrics = $_POST['lyrics'];
    $musictypeid = $_POST['musictypeid'];
    $creator = $_SESSION['user'];

    $allowedExtensions = ["mp3", "wav", "ogg", "flac", "m4a"];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if (!in_array($fileExtension, $allowedExtensions)) {
        $_SESSION['error_message'] = 'Niedozwolone rozszerzenie pliku.';
        header('Location: spotify.php');
        exit();
    }

    $ftpConn = ftp_connect($ftpServer);

    if ($ftpConn) {
        $login = ftp_login($ftpConn, $ftpUsername, $ftpPassword);
        if ($login) {
            if (ftp_chdir($ftpConn, $ftpSongDir)) {
                if (ftp_put($ftpConn, $fileName, $song, FTP_BINARY)) {
                    $song = $_FILES['file-input']['name'];
                    $dbConn = mysqli_connect($dbHost, $dbUsername, $dbPassword, $dbDatabase);
                    if (!$dbConn) { //! Nie można połączyć z bazą danych
                        $_SESSION['error_message'] = 'Błąd połączenia z bazą danych.';
                        header('Location: spotify.php');
                        exit();
                    } else {
                        mysqli_query($dbConn, "SET NAMES 'utf8'");
                        $stmt = mysqli_prepare($dbConn, "INSERT INTO song (title, musician, creator, filename, lyrics, musictypeid) VALUES (?, ?, ?, ?, ?, ?)");
                        mysqli_stmt_bind_param($stmt, 'sssssi', $title, $musician, $_SESSION['user'], $song, $lyrics, $musictypeid);
                        $stmtResult = mysqli_stmt_execute($stmt);

                        if ($_POST['playlistid'] != "null") {
                            $result = mysqli_query($dbConn, "SELECT * FROM song WHERE title = '$title' AND creator = '$creator' AND musician = '$musician' AND musictypeid = $musictypeid AND lyrics = '$lyrics';");
                            $row = mysqli_fetch_assoc($result);
                            $newSongID = $row['id'];

                            $stmt = mysqli_prepare($dbConn, "INSERT INTO playlistdatabase (playlistid, songid) VALUES (?, ?)");
                            mysqli_stmt_bind_param($stmt, 'ii', $_POST['playlistid'], $newSongID);
                            $stmtResult = mysqli_stmt_execute($stmt);
                        }

                        $_SESSION['success_message'] = 'Utwór został dodany pomyślnie.';
                        header('Location: spotify.php');
                        exit();
                    }
                } else { //! Nie można przesłać pliku na serwer FTP
                    $_SESSION['error_message'] = 'Błąd przesyłania pliku na serwer FTP.';
                    header('Location: spotify.php');
                    exit();
                }
            } else { //! Nie można zmienić katalogu na serwerze FTP
                $_SESSION['error_message'] = 'Błąd zmiany katalogu na serwerze FTP.';
                header('Location: spotify.php');
                exit();
            }
        } else { //! Nie można zalogować się do serwera FTP
            $_SESSION['error_message'] = 'Błąd logowania do serwera FTP.';
            header('Location: spotify.php');
            exit();
        }
    } else { //! Nie można połączyć się z serwerem FTP
        $_SESSION['error_message'] = 'Błąd połączenia z serwerem FTP.';
        header('Location: spotify.php');
        exit();
    }
} else { //! Nieprawidłowe żądanie
    $_SESSION['error_message'] = 'Nieprawidłowe żądanie.';
    header('Location: spotify.php');
    exit();
}
