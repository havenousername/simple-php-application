<?php

use JetBrains\PhpStorm\Pure;

include "MatchElement.php";
include(dirname(__FILE__)."/../interfaces/Comparable.php");

class TeamMatch implements Comparable {
    private static int $counter = 0;
    private int $id;
    private MatchElement $home;
    private MatchElement $away;
    private string $date;

    public function __construct(array $teamHome, array $teamAway, string $date)
    {

        $this->id = self::$counter++;
        $this->date = $date;
        $this->home = new MatchElement($teamHome[0], $teamHome[1]);
        $this->away = new MatchElement($teamAway[0], $teamAway[1]);
    }

    /**
     * @return MatchElement
     */
    public function getHome(): MatchElement
    {
        return $this->home;
    }

    public function getArray(): array
    {
        return [
          'id' => $this->getId(),
          'home' => ($this->getHome())->getArray(),
          'away' => $this->getAway()->getArray(),
          'date' => $this->getDate()
        ];
    }

    /**
     * @return MatchElement
     */
    public function getAway(): MatchElement
    {
        return $this->away;
    }

    /**
     * @return string
     */
    public function getDate(): string
    {
        return $this->date;
    }

    /**
     * @param string date $
     */
    public function setDate(string $date) {
        $this->date = $date;
    }

    public function setAwayScore(int $score) {
        $this->away->setScore($score);
    }

    public function setHomeScore(int $score) {
        $this->home->setScore($score);
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    #[Pure] public function isHomeWon(): bool {
        return $this->home->getScore() > $this->getAway()->getScore();
    }

    /**
     * @return bool
     */
    #[Pure] public function isAwayWon(): bool {
        return !$this->isHomeWon() && !$this->isDraw();
    }

    /**
     * @return bool
     */
    #[Pure] public function isDraw(): bool {
        return $this->home->getScore() == $this->getAway()->getScore();
    }

    /**
     * @param Comparable $one
     * @param Comparable $two
     * @return int
     */
    public static function compare(Comparable $one, Comparable $two): int
    {
        return $one->compareTo($two);
    }

    #[Pure] public function compareTo(Comparable $other): int
    {
        return strtotime($this->getDate()) > strtotime($other->getDate());
    }
}