<?php
error_reporting(E_ALL);
ini_set("display_errors","On");

include_once "model/Team.php";
include_once "model/Comment.php";
include_once "Data.php";
include_once 'Register.php';
session_start();
$loggedId = isset($_COOKIE['logged']) ? intval($_COOKIE['logged']) : null;


if ($_GET) {
    if (isset($_GET['register'])) {
        header('Location: register.php');
    } elseif (isset($_GET['login'])) {
        header('Location: login.php');
    }

    $team_id = $_GET['id'];
    /** @var Data $team */
    $data = $_SESSION['data'];

    /** @var Team $team */
    $team = $data->getTeams()[$team_id];

    /** @var User|null $user */
    $user = isset($loggedId) ? $_SESSION['users'][$loggedId] : null;

    if ($_POST) {
        $commentText = $_POST['comment'] ?? '';

        if (isset($_POST['delete-comment'])) {
            Comment::removeComment(intval($_POST['delete-comment']));
        } else if (isset($_POST['logout'])) {
            Register::logout();
            header('Location: team-details.php');
        } else if (!isset($commentText) || $commentText == '') {
            $errors['comment'] = "Comment can not be empty";
        } else {
            $comment = new Comment($user, $commentText, $team);
            $_SESSION['comments'][$comment->getId()] = $comment;
        }
    }
    /** @var Comment[] $comments */
    $comments = Comment::getCommentsByTeam($team);
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
    <title>Document</title>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid justify-content-between">
            <?php if (isset($user)): ?>
                <span class="navbar-brand mb-0 h1">
                    <a href="index.php" class="nav-link active">
                        Welcome, <?= $user->getUsername() ?>
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
    <?php if (isset($team) && isset($data)): ?>
        <div class="container">
            <div class="d-flex justify-content-between mt-3 fs-5">
                 <span>Team Name</span>
                <?php if (!$team->isFavourite()): ?>
                    <a href="helpers/favourite.php?id=<?= $team->getId() ?>">
                        <img src="./assets/star.svg" alt="not favourite">
                    </a>
                <?php else: ?>
                    <a href="helpers/favourite.php?id=<?= $team->getId() ?>">
                        <img src="./assets/star-fill.svg" alt="favourite">
                    </a>
                <?php endif; ?>

            </div>
            <h4 class="fw-bold fs-3">
                <?= $team->getName()?>
            </h4>
            <span class="d-block mt-3 fs-5">
                Matches
            </span>
            <ul class="list-group">
                <?php if (count($data->filterLastMatches($team)) === 0): ?>
                    <blockquote class="blockquote">
                        <p>No matches were played yet by this team</p>
                    </blockquote>
                <?php else: ?>
                    <?php foreach ($data->filterLastMatches($team) as $match): ?>
                        <li
                            class="list-group-item d-flex justify-content-between align-items-start border
                        <?= $match->isHomeWon() ? 'border-success' : ($match->isDraw() ? 'border-warning' : 'border-danger') ?>"
                        >
                            <div class="ms-2 me-auto">
                                <div class="fw-bold">
                                    <?= $data->getTeams()[$match->getHome()->getId()]->getName() ?>
                                    -
                                    <?= $data->getTeams()[$match->getAway()->getId()]->getName() ?>
                                </div>
                                <?= $match->getDate() ?>
                                <?php if(isset($user) && $user->getRole() === Role::$ADMIN): ?>
                                    <a href="match-details.php?id=<?= $match->getId() ?>" class="card-link d-block">Edit Match</a>
                                <?php endif; ?>
                            </div>
                            <span class="badge rounded-pill
                            <?= $match->isHomeWon() ? 'bg-success' : ($match->isDraw() ? 'bg-warning' : 'bg-danger') ?>"
                            >
                                        <?= $match->getHome()->getScore() ?>
                                            -
                                        <?= $match->getAway()->getScore() ?>
                                    </span>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>


            </ul>
            <span class="d-block mt-3 fs-5">
                Comments
            </span>
            <?php if (isset($loggedId)): ?>
                <div id="add-comment-wrapper">
                    <button id="add-comment" type="button" class="btn btn-success">
                        Add comment
                    </button>
                </div>
                <div id="form" class="form">
                </div>
            <?php else: ?>
                Please register first in order to add comments
            <?php endif; ?>
            <div class="d-flex flex-wrap">
                <?php if (isset($comments)): ?>
                    <?php foreach ($comments as $comment): ?>
                        <div class="card text-dark bg-light m-2" style="flex-basis: 40%">
                            <div class="card-header">
                                <?= $comment->getAuthor()->getUsername() ?>
                            </div>
                            <div class="card-body">
                                <p class="card-text">
                                    <?= $comment->getText() ?>
                                </p>
                                <?php if(isset($user) and $user->getRole() === Role::$ADMIN): ?>
                                    <form method="post">
                                        <input type="text" class="d-none" name="delete-comment" value="<?= $comment->getId() ?>">
                                        <button type="submit" id="delete-comment" class="btn btn-danger my-2">Delete comment</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const btnAddComment = document.getElementById('add-comment');

            const createAddCommentFields = () => {
                const dangerButton = document.createElement('button');
                dangerButton.classList.add('btn');
                dangerButton.classList.add('btn-danger');
                dangerButton.classList.add('my-2');
                dangerButton.setAttribute('type', 'button');
                dangerButton.innerHTML = 'Close';

                const form = document.createElement('div');
                form.innerHTML = `
                <form method="post">
                    <textarea class="form-control" aria-label="With textarea" name="comment"></textarea>
                    <?php if(isset($errors['comment'])): ?>
                        <div id="comment-error" class="form-text text-danger">
                            <?= $errors['comment'] ?>
                        </div>
                    <?php endif; ?>
                    <button type="submit" id="add-comment-submit" class="btn btn-success my-2">Add comment</button>
                </form>
                `;

                const wrapper = document.getElementById('form');
                wrapper.appendChild(dangerButton);
                wrapper.appendChild(form);

                dangerButton.addEventListener('click', () => {
                    btnAddComment.classList.remove('d-none');
                    form.remove();
                    dangerButton.remove();
                });

                btnAddComment.classList.add('d-none');
            }

            if (!btnAddComment) {
                return;
            }

            <?php if (isset($errors['comment'])) ?>
                createAddCommentFields();
            <?php ?>

            btnAddComment.addEventListener('click', () => {
               createAddCommentFields();
            });
        });
    </script>
</body>
</html>
