<?php

class Match
{
    private $scores = array();
    public $match_id = 0;
    public $game_id = 0;
    public $datetime = "";
    private $match_result = "";

    // Constructs a Match class out of a mysql select result.
    function __construct($matches_result = "", $row = 0)
    {
        if ($matches_result)
        {
            $this->match_id = mysql_result($matches_result, $row, 'id');
            $this->game_id = mysql_result($matches_result, $row, 'game');
            $this->datetime = mysql_result($matches_result, $row, 'date');
        }
    }

    function get_scores()
    {
        if ($this->scores)
            return $this->scores;

        $query = "SELECT * FROM `scores` WHERE `match`='$this->match_id'";
        $scores_result = mysql_query($query) or die(mysql_error());
        $num_scores = mysql_numrows($scores_result);

        for ($i = 0; $i < $num_scores; $i++)
        {
            $score = mysql_result($scores_result, $i, 'score');
            $player = mysql_result($scores_result, $i, 'player');
            $this->scores[$player] = $score;
        }

        return $this->scores;
    }
}


class MatchIterator implements Iterator
{
    public $num_matches = 0;
    private $game = "";
    private $valid = FALSE;
    private $matches_result = "";
    private $row = 0;

    function __construct($game = 0)
    {
        $this->game = $game;
        $this->reset();
    }

    protected function reset()
    {
        if ($this->game)
            $game_choice = "WHERE `game`='$this->game' ";
        else
            $game_choice = "";

        $query = "SELECT * FROM `matches` $game_choice ORDER BY `date`";
        $this->matches_result = mysql_query($query) or die(mysql_error());
        $this->num_matches = mysql_numrows($this->matches_result);
    }

    function rewind()
    {
        $this->reset();
        $this->row = 0;
        $this->match = new Match();
        $this->next();
    }

    function current()
    {
        return $this->match;
    }

    function key()
    {
        return $this->match->match_id;
    }

    function next()
    {
        if ($this->row >= $this->num_matches)
        {
            $this->valid = FALSE;
            $this->match = new Match();
            return $this->match;
        }

        $this->match = new Match($this->matches_result, $this->row);
        $this->valid = TRUE;
        $this->row++;
        return $this->match;
    }

    function valid()
    {
        return $this->valid;
    }

}

?>
