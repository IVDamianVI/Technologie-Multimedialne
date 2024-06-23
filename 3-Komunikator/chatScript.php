<?php
declare(strict_types=1);
session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: logIn.php');
    exit();
}
require('access.php');
$dbConn = mysqli_connect($dbHost, $dbUsername, $dbPassword, $dbDatabase);
$username = $_SESSION['user'];
$userGroup = $_SESSION['userGroup'];

if ($dbConn) {
    mysqli_query($dbConn, "SET NAMES 'utf8'");
    
    if ($userGroup === "admin") {
        $query = "SELECT * FROM messages ORDER BY datetime ASC";
        $stmt = mysqli_prepare($dbConn, $query);
    } else {
        $query = "SELECT * FROM messages WHERE fromUser = ? OR toUser = ? ORDER BY datetime ASC LIMIT 5";
        $stmt = mysqli_prepare($dbConn, $query);
        mysqli_stmt_bind_param($stmt, 'ss', $username, $username);
    }

    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);

        function convertLinksToHTML($message)
        {
            $pattern = '/(http[s]?:\/\/[^\s]+)/';
            $messageWithLinks = preg_replace($pattern, '<a href="$1" class="message-link" target="_blank">$1</a>', $message);
            return $messageWithLinks;
        }

        $output = '';

        while ($row = mysqli_fetch_assoc($result)) {
            if ($row['message'] != "NULL") {
                $message = convertLinksToHTML($row['message']);
            }
            $chatPath = 'media/chat/' . $row['fromUser'] . '/';
            $attachment = $row['attachment'];
            $isCurrentUser = ($row['fromUser'] === $username) ? true : false;
            $messageClass = ($isCurrentUser) ? 'sent-message' : 'received-message';
            $src = 'src="' . $chatPath . $attachment . '"';
            $href = 'href="' . $chatPath . $attachment . '"';
            $style = 'style="max-width: 90%; max-height: 15em;"';
            $messageDatetime = date("H:i", strtotime($row['datetime']));
            $messageDate = date("d.m", strtotime($row['datetime']));

            if ($messageClass == 'received-message') {
                if ($userGroup === "admin") {
                    $output .='<span class="fromUser">' . $messageDate . ' ' . $messageDatetime . ' '. $row['fromUser'] . ' <i class="bi bi-caret-right-fill"></i> ' . $row['toUser'] . '</span>';
                }else{
                    $output .='<span class="fromUser">' . $messageDate . ' ' . $messageDatetime . ' '. $row['fromUser'] . '</span>';
                }
            } else {
                $output .='<span class="toUser">' . $messageDate . ' ' . $messageDatetime . ' <i class="bi bi-caret-right-fill"></i> ' . $row['toUser'] . '</span>';
            }

            $output .= '<div class="list-group-item ' . $messageClass . '">';
            // $output .= '<div class="message-header">';
            // $output .= '<span class="message-datetime">' . $messageDate . ' ' . $messageDatetime . '</span> ';
            // if ($messageClass == 'received-message') {
            //     if ($userGroup === "admin") {
            //         $output .= '<span class="message-sender">' . $row['fromUser'] . ' <i class="bi bi-caret-right-fill"></i> ' . $row['toUser'] . '</span>';
            //     }else{
            //         $output .= '<span class="message-sender">' . $row['fromUser'] . '</span>';
            //     }
            // } else {
            //     $output .= '<span class="message-sender"> <i class="bi bi-caret-right-fill"></i> ' . $row['toUser'] . '</span>';
            // }
            // $output .= '</div>';
            if (($row['attachment'] != "NULL" || $row['attachment'] !== "") && $message != "NULL") {
                $output .= '<p class="message-text">' . $message . '</p>';
                if (isset($row['attachment'])) {
                    if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $attachment)) {
                        $output .= '<a ' . $href . ' target="_blank">
                        <img ' . $src . ' ' . $style . ' alt="Obraz">
                        </a>';
                    } else if (preg_match('/\.mp3$/i', $attachment)) {
                        $output .= '<audio controls ' . $style . '>
                                <source ' . $src . ' type="audio/mpeg">
                                Twoja przeglądarka nie obsługuje odtwarzacza audio.
                              </audio>';
                    } else if (preg_match('/\.(mp4|avi|mkv)$/i', $attachment)) {
                        $output .= '<a ' . $href . ' target="_blank">
                                <video ' . $style . ' controls autoplay muted>
                                <source ' . $src . ' type="video/mp4">
                                Twoja przeglądarka nie obsługuje odtwarzacza wideo.
                              </video></a>';
                    } else if (preg_match('/\.[^.]+$/i', $attachment)) {
                        $output .= '<a ' . $href . ' class="attachmentFile" download>
                        <i class="bi bi-file-earmark-fill"></i> ' . $attachment . '
                            </a>';
                    }
                }
            } else if ($message != "NULL") {
                $output .= '<p class="message-text">' . $message . '</p>';
            } else if ($row['attachment'] != "NULL" || $row['attachment'] != "") {
                if (isset($row['attachment'])) {
                    if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $attachment)) {
                        $output .= '<img ' . $src . ' ' . $style . ' alt="Obraz">';
                    } else if (preg_match('/\.mp3$/i', $attachment)) {
                        $output .= '<audio controls ' . $style . '>
                                <source ' . $src . ' type="audio/mpeg">
                                Twoja przeglądarka nie obsługuje odtwarzacza audio.
                                </audio>';
                    } else if (preg_match('/\.(mp4|avi|mkv)$/i', $attachment)) {
                        $output .= '<a ' . $href . ' target="_blank">
                                <video ' . $style . ' controls autoplay muted>
                                <source ' . $src . ' type="video/mp4">
                                Twoja przeglądarka nie obsługuje odtwarzacza wideo.
                                </video></a>';
                    } else if (preg_match('/\.[^.]+$/i', $attachment)) {
                        $output .= '<a ' . $href . ' class="attachmentFile" download>
                                    <i class="bi bi-file-earmark-fill"></i> ' . $attachment . '
                                    </a>';
                    }
                }
            }
            $output .= '</div>';
        }

        echo $output;
    } else {
        $_SESSION['error_message'] = 'Błąd przy pobieraniu wiadomości.';
    }
}

mysqli_close($dbConn);
?>
