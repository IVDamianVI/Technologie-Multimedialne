<div class="container pb-5 grey-panel cloud-item">
    <?php
    $dbConn = mysqli_connect($dbHost, $dbUsername, $dbPassword, $dbDatabase);
    $username = $_SESSION['user'];
    $userGroup = $_SESSION['userGroup'];

    if ($dbConn) {
        mysqli_query($dbConn, "SET NAMES 'utf8'");

        $query = "SELECT * FROM cloud WHERE username = '$username' ORDER BY fileName ASC";
        $result = mysqli_query($dbConn, $query);
        $items = 5;
        $itemsInRow = 1;
        while ($row = mysqli_fetch_assoc($result)) {
            $id = $row['id'];
            $fileName = $row['fileName'];
            $fileSizeInBytes = $row['fileSize'];
            $fileSizeInMB = round($fileSizeInBytes / 1048576, 2) . " MB";
            $fileType = $row['fileType'];
            if ($fileType == 'directory') {
                $icon = 'icon bi bi-folder-fill';
                $fileSizeInMB = '';
            } else {
                $icon = 'icon';
            }
            $fileCreated = date("d.m.Y H:i", strtotime($row['created']));
            if ($username == $_SESSION['user']) {
                $user = "Ja";
            } else {
                $user = $username;
            }
            $owner = '<img src="media/avatar/' . $_SESSION['avatar'] . '" style="width: 1em; height: 1em;" class="rounded-circle"/> ' . $user;

            if ($items % 5 === 0) {
                echo '<div class="row">';
            }
            echo '<div class="col">';

            echo '<a class="file" href="media/cloud/' . $username . '/' . $fileName . '" target="_parent">';
            echo '<div class="item">
                <div class="item-icon">';
                if($fileType == 'directory'){
                    echo '<i class="icon bi bi-folder-fill"></i>';
                }else if($fileType == 'image/gif' || $fileType == 'image/jpeg' || $fileType == 'image/png' || $fileType == 'image/svg+xml'){
                    echo '<img src="media/cloud/' . $username . '/' . $fileName . '" alt="' . $fileName . '" class="img-cloud" />';
                }else if($fileType == 'video/mp4' || $fileType == 'video/ogg' || $fileType == 'video/webm'){
                    echo '<video class="img-cloud"><source src="media/cloud/' . $username . '/' . $fileName . '" type="' . $fileType . '"></video>';
                }else {
                    echo '<i class="icon bi bi-file-earmark-fill"></i>';
                }
                echo '</div></a>
                <div class="item-info">
                    <div class="dropdown">
                    <i class="bi bi-three-dots-vertical orange" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>
                    <a class="file" href="media/cloud/' . $username . '/' . $fileName . '" target="_parent"><span class="item-name" style="word-wrap: break-word;">' . $fileName.'</span></a>
                        <div class="dropdown-menu" aria-labelledby="optionsDropdown">';
                        if ($fileType == 'directory') {
                            echo '<a class="dropdown-item options-item options-item-rename" onclick="renameFile(`' . $fileName . '`, ' . $id . ')"
                                    href="#"><i class="bi bi-pencil-fill"></i> Zmień nazwę</a>
                                <a class="dropdown-item options-item options-item-delete" onclick="deleteFile(`' . $fileName . '`, ' . $id . ', `' . $fileType . '`)"
                                    href="#"><i class="bi bi-trash3-fill"></i> Usuń</a>';
                        } else {
                            echo '<a class="dropdown-item options-item" href="media/cloud/' . $username . '/' . $fileName . '" target="_blank"><i class="bi bi-eye-fill"></i> Podgląd</a>
                                <a class="dropdown-item options-item" href="media/cloud/' . $username . '/' . $fileName . '" download><i class="bi bi-download"></i> Pobierz</a>
                                <a class="dropdown-item options-item options-item-rename" onclick="renameFile(`' . $fileName . '`, ' . $id . ')"
                                    href="#"><i class="bi bi-pencil-fill"></i> Zmień nazwę</a>
                                <a class="dropdown-item options-item options-item-duplicate" onclick="duplicateFile(`' . $fileName . '`,' . $id . ')"
                                    href="#"><i class="bi bi-files"></i> Utwórz kopię</a>
                                <a class="dropdown-item options-item options-item-delete" onclick="deleteFile(`' . $fileName . '`, ' . $id . ', `' . $fileType . '`)"
                                    href="#"><i class="bi bi-trash3-fill"></i> Usuń</a>';
                        }
                        echo'</div>
                    </div>
                </div>
            </div>
        </div>';
            if ($itemsInRow % 5 === 0) {
                echo '</div>';
            }
            $items++;
            $itemsInRow++;
        }
    }

    mysqli_close($dbConn);
    ?>
</div>