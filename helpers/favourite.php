<?php
error_reporting(E_ALL);
ini_set("display_errors","On");
include(dirname(__FILE__)."/../Data.php");
session_start();

if ($_GET) {
    $team_id = $_GET['id'];
    /** @var Data $team */
    $data = $_SESSION['data'];

    /** @var Team $team */
    $team = $data->getTeams()[$team_id];

    $team->setIsFavourite(!$team->isFavourite());
    header('Location: ../team-details.php?id='. $team->getId());
}
