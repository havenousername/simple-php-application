<?php


class MatchElement
{
    private int $id;
    private int $score;

    public function __construct($id, $score)
    {
        $this->id = $id;
        $this->score = $score;
    }

    /**
     * @return int
     */
    public function getScore(): int
    {
        return $this->score;
    }

    public function setScore(int $score) {
        $this->score = $score;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    public function getArray(): array
    {
        return [
            'id' => $this->id,
            'score' => $this->score
        ];
    }


}