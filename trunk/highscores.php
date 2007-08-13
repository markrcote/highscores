<html>
<title>Board games!</title>
<head>
<?php
include 'hs_common.inc';

mysql_connect(localhost,$username,$password);
@mysql_select_db($database) or die("Unable to select database");

$num_players = load_array('players', 'name', $players);
$num_games = load_array('games', 'name', $games);

mysql_close();
?>

<script language="JavaScript" src="/hs_common.js"></script>
<script language="JavaScript">
var num_playing = 0;

<?php
js_arrays($num_players, $players, $num_games, $games);
?>
</script>

</head>

<body>

<h3>Player Stats</h3>
<p>
<form action="player_stats.php" method="post" id="playerstats">
<script type="text/javascript">createSelect('playerstats', 'stats_player', players, num_players)</script>
<input type="submit" value="Check Stats">
</form>
</p>

<h3>Game Stats</h3>
<p>
<form action="game_stats.php" method="post" id="gamestats">
<script type="text/javascript">createSelect('gamestats', 'stats_game', games, num_games)</script>
<input type="submit" value="Check Stats">
</form>
</p>

<p><a href="data_entry.php">Add</a> new matches, players, and games.</p>

</body>
</html>
