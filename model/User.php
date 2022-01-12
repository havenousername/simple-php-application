<?php
require_once 'enums/Role.php';

class User
{
    private static int $counter = 0;
    private int $id;
    private string $username;
    private string $email;
    private string $password;
    private int $role;

    /**
     * User constructor.
     * @param string $username
     * @param string $email
     * @param string $password
     * @param int|null $role
     */
    public function __construct(string $username, string $email, string $password, int $role = null)
    {
        if (!(session_status() === PHP_SESSION_ACTIVE)) {
            session_start();
        }

        self::$counter = count($_SESSION['users']);
        $this->id = self::$counter;
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;

        if ($role == null) {
            $this->role = Role::$COMMENTATOR;
        } else {
            $this->role = $role;
        }

        $_SESSION['users'][] = $this;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }


    /**
     * @param string $password
     * @return bool
     */
    public function equals(string $password): bool {
        return $password === $this->password;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param User $user
     * @return false|mixed
     */
    public static function hasUser(User $user) {
        return $_SESSION['users'][$user->getId()] ?? false;
    }

    /**
     * @param string $email
     * @return User | bool
     */
    public static function getUserFromEmail(string $email): User|bool
    {
        return current(array_filter($_SESSION['users'], function (User $user) use ($email) {
            return $user->getEmail() == $email;
        }));
    }

    /**
     * @param string $username
     * @return User|bool
     */
    public static function getUserFromUsername(string $username): User|bool
    {
        return current(array_filter($_SESSION['users'], function (User $user) use ($username) {
            return $user->getUsername() == $username;
        }));
    }

    /**
     * @param string $email
     * @return bool
     */
    public static function hasEmail(string $email): bool
    {
        return count(array_filter($_SESSION['users'], function (User $user) use ($email) {
            return $user->getEmail() == $email;
        })) > 0 ?? 0;
    }

    /**
     * @param string $username
     * @return bool
     */
    public static function hasUsername(string $username): bool
    {
        return count(array_filter($_SESSION['users'], function (User $user) use ($username) {
            return $user->getUsername() == $username;
        })) > 0;
    }

    /**
     * @return int
     */
    public function getRole(): int
    {
        return $this->role;
    }
}