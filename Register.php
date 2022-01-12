<?php
require_once 'model/User.php';

class Register {
    public static function logIn(User $user) {
        setcookie('logged', $user->getId(), time() + 3600 * 24); // expires in 24 hours
    }

    public static function logout() {
        setcookie('logged', "", time() - 3600);
    }
}