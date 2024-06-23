<?php

declare(strict_types=1);
session_start();
require('access.php');

$dbConn = mysqli_connect($dbHost, $dbUsername, $dbPassword, $dbDatabase);
if (!$dbConn) {
    header("Location: forum.php?temat=$id");
    exit();
}

mysqli_query($dbConn, "SET NAMES 'utf8'");

$fromUser = mysqli_real_escape_string($dbConn, $_POST['creator']);
$id = intval($_POST['topic']);
$message = mysqli_real_escape_string($dbConn, $_POST['message']);

$wordMap = [
    'chuj ci w dupe' => 'jesteś super',
    'jebać cie' => 'pozdrawiam Cie',
    'cholera' => 'kurcze',
    'kurwa' => 'kurde',
    'debil' => 'niemądry',
    'spierdalaj' => 'spadaj',
    'chuj' => 'kijek',
    'chuju' => 'kolego',
    'dupa' => 'pupcia',
    'dupe' => 'pupcię',
];

$badWordDetected = false;

foreach ($wordMap as $badWord => $niceWord) {
    $pattern = '/' . preg_quote($badWord, '/') . '/i';
    $message = preg_replace_callback($pattern, function ($matches) use ($niceWord, &$badWordDetected) {
        $badWordDetected = true;
        $wordToReplace = ctype_upper($matches[0][0]) ? ucfirst($niceWord) : $niceWord;
        return "<i>{$wordToReplace}</i>";
    }, $message);
}

$sql = "INSERT INTO comments (topic, creator, text) 
            VALUES (?, ?, ?)";
$stmt = mysqli_prepare($dbConn, $sql);
mysqli_stmt_bind_param($stmt, 'iss', $id, $fromUser, $message);
$sendQuery = mysqli_stmt_execute($stmt);

if ($badWordDetected) {
    $sqlBlacklist = "INSERT INTO blacklist (username, reason, topic) VALUES (?,?,?)";
    $stmtBlacklist = mysqli_prepare($dbConn, $sqlBlacklist);
    mysqli_stmt_bind_param($stmtBlacklist, 'ssi', $fromUser, $_POST['message'], $id);
    $sendQueryBlacklist = mysqli_stmt_execute($stmtBlacklist);

    $_SESSION['error_message'] = 'Twój komentarz zawierał niedozwolone słowa.';
}

mysqli_close($dbConn);
header("Location: forum.php?temat=$id");
exit();
