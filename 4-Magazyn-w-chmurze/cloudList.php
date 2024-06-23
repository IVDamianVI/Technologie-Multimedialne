<div class="container pb-5 cloud-list">
    <table id="table" class="table table-sm table-hover">
        <thead>
            <tr>
                <th class="th-sm">Nazwa <i class="fa fa-fw fa-sort"></i></th>
                <th class="th-sm pc-only">Właściciel <i class="fa fa-fw fa-sort"></i></th>
                <th class="th-sm">Utworzono <i class="fa fa-fw fa-sort"></i></th>
                <th class="th-sm">Rozmiar pliku <i class="fa fa-fw fa-sort"></i></th>
                <!-- <th class="th-sm">Typ</th> -->
                <th class="options">
                    <i class="bi bi-three-dots-vertical"></i>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php
            $dbConn = mysqli_connect($dbHost, $dbUsername, $dbPassword, $dbDatabase);
            $username = $_SESSION['user'];
            $userGroup = $_SESSION['userGroup'];

            if ($dbConn) {
                mysqli_query($dbConn, "SET NAMES 'utf8'");

                $query = "SELECT * FROM cloud WHERE username = '$username' ORDER BY fileName ASC";
                $result = mysqli_query($dbConn, $query);
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

                    echo '<tr class="align-middle">';
                    echo '<td><a class="file" href="media/cloud/' . $username . '/' . $fileName . '" target="_parent"><span class="' . $icon . '"></span> ' . $fileName . '</a></td>';
                    echo '<td class="pc-only">' . $owner . '</td>';
                    echo "<td>$fileCreated</td>";
                    echo "<td>$fileSizeInMB</td>";
                    // echo "<td>$fileType</td>";
                    echo '<td class="options">
                            <div class="dropdown">
                                <i class="bi bi-three-dots-vertical orange" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>
                                <div class="dropdown-menu" aria-labelledby="optionsDropdown">
                                    <div class="dropdown">';
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
                    echo '</div></div></td>';
                    echo "</tr>";
                }
            }

            mysqli_close($dbConn);
            ?>
        </tbody>
    </table>
</div>