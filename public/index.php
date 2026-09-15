<?php

if (!isset($_SESSION))
{
    session_start();
}

if (isset($_SESSION['login']))
{
    header('Location: pages/lista.php');
    exit;
}

header('Location: pages/login.php');
exit;

?>
