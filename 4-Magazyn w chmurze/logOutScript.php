<?php declare(strict_types=1);
session_start();
if (isset($_SESSION['loggedin']))
{
session_start();
session_unset();
header('Location: logIn.php');
}
?>


