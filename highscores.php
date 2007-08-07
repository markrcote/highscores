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

<SCRIPT LANGUAGE="JavaScript" SRC="/CalendarPopup.js"></SCRIPT>
<SCRIPT LANGUAGE="JavaScript">document.write(getCalendarStyles());</SCRIPT>
<SCRIPT LANGUAGE="JavaScript">
var cal = new CalendarPopup("testdiv1");
var num_playing = 0;

<?php
// Load arrays into javascript.
echo "var num_players = $num_players;\n";
echo "var num_games = $num_games;\n";
echo "var players = new Array($num_players);\n";
echo "var games = new Array($num_games);\n";
for ($i = 1; $i <= $num_players; $i++)
    echo "players[$i] = \"", $players[$i], "\";\n";
for ($i = 1; $i <= $num_games; $i++)
    echo "games[$i] = \"", $games[$i], "\";\n";
?>


function createSelect(form, row_name, array, num)
{
    var select = document.createElement("select");
    select.setAttribute("name", row_name);

    for (var i = 0; i < num; i++)
        select.options[i] = new Option(array[i+1], i+1, false);
    
    var p = document.createElement("p");
    form = document.getElementById(form);
    form.appendChild(p);
    form.appendChild(select);
}


function createScoreRow(form)
{
    row_name = "player";
    row_name = row_name.concat(num_playing);
    createSelect(form, row_name, players, num_players);

    var score = document.createElement("input");
    score.setAttribute("type", "text");
    score_name = "score";
    score_name = score_name.concat(num_playing);
    score.setAttribute("name", score_name);
    score.setAttribute("size", "5");

    form = document.getElementById(form);
    form.appendChild(score);
    num_playing++;
    num_players_hidden = document.getElementById("num_players_hidden");
    num_players_hidden.setAttribute("value", num_playing);
}

</SCRIPT>
</head>

<body>

<h3>Add new match</h3>

<table>
<tr>
<td>
<form name="match" action="insert_match.php" method="post" id="matchform">

<select name="game">
<?php
for ($i = 1; $i <= $num_games; $i++)
    echo "<option value=$i>", $games[$i], "</option>\n";
?>
</select>

<INPUT TYPE="text" NAME="date1" VALUE="" SIZE=10>
<A HREF="#"
   onClick="cal.select(document.forms['match'].date1,'anchor1','yyyy-MM-dd'); return false;"
NAME="anchor1" ID="anchor1">+</A>&nbsp;&nbsp;<input type="text" name="hour" value="" size=2>:<input type="text" name="minute" value="" size=2>
</td></tr>
<tr>
<td>
<input type="button" onClick="createScoreRow('matchform')" value="Add Player"/>
</td></tr>
<tr><td>
<input type="submit" value="Submit">
</td></tr>
</table>
<input type="hidden" name="num_players" id="num_players_hidden" value="0"/>

</form>

<script type="text/javascript">createScoreRow('matchform')</script>

<p>
<table>
<form action="insert_game_player.php" method="post">
<tr><td><b>Add game:</b></td><td><input type="text" size="25" name="newgame"></td></tr>
<tr><td><b>Add player:</b></td><td><input type="text" size="25" name="newplayer">
<input type="submit" value="Add"></td></tr>
</form>
</table>
</p>

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

<DIV ID="testdiv1" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>

</body>
</html>
