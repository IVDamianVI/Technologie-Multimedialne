<?php

declare(strict_types=1);
session_start();
if (isset($_SESSION['loggedin'])) {
    $user = $_SESSION['user'];
    $userGroup = $_SESSION['userGroup'];
} else {
    $userGroup = 'guest';
    $user = 'Gość';
}

?>
<header>
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <!-- Toggle button START -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarButtons"
                aria-controls="navbarButtons" aria-expanded="false" aria-label="Toggle navigation">
                <i class="bi bi-list" style="font-size: 1em;"></i>
            </button>
            <a class="navbar-brand" href="./">
                <img src="media/favicon/favicon-orange.png" id="logo" alt="Logo"
                    style="margin-top: -1px; margin-left: 2px;" />
            </a>
            <!-- Toggle button END -->
            <!-- Account Mobile START -->
            <div class="mobile-only">
                <?php include 'accountCircle.php'; ?>
            </div>
            <!-- Account Mobile END -->
            <!-- Collapsible wrapper START -->
            <div class="collapse navbar-collapse align-items-center" id="navbarButtons" toggle="collapse"
                data-target=".navbar-collapse">
                <ul class="navbar-nav align-items-center">
                    <?php
                    require_once 'access.php';
                    $dbConnection = new mysqli($dbHost, $dbUsername, $dbPassword, $dbDatabase);
                    if ($dbConnection->connect_error) {
                        die("Connection failed: " . $dbConnection->connect_error);
                    }
                    $query = "SELECT * FROM `cms_headers` WHERE `id_cms` = 1";
                    $result = $dbConnection->query($query);
                    while ($row = $result->fetch_assoc()) {
                        echo '<li class="nav-item ';
                        if ($_SESSION['page'] == $row['url'] . '.php') {
                            echo 'wybrana-strona';
                        }
                        echo '">';
                        echo '<a class="nav-link" href="' . $row['url'] . '.php">' . $row['name'] . '</a>';
                        echo '</li>';
                    }
                    $dbConnection->close();
                    ?>
                    <?php if ($userGroup == 'admin') { ?>
                        <li class="nav-item <?php if ($_SESSION['page'] == 'edit-header.php')
                            echo 'wybrana-strona'; ?>">
                            <a class="nav-link" href="edit-header.php"><i class="bi bi-pencil-fill"></i> Edytuj menu</a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
            <!-- Collapsible wrapper END -->
            <!-- Account PC START -->
            <div class="pc-only" style="<?php if (!isset($_SESSION['loggedin'])) {
                echo 'display: none;';
            } ?>">
                <div class="dropdown">
                    <img src="media/avatar/<?php echo $_SESSION['avatar']; ?>" alt="Avatar"
                        style="margin-top: -1px; margin-left: 2px;" id="account" data-bs-toggle="dropdown"
                        class="mx-auto rounded-circle img-end d-block" />
                    <ul class="dropdown-menu dropdown-menu-end mt-2 mb-2"
                        style="background-color: #0e0e0e !important; border: 2px solid #151515; border-radius: 15px !important;">
                        <li style="margin-left: 15px; margin-right: 15px; margin-top: 15px;">
                            <p style="font-size: 1.5em; font-weight: bold; line-height: 1px; margin: 0; padding: 0;">
                                <?php echo $_SESSION['user']; ?>
                            </p><br>
                            <a href="profile.php" id="showProfile" class="text-top"
                                style="line-height: 0px; margin: 0; padding: 0;">
                                <p>Zobacz profil</p>
                            </a>
                        </li>
                        <!-- Przycisk do zmiany avatara -->
                        <li style="margin-left: 15px; margin-right: 15px;" class="text-center">
                            <a class="dropdown-item" href="changeAvatar.php">
                                <i class="bi bi-image"></i> Zmień avatar</a>
                        </li>
                        <!-- Przycisk do wylogowania -->
                        <li style="margin-left: 15px; margin-right: 15px;" class="text-center">
                            <a class="dropdown-item" href="logOutScript.php">
                                <i class="bi bi-box-arrow-right"></i> Wyloguj się</a>
                        </li>
                    </ul>
                </div>
            </div>
            <!-- Account PC END -->

            <div class="pc-only" style="<?php if (isset($_SESSION['loggedin'])) {
                echo 'display: none;';
            } ?>">
                <div class="dropdown">
                    <img src="media/avatar/_default_avatar.svg" alt="Avatar" style="margin-top: -1px; margin-left: 2px;"
                        id="account" data-bs-toggle="dropdown" class="mx-auto rounded-circle img-end d-block" />
                    <ul class="dropdown-menu dropdown-menu-end mt-2 mb-2"
                        style="background-color: #0e0e0e !important; border: 2px solid #151515; border-radius: 15px !important;">
                        <li style="margin-left: 15px; margin-right: 15px; margin-top: 15px;">
                            <p style="font-size: 1.5em; font-weight: bold; line-height: 1px; margin: 0; padding: 0;">
                                Gość
                            </p><br />
                        </li>
                        <!-- Przycisk do zalogowania -->
                        <li style="margin-left: 15px; margin-right: 15px;" class="text-center">
                            <a class="dropdown-item" href="logIn.php">
                                Zaloguj się
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

        </div>
        <!-- Container wrapper END -->
    </nav>

</header>