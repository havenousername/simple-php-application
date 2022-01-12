<?php
require_once 'User.php';

class Comment
{
    private int $id;
    private User $author;
    private string $text;
    private Team $team;


    public function __construct(User $author, string $text,Team $team) {
        if (!(session_status() === PHP_SESSION_ACTIVE)) {
            session_start();
        }
        $this->id = count($_SESSION['comments']);
        $this->author = $author;
        $this->text = $text;
        $this->team = $team;
    }

    /**
     * @return Team
     */
    public function getTeam(): Team
    {
        return $this->team;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @return User
     */
    public function getAuthor(): User
    {
        return $this->author;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    public static function removeComment(int $id) {
        unset($_SESSION['comments'][$id]);
    }

    public static function getCommentsByTeam(Team $team): array
    {
        return array_filter($_SESSION['comments'] ?? [], function ($comment) use ($team) {
            return $comment->getTeam()->getId() === $team->getId();
        });
    }

}