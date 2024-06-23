<?php

declare(strict_types=1);
session_start();
$user = $_SESSION['user'];
$userGroup = $_SESSION['userGroup'];
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
                    <!-- <li class="nav-item <?php //if ($_SESSION['page'] == 'index.php')
                        //echo 'wybrana-strona'; ?>">
                        <a class="nav-link" href="index.php">Strona Główna</a>
                    </li> -->
                    <li class="nav-item <?php if ($_SESSION['page'] == 'spotify.php' || $_SESSION['page'] == 'spotify-upload.php' || $_SESSION['page'] == 'spotify-utwory.php')
                        echo 'wybrana-strona'; ?>">
                        <a class="nav-link" href="spotify.php">Spotify</a>
                    </li>
                    <li class="nav-item <?php if ($_SESSION['page'] == 'films.php'|| $_SESSION['page'] == 'film-upload.php' || $_SESSION['page'] == 'films-all.php' || $_SESSION['page'] == 'film-playlist.php' || $_SESSION['page'] == 'film.php')
                        echo 'wybrana-strona'; ?>">
                        <a class="nav-link" href="films.php">Netflix</a>
                    </li>
                    <li class="nav-item <?php if ($_SESSION['page'] == 'chat.php')
                        echo 'wybrana-strona'; ?>">
                        <a class="nav-link" href="chat.php">Komunikator</a>
                    </li>
                    <li class="nav-item <?php if ($_SESSION['page'] == 'cloud.php')
                        echo 'wybrana-strona'; ?>">
                        <a class="nav-link" href="cloud.php">My Cloud</a>
                    </li>
                    <li class="nav-item dropdown <?php if ($_SESSION['page'] == 'netstat.php' || $_SESSION['page'] == 'skrypty.php' || $_SESSION['page'] == 'geolocation.php')
                        echo 'wybrana-strona'; ?>">
                        <?php
                        if ($userGroup === "admin") {
                            echo '<a class="nav-link dropdown-toggle" href="" data-bs-toggle="dropdown">Geolokalizacja</a>';
                        } else {
                            echo '<a class="nav-link" href="geolocation.php">Logowania</a>';
                        }
                        ?>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="netstat.php">Netstat</a></li>
                            <li><a class="dropdown-item" href="phpInfo.php">PHP Info</a></li>
                            <li><a class="dropdown-item" href="skrypty.php">Skrypty</a></li>
                            <?php
                            if ($userGroup === "admin") {
                                echo '<li><a class="dropdown-item" href="geolocation.php">Geolokalizacja</a></li>';
                            } else {
                                echo '<li><a class="dropdown-item" href="geolocation.php">Historia logowania</a></li>';
                            }
                            ?>
                        </ul>
                    </li>
                </ul>
            </div>
            <!-- Collapsible wrapper END -->
            <!-- Account PC START -->
            <div class="pc-only">
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
                        <!-- Przycisk do chatu -->
                        <li style="margin-left: 15px; margin-right: 15px;" class="text-center">
                            <a class="dropdown-item" href="chat.php">
                                <i class="bi bi-chat-dots-fill"></i> Komunikator</a>
                        </li>
                        <!-- Przycisk do chmury -->
                        <li style="margin-left: 15px; margin-right: 15px;" class="text-center">
                            <a class="dropdown-item" href="cloud.php">
                                <i class="bi bi-cloud-fill"></i> Chmura</a>
                        </li>
                        <!-- Przycisk do zmiany avatara -->
                        <li style="margin-left: 15px; margin-right: 15px;" class="text-center">
                            <a class="dropdown-item" href="changeAvatar.php">
                                <i class="bi bi-image"></i> Zmień avatar</a>
                        </li>
                        <!-- Przycisk do chatu -->
                        <li style="margin-left: 15px; margin-right: 15px;" class="text-center">
                            <a class="dropdown-item" href="geolocation.php">
                                <i class="bi bi-clock-history"></i> Historia logowań</a>
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
        </div>
        <!-- Container wrapper END -->
    </nav>

</header>