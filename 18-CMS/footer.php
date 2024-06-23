<?php

declare(strict_types=1);
?>
<footer class="text-center text-white <?php echo $isMobile ? '' : 'fixed-bottom'; ?>"
    style="background-color: #151515;">
    <!-- Grid container -->
    <div class="container p-2">
        <!-- Section: Social media -->
        <section class="" style="color: #c6c6c6;">
            <!-- Facebook -->
            <a class="btn btn-outline-light btn-floating m-1 rounded-circle social-media"
                href="https://www.facebook.com/ivdamianvi" role="button">
                <i class="bi bi-facebook"></i></a>
            <!-- Instagram -->
            <a class="btn btn-outline-light btn-floating m-1 rounded-circle social-media"
                href="https://www.instagram.com/ivdamianvi" role="button">
                <i class="bi bi-instagram"></i></a>
            <!-- Github -->
            <a class="btn btn-outline-light btn-floating m-1 rounded-circle social-media"
                href="https://github.com/ivdamianvi" role="button">
                <i class="bi bi-github"></i></a>
            <!-- Discord -->
            <a class="btn btn-outline-light btn-floating m-1 rounded-circle social-media"
                href="https://discordapp.com/users/ivdamianvi" role="button">
                <i class="bi bi-discord"></i></a>
        </section>
        <!-- Section: Social media -->
    </div>
    <!-- Grid container -->
    <!-- Copyright -->
    <div class="text-center p-3" style="background-color: #0e0e0e; color: #c6c6c6;">
        <!-- Change the year to the current year -->
        <?php echo '© ' . date('Y') . ' '; ?>
        <a class="" style="text-decoration: none; color: #ff9000;" href="../index.html">Damian Grubecki</a>
    </div>
    <!-- Copyright -->
</footer>