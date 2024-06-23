<?php

declare(strict_types=1);
session_start();

$current_page = basename($_SERVER['PHP_SELF']);
if (!isset($_SESSION['loggedin'])) {
    header('Location: logIn.php');
    exit();
}

require('access.php');

function getDbConnection()
{
    global $dbHost, $dbUsername, $dbPassword, $dbDatabase;
    $dbConn = mysqli_connect($dbHost, $dbUsername, $dbPassword, $dbDatabase);
    if (!$dbConn) {
        $_SESSION['error_message'] = 'Błąd połączenia z bazą danych.';
        header('Location: spotify.php');
        exit();
    }
    mysqli_query($dbConn, "SET NAMES 'utf8'");
    return $dbConn;
}

function checkPlaylistName($dbConn, $name, $id)
{
    $stmt = mysqli_prepare($dbConn, 'SELECT name FROM playlistname WHERE creator = ? AND name = ?');
    mysqli_stmt_bind_param($stmt, 'ss', $_SESSION['user'], $name);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) == 0) {
        return $name;
    } else {
        $id++;
        return checkPlaylistName($dbConn, "Moja playlista #$id", $id);
    }
}

$id = 1;
$name = "Moja playlista #$id";
$user = $_SESSION['user'];
$public = 0;

$dbConn = getDbConnection();

$name = checkPlaylistName($dbConn, $name, $id);

$stmt = mysqli_prepare($dbConn, "INSERT INTO playlistname (creator, name, public) VALUES (?, ?, ?)");
mysqli_stmt_bind_param($stmt, 'ssi', $user, $name, $public);
$stmtResult = mysqli_stmt_execute($stmt);

if ($stmtResult) {
    $_SESSION['success_message'] = 'Playlista została stworzona pomyślnie.';
} else {
    $_SESSION['error_message'] = 'Nie udało się utworzyć playlisty.';
}

$result = mysqli_query($dbConn, "SELECT * FROM playlistname WHERE name = '$name' AND creator = '$user';");
$row = mysqli_fetch_assoc($result);
$newPlaylistID = $row['id'];

mysqli_close($dbConn);
header('Location: spotify-playlist.php?id='.$newPlaylistID);
exit();
