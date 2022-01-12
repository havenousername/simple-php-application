<?php

class MatchesIterable implements Iterator {
    /**
     * @var int
     */
    private int $counter;
    /**
     * @var TeamMatch[]
     */
    private array $teamMatches;

    /**
     * @var int
     */
    private int $offset;

    /**
     * MatchesIterable constructor.
     * @param TeamMatch[] $teamMatches
     */
    public function __construct(array $teamMatches, $offset = 5)
    {
        $this->counter = 0;
        $this->teamMatches = $teamMatches;
        $this->offset = $offset;
    }

    /**
     * @param Team $team
     */
    public function filterByTeam(Team $team)
    {
        $matches = array_filter($this->teamMatches, function ($match) use ($team) {
            return $match->getHome()->getId() == $team->getId();
        });

        usort($matches, "\\TeamMatch::compare");
        $this->update(array_reverse($matches));
    }

    public function update($matches)
    {
        $this->teamMatches = $matches;
    }

    public function current()
    {
        return array_slice($this->teamMatches, $this->offset * $this->counter , $this->offset);
    }

    public function next()
    {
        $this->counter++;
    }

    public function previous()
    {
        $this->counter--;
    }

    public function key(): int
    {
        return $this->counter;
    }

    public function valid(): bool
    {
        return isset($this->teamMatches[$this->counter * $this->offset]);
    }

    public function hasNext(): bool
    {
        return isset($this->teamMatches[$this->counter * $this->offset + $this->offset + 1]);
    }

    public function hasPrevious(): bool
    {
        return isset($this->teamMatches[$this->counter * $this->offset - 1]);
    }


    public function rewind()
    {
        $this->counter = 0;
    }

    /**
     * @return int
     */
    public function size(): int
    {
        return count($this->teamMatches);
    }
}