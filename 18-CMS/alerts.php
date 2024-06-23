<?php
declare(strict_types=1);
session_start();
?>
<style>
    .alert {
        border: none !important;
        padding: .5rem !important;
        margin: 0px !important;
    }

    .alert-danger {
        background-color: #f8d7da50 !important;
    }

    .alert-success {
        background-color: #d1e7dd50 !important;
    }

    .alert-warning {
        background-color: rgba(231, 226, 209, 0.31) !important;
    }

    .alert hr {
        margin: .2em !important;
    }
</style>
<?php
if (isset($_SESSION['error_message'])) {
    echo '<div class="alert alert-danger" role="alert">';
    echo '<b><i class="bi bi-exclamation-triangle-fill"></i> Wystąpił problem</b><hr/>';
    echo $_SESSION['error_message'];
    echo '</div>';
    unset($_SESSION['error_message']);
} else if (isset($_SESSION['success_message'])) {
    echo '<div class="alert alert-success" role="alert">';
    echo '<b><i class="bi bi-check-circle-fill"></i> Sukces</b><hr/>';
    echo $_SESSION['success_message'];
    echo '</div>';
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['warning_message'])) {
    echo '<div class="alert alert-warning" role="alert">';
    echo '<b><i class="bi bi-info-circle-fill"></i> Uwaga</b><hr/>';
    echo $_SESSION['warning_message'];
    echo '</div>';
    unset($_SESSION['warning_message']);
}
?>