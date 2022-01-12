<?php
error_reporting(E_ALL);
ini_set("display_errors","On");
require_once 'model/User.php';
require_once 'Register.php';
require_once 'Validation.php';

session_start();

if ($_POST) {
    $username = $_POST['username'] ?? null;
    $password = $_POST['password'] ?? null;
    $passwordRepeat = $_POST['repeat'] ?? null;
    $email = $_POST['email'] ?? null;
    $users = $_SESSION['users'];


    if (!isset($username) || $username == '') {
        $errors['username-required'] = 'Username field is required';
    } elseif (User::hasUsername($username)) {
        $errors['username-required'] = 'Username should be unique';
    }

    if (!isset($email) || $email == '') {
        $errors['email-required'] = 'Email field is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email-format'] = 'Email should have specified format required';
    } elseif (User::hasEmail($email)) {
        $errors['email-exists'] = 'This email is already registered at our service';
    }


    if (!isset($password) || $password == '') {
        $errors['password-required'] = 'Password field is required';
    } elseif ($passwordRepeat != $password) {
        $errors['password-repeat'] = 'Passwords dont match';
    }  else {
        Validation::validatedPassword($password, $errors);
    }

    if (!isset($errors)) {
        $user = new User($username, $email, $password);
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
    <title>Register</title>
</head>
<body>
<div class="container mt-5">
    <h1 class="fs-2 text-center">Register new user</h1>
    <form method="post" novalidate>
        <div class="mb-3">
            <label for="input-username" class="form-label">Username*</label>
            <input name="username" type="email" class="form-control" autocomplete="false" id="input-username" aria-describedby="username-errors"
                   value="<?= $username ?? '' ?>"
            >
            <?php if(isset($errors['username-required'])): ?>
                <div id="username-error" class="form-text text-danger">
                    <?= $errors['username-required'] ?>
                </div>
            <?php elseif (isset($errors['username-repeated'])): ?>
                <div id="username-error" class="form-text text-danger">
                    <?= $errors['username-repeated'] ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="mb-3">
            <label for="input-email" class="form-label">Email address*</label>
            <input name="email" type="email" class="form-control" autocomplete="false" id="input-email" aria-describedby="emailHelp"
                   value="<?= $email ?? '' ?>"
            >
            <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div>
            <?php if(isset($errors['email-required'])): ?>
                <div id="email-error" class="form-text text-danger">
                    <?= $errors['email-required'] ?>
                </div>
            <?php elseif (isset($errors['email-format'])): ?>
                <div id="email-error" class="form-text text-danger">
                    <?= $errors['email-format'] ?>
                </div>
            <?php elseif (isset($errors['email-exists'])): ?>
                <div id="email-error" class="form-text text-danger">
                    <?= $errors['email-exists'] ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="mb-3">
            <label for="input-password" class="form-label">Password*</label>
            <input name="password" type="password" class="form-control" autocomplete="false" id="input-password"
                   value="<?= $password ?? '' ?>"
            >
            <?php if(isset($errors['password-required'])): ?>
                <div id="email-error" class="form-text text-danger">
                    <?= $errors['password-required'] ?>
                </div>
            <?php elseif (isset($errors['password-validation'])): ?>
                <div id="email-error" class="form-text text-danger">
                    <?= $errors['password-validation'] ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="mb-3">
            <label for="input-repeat" class="form-label">Repeat Password*</label>
            <input name="repeat" type="password" class="form-control" id="input-repeat">
            <?php if(isset($errors['password-repeat'])): ?>
                <div id="email-error" class="form-text text-danger">
                    <?= $errors['password-repeat'] ?>
                </div>
            <?php endif; ?>
        </div>
        <button type="submit" class="btn btn-primary">Register</button>
    </form>
</div>
</body>
</html>
