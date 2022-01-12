<?php
error_reporting(E_ALL);
ini_set("display_errors","On");

require_once('Data.php' );
require_once 'model/User.php';
require_once 'Register.php';
require_once 'model/MatchesIterable.php';

session_start();

if (!isset($_SESSION['data'])) {
    $_SESSION['data'] = new Data();
    $_SESSION['comments'] = [];
    $_SESSION['users'] = [];
}

if ($_GET) {
    if (isset($_GET['register'])) {
        header('Location: register.php');
    } elseif (isset($_GET['login'])) {
        header('Location: login.php');
    }
}

if ($_POST) {
    if (isset($_POST['logout'])) {
        Register::logout();
        header('Location: index.php');
    }
}
/** @var Data $data */
$data = $_SESSION['data'];
/** @var User[] $users */
$users = $_SESSION['users'] ?? [];
$loggedId = isset($_COOKIE['logged']) ? intval($_COOKIE['logged']) : null;

$showFavourites = $_SESSION['indexTab'] ?? false;

// create admin
if (!User::hasEmail('admin@eltestadium.hu')) {
    $admin = new User("admin", "admin@eltestadium.hu", "admin", Role::$ADMIN);
}

$matches[] = [];
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>ELTE Stadium</title>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid justify-content-between">
        <?php if (isset($loggedId)): ?>
            <span class="navbar-brand mb-0 h1">
                <a href="index.php" class="nav-link active">
                    Welcome, <?= $users[$loggedId]->getUsername() ?>
                </a>
            </span>
            <form method="post">
                <button type="submit"
                        class="btn btn-primary me-4"
                        data-bs-toggle="collapse"
                        data-bs-target="#navbarNav"
                        aria-controls="navbarNav"
                        aria-expanded="false"
                        aria-label="Toggle navigation"
                        name="logout"
                >
                    Log out
                </button>
            </form>
        <?php else: ?>
            <span class="navbar-brand mb-0 h1">
                <a href="index.php" class="nav-link active">
                    Elte Stadium
                </a>
            </span>
            <form method="get">
                <button type="submit"
                        class="btn btn-primary me-4"
                        data-bs-toggle="collapse"
                        data-bs-target="#navbarNav"
                        aria-controls="navbarNav"
                        aria-expanded="false"
                        aria-label="Toggle navigation"
                        name="login"
                >
                    Login
                </button>
                <button type="submit" class="btn btn-outline-primary" name="register">
                    Register
                </button>
            </form>
        <?php endif; ?>
    </div>
</nav>
<div class="container">
    <h1 class="fs-2 text-center">Welcome to ELTE Stadium</h1>
    <p class="fs-3">Here you can watch teams and matches which will be happening between them in our splendid facility</p>
    <ul class="nav justify-content-center">
        <li class="nav-item">
            <a class="nav-link active" href="helpers/index-tabs.php?tab=all">Show all</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="helpers/index-tabs.php?tab=favourites">Show favourites</a>
        </li>
    </ul>
    <ul class="list-group">
        <?php foreach (( $showFavourites ? $data->getFavouriteTeams() : $data->getTeams()) as $team): ?>
            <li class="list-group-item">
                <div class="card">
                    <div class="card-header">
                        <?= $team->getName() ?>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><?= $team->getCity() ?></h5>
                        <p class="card-text">Last 5 matches</p>
                        <a href="team-details.php?id=<?= $team->getId() ?>" class="card-link d-block">Go to details</a>
                        <ul id="match-list-<?= $team->getId() ?>" class="list-group">
                            <?php foreach ($data->getCurrentMatch($team, $matches)->current() as $match): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div class="ms-2 me-auto">
                                        <div class="fw-bold">
                                            <?= $data->getTeams()[$match->getHome()->getId()]->getName() ?>
                                            -
                                            <?= $data->getTeams()[$match->getAway()->getId()]->getName() ?>
                                        </div>
                                        <?= $match->getDate() ?>
                                    </div>
                                    <span class="badge bg-primary rounded-pill">
                                        <?= $match->getHome()->getScore() ?>
                                            -
                                        <?= $match->getAway()->getScore() ?>
                                    </span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <?php if ($data->getCurrentMatch($team, $matches)->size() > 4): ?>
                        <div class="d-flex justify-content-center">
                            <button
                                    aria-describedby="<?= $team->getId() ?>"
                                    class="prev page-link m-2 <?=  $data->getCurrentMatch($team, $matches)->hasPrevious() ? 'd-block' : 'd-none' ?>"
                                    aria-label="Previous"
                            >
                                <span aria-hidden="true">&laquo; Previous</span>
                            </button>
                            <button
                                    aria-describedby="<?= $team->getId() ?>"
                                    class="next page-link m-2 <?=  $data->getCurrentMatch($team, $matches)->hasNext() ? 'd-block' : 'd-none' ?>"
                                    aria-label="Next"
                            >
                                <span aria-hidden="true">Next &raquo; </span>
                            </button>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const nexts = [...document.getElementsByClassName('next')];
        const prevs = [...document.getElementsByClassName('prev')];
        function send(teamId, type, typeElemNext, typeElemPrev) {
            const xhttp = new XMLHttpRequest();
            xhttp.open('GET', `./helpers/go-next-matches.php?team-id=${teamId}&${type}=true`);
            xhttp.send();
            xhttp.onload = function () {
                if (xhttp.status === 200) {
                    const res = JSON.parse(xhttp.response);
                    const currentMatchList = document.getElementById('match-list-' + teamId);
                    const matches = res.matches;
                    const teams = res.teams;
                    let matchesElems = '';
                    for (let i = 0; i < matches.length; i++) {
                        matchesElems += `
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div class="ms-2 me-auto">
                                        <div class="fw-bold">
                                            ${teams[matches[i].home.id].name} - ${teams[matches[i].away.id].name}
                                        </div>
                                        ${matches[i].date}
                                    </div>
                                    <span class="badge bg-primary rounded-pill">
                                        ${matches[i].home.score} - ${matches[i].away.score}
                                    </span>
                                </li>
                        `;
                    }

                    if (res.hasNext) {
                        typeElemNext.classList.remove('d-none');
                        typeElemNext.classList.add('d-flex');
                    } else {
                        typeElemNext.classList.remove('d-flex');
                        typeElemNext.classList.add('d-none');
                    }

                    if (res.hasPrev) {
                        typeElemPrev.classList.remove('d-none');
                        typeElemPrev.classList.add('d-flex');
                    } else {
                        typeElemPrev.classList.remove('d-flex');
                        typeElemPrev.classList.add('d-none');
                    }
                    currentMatchList.innerHTML = matchesElems;
                } else {
                    console.error(JSON.parse(xhttp.response));
                }
            }
        }

       nexts.map((next, i) => {
           next.addEventListener('click', (e) => {
               e.preventDefault();
               send(+next.getAttribute('aria-describedby'), 'next', next, prevs[i]);
           });
       });

       prevs.map((prev, i) => {
           prev.addEventListener('click', (e) => {
               e.preventDefault();
               send(+prev.getAttribute('aria-describedby'), 'prev', nexts[i], prev);
           })
       })
    });
</script>
</body>
</html>
