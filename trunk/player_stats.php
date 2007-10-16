<?php

include 'hs_common.inc';
include 'match.php';
include 'plotter.php';

interface iCalculator
{
    public function process_match($match);
    public function done();
}


abstract class PlotterCalculator implements iCalculator
{
    protected $plotter;
    public $player = 0;
    public $game = 0;

    abstract protected function plot_string($match, $player_score,
                                            $highest, $winner_tied);
    abstract protected function print_stats();

    function __construct($player, $game)
    {
        $this->plotter = new Plotter('/var/www/plots', '/tmp/plotdata');
        $this->player = $player;
        $this->game = $game;
        $this->num_games = load_array('games', 'name', $this->games);
        $this->player_name = get_name("players", $player);
        
        if ($game)
            $this->game_name = get_name("games", $game);
        else
            $this->game_name = "All games";
    }

    function process_match($match)
    {
        $player_present = FALSE;
        $highest = 0;
        $winner_tied = FALSE;
        $player_score = 0;
        $scores = $match->get_scores();

        foreach ($scores as $player => $score)
        {
            if ($this->player == $player)
            {
                $player_present = TRUE;
                $player_score = $score;
            }
            if ($score == $highest)
                $winner_tied = TRUE;
            elseif ($score > $highest)
            {
                $highest = $score;
                $winner_tied = FALSE;
            }
        }

        if (!$player_present)
            return;

        $this->plotter->data($this->plot_string($match, $player_score,
                                                $highest, $winner_tied));
    }        

    function done()
    {
        $png_name = $this->player . time();
        $plot_results = $this->plotter->plot($png_name, $this->plot_title());

        echo "<h3>Stats for $this->player_name</h3>";
        echo "<p><table width=80%><tr><td valign=top>";
        echo $this->print_stats();
        echo "</td><td>";
        echo "<img src=\"/plots/$png_name.png\">";
        echo "</td></tr></table></p>";
    }

    abstract protected function plot_title();
}

class WinRatioCalculator extends PlotterCalculator
{
    // Not strictly necessary, but this way a null string isn't displayed.
    protected $total_wins = 0;
    protected $total_draws = 0;
    protected $total_losses = 0;
    protected $wins = array();
    protected $draws = array();
    protected $losses = array();

    protected function plot_string($match, $player_score, $highest,
                                   $winner_tied)
    {
        if ($player_score != $highest)
        {
            $this->total_losses++;
            $this->losses[$match->game_id]++;
        }
        elseif ($winner_tied)
        {
            $this->total_draws++;
            $this->draws[$match->game_id]++;
        }
        else
        {
            $this->total_wins++;
            $this->wins[$match->game_id]++;
        }

        if ($this->total_wins + $this->total_losses)
            $wl_ratio = $this->total_wins /
                       ($this->total_wins + $this->total_losses) * 100;
        else
            $wl_ratio = 0;

        return "$match->datetime $wl_ratio";
    }
    
    protected function print_stats()
    {
        $best_games = array();
        
        // Don't calculate nor display best games if particular game selected.
        if (!$this->game && ($this->total_wins || $this->total_draws))
        {
            if ($this->total_wins)
                $array = &$this->wins;
            else
                $array = &$this->draws;
            $highest_wins = 0;
            for ($i = 1; $i <= $this->num_games; $i++)
            {
                if ($array[$i] > $highest_wins)
                {
                    $best_games = array($this->games[$i]);
                    $highest_wins = $array[$i];
                }
                elseif ($array[$i] == $highest_wins)
                    array_push($best_games, $this->games[$i]);
            }
        }

        $stats = "<table><tr><td><b>Wins:</b></td>" .
                            "<td>$this->total_wins</td></tr>" .
                        "<tr><td><b>Draws:</b></td>" .
                            "<td>$this->total_draws</td></tr>" .
                        "<tr><td><b>Losses:</b></td>" .
                            "<td>$this->total_losses</td></tr>";
        if (!$this->game)
        {
            $stats .= "<tr><td><b>Best game";
            $stats .= count($best_games) > 1 ? "s" : "";
            $stats .= " by<br>number of wins:</b></td><td>" .
                      join(', ', $best_games) . "</td></tr>";
        }

        $stats .= "</table>";
        return $stats;
    }

    protected function plot_title()
    {
        return "Win/Loss Ratio - $this->game_name";
    }
}


class PointRatioCalculator extends PlotterCalculator
{
    protected $cumulative = 0;
    protected $num_matches = 0;
    protected $win_loss_matches = 0;

    protected function plot_string($match, $player_score, $highest,
                                   $winner_tied)
    {
        // Ignore win-loss games.
        if ($highest <= 1)
        {
            $this->win_loss_matches++;
            return "";
        }
        $this->num_matches++;
        $point_percent = $player_score / $highest * 100;
        $this->cumulative += $point_percent;
        return "$match->datetime $point_percent";
    }
    
    protected function print_stats()
    {
        $stats = "<table><tr><td><b>Matches played:</b></td>" .
                 "<td>" . ($this->num_matches + $this->win_loss_matches) .
                 "</td></tr><tr><td><b>Average percentage<br>of highest " .
                 "score:</b></td>" .
                 "<td>" . (int) ($this->cumulative / $this->num_matches) .
                 "%</td></tr><tr><td></td></tr></table>";
        return $stats;
    }

    protected function plot_title()
    {
        return "Point Ratio - $this->game_name";
    }
}


class CalculatorFactory
{
    function get_calculator($type, $player, $game)
    {
        if ($type == 'wins')
            return new WinRatioCalculator($player, $game);
        elseif ($type == 'points')
            return new PointRatioCalculator($player, $game);
        else
            return NULL;
    }
}


$player = $_REQUEST['stats_player'];
$game = $_REQUEST['stats_game'];
$type = $_REQUEST['stats_type'];

if (!$player)
    $player = 0;

if (!$game)
    $game = 0;

if (!$type)
    $type = 'wins';

mysql_connect(localhost, $username, $password);
@mysql_select_db($database) or die('Unable to select database');

$matches = new MatchIterator($game);

if (!$matches->num_matches) die ('No matches played');

$factory = new CalculatorFactory();
$calculator = $factory->get_calculator($type, $player, $game);

foreach ($matches as $match)
    $calculator->process_match($match);

$calculator->done();

mysql_close();

?>
