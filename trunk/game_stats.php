<html>
<body>
<?php
include 'hs_common.inc';

mysql_connect(localhost, $username, $password);
@mysql_select_db($database) or die("Unable to select database");

$game = $_POST['stats_game'];

if (!$game) die("No game selected");

$num_players = load_array('players', 'name', $players);

$query = "SELECT * FROM `games` WHERE `id`='$game'";
$result = mysql_query($query) or die (mysql_error());
$game_name = utf8_encode(mysql_result($result, 0, 'name'));

$query = "SELECT * FROM `matches` WHERE `game`='$game'";
$matches_result = mysql_query($query) or die(mysql_error());
$num_matches = mysql_numrows($matches_result);

$best_players = array();
$most_wins = 0;
$worst_players = array();
$most_losses = 0;

$player_wins = array();
$player_losses = array();

for ($i = 0; $i < $num_matches; $i++)
{
    $match_num = mysql_result($matches_result, $i, 'id');
    $winners = array();
    $losers = array();
    $winning_score = 0;
    match_results($match_num, $winners, $losers, $winning_score);

    foreach ($winners as $winner)
    {
        $player_wins[$winner]++;
        if ($player_wins[$winner] > $most_wins)
        {
            $best_players = array($players[$winner]);
            $most_wins = $player_wins[$winner];
        }
        elseif ($player_wins[$winner] == $most_wins)
            array_push($best_players, $players[$winner]);
    }           

    foreach ($losers as $loser)
    {
        $player_losses[$loser]++;
        if ($player_losses[$loser] > $most_losses)
        {
            $worst_players = array($players[$loser]);
            $most_losses = $player_losses[$loser];
        }
        elseif ($player_losses[$loser] == $most_losses)
            array_push($worst_players, $players[$loser]);
    }
}

mysql_close();

echo "<h3>Stats for $game_name</h3>";
echo "<p><table>";
echo "<tr><td>Matches played:</td><td>$num_matches</td></tr>";
echo "<tr><td>Best player", count($best_players) > 1 ? "s" : "",
     " by number of wins:</td><td>", join(', ', $best_players), "</td></tr>";
echo "<tr><td>Worst player", count($worst_players) > 1 ? "s" : "",
     " by number of losses:</td><td>", join(', ', $worst_players),
     "</td></tr>";
echo "</table></p>";

back_button();
?>
</body>
</html>