<?php
require_once 'model/TeamMatch.php';
require_once 'model/Team.php';
require_once 'model/MatchesIterable.php';
require_once 'Storage.php';


class Data
{
    /**
     * @var array
     */
    private array $teamsSrc;

    /**
     * @var array
     */
    private array $matchesSrc;

    /**
     * @var TeamMatch[] array
     */
    private array $matches;

    /**
     * @var Team[] array
     */
    private array $teams;



    public function __construct($teamsSrc = 'data/teams.json', $matchesSrc = 'data/matches.json')
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        try {
            $teamsFile = new Storage(new JsonIO($teamsSrc));
            $matchesFile = new Storage(new JsonIO($matchesSrc));

            $this->teamsSrc = $teamsFile->findAll();
            $this->matchesSrc = $matchesFile->findAll();

            $this->teams = array();
            array_map(
                function ($key, $value) {
                    $this->teams[$key] = new Team($value[0], $value[1]);
                },
                array_keys($this->teamsSrc),
                array_values($this->teamsSrc)
            );

            $this->matches = array();
            array_map(
                function ($key, $value) {
                    $this->matches[$key] = new TeamMatch($value[0], $value[1], $value[2]);
                },
                array_keys($this->matchesSrc),
                array_values($this->matchesSrc)
            );
        } catch (Exception $exception) {
            echo $exception->getMessage();
        }
    }

    /**
     * @param Team $team
     * @param int|null $size
     * @return TeamMatch[] array
     */
    public function filterLastMatches(Team $team, int $size = null): array
    {
        $matches = array_filter($this->matches, function ($match) use ($team) {
            return $match->getHome()->getId() == $team->getId();
        });


        usort($matches, "\\TeamMatch::compare");
        if ($size !== null) {
            return array_slice(array_reverse($matches), 0, $size);
        }
        return array_reverse($matches);
    }

    /**
     * @param Team|int $team
     * @param array|null $matches
     * @return MatchesIterable
     */
    function getCurrentMatch(Team|int $team, array | null &$matches = null): MatchesIterable
    {
        /** @var Data $data */
        $data = $_SESSION['data'];
        $team = is_int($team) ? $this->getTeams()[$team] : $team;

        $matchIt = new MatchesIterable($data->getMatches());
        $matchIt->filterByTeam($team);
        $howMuch = $_SESSION['match-page'][$team->getId()] ?? 0;
        while ($howMuch > 0 && $matchIt->valid()) {
            $matchIt->next();
            $howMuch--;
        }

        if (isset($matches)) {
            $matches[$team->getId()] = $matchIt->current();
        }

        return $matchIt;
    }

    /**
     * @param int $id
     * @return false|TeamMatch
     */
    public function getMatchById(int $id): bool|TeamMatch
    {
        return current(array_filter($this->matches, function ($match) use ($id) {
            return $match->getId() == $id;
        }));
    }

    public function getTeamById(int $id): bool|Team
    {
        return current(array_filter($this->teams, function ($match) use ($id) {
            return $match->getId() == $id;
        }));
    }

    public function getFavouriteTeams(): array {
        return array_filter($this->teams, function ($team) {
           return $team->isFavourite();
        });
    }
    /**
     * @return Team[]
     */
    public function getTeams(): array
    {
        return $this->teams;
    }

    /**
     * @return TeamMatch[]
     */
    public function getMatches(): array
    {
        return $this->matches;
    }

}