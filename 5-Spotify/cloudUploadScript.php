<?php

session_start();
require('access.php');

$dbConn = mysqli_connect($dbHost, $dbUsername, $dbPassword, $dbDatabase);

if (isset($_FILES['files'])) {
    $files = $_FILES['files'];

    $ftpConn = ftp_connect($ftpServer);
    $loginResult = ftp_login($ftpConn, $ftpUsername, $ftpPassword);

    if ($ftpConn && $loginResult) {
        ftp_pasv($ftpConn, true);

        ftp_chdir($ftpConn, $ftpCloudDir);

        $successCount = 0;
        $errorCount = 0;
        $errorMessages = [];

        foreach ($files['name'] as $key => $name) {
            $tmpFilePath = $files['tmp_name'][$key];

            if ($tmpFilePath != "") {
                $newFileName = $name;

                $counter = 1;
                while (ftp_size($ftpConn, $ftpCloudDir . '/' . $newFileName) !== -1) {
                    $info = pathinfo($name);
                    $extension = isset($info['extension']) ? $info['extension'] : '';
                    $newFileName = $info['filename'] . "($counter)." . $extension;
                    $counter++;
                }

                $remoteFilePath = $ftpCloudDir . '/' . $newFileName;

                $fileType = $_FILES['files']['type'][$key];
                $fileSize = $_FILES['files']['size'][$key];

                if (ftp_put($ftpConn, $remoteFilePath, $tmpFilePath, FTP_BINARY)) {
                    $stmt = mysqli_prepare($dbConn, "INSERT INTO cloud (username, path, fileName, fileType, fileSize) VALUES (?, ?, ?, ?, ?)");
                    mysqli_stmt_bind_param($stmt, 'ssssi', $_SESSION['user'], $_SESSION['user'], $newFileName, $fileType, $fileSize);
                    $stmtResult = mysqli_stmt_execute($stmt);

                    if ($stmtResult) {
                        $successCount++;
                    } else {
                        $errorCount++;
                        $errorMessages[] = "Błąd bazy danych podczas dodawania pliku $newFileName.";
                    }
                } else {
                    $errorCount++;
                    $errorMessages[] = "Błąd podczas przesyłania pliku $newFileName na serwer FTP.";
                }
            }
        }

        ftp_close($ftpConn);

        if ($successCount > 0) {
            $_SESSION['success_message'] = 'Pomyślnie przesłano ' . $successCount . ' plik(i).';
            header('Location: cloud.php');
            exit();
        } elseif ($errorCount > 0) {
            $_SESSION['error_message'] = 'Wystąpiły błędy podczas przesyłania plików:<br>' . implode("<br>", $errorMessages);
            header('Location: cloud.php');
            exit();
        } else {
            $_SESSION['error_message'] = 'Błąd przesyłania plików. Nie udało się przesłać żadnego pliku.';
            header('Location: cloud.php');
            exit();
        }
    } else {
        $_SESSION['error_message'] = 'Błąd logowania do serwera FTP.';
        header('Location: cloud.php');
        exit();
    }
} else {
    $_SESSION['error_message'] = 'Brak przesłanych plików.';
    header('Location: cloud.php');
    exit();
}

?>
