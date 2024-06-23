<?php
declare(strict_types=1);
session_start();
require('access.php');

$dbConn = mysqli_connect($dbHost, $dbUsername, $dbPassword, $dbDatabase);
$user = htmlentities($_POST['user'], ENT_QUOTES, "UTF-8");
$pass = $_POST['pass'];
$wrongPass = 'Nieprawidłowa nazwa użytkownika/hasło!';

if ($dbConn) {
    mysqli_query($dbConn, "SET NAMES 'utf8'");
    
    $stmt = mysqli_prepare($dbConn, "SELECT * FROM users WHERE BINARY username=?");
    mysqli_stmt_bind_param($stmt, 's', $user);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($usersArray = mysqli_fetch_array($result)) {
        $passHash = $usersArray['password'];

        if (password_verify($pass, $passHash)) {
            $stmt = mysqli_prepare($dbConn, "SELECT * FROM users WHERE BINARY username=?");
            mysqli_stmt_bind_param($stmt, 's', $user);
            mysqli_stmt_execute($stmt);
            $userAssoc = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

            session_start();
            $_SESSION['loggedin'] = true;
            $_SESSION['user'] = $user;
            $_SESSION['avatar'] = $userAssoc['avatar'];
            $_SESSION['page'] = 'index.php';
            $_SESSION['userid'] = $userAssoc['id'];
            $_SESSION['created'] = $userAssoc['created'];
            $_SESSION['banner'] = $userAssoc['banner'];
            $_SESSION['userGroup'] = $userAssoc['userGroup'];
            header('Location: index.php');
        } else {
            $_SESSION['error_message'] = $wrongPass;
            header('Location: logIn.php');
            exit();
        }
    } else {
        $_SESSION['error_message'] = $wrongPass;
        header('Location: logIn.php');
        exit();
    }
} else {
    $_SESSION['error_message'] = 'Błąd połączenia z bazą danych.';
    header('Location: logIn.php');
    exit();
}

mysqli_close($dbConn);
?>
