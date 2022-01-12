<?php
error_reporting(E_ALL);
ini_set("display_errors","On");
include_once 'Validation.php';
include_once 'model/User.php';
include_once 'Register.php';

session_start();

if ($_POST) {
    $username = $_POST['username'] ?? null;
    $password = $_POST['password'] ?? null;

    if (!isset($username) || $username == '') {
        $errors['username-required'] = 'Username field is required';
    } elseif (!User::hasUsername($username)) {
        $errors['username-missing'] = 'Such username doesnt exits, please register first';
    } else {
        $user = User::getUserFromUsername($username);
    }

    if (!isset($password) || $password == '') {
        $errors['password-required'] = 'Password field is required';
    } elseif (isset($user) && !$user->equals($password)) {
        $errors['password-wrong'] = 'Entered password is incorrect. Please try again';
    } elseif (isset($user)) {
        Register::logIn($user);
        header('Location: index.php');
    }
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
    <title>Login</title>
</head>
<body>
<h1 class="fs-2 text-center mt-5">Login user</h1>
<div class="container">
    <form method="post" novalidate>
        <div class="mb-3">
            <label for="input-email" class="form-label">Username</label>
            <input name="username" type="email" class="form-control" autocomplete="false" id="input-username" aria-describedby="username-errors"
                   value="<?= $username ?? '' ?>"
            >
            <?php if(isset($errors['username-required'])): ?>
                <div id="username-error" class="form-text text-danger">
                    <?= $errors['username-required'] ?>
                </div>
            <?php elseif (isset($errors['username-missing'])): ?>
                <div id="username-error" class="form-text text-danger">
                    <?= $errors['username-missing'] ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="mb-3">
            <label for="input-password" class="form-label">Password</label>
            <input name="password" type="password" class="form-control" autocomplete="false" id="input-password"
                   value="<?= $password ?? '' ?>"
            >
            <?php if(isset($errors['password-required'])): ?>
                <div id="password-error" class="form-text text-danger">
                    <?= $errors['password-required'] ?>
                </div>
            <?php elseif (isset($errors['password-wrong'])): ?>
                <div id="password-error" class="form-text text-danger">
                    <?= $errors['password-wrong'] ?>
                </div>
            <?php endif; ?>
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
    </form>
</div>
</body>
</html>
