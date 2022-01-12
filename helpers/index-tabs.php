<?php
session_start();
if ($_GET) {
    $_SESSION['indexTab'] = $_GET['tab'] === 'favourites';
    header('Location: ../index.php');
}