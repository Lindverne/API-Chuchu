<?php

function VerificarLogin(): void
{
    if (!isset($_SESSION))
    {
        session_start();
    }

    if (!isset($_SESSION['login']))
    {
        header('Location: login.php');
        exit;
    }
}
