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

<script type="text/javascript" src="/hs_common.js"></script>
<script type="text/javascript">
var num_playing = 0;

<?php
js_arrays($num_players, $players, $num_games, $games);
?>

function submitForm(url, request)
{ 
    var xhr; 

    document.getElementById("zone").innerHTML = "<p>Thinking...</p>";

    if (window.XMLHttpRequest)
    {
        xhr = new XMLHttpRequest();
        if (xhr.overrideMimeType) 
        {
            xhr.overrideMimeType('text/html');
        }
    } 
    else if (window.ActiveXObject) 
    {
        try
        {
            xhr = new ActiveXObject("Msxml2.XMLHTTP");
        }
        catch (e)
        {
            try
            {
                xhr = new ActiveXObject("Microsoft.XMLHTTP");
            }
            catch (e) {}
        }
    }

    xhr.onreadystatechange  = function()
    { 
        if (xhr.readyState  == 4)
        {
            if (xhr.status  == 200) 
                document.getElementById("zone").innerHTML =
                xhr.responseText;
            else 
                document.getElementById("zone").innerHTML = 
                "Error code " + xhr.status;
        }
    }; 

    xhr.open('POST', url, true); 
    xhr.setRequestHeader('Content-Type',
                         'application/x-www-form-urlencoded');
    xhr.send(request);
} 


function checkPlayerStats()
{
    var stats_player = document.getElementById("player_stats_player");
    var stats_game = document.getElementById("player_stats_game");
    var stats_type = document.getElementById("player_stats_type");
    var plyr_id = stats_player.options[stats_player.selectedIndex].value;
    var game_id = stats_game.options[stats_game.selectedIndex].value;
    var type_id = stats_type.options[stats_type.selectedIndex].value;
    var request = 'stats_player=';
    request = request.concat(plyr_id, '&stats_game=', game_id, '&stats_type=',
                             type_id);
    submitForm('player_stats.php', request);
}


function checkGameStats()
{
    var stats_game = document.getElementById("game_stats");
    var game_id = stats_game.options[stats_game.selectedIndex].value;
    var request = 'stats_game=';
    request = request.concat(game_id);
    submitForm('game_stats.php', request);
}
</script>

</head>

<body>

<table width=100% bgcolor=#A08080>
<tr><td><b><font size=+1>Player Stats</font></b></td><td><b><font size=+1>Game Stats</font></b></td>
<td rowspan=2>
<b><a href="data_entry.php">Add</a> new matches, players, and games.</b>
</td>
</tr>
<tr><td>
<table cellpadding=10><tr><td id="playercell"></td>
<form name="playerstatsform" id="playerstatsform" method="post">
<script type="text/javascript">
createSelect('playercell', 'player_stats_player', players, num_players)
</script>

<td id="gamecell"></td></tr>
<script type="text/javascript">
stats_game = createSelect('gamecell', 'player_stats_game', games, num_games);
stats_game.options[stats_game.length] = new Option("-All games-", 0, true);
</script>

<tr><td>
<select name="player_stats_type" id="player_stats_type">
  <option value="wins">Win/Loss Ratio</option>
  <option value="points">Point Ratio</option>
</select>
</td><td><input type="button" value="Check Stats" onclick="checkPlayerStats()">
</td></tr>
</form>

</table>
</td>
<td>
<table cellpadding=10>
<form name="gamestatsform" id="gamestatsform" method="post">
<tr><td id="gamestatsgame">
<script type="text/javascript">
createSelect('gamestatsgame', 'game_stats', games, num_games)
</script></td></tr>
<tr><td>
<input type="button" value="Check Stats" onclick="checkGameStats()">
</form>
</td></tr></table>
</td>
</tr>
</table>

<div id="zone"></div>

<script type="text/javascript">checkPlayerStats()</script>

</body>
</html>
