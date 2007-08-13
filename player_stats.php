<html>
<body>
<?
include 'hs_common.inc';

mysql_connect(localhost,$username,$password);
@mysql_select_db($database) or die("Unable to select database");

$player = $_POST['stats_player'];

if (!$player) die("No player selected");

$num_games = load_array('games', 'name', $games);

$query = "SELECT * FROM `players` WHERE `id`='$player'";
$result = mysql_query($query) or die (mysql_error());
$player_name = utf8_encode(mysql_result($result, 0, 'name'));

$query = "SELECT * FROM `matches` ORDER BY `date`";
$matches_result = mysql_query($query) or die(mysql_error());
$num_matches = mysql_numrows($matches_result);

if (!$num_matches) die ("No matches played");

// Not strictly necessary, but this way a null string isn't displayed.
$total_wins = 0;
$total_draws = 0;
$total_losses = 0;

$gnuplot_file = fopen("/tmp/plotdata", "w");

for ($i = 0; $i < $num_matches; $i++)
{
    $match_num = mysql_result($matches_result, $i, 'id');
    $game_num = mysql_result($matches_result, $i, 'game');
    $query = "SELECT * FROM `scores` WHERE `match`='$match_num'";
    $scores_result = mysql_query($query) or die(mysql_error());
    $num_scores = mysql_numrows($scores_result);
    $player_present = FALSE;
    $highest = 0;
    $winner_tied = FALSE;
    $player_score = 0;

    for ($j = 0; $j < $num_scores; $j++)
    {
        $score = mysql_result($scores_result, $j, 'score');
        $this_player = mysql_result($scores_result, $j, 'player');
        if ($this_player == $player)
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

    if ($player_present)
    {
        if ($player_score != $highest)
        {
            $total_losses++;
            $losses[$game_num]++;
        }
        elseif ($winner_tied)
        {
            $total_draws++;
            $draws[$game_num]++;
        }
        else
        {
            $total_wins++;
            $wins[$game_num]++;
        }
    }

    if ($total_wins + $total_losses)
        $wl_ratio = $total_wins / ($total_wins + $total_losses) * 100;
    else
        $wl_ratio = 0;

    fwrite($gnuplot_file, mysql_result($matches_result, $i, 'date') . " " .
           $wl_ratio . "\n");

}

$best_games = array();

if ($total_wins || $total_draws)
{
    if ($total_wins)
        $array = &$wins;
    else
        $array = &$draws;
    $highest_wins = 0;
    for ($i = 1; $i <= $num_games; $i++)
    {
        if ($array[$i] > $highest_wins)
        {
            $best_games = array($games[$i]);
            $highest_wins = $array[$i];
        }
        elseif ($array[$i] == $highest_wins)
            array_push($best_games, $games[$i]);
    }
}

mysql_close();

$plot_results = system("gnuplot /var/www/hs.gp", $retvar);

echo "<h3>Stats for $player_name</h3>";
echo "<p><table><tr><td>";
echo "<table><tr><td>Wins:</td><td>$total_wins</td></tr>",
     "<tr><td>Draws:</td><td>$total_draws</td></tr>",
     "<tr><td>Losses:</td><td>$total_losses</td></tr>";
echo "<tr><td>Best game", count($best_games) > 1 ? "s" : "",
     " by number of wins:</td><td>", join(', ', $best_games), "</td></tr>";
echo "</table>";
echo "</td><td>";
echo "$plot_results<br>$retvar<br><img src=\"/hs.png\">";
echo "</td></tr></table></p>";

back_button();

?>
</body>
</html>
