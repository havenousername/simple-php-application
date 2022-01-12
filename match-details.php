<?php
error_reporting(E_ALL);
ini_set("display_errors","On");

require_once 'Data.php';
require_once 'model/User.php';
require_once 'model/TeamMatch.php';

session_start();

/**
 * @param $date
 * @param string $format
 * @return bool
 */
function validateDate($date, $format = 'Y-m-d'): bool
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

$loggedId = isset($_COOKIE['logged']) ? intval($_COOKIE['logged']) : null;
if (isset($_GET['id'])) {
    /** @var User|null $user */
    $user = isset($loggedId) ? $_SESSION['users'][$loggedId] : null;

    if ($user->getRole() !== Role::$ADMIN) {
        header('Location: index.php');
    }

    $id = $_GET['id'];
    /** @var Data $_SESSION */
    $data = $_SESSION['data'];
    /** @var TeamMatch $match */
    $match = $data->getMatchById($id);

    if ($_POST) {
        $homeScore = $_POST['home-match'] ?? null;
        $awayScore = $_POST['away-match'] ?? null;
        $date = $_POST['date-match'] ?? null;

        if (is_null($homeScore)) {
            $errors['homeScore'] = 'Score of home team is required';
        } elseif (!filter_var($homeScore, FILTER_VALIDATE_INT)) {
            $errors['homeScore'] = 'Score of home is not integer';
        } elseif ($homeScore < 0) {
            $errors['homeScore'] = 'Score of home could not be less than 0';
        }

        if (is_null($awayScore)) {
            $errors['awayScore'] = 'Score of away team is required';
        } elseif (!filter_var($awayScore, FILTER_VALIDATE_INT)) {
            $errors['awayScore'] = 'Score of away team is not integer';
        } elseif ($awayScore < 0) {
            $errors['awayScore'] = 'Score of away team could not be less than 0';
        }

        if (is_null($date)) {
            $errors['date'] = 'Date of match is required';
        } elseif (!validateDate($date)) {
            $errors['date'] = 'Date should have format "Y-m-d"';
        }

        if (!isset($errors)) {
            $match->setDate($date);
            $match->setAwayScore($awayScore);
            $match->setHomeScore($homeScore);
            header('Location: team-details.php?id='. $match->getHome()->getId());
        }
    }
} else {
    header('Location: index.php');
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Match details</title>
</head>
<body>
    <?php if (isset($data) && isset($match)): ?>
        <div class="container mt-5">
            <h1 class="fs-2 text-center">Edit Match</h1>
            <form method="post">
                <div class="mb-3 row">
                    <label for="match" class="col-sm-2 col-form-label">Match</label>
                    <div class="col-sm-10">
                        <input
                            id="match"
                            class="form-control"
                            type="text"
                            value="<?= $data->getTeamById($match->getHome()->getId())->getName() ?> - <?= $data->getTeamById($match->getAway()->getId())->getName() ?>"
                            aria-label="readonly input"
                            readonly
                        >
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="home-match" class="col-sm-2 col-form-label">Home result</label>
                    <div class="col-sm-10">
                        <input
                            type="text"
                            class="form-control"
                            id="home-match"
                            name="home-match"
                            value="<?= $match->getHome()->getScore() ?>"
                        >
                    </div>
                    <?php if(isset($errors['homeScore'])): ?>
                        <div id="error" class="form-text text-danger">
                            <?= $errors['homeScore'] ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="mb-3 row">
                    <label for="away-match" class="col-sm-2 col-form-label">Away result</label>
                    <div class="col-sm-10">
                        <input
                            type="text"
                            class="form-control"
                            name="away-match"
                            id="away-match"
                            value="<?= $match->getAway()->getScore() ?>"
                        >
                    </div>
                    <?php if(isset($errors['awayScore'])): ?>
                        <div id="error" class="form-text text-danger">
                            <?= $errors['awayScore'] ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="mb-3 row">
                    <label for="date-match" class="col-sm-2 col-form-label">Date</label>
                    <div class="col-sm-10">
                        <input
                            type="text"
                            class="form-control"
                            name="date-match"
                            id="date-match"
                            value="<?= $match->getDate() ?>"
                        >
                    </div>
                    <?php if(isset($errors['date'])): ?>
                        <div id="error" class="form-text text-danger">
                            <?= $errors['date'] ?>
                        </div>
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary">Update match</button>
            </form>
        </div>
    <?php endif; ?>

</body>
</html>
