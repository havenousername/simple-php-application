<?php
include(dirname(__FILE__)."/../Data.php");
error_reporting(E_ALL);
ini_set("display_errors","On");

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (isset($_GET['team-id'])) {
    $teamId = intval($_GET['team-id']) ?? 0;
    if (isset($_GET['next'])) {
        $_SESSION['match-page'][$teamId]++;
    } else {
        $_SESSION['match-page'][$teamId]--;
    }

    /** @var Data $data */
    $data = $_SESSION['data'];

    $team = $data->getTeams()[$teamId];

    if (!$team) {
        http_response_code(400);
        echo json_encode(array('error' => 'Such team does not exist'));
    }

    echo json_encode(
        array('matches' => array_map( function ($match) {
                return $match->getArray();
            },
                $data->getCurrentMatch($team)->current()
            ),
            'teams' => array_map(function ($team) {
                return $team->getArray();
            },$data->getTeams()),
            'hasNext' => $data->getCurrentMatch($team)->hasNext(),
            'hasPrev' => $data->getCurrentMatch($team)->hasPrevious()
        )
    );
}
